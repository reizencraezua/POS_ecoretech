<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\DiscountRule;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Employee;
use App\Models\Log;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Quotation::onlyTrashed()->with(['customer', 'details'])
            : Quotation::with(['customer', 'details']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search in quotation fields
                $q->where('quotation_id', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('terms_and_conditions', 'like', "%{$search}%")
                  ->orWhere('total_amount', 'like', "%{$search}%")
                  // Search in customer fields
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('customer_firstname', 'like', "%{$search}%")
                                   ->orWhere('customer_lastname', 'like', "%{$search}%")
                                   ->orWhere('business_name', 'like', "%{$search}%")
                                   ->orWhere('customer_email', 'like', "%{$search}%")
                                   ->orWhere('customer_phone', 'like', "%{$search}%")
                                   ->orWhere('customer_address', 'like', "%{$search}%");
                  })
                  // Search in quotation details
                  ->orWhereHas('details', function ($detailQuery) use ($search) {
                      $detailQuery->where('product_name', 'like', "%{$search}%")
                                 ->orWhere('service_name', 'like', "%{$search}%")
                                 ->orWhere('description', 'like', "%{$search}%")
                                 ->orWhere('quantity', 'like', "%{$search}%")
                                 ->orWhere('unit_price', 'like', "%{$search}%")
                                 ->orWhere('total_price', 'like', "%{$search}%");
                  });
            });
        }

        $quotations = $query->orderBy('quotation_id', 'desc')->paginate(15)->appends($request->query());

        // If it's an AJAX request, return only the table content
        if ($request->ajax()) {
            return view('admin.quotations.partials.quotations-table', compact('quotations', 'showArchived'));
        }

        return view('admin.quotations.index', compact('quotations', 'showArchived'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();

        return view('admin.quotations.create', compact('customers', 'products', 'services', 'discountRules', 'units'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'quotation_date' => 'required|date',
                'valid_until' => 'required|date|after:quotation_date',
                'notes' => 'nullable|string|max:1000',
                'terms_and_conditions' => 'nullable|string|max:2000',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1|max:9999',
                'items.*.unit_id' => 'required|exists:units,unit_id',
                'items.*.size' => 'nullable|string|max:255',
                'items.*.price' => 'required|numeric|min:0|max:999999.99',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0|max:999999.99',
                'items.*.discountAmount' => 'nullable|numeric|min:0|max:999999.99',
                'items.*.discountRule' => 'nullable|string|max:255',
            ], [
                'customer_id.required' => 'Please select a customer.',
                'customer_id.exists' => 'Selected customer does not exist.',
                'quotation_date.required' => 'Quotation date is required.',
                'quotation_date.date' => 'Please enter a valid quotation date.',
                'valid_until.required' => 'Valid until date is required.',
                'valid_until.date' => 'Please enter a valid date.',
                'valid_until.after' => 'Valid until date must be after quotation date.',
                'items.required' => 'At least one item is required.',
                'items.min' => 'At least one item is required.',
                'items.*.type.required' => 'Item type is required.',
                'items.*.type.in' => 'Invalid item type selected.',
                'items.*.id.required' => 'Item ID is required.',
                'items.*.id.integer' => 'Invalid item ID.',
                'items.*.quantity.required' => 'Quantity is required.',
                'items.*.quantity.integer' => 'Quantity must be a whole number.',
                'items.*.quantity.min' => 'Quantity must be at least 1.',
                'items.*.quantity.max' => 'Quantity cannot exceed 9999.',
                'items.*.unit_id.required' => 'Unit is required.',
                'items.*.unit_id.exists' => 'Selected unit does not exist.',
                'items.*.price.required' => 'Price is required.',
                'items.*.price.numeric' => 'Price must be a number.',
                'items.*.price.min' => 'Price cannot be negative.',
                'items.*.price.max' => 'Price cannot exceed ₱999,999.99.',
            ]);

            DB::beginTransaction();

            $quotation = Quotation::create([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'],
                'terms_and_conditions' => $validated['terms_and_conditions'],
                'status' => 'Pending',
                'total_amount' => 0, // Will be calculated after details
                'created_by' => auth('web')->id(),
            ]);

            // Process quotation details
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];
                $layoutPrice = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0;
                $subtotal = $baseAmount + $layoutPrice;

                $quotation->details()->create([
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $layoutPrice,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Calculate final total amount using the model's calculation methods
            $finalTotalAmount = $quotation->fresh()->final_total_amount;
            
            // Update total amount
            $quotation->update(['total_amount' => $finalTotalAmount]);

            DB::commit();

            // Log the creation
            $this->logCreated(
                Log::TYPE_QUOTATION,
                $quotation->quotation_id,
                $this->generateTransactionName(Log::TYPE_QUOTATION, $quotation->quotation_id),
                $quotation->toArray()
            );

            return redirect()->route('admin.quotations.index')
                ->with('success', 'Quotation created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            LaravelLog::error('Quotation validation error: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            LaravelLog::error('Error creating quotation: ' . $e->getMessage());
            LaravelLog::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to create quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'details.product', 'details.service', 'details.unit', 'histories.updatedBy']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        $quotation->load(['details']);

        return view('admin.quotations.edit', compact('quotation', 'customers', 'products', 'services', 'discountRules', 'units'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        try {
            // Store original data for logging
            $originalData = $quotation->toArray();
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'quotation_date' => 'required|date',
                'valid_until' => 'required|date|after:quotation_date',
                'notes' => 'nullable|string|max:1000',
                'terms_and_conditions' => 'nullable|string|max:2000',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1|max:9999',
                'items.*.unit_id' => 'required|exists:units,unit_id',
                'items.*.size' => 'nullable|string|max:255',
                'items.*.price' => 'required|numeric|min:0|max:999999.99',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0|max:999999.99',
                'items.*.discountAmount' => 'nullable|numeric|min:0|max:999999.99',
                'items.*.discountRule' => 'nullable|string|max:255',
            ], [
                'customer_id.required' => 'Please select a customer.',
                'customer_id.exists' => 'Selected customer does not exist.',
                'quotation_date.required' => 'Quotation date is required.',
                'quotation_date.date' => 'Please enter a valid quotation date.',
                'valid_until.required' => 'Valid until date is required.',
                'valid_until.date' => 'Please enter a valid date.',
                'valid_until.after' => 'Valid until date must be after quotation date.',
                'items.required' => 'At least one item is required.',
                'items.min' => 'At least one item is required.',
                'items.*.type.required' => 'Item type is required.',
                'items.*.type.in' => 'Invalid item type selected.',
                'items.*.id.required' => 'Item ID is required.',
                'items.*.id.integer' => 'Invalid item ID.',
                'items.*.quantity.required' => 'Quantity is required.',
                'items.*.quantity.integer' => 'Quantity must be a whole number.',
                'items.*.quantity.min' => 'Quantity must be at least 1.',
                'items.*.quantity.max' => 'Quantity cannot exceed 9999.',
                'items.*.unit_id.required' => 'Unit is required.',
                'items.*.unit_id.exists' => 'Selected unit does not exist.',
                'items.*.price.required' => 'Price is required.',
                'items.*.price.numeric' => 'Price must be a number.',
                'items.*.price.min' => 'Price cannot be negative.',
                'items.*.price.max' => 'Price cannot exceed ₱999,999.99.',
            ]);

            DB::beginTransaction();

            $quotation->update([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'],
                'terms_and_conditions' => $validated['terms_and_conditions'],
            ]);

            // Delete existing quotation details
            $quotation->details()->delete();

            // Process new quotation details
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];
                $layoutPrice = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0;
                $subtotal = $baseAmount + $layoutPrice;

                $quotation->details()->create([
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $layoutPrice,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Calculate final total amount using the model's calculation methods
            $finalTotalAmount = $quotation->fresh()->final_total_amount;
            
            // Update total amount
            $quotation->update(['total_amount' => $finalTotalAmount]);

            DB::commit();

            // Log the update
            $this->logUpdated(
                Log::TYPE_QUOTATION,
                $quotation->quotation_id,
                $originalData,
                $quotation->fresh()->toArray(),
                $this->generateTransactionName(Log::TYPE_QUOTATION, $quotation->quotation_id)
            );

            return redirect()->route('admin.quotations.index')
                ->with('success', 'Quotation updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            LaravelLog::error('Quotation validation error: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            LaravelLog::error('Error updating quotation: ' . $e->getMessage());
            LaravelLog::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to update quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Closed',
        ]);

        $oldStatus = $quotation->status;
        $quotation->update(['status' => $validated['status']]);

        // Log the status change
        $this->logStatusChanged(
            Log::TYPE_QUOTATION,
            $quotation->quotation_id,
            $oldStatus,
            $validated['status'],
            $this->generateTransactionName(Log::TYPE_QUOTATION, $quotation->quotation_id)
        );

        return back()->with('success', 'Quotation status updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        // Log the deletion
        $this->logDeleted(
            Log::TYPE_QUOTATION,
            $quotation->quotation_id,
            $this->generateTransactionName(Log::TYPE_QUOTATION, $quotation->quotation_id),
            $quotation->toArray()
        );

        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation archived successfully.');
    }

    public function archive(Quotation $quotation)
    {
        // Log the deletion
        $this->logDeleted(
            Log::TYPE_QUOTATION,
            $quotation->quotation_id,
            $this->generateTransactionName(Log::TYPE_QUOTATION, $quotation->quotation_id),
            $quotation->toArray()
        );

        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation archived successfully.');
    }

    public function restore($quotationId)
    {
        $quotation = Quotation::withTrashed()->findOrFail($quotationId);
        $quotation->restore();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation restored successfully.');
    }

    /**
     * Calculate order discount based on quantity
     */
    private function calculateOrderDiscount($totalAmount, $totalQuantity)
    {
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        
        foreach ($discountRules as $rule) {
            if ($totalQuantity >= $rule->min_quantity && 
                ($rule->max_quantity === null || $totalQuantity <= $rule->max_quantity)) {
                if ($rule->discount_type === 'percentage') {
                    return $totalAmount * ($rule->discount_percentage / 100);
                } else {
                    return $rule->discount_amount;
                }
            }
        }
        
        return 0;
    }

    /**
     * Calculate product discount for a specific product group
     */
    private function calculateProductDiscount($subtotal, $quantity)
    {
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        
        foreach ($discountRules as $rule) {
            if ($quantity >= $rule->min_quantity && 
                ($rule->max_quantity === null || $quantity <= $rule->max_quantity)) {
                if ($rule->discount_type === 'percentage') {
                    return $subtotal * ($rule->discount_percentage / 100);
                } else {
                    return $rule->discount_amount;
                }
            }
        }
        
        return 0;
    }

    /**
     * Check if quotation has layout items
     */
    public function checkLayout(Quotation $quotation)
    {
        $hasLayout = $quotation->details()->where('layout', true)->exists();
        
        return response()->json(['hasLayout' => $hasLayout]);
    }

    public function getQuotationData(Quotation $quotation)
    {
        // Check if quotation has any layout requirements
        $hasLayout = $quotation->details()->where('layout', true)->exists();
        
        return response()->json([
            'final_total_amount' => $quotation->final_total_amount,
            'quotation_id' => $quotation->quotation_id,
            'has_layout' => $hasLayout
        ]);
    }

    /**
     * Convert quotation to job order
     */
    public function convertToJob(Request $request)
    {
        try {
            $validated = $request->validate([
                'quotation_id' => 'required|exists:quotations,quotation_id',
                'employee_id' => 'required|exists:employees,employee_id',
                'layout_employee_id' => 'nullable|exists:employees,employee_id',
                'deadline_date' => 'required|date|after:today',
                'payment_method' => 'nullable|in:Cash,GCash,Bank Transfer,Check,Credit Card',
                'payment_term' => 'nullable|in:Downpayment,Initial,Full',
                'amount_paid' => 'nullable|numeric|min:0',
                'reference_number' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            // Additional validation for downpayment
            if ($validated['payment_term'] === 'Downpayment') {
                if (empty($validated['amount_paid']) || $validated['amount_paid'] <= 0) {
                    throw new \Exception('Amount paid is required for downpayment.');
                }
                
                if (empty($validated['payment_method'])) {
                    throw new \Exception('Payment method is required for downpayment.');
                }
                
                // Validate reference number for GCash and Bank Transfer
                if (in_array($validated['payment_method'], ['GCash', 'Bank Transfer']) && empty($validated['reference_number'])) {
                    throw new \Exception('Reference number is required for GCash and Bank Transfer payments.');
                }
            }

            DB::beginTransaction();

            // Get quotation with details
            $quotation = Quotation::with(['customer', 'details.product', 'details.service'])->findOrFail($validated['quotation_id']);

            // Check if quotation is approved or closed
            if (!in_array($quotation->status, ['approved', 'closed', 'Closed'])) {
                throw new \Exception('Only approved or closed quotations can be converted to job orders.');
            }

            // Create order from quotation
            $order = Order::create([
                'order_id' => Order::generateOrderId(),
                'display_order_id' => Order::generateDisplayOrderId(),
                'customer_id' => $quotation->customer_id,
                'employee_id' => $validated['employee_id'],
                'layout_employee_id' => $validated['layout_employee_id'] ?? null,
                'order_date' => now()->toDateString(),
                'deadline_date' => $validated['deadline_date'],
                'order_status' => Order::STATUS_ON_PROCESS,
                'total_amount' => 0, // Will be calculated after details
                'created_by' => auth('admin')->id(),
            ]);

            // Convert quotation details to order details
            $totalAmount = 0;
            foreach ($quotation->details as $detail) {
                $baseAmount = $detail->quantity * $detail->price;
                $layoutPrice = $detail->layout ? $detail->layout_price : 0;
                $subtotal = $baseAmount + $layoutPrice;
                $totalAmount += $subtotal;

                $order->details()->create([
                    'quantity' => $detail->quantity,
                    'unit' => $detail->unit,
                    'size' => $detail->size,
                    'price' => $detail->price,
                    'subtotal' => $subtotal,
                    'layout' => $detail->layout,
                    'layout_price' => $layoutPrice,
                    'product_id' => $detail->product_id,
                    'service_id' => $detail->service_id,
                ]);
            }

            // Update order total amount
            $order->update(['total_amount' => $totalAmount]);

            // Create initial payment if provided
            if (!empty($validated['amount_paid']) && $validated['amount_paid'] > 0) {
                $amountPaid = (float) $validated['amount_paid'];
                $remaining = max(0, $totalAmount - $amountPaid);
                $change = $amountPaid > $totalAmount ? ($amountPaid - $totalAmount) : 0;

                $paymentRecord = Payment::create([
                    'payment_id' => Payment::generatePaymentId(),
                    'receipt_number' => 'RCPT-' . now()->format('YmdHis') . '-' . $order->order_id,
                    'payment_date' => now()->format('Y-m-d'),
                    'payment_method' => $validated['payment_method'] ?? 'Cash',
                    'payment_term' => $validated['payment_term'] ?? 'Initial',
                    'amount_paid' => $amountPaid,
                    'change' => $change,
                    'balance' => $remaining,
                    'reference_number' => $validated['reference_number'] ?? null,
                    'remarks' => $validated['payment_remarks'] ?? null,
                    'order_id' => $order->order_id,
                    'created_by' => auth('web')->id() ?? \App\Models\User::first()->id,
                    'received_by' => auth('web')->id() ?? \App\Models\User::first()->id,
                ]);

                // Log the payment creation
                $this->logCreated(
                    Log::TYPE_PAYMENT,
                    $paymentRecord->payment_id,
                    $this->generateTransactionName(Log::TYPE_PAYMENT, $paymentRecord->payment_id),
                    $paymentRecord->toArray()
                );
            }

            // Update quotation status to closed and archive it
            $quotation->update(['status' => 'closed']);
            $quotation->delete(); // Soft delete to archive the quotation

            // Log the quotation conversion to job order
            $this->logActivity(
                Log::TYPE_QUOTATION,
                (string)$quotation->quotation_id,
                Log::ACTION_CONVERTED_TO_ORDER,
                'Quotation #' . $quotation->quotation_id,
                [
                    'converted_to' => [
                        'order_id' => $order->order_id,
                        'order_date' => $order->order_date,
                        'deadline_date' => $order->deadline_date,
                        'employee_id' => $order->employee_id,
                        'layout_employee_id' => $order->layout_employee_id,
                        'total_amount' => $order->total_amount
                    ]
                ]
            );

            // Log the job order creation
            $this->logCreated(
                Log::TYPE_ORDER,
                $order->order_id,
                'Job Order #' . $order->order_id,
                $order->toArray()
            );

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Quotation converted to job order successfully. Order ID: ' . $order->order_id);

        } catch (\Exception $e) {
            DB::rollBack();
            LaravelLog::error('Error converting quotation to job order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to convert quotation to job order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get quotation data for AJAX requests
     */
    public function getData(Quotation $quotation)
    {
        try {
            $quotation->load(['details.product', 'details.service']);
            
            // Calculate if any items require layout
            $hasLayout = $quotation->details->some(function ($detail) {
                return ($detail->product && $detail->product->requires_layout) || 
                       ($detail->service && $detail->service->requires_layout);
            });

            return response()->json([
                'final_total_amount' => $quotation->final_total_amount,
                'has_layout' => $hasLayout,
                'quotation_id' => $quotation->quotation_id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch quotation data'], 500);
        }
    }

    /**
     * Get quotation items for AJAX requests
     */
    public function getItems(Quotation $quotation)
    {
        try {
            $quotation->load(['details.product', 'details.service', 'details.unit']);
            
            $items = $quotation->details->map(function ($detail) {
                return [
                    'item_type' => $detail->item_type,
                    'item_name' => $detail->product ? $detail->product->product_name : $detail->service->service_name,
                    'quantity' => $detail->quantity,
                    'unit_name' => $detail->unit ? $detail->unit->unit_name : null,
                    'size' => $detail->size,
                    'price' => $detail->price,
                    'layout' => $detail->layout,
                    'layout_price' => $detail->layout_price,
                    'subtotal' => $detail->subtotal
                ];
            });

            // Calculate total layout fees
            $layoutFees = $quotation->details->where('layout', true)->sum('layout_price');

            return response()->json([
                'items' => $items,
                'layout_fees' => $layoutFees,
                'total_amount' => $quotation->final_total_amount,
                'total_items' => $items->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch quotation items'], 500);
        }
    }
}