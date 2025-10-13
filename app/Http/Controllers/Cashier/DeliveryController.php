<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Allow all authenticated users to access deliveries

        $showArchived = $request->has('archived') && $request->archived;
        
        if ($showArchived) {
            $query = Delivery::onlyTrashed()->with(['order.customer', 'order.employee']);
        } else {
            $query = Delivery::with(['order.customer', 'order.employee']);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('delivery_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('delivery_date', '<=', $request->end_date);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('delivery_id', 'like', "%{$search}%")
                  ->orWhereHas('order.customer', function($customerQuery) use ($search) {
                      $customerQuery->where('customer_firstname', 'like', "%{$search}%")
                                   ->orWhere('customer_lastname', 'like', "%{$search}%")
                                   ->orWhere('business_name', 'like', "%{$search}%");
                  });
            });
        }

        $deliveries = $query->latest('delivery_date')->paginate(15)->appends($request->query());

        return view('cashier.deliveries.index', compact('deliveries', 'showArchived'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $orders = Order::whereNotIn('order_status', ['Completed', 'Cancelled'])
            ->whereDoesntHave('delivery')
            ->with('customer')
            ->get();

        // Get the pre-selected order if order_id is provided
        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::with('customer')->find($request->order_id);
        }

        $employees = Employee::with('job')->get();
        return view('cashier.deliveries.create', compact('orders', 'employees', 'selectedOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'delivery_date' => 'required|date',
                'delivery_address' => 'required|string|max:500',
                'driver_name' => 'nullable|string|max:100',
                'driver_contact' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:500',
                'status' => 'nullable|in:scheduled,in_transit,delivered,cancelled',
                'delivery_fee' => 'nullable|numeric|min:0',
            ]);

            // Check if order already has a delivery
            $existingDelivery = Delivery::where('order_id', $validated['order_id'])->first();
            if ($existingDelivery) {
                return redirect()->back()
                    ->with('error', 'This order already has a delivery record.')
                    ->withInput();
            }

            // Create delivery
            $delivery = Delivery::create([
                'delivery_id' => Delivery::generateDeliveryId(),
                'order_id' => $validated['order_id'],
                'delivery_date' => $validated['delivery_date'],
                'delivery_address' => $validated['delivery_address'],
                'driver_name' => $validated['driver_name'],
                'driver_contact' => $validated['driver_contact'],
                'notes' => $validated['notes'],
                'status' => $validated['status'] ?? 'scheduled',
                'delivery_fee' => $validated['delivery_fee'] ?? 0,
                'created_by' => auth('web')->id() ?? App\Models\User::first()->id,
            ]);

            return redirect()->route('cashier.deliveries.index')
                ->with('success', 'Delivery scheduled successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating delivery: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create delivery. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['order.customer', 'order.employee', 'order.details.product', 'order.details.service']);
        return view('cashier.deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        $orders = Order::whereNotIn('order_status', ['Completed', 'Cancelled'])
            ->with('customer')
            ->get();

        return view('cashier.deliveries.edit', compact('delivery', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'delivery_date' => 'required|date',
                'delivery_address' => 'required|string|max:500',
                'driver_name' => 'nullable|string|max:100',
                'driver_contact' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:500',
                'status' => 'required|in:scheduled,in_transit,delivered,cancelled',
                'delivery_fee' => 'nullable|numeric|min:0',
            ]);

            // Update delivery
            $delivery->update([
                'order_id' => $validated['order_id'],
                'delivery_date' => $validated['delivery_date'],
                'delivery_address' => $validated['delivery_address'],
                'driver_name' => $validated['driver_name'],
                'driver_contact' => $validated['driver_contact'],
                'notes' => $validated['notes'],
                'status' => $validated['status'],
                'delivery_fee' => $validated['delivery_fee'] ?? 0,
            ]);

            // If delivered, update order status to completed
            if ($validated['status'] === 'delivered') {
                $delivery->order->update(['order_status' => 'Completed']);
            }

            return redirect()->route('cashier.deliveries.index')
                ->with('success', 'Delivery updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating delivery: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update delivery. Please try again.')
                ->withInput();
        }
    }

    /**
     * Archive a delivery
     */
    public function archive(Delivery $delivery)
    {
        try {
            $delivery->delete();
            return redirect()->route('cashier.deliveries.index')
                ->with('success', 'Delivery archived successfully.');
        } catch (\Exception $e) {
            Log::error('Error archiving delivery: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to archive delivery. Please try again.');
        }
    }

    /**
     * Restore an archived delivery
     */
    public function restore($id)
    {
        try {
            $delivery = Delivery::withTrashed()->findOrFail($id);
            $delivery->restore();
            return redirect()->route('cashier.deliveries.index')
                ->with('success', 'Delivery restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring delivery: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to restore delivery. Please try again.');
        }
    }

    /**
     * Update delivery status
     */
    public function updateStatus(Request $request, Delivery $delivery)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_transit,delivered,cancelled'
        ]);

        $delivery->update(['status' => $request->status]);

        // If delivered, update order status to completed
        if ($request->status === 'delivered') {
            $delivery->order->update(['order_status' => 'Completed']);
        }

        return redirect()->back()
            ->with('success', "Delivery status updated to " . ucfirst(str_replace('_', ' ', $request->status)) . ".");
    }
}
