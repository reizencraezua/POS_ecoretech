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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
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
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $quotations = $query->latest('quotation_date')->paginate(15)->appends($request->query());

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
            // Debug: Log the incoming request data
            \Log::info('Quotation creation request data:', $request->all());
            \Log::info('Quotation creation attempt started');

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'quotation_date' => 'required|date',
                'valid_until' => 'required|date|after:quotation_date',
                'notes' => 'nullable|string',
                'terms_and_conditions' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_id' => 'required|exists:units,unit_id',
                'items.*.size' => 'nullable|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0',
                'items.*.discountAmount' => 'nullable|numeric|min:0',
                'items.*.discountRule' => 'nullable|string',
            ]);

            $totalAmount = 0;

            $quotation = Quotation::create([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'],
                'terms_and_conditions' => $validated['terms_and_conditions'],
                'status' => 'Pending',
                'total_amount' => 0, // Will be calculated after details
            ]);

            // Process quotation details following the same formula as orders
            foreach ($validated['items'] as $item) {
                // Store the base amount (Quantity × Price) for each item
                $baseAmount = $item['quantity'] * $item['price'];

                $quotation->details()->create([
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $baseAmount, // Store base amount (Quantity × Price)
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $item['layoutPrice'] ?? 0,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Calculate using the same formula as orders
            // Formula 1: Total Amount = (Quantity × Unit Price)
            $totalAmount = $quotation->details->sum(function ($detail) {
                return $detail->quantity * $detail->price;
            });
            
            // Formula 2: Sub Total = Total Amount ÷ 1.12
            $subTotal = $totalAmount / 1.12;
            
            // Formula 3: VAT Tax = Total Amount × 0.12
            $vatAmount = $totalAmount * 0.12;
            
            // Formula 4: Discount Amount = Total Amount × Discount Rate
            $totalQuantity = array_sum(array_column($validated['items'], 'quantity'));
            $discountAmount = $this->calculateOrderDiscount($totalAmount, $totalQuantity);
            
            // Formula 5: Final Total Amount = (Total Amount - Discount Amount) + layout fee
            $layoutFees = $quotation->details->sum(function ($detail) {
                return $detail->layout ? $detail->layout_price : 0;
            });
            $finalTotalAmount = ($totalAmount - $discountAmount) + $layoutFees;
            
            // Update quotation with final total amount
            $quotation->update(['total_amount' => $finalTotalAmount]);

            return redirect()->route('admin.quotations.index')
                ->with('success', 'Quotation created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in quotation creation:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating quotation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'details.product', 'details.service']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $quotation->load(['details']);

        return view('admin.quotations.edit', compact('quotation', 'customers', 'products', 'services', 'discountRules'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after:quotation_date',
            'notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,service',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.layout' => 'nullable|in:on,1,true,false,0',
            'items.*.layoutPrice' => 'nullable|numeric|min:0',
            'items.*.discountAmount' => 'nullable|numeric|min:0',
            'items.*.discountRule' => 'nullable|string',
        ]);

        $totalAmount = 0;

        $quotation->update([
            'customer_id' => $validated['customer_id'],
            'quotation_date' => $validated['quotation_date'],
            'valid_until' => $validated['valid_until'],
            'notes' => $validated['notes'],
            'terms_and_conditions' => $validated['terms_and_conditions'],
        ]);

        // Delete existing quotation details
        $quotation->details()->delete();

        // Create new quotation details following the same formula as orders
        foreach ($validated['items'] as $item) {
            // Store the base amount (Quantity × Price) for each item
            $baseAmount = $item['quantity'] * $item['price'];

            $quotation->details()->create([
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'size' => $item['size'],
                'price' => $item['price'],
                'subtotal' => $baseAmount, // Store base amount (Quantity × Price)
                'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                'layout_price' => $item['layoutPrice'] ?? 0,
                'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                'service_id' => $item['type'] === 'service' ? $item['id'] : null,
            ]);
        }

        // Calculate using the same formula as orders
        // Formula 1: Total Amount = (Quantity × Unit Price)
        $totalAmount = $quotation->details->sum(function ($detail) {
            return $detail->quantity * $detail->price;
        });
        
        // Formula 2: Sub Total = Total Amount ÷ 1.12
        $subTotal = $totalAmount / 1.12;
        
        // Formula 3: VAT Tax = Total Amount × 0.12
        $vatAmount = $totalAmount * 0.12;
        
        // Formula 4: Discount Amount = Total Amount × Discount Rate
        $totalQuantity = array_sum(array_column($validated['items'], 'quantity'));
        $discountAmount = $this->calculateOrderDiscount($totalAmount, $totalQuantity);
        
        // Formula 5: Final Total Amount = (Total Amount - Discount Amount) + layout fee
        $layoutFees = $quotation->details->sum(function ($detail) {
            return $detail->layout ? $detail->layout_price : 0;
        });
        $finalTotalAmount = ($totalAmount - $discountAmount) + $layoutFees;
        
        // Update quotation with final total amount
        $quotation->update(['total_amount' => $finalTotalAmount]);

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation updated successfully.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Closed',
        ]);

        $quotation->update(['status' => $validated['status']]);

        return back()->with('success', 'Quotation status updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation archived successfully.');
    }

    public function archive(Quotation $quotation)
    {
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

                Payment::create([
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
                ]);
            }

            // Update quotation status to closed and archive it
            $quotation->update(['status' => 'closed']);
            $quotation->delete(); // Soft delete to archive the quotation

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Quotation converted to job order successfully. Order ID: ' . $order->order_id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting quotation to job order: ' . $e->getMessage());
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
}
