<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Service;
use App\Models\Quotation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class JobOrderController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Allow all authenticated users to view job orders

        $query = Order::with(['customer', 'employee', 'details.unit', 'payments', 'creator']);

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $status = $request->status;
                
                // Handle due date filters
                if ($status === 'due_today') {
                    $query->whereNotNull('deadline_date')
                          ->whereDate('deadline_date', now()->toDateString())
                          ->whereNotIn('order_status', ['Completed', 'Cancelled']);
                } elseif ($status === 'due_tomorrow') {
                    $query->whereNotNull('deadline_date')
                          ->whereDate('deadline_date', now()->addDay()->toDateString())
                          ->whereNotIn('order_status', ['Completed', 'Cancelled']);
                } elseif ($status === 'due_3_days') {
                    $query->whereNotNull('deadline_date')
                          ->whereBetween('deadline_date', [now()->addDay(), now()->addDays(3)])
                          ->whereNotIn('order_status', ['Completed', 'Cancelled']);
                } elseif ($status === 'overdue') {
                    $query->whereNotNull('deadline_date')
                          ->whereDate('deadline_date', '<', now()->toDateString())
                          ->whereNotIn('order_status', ['Completed', 'Cancelled']);
                } else {
                    // Regular status filter
                    $query->where('order_status', $status);
                }
            }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('customer_firstname', 'like', "%{$search}%")
                                   ->orWhere('customer_lastname', 'like', "%{$search}%")
                                   ->orWhere('business_name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest('order_date')->paginate(15)->appends($request->query());

        return view('cashier.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $employees = Employee::orderBy('employee_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        $quotations = Quotation::where('status', Quotation::STATUS_APPROVED)->with('customer')->get();
        $discountRules = \App\Models\DiscountRule::active()->validAt()->orderBy('min_quantity')->get();

        return view('cashier.orders.create', compact('customers', 'employees', 'products', 'services', 'units', 'quotations', 'discountRules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'employee_id' => 'required|exists:employees,employee_id',
                'order_date' => 'required|date',
                'delivery_date' => 'nullable|date|after_or_equal:order_date',
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

            // Create order
            $order = Order::create([
                'order_id' => Order::generateOrderId(),
                'customer_id' => $validated['customer_id'],
                'employee_id' => $validated['employee_id'],
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'],
                'order_status' => Order::STATUS_ON_PROCESS,
                'total_amount' => 0, // Will be calculated after details
                'created_by' => auth('web')->id(),
            ]);

            // Process order details
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];

                $order->details()->create([
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $baseAmount,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0,
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

            return redirect()->route('cashier.orders.index')
                ->with('success', 'Job order created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating job order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create job order. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Prevent viewing voided orders
        if ($order->order_status === Order::STATUS_VOIDED) {
            return redirect()->route('cashier.orders.index')
                ->with('error', 'Cannot view voided orders.');
        }

                $order->load(['customer', 'employee', 'details.product', 'details.service', 'details.unit', 'payments']);
        return view('cashier.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Prevent editing voided orders
        if ($order->order_status === Order::STATUS_VOIDED) {
            return redirect()->route('cashier.orders.index')
                ->with('error', 'Cannot edit voided orders.');
        }

        $customers = Customer::orderBy('customer_firstname')->get();
        $employees = Employee::with('job')->orderBy('employee_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $discountRules = \App\Models\DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        
        $order->load(['details.product', 'details.service', 'payments']);
        
        // Check if order has payments
        $hasPayments = $order->payments->count() > 0;

        return view('cashier.orders.edit', compact('order', 'customers', 'employees', 'products', 'services', 'discountRules', 'units', 'hasPayments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Prevent updating voided orders
        if ($order->order_status === Order::STATUS_VOIDED) {
            return redirect()->route('cashier.orders.index')
                ->with('error', 'Cannot update voided orders.');
        }

        // Check if order has payments - if so, prevent updating existing items
        $hasPayments = $order->payments()->count() > 0;
        if ($hasPayments && $request->has('items')) {
            // Allow only adding new items, not modifying existing ones
            $existingItemCount = $order->details()->count();
            $submittedItemCount = count($request->input('items', []));
            
            // If trying to modify existing items (submitted count is less than existing count)
            if ($submittedItemCount < $existingItemCount) {
                return redirect()->back()
                    ->with('error', 'Cannot remove existing items when payments exist. You can only add new items.');
            }
        }

        // Check for admin password only if payment is being processed
        $hasPayment = !empty($request->input('payment.amount_paid')) && $request->input('payment.amount_paid') > 0;
        if ($hasPayment && !$this->verifyAdminPassword($request)) {
            return redirect()->back()
                ->withErrors(['admin_password' => 'Invalid admin password.'])
                ->withInput();
        }

        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'employee_id' => 'required|exists:employees,employee_id',
                'order_date' => 'required|date',
                'delivery_date' => 'nullable|date|after_or_equal:order_date',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_id' => 'required|exists:units,unit_id',
                'items.*.size' => 'nullable|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0',
                // Payment validation
                'payment.payment_method' => 'nullable|in:Cash,Check,GCash,Bank Transfer,Credit Card',
                'payment.amount_paid' => 'nullable|numeric|min:0.01',
                'payment.payment_date' => 'nullable|date',
                'payment.payment_term' => 'nullable|in:Downpayment,Initial,Partial,Full',
                'payment.reference_number' => 'nullable|string|max:100',
                'payment.remarks' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Update order
            $order->update([
                'customer_id' => $validated['customer_id'],
                'employee_id' => $validated['employee_id'],
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'],
            ]);

            // Delete existing order details
            $order->details()->delete();

            // Process new order details
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];

                $order->details()->create([
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $baseAmount,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0,
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

            // Handle payment creation if payment data is provided
            if (!empty($validated['payment']['amount_paid']) && $validated['payment']['amount_paid'] > 0) {
                $paymentData = $validated['payment'];
                
                // Check if payment amount exceeds remaining balance
                if ($paymentData['amount_paid'] > $order->remaining_balance) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Payment amount cannot exceed remaining balance of ₱' . number_format($order->remaining_balance, 2))
                        ->withInput();
                }

                // Create payment
                Payment::create([
                    'payment_id' => Payment::generatePaymentId(),
                    'receipt_number' => 'RCPT-' . now()->format('YmdHis') . '-' . $order->order_id,
                    'order_id' => $order->order_id,
                    'payment_method' => $paymentData['payment_method'],
                    'amount_paid' => $paymentData['amount_paid'],
                    'payment_date' => $paymentData['payment_date'] ?? now()->format('Y-m-d'),
                    'payment_term' => $paymentData['payment_term'],
                    'reference_number' => $paymentData['reference_number'],
                    'remarks' => $paymentData['remarks'],
                ]);

                // Check if order is fully paid
                if ($order->remaining_balance <= 0) {
                    $order->update(['order_status' => 'For Releasing']);
                }
            }

            DB::commit();

            return redirect()->route('cashier.orders.index')
                ->with('success', 'Job order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating job order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update job order. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        try {
            $validStatuses = [
                Order::STATUS_ON_PROCESS,
                Order::STATUS_DESIGNING, 
                Order::STATUS_PRODUCTION,
                Order::STATUS_FOR_RELEASING,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED
            ];
            
            // Additional validation to ensure status is valid
            if (!in_array($request->order_status, $validStatuses)) {
                Log::error('Invalid status received', [
                    'received_status' => $request->order_status,
                    'valid_statuses' => $validStatuses
                ]);
                return redirect()->back()
                    ->with('error', 'Invalid status selected. Please try again.');
            }
            
            $request->validate([
                'order_status' => 'required|in:' . implode(',', $validStatuses)
            ]);

            $order->update(['order_status' => $request->order_status]);

            Log::info('Order status updated successfully', [
                'order_id' => $order->order_id,
                'new_status' => $request->order_status
            ]);

            return redirect()->back()
                ->with('success', "Order status updated to {$request->order_status}.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating order status', [
                'order_id' => $order->order_id,
                'order_status' => $request->order_status,
                'errors' => $e->errors()
            ]);
            return redirect()->back()
                ->with('error', 'Invalid status selected. Please try again.')
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update order status. Please try again.');
        }
    }

    /**
     * Void order with admin password confirmation
     */
    public function void(Request $request, Order $order)
    {
        $request->validate([
            'admin_password' => 'required|string',
            'void_reason' => 'required|string|max:500'
        ]);

        // Verify admin password against system administrator
        $systemAdmin = \App\Models\User::where('role', 'super_admin')->first();
        if (!$systemAdmin || !\Hash::check($request->admin_password, $systemAdmin->password)) {
            return redirect()->back()
                ->with('error', 'Invalid admin password. Void operation cancelled.');
        }

        try {
            // Update order status to voided
            $order->update([
                'order_status' => Order::STATUS_VOIDED,
                'voided_at' => now(),
                'voided_by' => auth('web')->id(),
                'void_reason' => $request->void_reason
            ]);

            // Soft delete the order (archive it)
            $order->delete();

            return redirect()->route('cashier.orders.index')
                ->with('success', 'Order has been voided and archived successfully.');
        } catch (\Exception $e) {
            Log::error('Error voiding order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to void order. Please try again.');
        }
    }

    private function verifyAdminPassword(Request $request)
    {
        $adminPassword = $request->input('admin_password');
        if (!$adminPassword) {
            return false;
        }

        // Get admin user (assuming admin user ID is 1)
        $admin = \App\Models\User::find(1);
        if (!$admin) {
            return false;
        }

        return \Hash::check($adminPassword, $admin->password);
    }

    private function calculateOrderDiscount($subTotal, $totalQuantity)
    {
        $discountRules = \App\Models\DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        
        foreach ($discountRules as $rule) {
            if ($totalQuantity >= $rule->min_quantity && 
                ($rule->max_quantity === null || $totalQuantity <= $rule->max_quantity)) {
                
                if ($rule->discount_type === 'percentage') {
                    return $subTotal * ($rule->discount_percentage / 100);
                } else {
                    return $rule->discount_amount;
                }
            }
        }
        
        return 0;
    }
}
