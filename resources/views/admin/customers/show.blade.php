@extends('layouts.admin')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')
@section('page-description', 'View detailed information about this customer')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $customer->display_name }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            @if($customer->business_name)
                                <span><i class="fas fa-building mr-1"></i>{{ $customer->business_name }}</span>
                            @endif
                            @if($customer->customer_email)
                                <span><i class="fas fa-envelope mr-1"></i>{{ $customer->customer_email }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Customer
                    </a>
                    <form method="POST" action="{{ route('admin.customers.archive', $customer) }}" class="inline" onsubmit="return confirm('Are you sure you want to archive this customer?')">
                        @csrf
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-archive mr-2"></i>
                            Archive
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">First Name</label>
                            <p class="text-gray-900 font-medium">{{ $customer->customer_firstname }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Name</label>
                            <p class="text-gray-900 font-medium">{{ $customer->customer_lastname }}</p>
                        </div>
                        @if($customer->customer_middlename)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Middle Name</label>
                            <p class="text-gray-900 font-medium">{{ $customer->customer_middlename }}</p>
                        </div>
                        @endif
                        @if($customer->customer_email)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900 font-medium">{{ $customer->customer_email }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Address</label>
                        <p class="text-gray-900 font-medium">{{ $customer->customer_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            @if($customer->business_name || $customer->tin)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Business Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($customer->business_name)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Business Name</label>
                            <p class="text-gray-900 font-medium">{{ $customer->business_name }}</p>
                        </div>
                        @endif
                        @if($customer->tin)
                        <div>
                            <label class="text-sm font-medium text-gray-500">TIN</label>
                            <p class="text-gray-900 font-medium">{{ $customer->tin }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Primary Contact Person</label>
                            <p class="text-gray-900 font-medium">{{ $customer->contact_person1 }}</p>
                            <p class="text-sm text-gray-600">{{ $customer->contact_number1 }}</p>
                        </div>
                        @if($customer->contact_person2)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Secondary Contact Person</label>
                            <p class="text-gray-900 font-medium">{{ $customer->contact_person2 }}</p>
                            <p class="text-sm text-gray-600">{{ $customer->contact_number2 }}</p>
                        </div>
                        @endif
                    </div>
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
                            <span class="text-lg font-semibold text-gray-900">{{ $customer->orders->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Quotations</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $customer->quotations->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Spent</span>
                            <span class="text-lg font-semibold text-green-600">₱{{ number_format($customer->orders->sum('total_amount'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            @if($customer->orders->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($customer->orders->take(5) as $order)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Order #{{ $order->order_id }}</p>
                                <p class="text-xs text-gray-500">{{ $order->order_date->format('M d, Y') }}</p>
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
                    @if($customer->orders->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.orders.index', ['search' => $customer->display_name]) }}" class="text-sm text-blue-600 hover:text-blue-800">
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
