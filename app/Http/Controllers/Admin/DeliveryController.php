<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Employee;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Delivery::with(['order.customer', 'employee'])->onlyTrashed()
            : Delivery::with(['order.customer', 'employee']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search in delivery fields
                $q->where('delivery_id', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('delivery_address', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  // Search in order fields
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('order_id', 'like', "%{$search}%")
                                ->orWhere('order_status', 'like', "%{$search}%");
                  })
                  // Search in customer fields
                  ->orWhereHas('order.customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('customer_firstname', 'like', "%{$search}%")
                                   ->orWhere('customer_lastname', 'like', "%{$search}%")
                                   ->orWhere('business_name', 'like', "%{$search}%")
                                   ->orWhere('customer_email', 'like', "%{$search}%")
                                   ->orWhere('customer_phone', 'like', "%{$search}%");
                  })
                  // Search in employee fields
                  ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                      $employeeQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $deliveries = $query->latest('delivery_date')->paginate(15);
        $deliveries->appends($request->query());

        // If it's an AJAX request, return only the table content
        if ($request->ajax()) {
            return view('admin.deliveries.partials.deliveries-table', compact('deliveries', 'showArchived'));
        }

        // If it's an AJAX request, return only the table content
        if ($request->ajax()) {
            return view('admin.deliveries.partials.deliveries-table', compact('deliveries', 'showArchived'));
        }

        return view('admin.deliveries.index', compact('deliveries', 'showArchived'));
    }

    public function create(Request $request)
    {
        $orders = Order::with('customer')->where('order_status', '!=', 'Cancelled')->get();
        $employees = Employee::with('job')->get();
        
        // Get the pre-selected order if order_id is provided
        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::with('customer')->find($request->order_id);
        }

        return view('admin.deliveries.create', compact('orders', 'employees', 'selectedOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'employee_id' => 'nullable|exists:employees,employee_id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'delivery_address' => 'required|string|max:500',
            'driver_name' => 'nullable|string|max:255',
            'driver_contact' => 'nullable|string|max:20',
            'status' => 'required|in:scheduled,in_transit,delivered,cancelled',
            'delivery_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Delivery::create($validated);

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery scheduled successfully.');
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['order.customer']);
        return view('admin.deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        $orders = Order::with('customer')->where('order_status', '!=', 'Cancelled')->get();
        $employees = Employee::with('job')->get();
        return view('admin.deliveries.edit', compact('delivery', 'orders', 'employees'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'employee_id' => 'nullable|exists:employees,employee_id',
            'delivery_date' => 'required|date',
            'delivery_address' => 'required|string|max:500',
            'driver_name' => 'nullable|string|max:255',
            'driver_contact' => 'nullable|string|max:20',
            'status' => 'required|in:scheduled,in_transit,delivered,cancelled',
            'delivery_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $delivery->update($validated);

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery updated successfully.');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery archived successfully.');
    }

    public function archive(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery archived successfully.');
    }

    public function restore($deliveryId)
    {
        $delivery = Delivery::withTrashed()->findOrFail($deliveryId);
        $delivery->restore();

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery restored successfully.');
    }
}
