<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Service;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Order::onlyTrashed()->with(['customer', 'employee', 'details', 'payments'])
            : Order::with(['customer', 'employee', 'details', 'payments']);

        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest('order_date')->paginate(15)->appends($request->query());

        return view('admin.orders.index', compact('orders', 'showArchived'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $employees = Employee::with('job')->orderBy('employee_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();

        return view('admin.orders.create', compact('customers', 'employees', 'products', 'services', 'discountRules'));
    }

    public function store(Request $request)
    {
        try {
            // Debug: Log the incoming request data
            \Log::info('Order creation request data:', $request->all());
            \Log::info('Order creation attempt started');

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'employee_id' => 'required|exists:employees,employee_id',
                'layout_employee_id' => 'nullable|exists:employees,employee_id',
                'order_date' => 'required|date',
                'deadline_date' => 'required|date|after:order_date',
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
                // optional initial payment
                'payment.payment_date' => 'nullable|date',
                'payment.payment_method' => 'nullable|in:Cash,GCash,Bank Transfer,Check,Credit Card',
                'payment.payment_term' => 'nullable|in:Downpayment,Initial,Full',
                'payment.amount_paid' => 'nullable|numeric|min:0',
                'payment.reference_number' => 'nullable|string|max:255',
                'payment.remarks' => 'nullable|string',
            ]);

            $totalAmount = 0;

            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'employee_id' => $validated['employee_id'],
                'layout_employee_id' => $validated['layout_employee_id'] ?? null,
                'order_date' => $validated['order_date'],
                'deadline_date' => $validated['deadline_date'],
                'order_status' => 'On-Process',
                'total_amount' => 0, // Will be calculated after details
                'layout_design_fee' => 0,
            ]);

            // Process order details
            foreach ($validated['items'] as $item) {
                // Calculate base amount (Quantity × Price)
                $baseAmount = $item['quantity'] * $item['price'];

                // Apply discount to base amount
                $discountAmount = $item['discountAmount'] ?? 0;
                $subtotal = $baseAmount - $discountAmount;

                // Add layout price if checked (after discount)
                $layoutChecked = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]);
                if ($layoutChecked) {
                    $subtotal += $item['layoutPrice'] ?? 0;
                }

                $subtotal = max(0, $subtotal); // Ensure subtotal is not negative
                $vat = $subtotal * 0.12;
                $totalAmount += $subtotal + $vat; // Add VAT to total

                $order->details()->create([
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'vat' => $vat,
                    'discount' => $discountAmount,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $item['layoutPrice'] ?? 0,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Update order with final total amount
            $order->update(['total_amount' => $totalAmount]);

            // Optional initial payment at creation
            if (!empty($validated['payment']['amount_paid'])) {
                $payment = $validated['payment'];
                $totalPaid = (float) $payment['amount_paid'];
                $remaining = max(0, $order->total_amount - $totalPaid);
                $change = $totalPaid > (float) $order->total_amount ? ($totalPaid - (float) $order->total_amount) : 0;

                \App\Models\Payment::create([
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
                ]);

                // Update order status if fully paid
                if ($remaining <= 0) {
                    $order->update(['order_status' => 'Completed']);
                }
            }

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in order creation:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating order:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create order: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'employee.job',
            'layoutEmployee.job',
            'details.product',
            'details.service',
            'payments',
            'deliveries'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $employees = Employee::with('job')->orderBy('employee_firstname')->get();
        $products = Product::orderBy('product_name')->get();
        $services = Service::orderBy('service_name')->get();
        $order->load(['details']);

        return view('admin.orders.edit', compact('order', 'customers', 'employees', 'products', 'services'));
    }

    public function update(Request $request, Order $order)
    {
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
            'items.*.unit' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.layout' => 'nullable|in:on,1,true,false,0',
            'items.*.layoutPrice' => 'nullable|numeric|min:0',
            'items.*.discountAmount' => 'nullable|numeric|min:0',
            'items.*.discountRule' => 'nullable|string',
        ]);

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

        // Create new order details
        foreach ($validated['items'] as $item) {
            // Calculate base amount (Quantity × Price)
            $baseAmount = $item['quantity'] * $item['price'];

            // Apply discount to base amount
            $discountAmount = $item['discountAmount'] ?? 0;
            $subtotal = $baseAmount - $discountAmount;

            // Add layout price if checked (after discount)
            $layoutChecked = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]);
            if ($layoutChecked) {
                $subtotal += $item['layoutPrice'] ?? 0;
            }

            $subtotal = max(0, $subtotal); // Ensure subtotal is not negative
            $vat = $subtotal * 0.12;
            $totalAmount += $subtotal + $vat; // Add VAT to total

            $order->details()->create([
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'size' => $item['size'],
                'price' => $item['price'],
                'subtotal' => $subtotal,
                'vat' => $vat,
                'discount' => $discountAmount,
                'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                'layout_price' => $item['layoutPrice'] ?? 0,
                'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                'service_id' => $item['type'] === 'service' ? $item['id'] : null,
            ]);
        }

        // Update order with final total amount
        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:On-Process,Designing,Production,For Releasing,Completed,Cancelled',
        ]);

        $order->update(['order_status' => $validated['order_status']]);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
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
}
