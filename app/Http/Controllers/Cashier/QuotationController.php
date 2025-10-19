<?php

namespace App\Http\Controllers\Cashier;

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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Allow all authenticated users to view quotations

        $query = Quotation::with(['customer', 'details']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $quotations = $query->latest('quotation_date')->paginate(15)->appends($request->query());

        return view('cashier.quotations.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();

        return view('cashier.quotations.create', compact('customers', 'products', 'services', 'units', 'discountRules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'quotation_date' => 'required|date',
                'valid_until' => 'required|date|after:quotation_date',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_id' => 'required|integer',
                'items.*.size' => 'nullable|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0',
            ]);

            // Create quotation
            $quotation = Quotation::create([
                'quotation_id' => Quotation::generateQuotationId(),
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'status' => Quotation::STATUS_PENDING,
                'total_amount' => 0, // Will be calculated after details
                'created_by' => auth('web')->id() ?? App\Models\User::first()->id, // Fallback to first user if not authenticated
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

            // Log the quotation creation
            $this->logCreated(
                Log::TYPE_QUOTATION,
                $quotation->quotation_id,
                $this->generateTransactionName(Log::TYPE_QUOTATION, $quotation->quotation_id),
                $quotation->toArray()
            );

            return redirect()->route('cashier.quotations.index')
                ->with('success', 'Quotation created successfully.');
        } catch (\Exception $e) {
            LaravelLog::error('Error creating quotation: ' . $e->getMessage());
            LaravelLog::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to create quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'details.product', 'details.service', 'histories.updatedBy']);
        return view('cashier.quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        // Check if quotation has payments (converted to order with payments)
        if ($quotation->hasPayments()) {
            return redirect()->route('cashier.quotations.index')
                ->with('error', 'Cannot edit quotation because it has been converted to an order with payments.');
        }

        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        $quotation->load(['details']);

        return view('cashier.quotations.edit', compact('quotation', 'customers', 'products', 'services', 'discountRules', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Check if quotation has payments (converted to order with payments)
        if ($quotation->hasPayments()) {
            return redirect()->route('cashier.quotations.index')
                ->with('error', 'Cannot edit quotation because it has been converted to an order with payments.');
        }

        try {
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
            ]);

            DB::beginTransaction();

            // Update quotation basic info
            $quotation->update([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'],
                'terms_and_conditions' => $validated['terms_and_conditions'],
            ]);

            // Delete existing details
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

            return redirect()->route('cashier.quotations.index')
                ->with('success', 'Quotation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update quotation status
     */
    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:Pending,Closed'
        ]);

        $quotation->update(['status' => $request->status]);

        $statusMessage = ucfirst($request->status);
        return redirect()->back()
            ->with('success', "Quotation {$statusMessage} successfully.");
    }

    /**
     * Archive the specified quotation
     */
    public function archive(Quotation $quotation)
    {
        try {
            $quotation->delete();
            return redirect()->route('cashier.quotations.index')
                ->with('success', 'Quotation archived successfully.');
        } catch (\Exception $e) {
            Log::error('Error archiving quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to archive quotation. Please try again.');
        }
    }

    /**
     * Check if quotation has layout items
     */
    public function checkLayout(Quotation $quotation)
    {
        $hasLayout = $quotation->details()->where('layout', true)->exists();
        
        return response()->json(['hasLayout' => $hasLayout]);
    }

    /**
     * Get quotation data for conversion
     */
    public function getData(Quotation $quotation)
    {
        try {
            $quotation->load(['details.product', 'details.service', 'details.unit']);
            
            // Calculate if any items require layout
            $hasLayout = $quotation->details->some(function ($detail) {
                return ($detail->product && $detail->product->requires_layout) || 
                       ($detail->service && $detail->service->requires_layout);
            });

            return response()->json([
                'final_total_amount' => $quotation->final_total_amount,
                'has_layout' => $hasLayout,
                'quotation_id' => $quotation->quotation_id,
                'details' => $quotation->details->map(function ($detail) {
                    return [
                        'item_type' => $detail->item_type,
                        'quantity' => $detail->quantity,
                        'size' => $detail->size,
                        'price' => $detail->price,
                        'layout' => $detail->layout,
                        'layout_price' => $detail->layout_price,
                        'product' => $detail->product ? [
                            'product_name' => $detail->product->product_name
                        ] : null,
                        'service' => $detail->service ? [
                            'service_name' => $detail->service->service_name
                        ] : null,
                        'unit' => $detail->unit ? [
                            'unit_name' => $detail->unit->unit_name
                        ] : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch quotation data'], 500);
        }
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
                'order_date' => 'required|date',
                'deadline_date' => 'required|date|after:order_date',
                'payment_method' => 'nullable|in:Cash,GCash,Bank Transfer,Check,Credit Card',
                'payment_term' => 'nullable|in:Downpayment,Initial,Full',
                'amount_paid' => 'nullable|numeric|min:0',
                'reference_number' => 'nullable|string|max:255',
                'payment_remarks' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Get quotation with details
            $quotation = Quotation::with(['customer', 'details.product', 'details.service'])->findOrFail($validated['quotation_id']);

            // Check if quotation is closed
            if ($quotation->status !== 'Closed') {
                throw new \Exception('Only closed quotations can be converted to job orders.');
            }

            // Create order from quotation
            $order = Order::create([
                'order_id' => Order::generateOrderId(),
                'display_order_id' => Order::generateDisplayOrderId(),
                'customer_id' => $quotation->customer_id,
                'employee_id' => $validated['employee_id'],
                'layout_employee_id' => $validated['layout_employee_id'] ?? null,
                'order_date' => $validated['order_date'],
                'deadline_date' => $validated['deadline_date'],
                'order_status' => Order::STATUS_ON_PROCESS,
                'total_amount' => 0, // Will be calculated after details
                'created_by' => auth('web')->id() ?? App\Models\User::first()->id,
            ]);

            // Convert quotation details to order details
            foreach ($quotation->details as $detail) {
                $baseAmount = $detail->quantity * $detail->price;
                $layoutPrice = $detail->layout ? $detail->layout_price : 0;
                $subtotal = $baseAmount + $layoutPrice;
                
                // Calculate VAT (12% of base amount)
                $vatAmount = $baseAmount * 0.12;
                
                // Calculate discount (if any - this will be calculated at order level)
                $discountAmount = 0; // Individual item discount, if any

                $order->details()->create([
                    'quantity' => $detail->quantity,
                    'unit_id' => $detail->unit_id,
                    'size' => $detail->size,
                    'price' => $detail->price,
                    'subtotal' => $subtotal,
                    'vat' => $vatAmount,
                    'discount' => $discountAmount,
                    'layout' => $detail->layout,
                    'layout_price' => $layoutPrice,
                    'product_id' => $detail->product_id,
                    'service_id' => $detail->service_id,
                ]);
            }

            // Calculate final total amount using the order's calculation methods
            $finalTotalAmount = $order->fresh()->final_total_amount;
            
            // Update order total amount
            $order->update(['total_amount' => $finalTotalAmount]);

            // Create initial payment if provided
            if (!empty($validated['amount_paid']) && $validated['amount_paid'] > 0) {
                $amountPaid = (float) $validated['amount_paid'];
                
                // Validate payment amount
                if ($amountPaid > $finalTotalAmount) {
                    throw new \Exception('Payment amount cannot exceed the total amount of ₱' . number_format($finalTotalAmount, 2) . '.');
                }
                
                // For downpayments, ensure minimum 50% requirement
                if ($validated['payment_term'] === 'Downpayment') {
                    $requiredDownpayment = $finalTotalAmount * 0.5;
                    if ($amountPaid < $requiredDownpayment) {
                        throw new \Exception('Downpayment must be at least 50% of the total amount (₱' . number_format($requiredDownpayment, 2) . ').');
                    }
                }
                
                $remaining = max(0, $finalTotalAmount - $amountPaid);
                $change = $amountPaid > $finalTotalAmount ? ($amountPaid - $finalTotalAmount) : 0;

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

            // Update quotation status to closed
            $quotation->update(['status' => 'closed']);

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

            return redirect()->route('cashier.orders.index')
                ->with('success', 'Quotation converted to job order successfully. Order ID: ' . $order->order_id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting quotation to job order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to convert quotation to job order: ' . $e->getMessage())
                ->withInput();
        }
    }
}
