@extends('layouts.cashier')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')
@section('page-description', 'View customer information and order history')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Customer Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">
                            {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}
                        </h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-id-card mr-1"></i>ID: {{ $customer->customer_id }}</span>
                            @if($customer->business_name)
                                <span><i class="fas fa-building mr-1"></i>{{ $customer->business_name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('cashier.customers.edit', $customer) }}" 
                       class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Customer
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 w-5"></i>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $customer->contact_number1 }}</div>
                                <div class="text-xs text-gray-500">Primary Contact</div>
                            </div>
                        </div>
                        
                        @if($customer->contact_number2)
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 w-5"></i>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $customer->contact_number2 }}</div>
                                <div class="text-xs text-gray-500">Secondary Contact</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($customer->email)
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $customer->email }}</div>
                                <div class="text-xs text-gray-500">Email Address</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Address Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-400 w-5 mt-1"></i>
                            <div class="ml-3">
                                <div class="text-sm text-gray-900">{{ $customer->address }}</div>
                                <div class="text-sm text-gray-600">{{ $customer->city }}, {{ $customer->province }}</div>
                                @if($customer->postal_code)
                                    <div class="text-xs text-gray-500">{{ $customer->postal_code }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Order History</h3>
        </div>
        
        <div class="overflow-x-auto">
            @if($customer->orders->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customer->orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('cashier.orders.show', $order) }}" class="text-maroon hover:text-maroon-dark font-medium">
                                    #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->order_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($order->order_status === 'Completed') bg-green-100 text-green-800
                                    @elseif($order->order_status === 'Cancelled') bg-red-100 text-red-800
                                    @elseif($order->order_status === 'For Releasing') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₱{{ number_format($order->final_total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                ₱{{ number_format($order->total_paid, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $order->remaining_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                ₱{{ number_format($order->remaining_balance, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                        <p class="text-lg font-medium">No orders found</p>
                        <p class="text-sm">This customer hasn't placed any orders yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
