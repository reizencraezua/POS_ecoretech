@extends('layouts.cashier')

@section('title', 'Quotation Details')
@section('page-title', 'Quotation Details')
@section('page-description', 'View quotation information and details')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.quotations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Quotations
    </a>
    
    @if($quotation->status === 'pending')
        <form method="POST" action="{{ route('cashier.quotations.update-status', $quotation) }}" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="approved">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
                    onclick="return confirm('Approve this quotation?')">
                <i class="fas fa-check mr-2"></i>
                Approve
            </button>
        </form>
        
        <form method="POST" action="{{ route('cashier.quotations.update-status', $quotation) }}" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="rejected">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
                    onclick="return confirm('Reject this quotation?')">
                <i class="fas fa-times mr-2"></i>
                Reject
            </button>
        </form>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Quotation Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Quotation #{{ $quotation->quotation_id }}</h2>
                <p class="text-gray-600">Created on {{ $quotation->quotation_date->format('F d, Y') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($quotation->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($quotation->status === 'approved') bg-green-100 text-green-800
                    @elseif($quotation->status === 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($quotation->status) }}
                </span>
                <p class="text-sm text-gray-500 mt-1">Valid until {{ $quotation->valid_until->format('F d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Name</p>
                <p class="text-sm text-gray-900">{{ $quotation->customer->customer_firstname }} {{ $quotation->customer->customer_lastname }}</p>
            </div>
            @if($quotation->customer->business_name)
            <div>
                <p class="text-sm font-medium text-gray-500">Business Name</p>
                <p class="text-sm text-gray-900">{{ $quotation->customer->business_name }}</p>
            </div>
            @endif
            <div>
                <p class="text-sm font-medium text-gray-500">Email</p>
                <p class="text-sm text-gray-900">{{ $quotation->customer->customer_email }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Phone</p>
                <p class="text-sm text-gray-900">{{ $quotation->customer->customer_phone }}</p>
            </div>
            @if($quotation->customer->customer_address)
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500">Address</p>
                <p class="text-sm text-gray-900">{{ $quotation->customer->customer_address }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Quotation Items -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quotation Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quotation->details as $detail)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $detail->product ? $detail->product->product_name : $detail->service->service_name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $detail->product ? 'Product' : 'Service' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $detail->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $detail->unit ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $detail->size ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₱{{ number_format($detail->price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($detail->layout)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Yes
                                </span>
                                @if($detail->layout_price > 0)
                                    <div class="text-xs text-gray-500 mt-1">₱{{ number_format($detail->layout_price, 2) }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₱{{ number_format($detail->subtotal, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Total Amount -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-end">
            <div class="w-64">
                <div class="flex justify-between items-center py-2">
                    <span class="text-lg font-medium text-gray-900">Total Amount:</span>
                    <span class="text-2xl font-bold text-gray-900">₱{{ number_format($quotation->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
