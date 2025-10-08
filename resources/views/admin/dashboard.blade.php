@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Ecoretech Printing Shop Dashboard')
@section('page-description', 'Overview of printing shop operations')

@section('header-actions')
<div class="flex items-center gap-6">
    <!-- Monthly Sales Display -->
    
    <!-- Date Filter Form -->
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-end gap-3">
        <div>
            <label for="start_date" class="block text-xs font-medium text-gray-600 mb-1">Start date</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                   class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
        </div>
        <div>
            <label for="end_date" class="block text-xs font-medium text-gray-600 mb-1">End date</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                   class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-md">Filter</button>
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
    </form>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                     <p class="text-sm font-medium text-gray-600">
                         @if(request('start_date') && request('end_date'))
                             Sales ({{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }})
                         @else
                             Total Sales (All Time)
                         @endif
                     </p>
                     <p class="text-3xl font-bold text-gray-900">₱{{ number_format($stats['monthly_sales'], 2) }}</p>
                </div>
                <div class="p-3 bg-green-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-chart-line text-green-500 text-xl"></i>
                </div>
            </div>
        </a>


        <a href="{{ route('admin.inventory.index') }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inventory Items</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_inventory_items']) }}</p>
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
                <div class="p-3 bg-purple-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-boxes text-purple-500 text-xl"></i>
                </div>
            </div>
        </a>

        <!-- Due Today Card -->
        <a href="{{ route('admin.orders.index', ['status' => 'due_today']) }}" class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Due Today</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $due_today_orders->count() }}</p>
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
                <div class="p-3 bg-orange-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-calendar-day text-orange-500 text-xl"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Sales Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Sales Overview</h3>
                <p class="text-sm text-gray-600">
                    @if(request('start_date') && request('end_date'))
                        Sales performance from {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} to {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                    @else
                        Sales performance for the last 6 months
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
                                                            bg-blue-100 text-blue-800
                                                            @break
                                                        @case('Designing')
                                                            bg-purple-100 text-purple-800
                                                            @break
                                                        @case('Production')
                                                            bg-yellow-100 text-yellow-800
                                                            @break
                                                        @case('For Releasing')
                                                            bg-orange-100 text-orange-800
                                                            @break
                                                        @case('Completed')
                                                            bg-green-100 text-green-800
                                                            @break
                                                        @case('Cancelled')
                                                            bg-red-100 text-red-800
                                                            @break
                                                        @default
                                                            bg-gray-100 text-gray-800
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
                            <p class="font-semibold text-red-600">₱{{ number_format($order->remaining_balance, 2) }}</p>
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

        <!-- Due Orders (1-3 days) -->
        @if($due_orders->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Orders Due Soon (1-3 days)</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-maroon hover:text-maroon-dark text-sm font-medium transition-colors">
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
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-maroon hover:text-maroon-dark">
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
    </div>

    <!-- Quick Actions and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <!-- <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.customers.index') }}" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:bg-gray-50 hover:border-maroon transition-all group">
                    <i class="fas fa-user-plus text-2xl text-maroon mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-gray-700">Add Customer</span>
                </a>
                <a href="{{ route('admin.quotations.create') }}" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-500 transition-all group">
                    <i class="fas fa-plus-circle text-2xl text-blue-500 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-gray-700">New Quotation</span>
                </a>
                <a href="{{ route('admin.orders.create') }}" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-500 transition-all group">
                    <i class="fas fa-plus-circle text-2xl text-green-500 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-gray-700">Create Order</span>
                </a>
            </div>
        </div> -->

        <!-- Today's Schedule -->
        <!-- <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Today's Schedule</h3>
                <p class="text-sm text-gray-600">{{ now()->format('l, F j, Y') }}</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Design Review</p>
                            <p class="text-xs text-gray-600">Wedding Invitations - 10:00 AM</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Production Deadline</p>
                            <p class="text-xs text-gray-600">Business Cards - 2:00 PM</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Delivery Schedule</p>
                            <p class="text-xs text-gray-600">Corporate Brochures - 4:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- System Status -->
        <!-- <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
                <p class="text-sm text-gray-600">Current system health</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Database</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-green-600">Online</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Storage</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-green-600">75% Available</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Backup</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-green-600">Last: Today</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Updates</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-yellow-600">1 Available</span>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
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
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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
                    }
                }
            }
        });
    });
</script>
@endsection