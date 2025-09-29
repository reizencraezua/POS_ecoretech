<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Payment::sum('amount_paid');
        $pendingOrders = Order::where('order_status', 'On-Process')->count();
        $completedOrders = Order::where('order_status', 'Completed')->count();

        // Recent orders
        $recentOrders = Order::with(['customer', 'employee'])
            ->latest('order_date')
            ->limit(5)
            ->get();

        // Monthly revenue for chart
        $monthlyRevenue = Payment::select(
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('YEAR(payment_date) as year'),
            DB::raw('SUM(amount_paid) as total')
        )
            ->whereYear('payment_date', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.reports.index', compact(
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'completedOrders',
            'recentOrders',
            'monthlyRevenue'
        ));
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::with(['customer', 'employee', 'details.product', 'details.service'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->orderBy('order_date', 'desc')
            ->get();

        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Sales by status
        $salesByStatus = $orders->groupBy('order_status')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        });

        // Top products/services
        $topItems = collect();
        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $item = $detail->product ?? $detail->service;
                if ($item) {
                    $key = $item->product_name ?? $item->service_name;
                    if (!$topItems->has($key)) {
                        $topItems->put($key, [
                            'name' => $key,
                            'quantity' => 0,
                            'revenue' => 0
                        ]);
                    }
                    $topItems[$key]['quantity'] += $detail->quantity;
                    $topItems[$key]['revenue'] += $detail->subtotal;
                }
            }
        }
        $topItems = $topItems->sortByDesc('revenue')->take(10);

        return view('admin.reports.sales', compact(
            'orders',
            'totalSales',
            'totalOrders',
            'averageOrderValue',
            'salesByStatus',
            'topItems',
            'startDate',
            'endDate'
        ));
    }

    public function incomeStatement(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Revenue from payments
        $totalRevenue = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount_paid');

        // Cost of goods sold (assuming products have cost)
        $costOfGoodsSold = Order::with('details.product')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->get()
            ->sum(function ($order) {
                return $order->details->sum(function ($detail) {
                    return $detail->product ?
                        ($detail->product->cost ?? 0) * $detail->quantity : 0;
                });
            });

        $grossProfit = $totalRevenue - $costOfGoodsSold;

        // Operating expenses (simplified - you might want to add an expenses table)
        $operatingExpenses = 0; // This would come from an expenses table

        $netIncome = $grossProfit - $operatingExpenses;

        // Monthly breakdown
        $monthlyData = collect();
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthRevenue = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount_paid');

            $monthlyData->push([
                'month' => $current->format('M Y'),
                'revenue' => $monthRevenue,
                'cost' => 0, // Would calculate from expenses
                'profit' => $monthRevenue
            ]);

            $current->addMonth();
        }

        return view('admin.reports.income', compact(
            'totalRevenue',
            'costOfGoodsSold',
            'grossProfit',
            'operatingExpenses',
            'netIncome',
            'monthlyData',
            'startDate',
            'endDate'
        ));
    }

    public function agingReport(Request $request)
    {
        // Get orders with outstanding balances
        $orders = Order::with(['customer', 'payments'])
            ->where('order_status', '!=', 'Cancelled')
            ->get()
            ->map(function ($order) {
                $totalPaid = $order->payments->sum('amount_paid');
                $balance = $order->total_amount - $totalPaid;

                if ($balance > 0) {
                    $daysOverdue = now()->diffInDays($order->deadline_date, false);
                    $agingCategory = $this->getAgingCategory($daysOverdue);

                    return [
                        'order' => $order,
                        'balance' => $balance,
                        'days_overdue' => $daysOverdue,
                        'aging_category' => $agingCategory
                    ];
                }
                return null;
            })
            ->filter()
            ->sortBy('days_overdue');

        // Group by aging categories
        $agingSummary = $orders->groupBy('aging_category')->map(function ($group) {
            return [
                'count' => count($group),
                'total' => collect($group)->sum('balance')
            ];
        });

        $totalOutstanding = $orders->sum('balance');

        return view('admin.reports.aging', compact(
            'orders',
            'agingSummary',
            'totalOutstanding'
        ));
    }

    private function getAgingCategory($daysOverdue)
    {
        if ($daysOverdue < 0) {
            return 'Current';
        } elseif ($daysOverdue <= 30) {
            return '1-30 Days';
        } elseif ($daysOverdue <= 60) {
            return '31-60 Days';
        } elseif ($daysOverdue <= 90) {
            return '61-90 Days';
        } else {
            return 'Over 90 Days';
        }
    }
}
