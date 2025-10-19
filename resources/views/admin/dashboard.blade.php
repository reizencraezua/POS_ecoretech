@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Ecoretech Printing Shop Dashboard')
@section('page-description', 'Overview of printing shop operations')

@section('header-actions')
<div class="flex items-center gap-6">
    <!-- Date Filter Form -->
    <form method="GET" action="{{ route('admin.dashboard') }}" id="dateFilterForm" class="flex items-end gap-3">
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
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
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
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow" x-data="{ salesVisible: true }">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-green-500 bg-opacity-10 rounded-full">
                        <i class="fas fa-chart-line text-green-500 text-xl"></i>
                    </div>
                    <div>
                         <div class="flex items-center justify-between mb-2">
                             <p class="text-sm font-medium text-gray-600">
                                 @if(request('start_date') && request('end_date'))
                                     Sales ({{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }})
                                 @else
                                     Total Sales
                                 @endif
                             </p>
                             <button @click="salesVisible = !salesVisible" 
                                     class="p-1 rounded-full hover:bg-gray-100 transition-colors"
                                     :title="salesVisible ? 'Hide sales amount' : 'Show sales amount'">
                                 <i class="fas text-gray-500 transition-colors" 
                                    :class="salesVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                             </button>
                         </div>
                    </div>
                </div>
                <div class="text-right">
                     <div x-show="salesVisible" x-transition>
                         <p class="text-3xl font-bold text-gray-900">₱{{ number_format($stats['monthly_sales'], 2) }}</p>
                     </div>
                     <div x-show="!salesVisible" x-transition>
                         <p class="text-3xl font-bold text-gray-400">••••••</p>
                     </div>
                </div>
            </div>
        </div>


        <a href="{{ route('admin.inventories.index') }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-purple-500 bg-opacity-10 rounded-full">
                        <i class="fas fa-boxes text-purple-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Inventory Items</p>
                        @if($stats['critical_inventory_items'] > 0)
                            <p class="text-xs text-red-600 mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $stats['critical_inventory_items'] }} critical
                            </p>
                        @elseif($stats['low_stock_items'] > 0)
                            <p class="text-xs text-yellow-600 mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $stats['low_stock_items'] }} low stock
                            </p>
                        @else
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                All items in stock
                            </p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_inventory_items']) }}</p>
                </div>
            </div>
        </a>

        <!-- Due Today Card -->
        <a href="{{ route('admin.orders.index', ['status' => 'due_today']) }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-orange-500 bg-opacity-10 rounded-full">
                        <i class="fas fa-calendar-day text-orange-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Due Today</p>
                        @if($due_today_orders->count() > 0)
                            <p class="text-xs text-orange-600 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $due_today_orders->count() }} orders due today
                            </p>
                        @else
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                No orders due today
                            </p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-900">{{ $due_today_orders->count() }}</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Sales Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    @if(request('start_date') && request('end_date'))
                        Daily Sales Overview
                    @else
                        Monthly Sales Overview
                    @endif
                </h3>
                <p class="text-sm text-gray-600">
                    @if(request('start_date') && request('end_date'))
                        Daily sales performance from {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} to {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                    @else
                        Monthly sales performance for the last 6 months
                    @endif
                </p>
            </div>
            <div class="p-6">
                <canvas id="salesChart" height="120"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Order Status</h3>
                <p class="text-sm text-gray-600">Current order distribution</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                    $statusColors = [
                        'On-Process' => 'bg-blue-500',
                        'Designing' => 'bg-purple-500',
                        'Production' => 'bg-yellow-500',
                        'For Releasing' => 'bg-orange-500',
                        'Completed' => 'bg-green-500',
                        'Cancelled' => 'bg-red-500'
                    ];
                    @endphp

                    @foreach($order_status_counts as $status => $count)
                    @php
                    $percentage = $total_orders > 0 ? ($count / $total_orders) * 100 : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $statusColors[$status] }} mr-2"></div>
                            <span class="text-sm text-gray-600">{{ $status }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium">{{ $count }}</span>
                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $statusColors[$status] }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders and Pending Payments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-maroon hover:text-maroon-dark text-sm font-medium transition-colors">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recent_orders->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_orders as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-maroon bg-opacity-10 rounded-full flex items-center justify-center">
                                    <i class="fas fa-receipt text-maroon"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $order->customer->display_name }}</h4>
                                    <p class="text-sm text-gray-600">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <div class="flex items-center mt-1">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    @switch($order->order_status)
                                                        @case('On-Process')
                                                           text-blue-800
                                                            @break
                                                        @case('Designing')
                                                            text-purple-800
                                                            @break
                                                        @case('Production')
                                                            text-yellow-800
                                                            @break
                                                        @case('For Releasing')
                                                            text-orange-800
                                                            @break
                                                        @case('Completed')
                                                            text-green-800
                                                            @break
                                                        @case('Cancelled')
                                                            text-red-800
                                                            @break
                                                        @default
                                                            text-gray-800
                                                    @endswitch
                                                ">
                                            {{ $order->order_status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">₱{{ number_format($order->final_total_amount, 2) }}</p>
                            <p class="text-sm text-gray-600">{{ $order->order_date->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">Due: {{ $order->deadline_date->format('M d') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium">No recent orders found</p>
                    <p class="text-sm">New orders will appear here</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Pending Payments</h3>
                    <a href="{{ route('admin.payments.index') }}" class="text-maroon hover:text-maroon-dark text-sm font-medium transition-colors">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($pending_payments->count() > 0)
                <div class="space-y-4">
                    @foreach($pending_payments as $order)
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-100 hover:bg-red-100 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $order->customer->display_name }}</h4>
                                    <p class="text-sm text-gray-600">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Due: {{ $order->deadline_date->format('M d, Y') }}
                                        @if($order->deadline_date->isPast())
                                        <span class="text-red-600 font-medium">(Overdue)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-red-600">-₱{{ number_format($order->remaining_balance, 2) }}</p>
                            <p class="text-sm text-gray-600">Outstanding</p>
                            <p class="text-xs text-gray-500">of ₱{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                    
                </div>
                @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-check-circle text-4xl mb-4 text-green-400"></i>
                    <p class="text-lg font-medium text-green-600">All payments up to date!</p>
                    <p class="text-sm">No outstanding balances</p>
                </div>
                @endif
            </div>
        </div>

        
    </div>

    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const chartLabels = @json($chartLabels ?? []);
        const chartData = @json($chartData ?? []);
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Sales',
                    data: chartData,
                    borderColor: '#800020',
                    backgroundColor: 'rgba(128, 0, 32, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#800020',
                    pointBorderColor: '#800020',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Sales: ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endsection