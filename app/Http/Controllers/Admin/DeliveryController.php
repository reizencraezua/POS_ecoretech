<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Employee;
use App\Models\Log;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Delivery::with(['order.customer', 'employee'])->onlyTrashed()
            : Delivery::with(['order.customer', 'employee']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }


        $deliveries = $query->orderBy('delivery_id', 'desc')->paginate(15);

        return view('admin.deliveries.index', compact('deliveries', 'showArchived'));
    }

    public function create(Request $request)
    {
        $orders = Order::whereNotIn('order_status', ['Completed', 'Cancelled'])
            ->whereDoesntHave('deliveries', function($query) {
                $query->where('status', 'delivered');
            })
            ->with('customer')
            ->get();
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

        $delivery = Delivery::create($validated);

        // Log the creation
        $this->logCreated(
            Log::TYPE_DELIVERY,
            $delivery->delivery_id,
            $this->generateTransactionName(Log::TYPE_DELIVERY, $delivery->delivery_id),
            $delivery->toArray()
        );

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
        // Store original data for logging
        $originalData = $delivery->toArray();
        
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

        // Log the update
        $this->logUpdated(
            Log::TYPE_DELIVERY,
            $delivery->delivery_id,
            $originalData,
            $delivery->fresh()->toArray(),
            $this->generateTransactionName(Log::TYPE_DELIVERY, $delivery->delivery_id)
        );

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery updated successfully.');
    }

    public function destroy(Delivery $delivery)
    {
        // Log the deletion
        $this->logDeleted(
            Log::TYPE_DELIVERY,
            $delivery->delivery_id,
            $this->generateTransactionName(Log::TYPE_DELIVERY, $delivery->delivery_id),
            $delivery->toArray()
        );

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
