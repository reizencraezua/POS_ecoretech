<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['order.customer']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('order.customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $deliveries = $query->latest('delivery_date')->paginate(15);

        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $orders = Order::with('customer')->where('order_status', '!=', 'Cancelled')->get();
        return view('admin.deliveries.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
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
        return view('admin.deliveries.edit', compact('delivery', 'orders'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
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
            ->with('success', 'Delivery deleted successfully.');
    }
}
