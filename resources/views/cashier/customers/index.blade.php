@extends('layouts.cashier')

@section('title', 'Customers')
@section('page-title', 'Customer Management')
@section('page-description', 'Manage customer information and records')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.customers.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Add Customer
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Search -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customers..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('cashier.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}
                                    </div>
                                    <div class="text-sm text-gray-500">ID: {{ $customer->customer_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $customer->contact_number1 }}</div>
                            @if($customer->contact_number2)
                                <div class="text-sm text-gray-500">{{ $customer->contact_number2 }}</div>
                            @endif
                            @if($customer->email)
                                <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($customer->business_name)
                                <div class="text-sm font-medium text-gray-900">{{ $customer->business_name }}</div>
                            @else
                                <span class="text-sm text-gray-400">Individual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $customer->city }}, {{ $customer->province }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->address }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('cashier.customers.show', $customer) }}" 
                                   class="text-maroon hover:text-maroon-dark" title="View Customer">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cashier.customers.edit', $customer) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit Customer">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No customers found</p>
                                <p class="text-sm">Get started by adding a new customer.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($customers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
