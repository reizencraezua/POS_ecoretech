@extends('layouts.admin')

@section('title', 'Service Details')
@section('page-title', 'Service Details')
@section('page-description', 'View detailed information about this service')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.services.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $service->service_name }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-tag mr-1"></i>₱{{ number_format($service->base_fee, 2) }}</span>
                            @if($service->layout_price > 0)
                                <span><i class="fas fa-palette mr-1"></i>+₱{{ number_format($service->layout_price, 2) }} layout</span>
                            @endif
                            @if($service->requires_layout)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Requires Layout</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.services.edit', $service) }}" 
                       class="bg-maroon hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        <span>Edit Service</span>
                    </a>
                    
                    <form method="POST" action="{{ route('admin.services.archive', $service) }}" class="inline" 
                          onsubmit="return confirm('Are you sure you want to archive this service?')">
                        @csrf
                        <button type="submit" 
                                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-archive"></i>
                            <span>Archive Service</span>
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.services.index') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Services</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Service Information -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Service Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Service Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Service Name</label>
                            <p class="text-gray-900 font-medium">{{ $service->service_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Base Fee</label>
                            <p class="text-gray-900 font-medium">₱{{ number_format($service->base_fee, 2) }}</p>
                        </div>
                        @if($service->layout_price > 0)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Layout Price</label>
                            <p class="text-gray-900 font-medium">₱{{ number_format($service->layout_price, 2) }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="text-sm font-medium text-gray-500">Requires Layout</label>
                            <p class="text-gray-900 font-medium">
                                @if($service->requires_layout)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Yes
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times mr-1"></i>No
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($service->description)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Description</label>
                        <p class="text-gray-900 font-medium">{{ $service->description }}</p>
                    </div>
                    @endif
                    
                    @if($service->layout_description)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Layout Description</label>
                        <p class="text-gray-900 font-medium">{{ $service->layout_description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Orders</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $totalOrders }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Revenue</span>
                            <span class="text-lg font-semibold text-green-600">₱{{ number_format($totalRevenue, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Paid</span>
                            <span class="text-lg font-semibold text-blue-600">₱{{ number_format($totalPaid, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Quantity</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $totalQuantity }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            @if($orders->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                        <p class="text-xs text-gray-500 mt-1">Click on any order to view details</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($orders->take(5) as $order)
                        <div class="flex justify-between items-center py-3 px-3 border-b border-gray-100 last:border-b-0 hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group rounded-lg" 
                             onclick="window.location.href='{{ route('admin.orders.show', $order) }}'">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600">Order #{{ $order->order_id }}</p>
                                    <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                                </div>
                                <p class="text-xs text-gray-500">{{ $order->customer->display_name ?? 'Unknown Customer' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($order->order_status === 'Completed') bg-green-100 text-green-800
                                    @elseif($order->order_status === 'On-Process') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'Cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $order->order_status }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($orders->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.orders.index', ['service' => $service->service_id]) }}" class="text-sm text-blue-600 hover:text-blue-800">
                            View all orders
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
