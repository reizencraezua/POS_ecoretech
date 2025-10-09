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
        // Check if user is cashier or admin
        $user = auth('web')->user();
        if (!$user || (!$user->isCashier() && !$user->isAdmin())) {
            abort(403, 'Access denied. Cashier or Admin role required.');
        }

        $query = Delivery::with(['order.customer', 'order.employee']);

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

        return view('cashier.deliveries.index', compact('deliveries'));
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
                'employee_id' => 'nullable|exists:employees,employee_id',
                'delivery_date' => 'required|date',
                'delivery_address' => 'required|string|max:255',
                'delivery_contact' => 'required|string|max:20',
                'delivery_notes' => 'nullable|string|max:500',
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
                'driver_contact' => $validated['delivery_contact'],
                'notes' => $validated['delivery_notes'],
                'status' => 'Scheduled',
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
     * Update delivery status
     */
    public function updateStatus(Request $request, Delivery $delivery)
    {
        $request->validate([
            'status' => 'required|in:Scheduled,In Transit,Delivered,Failed'
        ]);

        $delivery->update(['status' => $request->status]);

        // If delivered, update order status to completed
        if ($request->status === 'Delivered') {
            $delivery->order->update(['order_status' => 'Completed']);
        }

        return redirect()->back()
            ->with('success', "Delivery status updated to {$request->status}.");
    }
}
