@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')
@section('page-title', 'Cashier Dashboard')
@section('page-description', 'Overview of quotations, orders, and deliveries')

@section('header-actions')
<div class="flex items-center gap-6">
    <!-- Date Filter Form -->
    <form method="GET" action="{{ route('cashier.dashboard') }}" id="dateFilterForm" class="flex items-end gap-3">
        <div>
            <label for="start_date" class="block text-xs font-medium text-gray-600 mb-1">Start date</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                   class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                   onchange="autoFilter()">
        </div>
        <div>
            <label for="end_date" class="block text-xs font-medium text-gray-600 mb-1">End date</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                   class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                   onchange="autoFilter()">
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('cashier.dashboard') }}" class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
    </form>
</div>

<script>
function autoFilter() {
    document.getElementById('dateFilterForm').submit();
}
</script>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Status -->
    @if(request('start_date') || request('end_date'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-filter text-blue-600 mr-2"></i>
                <span class="text-sm font-medium text-blue-800">Filtered Results</span>
                <span class="text-sm text-blue-600 ml-2">
                    @if(request('start_date') && request('end_date'))
                        {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                    @elseif(request('start_date'))
                        From {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }}
                    @elseif(request('end_date'))
                        Until {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                    @endif
                </span>
            </div>
            <a href="{{ route('cashier.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <i class="fas fa-times mr-1"></i>Clear Filters
            </a>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('cashier.orders.index') }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Job Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
                    @if($stats['active_orders'] > 0)
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-play mr-1"></i>
                            {{ $stats['active_orders'] }} active
                        </p>
                    @endif
                </div>
                <div class="p-3 bg-green-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-shopping-cart text-green-500 text-xl"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('cashier.orders.index') }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Order Due Dates</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['due_orders_today']) }}</p>
                    @if($stats['due_orders_today'] > 0)
                        <p class="text-xs text-red-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Due today
                        </p>
                    @elseif($stats['due_orders_soon'] > 0)
                        <p class="text-xs text-orange-600 mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $stats['due_orders_soon'] }} due in 1-3 days
                        </p>
                    @else
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            No urgent due dates
                        </p>
                    @endif
                </div>
                <div class="p-3 bg-blue-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-calendar-alt text-blue-500 text-xl"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('cashier.deliveries.index') }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Deliveries</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_deliveries']) }}</p>
                    @if($stats['pending_deliveries'] > 0)
                        <p class="text-xs text-yellow-600 mt-1">
                            <i class="fas fa-truck mr-1"></i>
                            {{ $stats['pending_deliveries'] }} pending
                        </p>
                    @endif
                </div>
                <div class="p-3 bg-purple-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-truck text-purple-500 text-xl"></i>
                </div>
            </div>
        </a>

    </div>

    <!-- Recent Activity Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Quotations -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Quotations</h3>
                    <a href="{{ route('cashier.quotations.index') }}" class="text-cashier-blue hover:text-cashier-blue-dark text-sm font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recent_quotations->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_quotations as $quotation)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $quotation->customer->customer_firstname }} {{ $quotation->customer->customer_lastname }}</p>
                            <p class="text-sm text-gray-600">₱{{ number_format($quotation->total_amount, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($quotation->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($quotation->status === 'approved') bg-green-100 text-green-800
                                @elseif($quotation->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($quotation->status) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $quotation->quotation_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-file-invoice text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No quotations found</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Job Orders</h3>
                    <a href="{{ route('cashier.orders.index') }}" class="text-cashier-blue hover:text-cashier-blue-dark text-sm font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recent_orders->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_orders as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}</p>
                            <p class="text-sm text-gray-600">₱{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->order_status === 'On-Process') bg-blue-100 text-blue-800
                                @elseif($order->order_status === 'Designing') bg-purple-100 text-purple-800
                                @elseif($order->order_status === 'Production') bg-yellow-100 text-yellow-800
                                @elseif($order->order_status === 'For Releasing') bg-orange-100 text-orange-800
                                @elseif($order->order_status === 'Completed') bg-green-100 text-green-800
                                @elseif($order->order_status === 'Cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->order_status }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $order->order_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No orders found</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Due Orders (1-3 days) -->
    @if($due_orders->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Orders Due Soon (1-3 days)</h3>
                <a href="{{ route('cashier.orders.index') }}" class="text-maroon hover:text-maroon-dark text-sm font-medium">
                    View All Orders
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($due_orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $order->order_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->deadline_date->isToday()) bg-red-100 text-red-800
                                @elseif($order->deadline_date->isTomorrow()) bg-yellow-100 text-yellow-800
                                @else bg-orange-100 text-orange-800 @endif">
                                {{ $order->deadline_date->format('M d, Y') }}
                                @if($order->deadline_date->isToday())
                                    (Today)
                                @elseif($order->deadline_date->isTomorrow())
                                    (Tomorrow)
                                @else
                                    ({{ $order->deadline_date->diffInDays(now()) }} days)
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->order_status === 'On-Process') bg-blue-100 text-blue-800
                                @elseif($order->order_status === 'Designing') bg-purple-100 text-purple-800
                                @elseif($order->order_status === 'Production') bg-yellow-100 text-yellow-800
                                @elseif($order->order_status === 'For Releasing') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->order_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('cashier.orders.show', $order) }}" class="text-maroon hover:text-maroon-dark">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Pending Payments -->
    @if($pending_payments->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Orders with Pending Payments</h3>
                <a href="{{ route('cashier.payments.index') }}" class="text-maroon hover:text-maroon-dark text-sm font-medium">
                    View All Payments
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($pending_payments as $order)
                <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}</p>
                        <p class="text-sm text-gray-600">Order #{{ $order->order_id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold text-yellow-800">₱{{ number_format($order->remaining_balance, 2) }}</p>
                        <p class="text-xs text-gray-500">Remaining Balance</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
