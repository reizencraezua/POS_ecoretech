
@extends('layouts.admin')

@section('title', 'Service Details')
@section('page-title', $service->service_name)
@section('page-description', 'View detailed information about this service')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Back Button & Title -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.services.index') }}" 
               class="p-2 text-gray-600 hover:text-maroon hover:bg-gray-100 rounded-lg transition-all">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $service->service_name }}</h1>
                <p class="text-sm text-gray-500">Service Details & Performance</p>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.services.edit', $service) }}" 
                class="bg-maroon hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Edit Service</span>
            </a>
            
            <button type="button" 
                    onclick="if(confirm('Are you sure you want to archive this service?')) { document.getElementById('archive-form').submit(); }"
                    class="bg-gray-500 hover:bg-gray-300 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-archive"></i>
                <span>Archive Service</span>
            </button>
            
            <form id="archive-form" method="POST" action="{{ route('admin.services.archive', $service) }}" class="hidden">
                @csrf
            </form>
        </div>
    </div>

    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Base Fee Card -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Base Fee</p>
                    <p class="text-2xl font-bold text-maroon">₱{{ number_format($service->base_fee, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-maroon bg-opacity-10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tag text-maroon text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Quantity Served Card -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Quantity Served</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalQuantity }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-concierge-bell text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Service Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Service Information</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-50">
                            <span class="text-sm text-gray-600">Service Name</span>
                            <span class="text-sm font-medium text-gray-900">{{ $service->service_name }}</span>
                        </div>

                        <div class="flex items-center justify-between py-3 border-b border-gray-50">
                            <span class="text-sm text-gray-600">Base Fee</span>
                            <span class="text-sm font-semibold text-maroon">₱{{ number_format($service->base_fee, 2) }}</span>
                        </div>

                        @if($service->requires_layout)
                        <div class="flex items-center justify-between py-3 border-b border-gray-50">
                            <span class="text-sm text-gray-600">Layout Required</span>
                            <span class="text-sm font-medium text-green-600">Yes</span>
                        </div>
                        
                        @if($service->layout_price > 0)
                        <div class="flex items-center justify-between py-3 border-b border-gray-50">
                            <span class="text-sm text-gray-600">Layout Price</span>
                            <span class="text-sm font-semibold text-maroon">₱{{ number_format($service->layout_price, 2) }}</span>
                        </div>
                        @endif
                        @endif

                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-gray-600">Last Updated</span>
                            <span class="text-sm text-gray-900">{{ $service->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if($service->description)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Description</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $service->description }}</p>
                    </div>
                    @endif

                    @if($service->layout_description)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Layout Description</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $service->layout_description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order History Card -->
            @if($orders->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Order History</h2>
                            <p class="text-xs text-gray-500 mt-1">Click on any order to view details</p>
                        </div>
                        <span class="text-sm text-gray-500">{{ $orders->count() }} orders</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($orders as $order)
                            @php
                                $orderDetail = $order->details->where('service_id', $service->service_id)->first();
                            @endphp
                            <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.orders.show', $order) }}'">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-blue-600 group-hover:text-blue-800">
                                            #{{ $order->order_id }}
                                        </span>
                                        <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-maroon flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">
                                                {{ substr($order->customer->display_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $order->customer->display_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $order->order_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $orderDetail ? $orderDetail->quantity : 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($orderDetail ? $orderDetail->unit_price : 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    ₱{{ number_format($orderDetail ? $orderDetail->subtotal : 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-50 text-yellow-700',
                                            'processing' => 'bg-blue-50 text-blue-700',
                                            'completed' => 'bg-green-50 text-green-700',
                                            'cancelled' => 'bg-red-50 text-red-700',
                                            'shipped' => 'bg-purple-50 text-purple-700'
                                        ];
                                        $statusColor = $statusColors[strtolower($order->order_status)] ?? 'bg-gray-50 text-gray-700';
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Financial Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Financial Summary</h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Total Revenue -->
                    <div class="bg-gradient-to-br from-maroon to-red-700 rounded-lg p-5 text-white">
                        <p class="text-sm opacity-90 mb-1">Total Revenue</p>
                        <p class="text-3xl font-bold">₱{{ number_format($totalRevenue, 2) }}</p>
                    </div>

                    <!-- Paid Amount -->
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-green-600 mb-1">Total Paid</p>
                                <p class="text-xl font-bold text-green-700">₱{{ number_format($totalPaid, 2) }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Amount -->
                    <div class="bg-{{ ($totalRevenue - $totalPaid) > 0 ? 'red' : 'gray' }}-50 rounded-lg p-4 border border-{{ ($totalRevenue - $totalPaid) > 0 ? 'red' : 'gray' }}-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-{{ ($totalRevenue - $totalPaid) > 0 ? 'red' : 'gray' }}-600 mb-1">Outstanding</p>
                                <p class="text-xl font-bold text-{{ ($totalRevenue - $totalPaid) > 0 ? 'red' : 'gray' }}-700">
                                    ₱{{ number_format($totalRevenue - $totalPaid, 2) }}
                                </p>
                            </div>
                            <div class="w-10 h-10 bg-{{ ($totalRevenue - $totalPaid) > 0 ? 'red' : 'gray' }}-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-{{ ($totalRevenue - $totalPaid) > 0 ? 'exclamation-triangle' : 'check-circle' }} text-{{ ($totalRevenue - $totalPaid) > 0 ? 'red' : 'gray' }}-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Progress -->
                    <div class="pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-600">Payment Progress</span>
                            <span class="text-xs font-semibold text-gray-900">
                                {{ $totalRevenue > 0 ? round(($totalPaid / $totalRevenue) * 100) : 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all" 
                                 style="width: {{ $totalRevenue > 0 ? round(($totalPaid / $totalRevenue) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Payments Card -->
            @if(isset($payments) && $payments->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Payments</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @foreach($payments->take(5) as $payment)
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-maroon">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-lg font-bold text-gray-900">₱{{ number_format($payment->amount_paid, 2) }}</span>
                                <span class="text-xs font-medium text-gray-600 bg-white px-2 py-1 rounded-full">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-600 space-y-1">
                                <div>{{ $payment->payment_date->format('M d, Y') }}</div>
                                <div>Order #{{ $payment->order->order_id }}</div>
                            </div>
                        </div>
                        @endforeach
                        @if($payments->count() > 5)
                        <div class="text-center pt-2">
                            <span class="text-xs text-gray-500">
                                +{{ $payments->count() - 5 }} more payments
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection