@extends('layouts.admin')

@section('title', 'Product Details')
@section('page-title', $product->product_name)
@section('page-description', 'View detailed information about this product')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $product->product_name }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-tag mr-1"></i>₱{{ number_format($product->base_price, 2) }}</span>
                            <span><i class="fas fa-shopping-cart mr-1"></i>{{ $totalQuantity }} sold</span>
                            <span><i class="fas fa-file-invoice mr-1"></i>{{ $totalOrders }} orders</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">₱{{ number_format($totalRevenue, 2) }}</div>
                        <div class="text-sm text-gray-600">Total Revenue</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-semibold text-gray-700">₱{{ number_format($totalPaid, 2) }}</div>
                        <div class="text-sm text-gray-600">Total Paid</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold {{ ($totalRevenue - $totalPaid) > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₱{{ number_format($totalRevenue - $totalPaid, 2) }}
                        </div>
                        <div class="text-sm text-gray-600">Outstanding</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Product Overview -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Product Overview</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Product Details</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Name</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $product->product_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Base Price</span>
                                        <span class="text-sm font-medium text-gray-900">₱{{ number_format($product->base_price, 2) }}</span>
                                    </div>
                                    @if($product->product_description)
                                    <div class="pt-2 border-t border-gray-100">
                                        <span class="text-sm text-gray-600">Description</span>
                                        <p class="text-sm text-gray-900 mt-1">{{ $product->product_description }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Sales Performance</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Total Orders</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $totalOrders }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Quantity Sold</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $totalQuantity }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Avg per Order</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $totalOrders > 0 ? round($totalQuantity / $totalOrders, 1) : 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            @if($orders->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Order History</h3>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span>{{ $orders->count() }} orders</span>
                            <span>₱{{ number_format($totalRevenue, 2) }} revenue</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            @php
                                $orderDetail = $order->details->where('product_id', $product->product_id)->first();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                        #{{ $order->order_id }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->customer->display_name }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $order->order_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $orderDetail ? $orderDetail->quantity : 0 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($orderDetail ? $orderDetail->unit_price : 0, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($orderDetail ? $orderDetail->subtotal : 0, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ $order->order_status }}
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

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Financial Summary</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 mb-1">₱{{ number_format($totalRevenue, 2) }}</div>
                            <div class="text-sm text-gray-600">Total Revenue</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-xl font-semibold text-green-700 mb-1">₱{{ number_format($totalPaid, 2) }}</div>
                            <div class="text-sm text-green-600">Total Paid</div>
                        </div>
                        <div class="text-center p-4 {{ ($totalRevenue - $totalPaid) > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-lg">
                            <div class="text-lg font-semibold {{ ($totalRevenue - $totalPaid) > 0 ? 'text-red-700' : 'text-gray-700' }} mb-1">
                                ₱{{ number_format($totalRevenue - $totalPaid, 2) }}
                            </div>
                            <div class="text-sm {{ ($totalRevenue - $totalPaid) > 0 ? 'text-red-600' : 'text-gray-600' }}">Outstanding</div>
                        </div>
                        <div class="pt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Payment Progress</span>
                                <span>{{ $totalRevenue > 0 ? round(($totalPaid / $totalRevenue) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gray-600 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? round(($totalPaid / $totalRevenue) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('admin.products.edit', $product) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>Edit Product
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Products
                    </a>
                </div>
            </div>

            <!-- Recent Payments -->
            @if($payments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Recent Payments</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($payments->take(5) as $payment)
                        <div class="border-l-2 border-gray-200 pl-3 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">₱{{ number_format($payment->amount_paid, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-600">Order #{{ $payment->order->order_id }}</p>
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $payment->payment_method }}</span>
                            </div>
                        </div>
                        @endforeach
                        @if($payments->count() > 5)
                        <div class="text-center pt-2">
                            <span class="text-xs text-gray-500">+{{ $payments->count() - 5 }} more payments</span>
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