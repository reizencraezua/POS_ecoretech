<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Service;
use App\Models\DiscountRule;
use App\Models\Log;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LaravelLog;

class OrderController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Order::onlyTrashed()->with(['customer', 'employee', 'details.unit', 'payments', 'creator', 'voidedBy'])
            : Order::with(['customer', 'employee', 'details.unit', 'payments', 'creator', 'voidedBy']);

        if ($request->has('status') && $request->status !== '') {
            $status = $request->status;
            
            // Handle due date filters
            if ($status === 'due_today') {
                $query->whereNotNull('deadline_date')
                      ->whereDate('deadline_date', now()->toDateString())
                      ->whereNotIn('order_status', ['Completed', 'Cancelled', 'Voided']);
            } elseif ($status === 'due_tomorrow') {
                $query->whereNotNull('deadline_date')
                      ->whereDate('deadline_date', now()->addDay()->toDateString())
                      ->whereNotIn('order_status', ['Completed', 'Cancelled', 'Voided']);
            } elseif ($status === 'due_3_days') {
                $query->whereNotNull('deadline_date')
                      ->whereBetween('deadline_date', [now()->addDay(), now()->addDays(3)])
                      ->whereNotIn('order_status', ['Completed', 'Cancelled', 'Voided']);
            } elseif ($status === 'overdue') {
                $query->whereNotNull('deadline_date')
                      ->whereDate('deadline_date', '<', now()->toDateString())
                      ->whereNotIn('order_status', ['Completed', 'Cancelled', 'Voided']);
            } else {
                // Regular status filter
                $query->where('order_status', $status);
            }
        }


        // Date filtering
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        $orders = $query->orderBy('order_id', 'desc')->paginate(15);

        return view('admin.orders.index', compact('orders', 'showArchived'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $employees = Employee::with('job')->orderBy('employee_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();

        return view('admin.orders.create', compact('customers', 'employees', 'products', 'services', 'discountRules', 'units'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'employee_id' => 'required|exists:employees,employee_id',
                'layout_employee_id' => 'nullable|exists:employees,employee_id',
                'order_date' => 'required|date',
                'deadline_date' => 'required|date|after:order_date',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1|max:9999',
                'items.*.unit_id' => 'required|exists:units,unit_id',
                'items.*.size' => 'nullable|string|max:255',
                'items.*.price' => 'required|numeric|min:0|max:999999.99',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0|max:999999.99',
                // optional initial payment
                'payment.payment_date' => 'nullable|date',
                'payment.payment_method' => 'nullable|in:Cash,GCash,Bank Transfer,Check,Credit Card',
                'payment.payment_term' => 'nullable|in:Downpayment,Initial,Full',
                'payment.amount_paid' => 'nullable|numeric|min:0|max:999999.99',
                'payment.reference_number' => 'nullable|string|max:255',
                'payment.remarks' => 'nullable|string|max:1000',
            ], [
                'customer_id.required' => 'Please select a customer.',
                'customer_id.exists' => 'Selected customer does not exist.',
                'employee_id.required' => 'Please select an employee.',
                'employee_id.exists' => 'Selected employee does not exist.',
                'layout_employee_id.exists' => 'Selected layout employee does not exist.',
                'order_date.required' => 'Order date is required.',
                'order_date.date' => 'Please enter a valid order date.',
                'deadline_date.required' => 'Deadline date is required.',
                'deadline_date.date' => 'Please enter a valid deadline date.',
                'deadline_date.after' => 'Deadline date must be after order date.',
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

            $order = Order::create([
                'order_id' => Order::generateOrderId(),
                'display_order_id' => Order::generateDisplayOrderId(),
                'customer_id' => $validated['customer_id'],
                'employee_id' => $validated['employee_id'],
                'layout_employee_id' => $validated['layout_employee_id'] ?? null,
                'order_date' => $validated['order_date'],
                'deadline_date' => $validated['deadline_date'],
                'order_status' => 'On-Process',
                'total_amount' => 0, // Will be calculated after details
                'layout_design_fee' => 0,
                'created_by' => auth('web')->id(),
            ]);

            // Group items by product for discount calculation
            $productGroups = [];
            foreach ($validated['items'] as $item) {
                if ($item['type'] === 'product') {
                    $productId = $item['id'];
                    if (!isset($productGroups[$productId])) {
                        $productGroups[$productId] = [];
                    }
                    $productGroups[$productId][] = $item;
                }
            }

            // Calculate discounts for each product group
            $productDiscounts = [];
            foreach ($productGroups as $productId => $items) {
                $totalQuantity = array_sum(array_column($items, 'quantity'));
                $totalSubtotal = 0;
                
                foreach ($items as $item) {
                    $baseAmount = $item['quantity'] * $item['price'];
                    $layoutChecked = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]);
                    $subtotal = $baseAmount;
                    if ($layoutChecked) {
                        $subtotal += $item['layoutPrice'] ?? 0;
                    }
                    $totalSubtotal += $subtotal;
                }
                
                // Calculate discount for this product group
                $discount = $this->calculateProductDiscount($totalSubtotal, $totalQuantity);
                $productDiscounts[$productId] = $discount;
            }

            // Process order details
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];
                $layoutPrice = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0;
                $subtotal = $baseAmount + $layoutPrice;
                
                // Calculate VAT (12% of base amount)
                $vatAmount = $baseAmount * 0.12;
                
                // Calculate discount (if any - this will be calculated at order level)
                $discountAmount = 0; // Individual item discount, if any

                $order->details()->create([
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'vat' => $vatAmount,
                    'discount' => $discountAmount,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $layoutPrice,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Calculate final total amount using the model's calculation methods
            $finalTotalAmount = $order->fresh()->final_total_amount;
            
            // Update order with final total amount
            $order->update(['total_amount' => $finalTotalAmount]);

            // Optional initial payment at creation
            if (!empty($validated['payment']['amount_paid'])) {
                $payment = $validated['payment'];
                $totalPaid = (float) $payment['amount_paid'];
                $remaining = max(0, $finalTotalAmount - $totalPaid);
                $change = $totalPaid > $finalTotalAmount ? ($totalPaid - $finalTotalAmount) : 0;

                $paymentRecord = \App\Models\Payment::create([
                    'payment_id' => \App\Models\Payment::generatePaymentId(),
                    'receipt_number' => 'RCPT-' . now()->format('YmdHis') . '-' . $order->order_id,
                    'payment_date' => $payment['payment_date'] ?? now()->format('Y-m-d'),
                    'payment_method' => $payment['payment_method'] ?? 'Cash',
                    'payment_term' => $payment['payment_term'] ?? 'Initial',
                    'amount_paid' => $payment['amount_paid'],
                    'change' => $change,
                    'balance' => $remaining,
                    'reference_number' => $payment['reference_number'] ?? null,
                    'remarks' => $payment['remarks'] ?? null,
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

                // Note: Order status is not automatically changed to Completed when fully paid
                // The order status must be manually changed by the user
            }

            DB::commit();

            // Log the creation
            $this->logCreated(
                Log::TYPE_ORDER,
                $order->order_id,
                $this->generateTransactionName(Log::TYPE_ORDER, $order->order_id),
                $order->toArray()
            );

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            LaravelLog::error('Order validation error: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            LaravelLog::error('Error creating order: ' . $e->getMessage());
            LaravelLog::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to create order: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Order $order)
    {
        // Prevent viewing voided orders
        if ($order->order_status === Order::STATUS_VOIDED) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cannot view voided orders.');
        }

        $order->load([
            'customer',
            'employee.job',
            'layoutEmployee.job',
            'details.product',
            'details.service',
            'details.unit',
            'payments',
            'deliveries'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        // Prevent editing voided orders
        if ($order->order_status === Order::STATUS_VOIDED) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cannot edit voided orders.');
        }

        $customers = Customer::orderBy('customer_firstname')->get();
        $employees = Employee::with('job')->orderBy('employee_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        $order->load(['details', 'payments']);
        
        // Check if order has payments
        $hasPayments = $order->payments->count() > 0;

        return view('admin.orders.edit', compact('order', 'customers', 'employees', 'products', 'services', 'discountRules', 'units', 'hasPayments'));
    }

    public function update(Request $request, Order $order)
    {
        // Prevent updating voided orders
        if ($order->order_status === Order::STATUS_VOIDED) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cannot update voided orders.');
        }

        // Store original data for logging
        $originalData = $order->toArray();

        // Check if order has payments - if so, prevent updating existing items
        $hasPayments = $order->payments()->count() > 0;
        $hasPaymentInRequest = !empty($request->input('payment.amount_paid')) && $request->input('payment.amount_paid') > 0;
        
        // Only restrict item modifications if there are existing payments AND no new payment is being made
        if ($hasPayments && $request->has('items') && !$hasPaymentInRequest) {
            // Allow only adding new items, not modifying existing ones
            $existingItemCount = $order->details()->count();
            $submittedItemCount = count($request->input('items', []));
            
            // If trying to modify existing items (submitted count is less than existing count)
            if ($submittedItemCount < $existingItemCount) {
                return redirect()->back()
                    ->with('error', 'Cannot remove existing items when payments exist. You can only add new items or process a payment.');
            }
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'employee_id' => 'required|exists:employees,employee_id',
            'layout_employee_id' => 'nullable|exists:employees,employee_id',
            'order_date' => 'required|date',
            'deadline_date' => 'required|date|after:order_date',
            'order_status' => 'required|in:On-Process,Designing,Production,For Releasing,Completed,Cancelled',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,service',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_id' => 'required|exists:units,unit_id',
            'items.*.size' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.layout' => 'nullable|in:on,1,true,false,0',
            'items.*.layoutPrice' => 'nullable|numeric|min:0',
            // Optional additional payment
            'payment.payment_date' => 'nullable|date',
            'payment.payment_method' => 'nullable|in:Cash,GCash,Bank Transfer,Check,Credit Card',
            'payment.payment_term' => 'nullable|in:Downpayment,Initial,Full',
            'payment.amount_paid' => 'nullable|numeric|min:0',
            'payment.reference_number' => 'nullable|string|max:255',
            'payment.remarks' => 'nullable|string',
        ]);

        // Check if trying to set status to Completed but order is not fully paid
        if ($validated['order_status'] === 'Completed' && !$order->isFullyPaid()) {
            return redirect()->back()
                ->withErrors([
                    'order_status' => 'Cannot mark order as Completed. Order must be fully paid first. Remaining balance: ₱' . number_format($order->remaining_balance, 2)
                ])
                ->withInput();
        }

        $totalAmount = 0;

        $order->update([
            'customer_id' => $validated['customer_id'],
            'employee_id' => $validated['employee_id'],
            'layout_employee_id' => $validated['layout_employee_id'] ?? null,
            'order_date' => $validated['order_date'],
            'deadline_date' => $validated['deadline_date'],
            'order_status' => $validated['order_status'],
            'layout_design_fee' => 0,
        ]);

        // Delete existing order details
        $order->details()->delete();

        // Group items by product for discount calculation
        $productGroups = [];
        foreach ($validated['items'] as $item) {
            if ($item['type'] === 'product') {
                $productId = $item['id'];
                if (!isset($productGroups[$productId])) {
                    $productGroups[$productId] = [];
                }
                $productGroups[$productId][] = $item;
            }
        }

        // Calculate discounts for each product group
        $productDiscounts = [];
        foreach ($productGroups as $productId => $items) {
            $totalQuantity = array_sum(array_column($items, 'quantity'));
            $totalSubtotal = 0;
            
            foreach ($items as $item) {
                $baseAmount = $item['quantity'] * $item['price'];
                $layoutChecked = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]);
                $subtotal = $baseAmount;
                if ($layoutChecked) {
                    $subtotal += $item['layoutPrice'] ?? 0;
                }
                $totalSubtotal += $subtotal;
            }
            
            // Calculate discount for this product group
            $discount = $this->calculateProductDiscount($totalSubtotal, $totalQuantity);
            $productDiscounts[$productId] = $discount;
        }

        // Process order details following the new formula
        foreach ($validated['items'] as $item) {
            // Store the base amount (Quantity × Price) for each item
            $baseAmount = $item['quantity'] * $item['price'];

            $order->details()->create([
                'quantity' => $item['quantity'],
                'unit_id' => $item['unit_id'] ?? null,
                'size' => $item['size'],
                'price' => $item['price'],
                'subtotal' => $baseAmount, // Store base amount (Quantity × Price)
                'vat' => 0, // VAT will be calculated at order level
                'discount' => 0, // Discount will be calculated at order level
                'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                'layout_price' => $item['layoutPrice'] ?? 0,
                'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                'service_id' => $item['type'] === 'service' ? $item['id'] : null,
            ]);
        }

        // Calculate using the correct formula
        // Formula 1: Sub Total = (Quantity × Unit Price)
        $subTotal = $order->details->sum(function ($detail) {
            return $detail->quantity * $detail->price;
        });
        
        // Formula 2: VAT Tax = Sub Total × 0.12
        $vatAmount = $subTotal * 0.12;
        
        // Formula 3: Base Amount = Sub Total - VAT
        $baseAmount = $subTotal - $vatAmount;
        
        // Formula 4: Discount Amount = Sub Total × Discount Rate
        $totalQuantity = array_sum(array_column($validated['items'], 'quantity'));
        $discountAmount = $this->calculateOrderDiscount($subTotal, $totalQuantity);
        
        // Formula 5: Final Total Amount = (Sub Total - Discount Amount) + layout fee
        $layoutFees = $order->details->sum(function ($detail) {
            return $detail->layout ? $detail->layout_price : 0;
        });
        $finalTotalAmount = ($subTotal - $discountAmount) + $layoutFees;
        
        // Update order with final total amount
        $order->update(['total_amount' => $finalTotalAmount]);
        
        // Refresh the order to ensure relationships are loaded
        $order->refresh();

        // Update existing payment balances based on new total amount
        $this->updatePaymentBalances($order, $finalTotalAmount);

        // Optional additional payment at edit
        if (!empty($validated['payment']['amount_paid'])) {
            $payment = $validated['payment'];
            $totalPaid = (float) $payment['amount_paid'];
            $remaining = max(0, $finalTotalAmount - $totalPaid);
            $change = $totalPaid > $finalTotalAmount ? ($totalPaid - $finalTotalAmount) : 0;

            $paymentRecord = \App\Models\Payment::create([
                'payment_id' => \App\Models\Payment::generatePaymentId(),
                'receipt_number' => 'RCPT-' . now()->format('YmdHis') . '-' . $order->order_id,
                'payment_date' => $payment['payment_date'] ?? now()->format('Y-m-d'),
                'payment_method' => $payment['payment_method'] ?? 'Cash',
                'payment_term' => $payment['payment_term'] ?? 'Initial',
                'amount_paid' => $payment['amount_paid'],
                'change' => $change,
                'balance' => $remaining,
                'reference_number' => $payment['reference_number'] ?? null,
                'remarks' => $payment['remarks'] ?? null,
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

        // Log the update
        $this->logUpdated(
            Log::TYPE_ORDER,
            $order->order_id,
            $originalData,
            $order->fresh()->toArray(),
            $this->generateTransactionName(Log::TYPE_ORDER, $order->order_id)
        );

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Update payment balances when order total changes
     */
    private function updatePaymentBalances(Order $order, $newTotalAmount)
    {
        $payments = $order->payments()->orderBy('payment_date')->get();
        $runningTotal = 0;

        foreach ($payments as $payment) {
            $runningTotal += $payment->amount_paid;
            $remainingBalance = max(0, $newTotalAmount - $runningTotal);
            $change = $runningTotal > $newTotalAmount ? ($runningTotal - $newTotalAmount) : 0;

            $payment->update([
                'balance' => $remainingBalance,
                'change' => $change,
            ]);
        }

        // Log the payment balance update for debugging
        \Log::info('Payment balances updated for order ' . $order->order_id, [
            'new_total_amount' => $newTotalAmount,
            'total_paid' => $runningTotal,
            'remaining_balance' => max(0, $newTotalAmount - $runningTotal),
            'payments_updated' => $payments->count()
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:On-Process,Designing,Production,For Releasing,Completed,Cancelled',
        ]);

        // Check if trying to set status to Completed but order is not fully paid
        if ($validated['order_status'] === 'Completed' && !$order->isFullyPaid()) {
            return back()->withErrors([
                'order_status' => 'Cannot mark order as Completed. Order must be fully paid first. Remaining balance: ₱' . number_format($order->remaining_balance, 2)
            ]);
        }

        $oldStatus = $order->order_status;
        $order->update(['order_status' => $validated['order_status']]);

        // Log the status change
        $this->logStatusChanged(
            Log::TYPE_ORDER,
            $order->order_id,
            $oldStatus,
            $validated['order_status'],
            $this->generateTransactionName(Log::TYPE_ORDER, $order->order_id)
        );

        return back()->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
        // Log the deletion
        $this->logDeleted(
            Log::TYPE_ORDER,
            $order->order_id,
            $this->generateTransactionName(Log::TYPE_ORDER, $order->order_id),
            $order->toArray()
        );

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function archive(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order archived successfully.');
    }

    public function restore($orderId)
    {
        $order = Order::withTrashed()->findOrFail($orderId);
        $order->restore();
        return back()->with('success', 'Order restored successfully.');
    }

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
}
