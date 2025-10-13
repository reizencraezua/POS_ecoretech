<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\Quotation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        $startDate = $startDateParam ? Carbon::parse($startDateParam)->startOfDay() : null;
        $endDate = $endDateParam ? Carbon::parse($endDateParam)->endOfDay() : null;

        // Calculate total sales - exclude payments from voided orders
        $totalSales = Payment::whereHas('order', function($query) {
            $query->where('order_status', '!=', Order::STATUS_VOIDED);
        })->sum('amount_paid');
        
        // Calculate period sales for display (if date range is provided, show that range; otherwise show all-time)
        $periodSales = $totalSales;
        if ($startDate && $endDate) {
            $periodSales = Payment::whereHas('order', function($query) {
                $query->where('order_status', '!=', Order::STATUS_VOIDED);
            })->whereBetween('payment_date', [$startDate, $endDate])->sum('amount_paid');
        }

        // Inventory statistics
        $totalInventoryItems = Inventory::where('is_active', true)->count();
        $criticalInventoryItems = Inventory::where('is_active', true)
            ->whereRaw('stocks <= critical_level')
            ->count();
        $lowStockItems = Inventory::where('is_active', true)
            ->whereRaw('stocks <= critical_level * 1.5')
            ->whereRaw('stocks > critical_level')
            ->count();

        $stats = [
            'total_customers' => Customer::count(),
            'pending_quotations' => Quotation::where('status', Quotation::STATUS_PENDING)->count(),
            'active_orders' => Order::whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED, Order::STATUS_VOIDED])->count(),
            'monthly_sales' => $periodSales,
            'total_sales' => $totalSales,
            'total_inventory_items' => $totalInventoryItems,
            'critical_inventory_items' => $criticalInventoryItems,
            'low_stock_items' => $lowStockItems,
        ];

        // Job order distribution by status (active orders only)
        $statusList = [
            Order::STATUS_ON_PROCESS,
            Order::STATUS_DESIGNING,
            Order::STATUS_PRODUCTION,
            Order::STATUS_FOR_RELEASING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];

        $rawCounts = Order::select('order_status')
            ->whereIn('order_status', $statusList)
            ->groupBy('order_status')
            ->selectRaw('COUNT(*) as count')
            ->pluck('count', 'order_status')
            ->toArray();

        $order_status_counts = [];
        foreach ($statusList as $status) {
            $order_status_counts[$status] = (int) ($rawCounts[$status] ?? 0);
        }
        $total_orders = array_sum($order_status_counts);

        $recent_orders = Order::with(['customer', 'employee'])
            ->orderBy('order_date', 'desc')
            ->take(5)
            ->get();

        // Get orders with pending payments (outstanding balances)
        $pending_payments = Order::with(['customer', 'payments'])
            ->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->get()
            ->filter(function($order) {
                return $order->remaining_balance > 0;
            })
            ->take(5);

        // Orders due today
        $due_today_orders = Order::with(['customer', 'employee'])
            ->whereNotNull('deadline_date')
            ->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->whereDate('deadline_date', now()->toDateString())
            ->orderBy('deadline_date', 'asc')
            ->get();

        // Orders due in 1-3 days (excluding today)
        $due_orders = Order::with(['customer', 'employee'])
            ->whereNotNull('deadline_date')
            ->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->whereBetween('deadline_date', [now()->addDay(), now()->addDays(3)])
            ->orderBy('deadline_date', 'asc')
            ->get();

        // Monthly sales overview data for chart
        // Determine the period: either provided date range or last 6 months
        if ($startDate && $endDate && $startDate->lte($endDate)) {
            $periodStart = (clone $startDate)->startOfMonth();
            $periodEnd = (clone $endDate)->endOfMonth();
        } else {
            $periodEnd = Carbon::now()->endOfMonth();
            $periodStart = (clone $periodEnd)->subMonths(5)->startOfMonth();
        }

        // Initialize months map
        $monthsMap = [];
        $cursor = $periodStart->copy();
        while ($cursor->lte($periodEnd)) {
            $key = $cursor->format('Y-m');
            $monthsMap[$key] = 0.0;
            $cursor->addMonth();
        }

        // Fetch payments in period and aggregate in PHP (DB-agnostic)
        $paymentsQuery = Payment::query();
        $paymentsQuery->whereBetween('payment_date', [$periodStart->toDateString(), $periodEnd->toDateString()]);
        $payments = $paymentsQuery->get(['payment_date', 'amount_paid']);
        foreach ($payments as $payment) {
            $key = Carbon::parse($payment->payment_date)->format('Y-m');
            if (array_key_exists($key, $monthsMap)) {
                $monthsMap[$key] += (float) $payment->amount_paid;
            }
        }

        // Prepare labels and data
        $chartLabels = [];
        $chartData = [];
        foreach ($monthsMap as $ym => $sum) {
            $dt = Carbon::createFromFormat('Y-m', $ym);
            $chartLabels[] = $dt->format('M Y');
            $chartData[] = round($sum, 2);
        }

        return view('admin.dashboard', compact(
            'stats',
            'recent_orders',
            'pending_payments',
            'due_today_orders',
            'due_orders',
            'order_status_counts',
            'total_orders',
            'chartLabels',
            'chartData'
        ));
    }
}
