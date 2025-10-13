@extends('layouts.cashier')

@section('title', 'Delivery Details')
@section('page-title', 'Delivery #' . $delivery->delivery_id)
@section('page-description', 'View detailed information about this delivery')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cashier.deliveries.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Delivery #{{ $delivery->delivery_id }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $delivery->delivery_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-hashtag mr-1"></i>Order #{{ $delivery->order_id }}</span>
                            <span><i class="fas fa-user mr-1"></i>{{ $delivery->order->customer->customer_firstname }} {{ $delivery->order->customer->customer_lastname }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">₱{{ number_format($delivery->order->total_amount, 2) }}</div>
                        <div class="text-sm text-gray-600">Order Amount</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-semibold text-gray-700">₱{{ number_format($delivery->delivery_fee, 2) }}</div>
                        <div class="text-sm text-gray-600">Delivery Fee</div>
                    </div>
                    <span class="px-3 py-1 rounded-md text-sm font-medium 
                        @if($delivery->status == 'scheduled') bg-blue-100 text-blue-800
                        @elseif($delivery->status == 'in_transit') bg-yellow-100 text-yellow-800
                        @elseif($delivery->status == 'delivered') bg-green-100 text-green-800
                        @elseif($delivery->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-3 space-y-4">
            <!-- Delivery Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Delivery Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Customer Details</h4>
                                <div class="space-y-2">
                                    <p class="text-gray-900 font-medium">{{ $delivery->order->customer->customer_firstname }} {{ $delivery->order->customer->customer_lastname }}</p>
                                    @if($delivery->order->customer->business_name)
                                        <p class="text-sm text-gray-600">{{ $delivery->order->customer->business_name }}</p>
                                    @endif
                                    @if($delivery->order->customer->customer_contact)
                                        <p class="text-sm text-gray-600"><i class="fas fa-phone mr-1"></i>{{ $delivery->order->customer->customer_contact }}</p>
                                    @endif
                                    @if($delivery->order->customer->customer_email)
                                        <p class="text-sm text-gray-600"><i class="fas fa-envelope mr-1"></i>{{ $delivery->order->customer->customer_email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Driver Information</h4>
                                <div class="space-y-2">
                                    <p class="text-gray-900 font-medium">{{ $delivery->driver_name ?? 'Not Assigned' }}</p>
                                    @if($delivery->driver_contact)
                                        <p class="text-sm text-gray-600"><i class="fas fa-phone mr-1"></i>{{ $delivery->driver_contact }}</p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Schedule</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Delivery Date</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $delivery->delivery_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Created</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $delivery->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Delivery Address</h4>
                                <div class="space-y-2">
                                    <p class="text-sm text-gray-900 leading-relaxed">{{ $delivery->delivery_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Order Details</h3>
                        <a href="{{ route('cashier.orders.show', $delivery->order_id) }}" class="text-sm text-maroon hover:text-red-700 font-medium">
                            View Full Order <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Order Number</span>
                            <span class="text-sm font-medium text-gray-900">#{{ $delivery->order_id }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Order Date</span>
                            <span class="text-sm font-medium text-gray-900">{{ $delivery->order->order_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Items</span>
                            <span class="text-sm font-medium text-gray-900">{{ $delivery->order->details->count() }} items</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-sm text-gray-600">Order Total</span>
                            <span class="text-sm font-semibold text-maroon">₱{{ number_format($delivery->order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($delivery->notes)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Delivery Notes</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $delivery->notes }}</p>
                </div>
            </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('cashier.deliveries.edit', $delivery) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm inline-flex items-center justify-center">
                        Edit Delivery
                    </a>
                    <button type="button" 
                            onclick="if(confirm('Are you sure you want to archive this delivery?')) { document.getElementById('archive-form').submit(); }"
                            class="w-full w-full bg-red-100 hover:bg-red-200 text-red-900 px-3 py-2 rounded text-sm inline-flex items-center justify-center ">
                        Archive
                    </button>
                    <form id="archive-form" method="POST" action="{{ route('cashier.deliveries.archive', $delivery) }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

            <!-- Delivery Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-clock mr-2 text-maroon"></i>
                            Delivery Timeline
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Created</p>
                                <p class="text-xs text-gray-500">{{ $delivery->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Scheduled</p>
                                <p class="text-xs text-gray-500">{{ $delivery->delivery_date->format('M d, Y') }}</p>
                            </div>
                        </div>

                        @if($delivery->status == 'delivered')
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-truck text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Delivered</p>
                                <p class="text-xs text-gray-500">{{ $delivery->updated_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calculator mr-2 text-maroon"></i>
                        Financial Summary
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Amount:</span>
                            <span class="font-medium">₱{{ number_format($delivery->order->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery Fee:</span>
                            <span class="font-medium">₱{{ number_format($delivery->delivery_fee, 2) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-bold">
                            <span>TOTAL AMOUNT:</span>
                            <span>₱{{ number_format($delivery->order->total_amount + $delivery->delivery_fee, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
