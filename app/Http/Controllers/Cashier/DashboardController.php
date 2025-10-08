<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // Check if user is cashier
        if (!auth('admin')->user()->isCashier()) {
            abort(403, 'Access denied. Cashier role required.');
        }

        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        $startDate = $startDateParam ? Carbon::parse($startDateParam)->startOfDay() : null;
        $endDate = $endDateParam ? Carbon::parse($endDateParam)->endOfDay() : null;

        // Calculate period sales for display
        $periodSales = Payment::sum('amount_paid');
        if ($startDate && $endDate) {
            $periodSales = Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount_paid');
        }

        $stats = [
            'total_quotations' => Quotation::count(),
            'pending_quotations' => Quotation::where('status', Quotation::STATUS_PENDING)->count(),
            'total_orders' => Order::count(),
            'active_orders' => Order::whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])->count(),
            'total_deliveries' => Delivery::count(),
            'pending_deliveries' => Delivery::where('status', '!=', 'Delivered')->count(),
            'period_sales' => $periodSales,
            'total_sales' => Payment::sum('amount_paid'),
        ];

        // Recent quotations
        $recent_quotations = Quotation::with(['customer'])
            ->orderBy('quotation_date', 'desc')
            ->take(5)
            ->get();

        // Recent orders
        $recent_orders = Order::with(['customer', 'employee'])
            ->orderBy('order_date', 'desc')
            ->take(5)
            ->get();

        // Recent deliveries
        $recent_deliveries = Delivery::with(['order.customer'])
            ->orderBy('delivery_date', 'desc')
            ->take(5)
            ->get();

            // Orders with pending payments
            $pending_payments = Order::with(['customer', 'payments'])
                ->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
                ->get()
                ->filter(function($order) {
                    return $order->remaining_balance > 0;
                })
                ->take(5);

            // Orders due in 1-3 days
            $due_orders = Order::with(['customer', 'employee'])
                ->whereNotNull('deadline_date')
                ->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
                ->whereBetween('deadline_date', [now()->addDay(), now()->addDays(3)])
                ->orderBy('deadline_date', 'asc')
                ->get();

            return view('cashier.dashboard', compact(
                'stats',
                'recent_quotations',
                'recent_orders',
                'recent_deliveries',
                'pending_payments',
                'due_orders'
            ));
    }
}
