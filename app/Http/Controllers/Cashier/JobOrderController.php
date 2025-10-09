<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Service;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobOrderController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user is cashier or admin
        $user = auth('web')->user();
        if (!$user->isCashier() && !$user->isAdmin()) {
            abort(403, 'Access denied. Cashier or Admin role required.');
        }

        $query = Order::with(['customer', 'employee', 'payments', 'creator']);

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

        return view('cashier.orders.create', compact('customers', 'employees', 'products', 'services', 'units', 'quotations'));
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
                'items.*.unit' => 'required|string',
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
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];
                $layoutPrice = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0;
                $subtotal = $baseAmount + $layoutPrice;
                $totalAmount += $subtotal;

                $order->details()->create([
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $layoutPrice,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Update total amount
            $order->update(['total_amount' => $totalAmount]);

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
        $order->load(['customer', 'employee', 'details.product', 'details.service', 'payments']);
        return view('cashier.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Check if user is cashier or admin
        $user = auth('web')->user();
        if (!$user->isCashier() && !$user->isAdmin()) {
            abort(403, 'Access denied. Cashier or Admin role required.');
        }

        $customers = Customer::orderBy('customer_firstname')->get();
        $employees = Employee::orderBy('employee_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        
        $order->load(['details.product', 'details.service']);

        return view('cashier.orders.edit', compact('order', 'customers', 'employees', 'products', 'services', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Check for admin password
        if (!$this->verifyAdminPassword($request)) {
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
                'items.*.unit' => 'required|string',
                'items.*.size' => 'nullable|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0',
            ]);

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
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];
                $layoutPrice = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0;
                $subtotal = $baseAmount + $layoutPrice;
                $totalAmount += $subtotal;

                $order->details()->create([
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $layoutPrice,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Update total amount
            $order->update(['total_amount' => $totalAmount]);

            return redirect()->route('cashier.orders.index')
                ->with('success', 'Job order updated successfully.');
        } catch (\Exception $e) {
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
        $request->validate([
            'status' => 'required|in:On-Process,Designing,Production,For Releasing,Completed,Cancelled'
        ]);

        $order->update(['order_status' => $request->status]);

        return redirect()->back()
            ->with('success', "Order status updated to {$request->status}.");
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

        // Verify admin password
        $admin = auth('web')->user();
        if (!\Hash::check($request->admin_password, $admin->password)) {
            return redirect()->back()
                ->with('error', 'Invalid admin password. Void operation cancelled.');
        }

        try {
            // Update order status to voided
            $order->update([
                'order_status' => 'Voided',
                'voided_at' => now(),
                'voided_by' => $admin->id,
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
}
