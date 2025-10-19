@extends('layouts.admin')

@section('title', 'Quotation Details')
@section('page-title', 'Quotation #' . $quotation->quotation_id)
@section('page-description', 'View detailed information about this quotation')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.quotations.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Quotation #{{ $quotation->quotation_id }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $quotation->quotation_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-user mr-1"></i>{{ $quotation->customer->display_name }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">₱{{ number_format($quotation->final_total_amount, 2) }}</div>
                        <div class="text-sm text-gray-600">Total Amount</div>
                    </div>
                    <span class="px-3 py-1 rounded-md text-sm font-medium {{ $quotation->status == 'Pending' ? 'text-maroon' : 'text-gray-800' }}">
                        {{ $quotation->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-3 space-y-4">
            <!-- Quotation Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quotation Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Customer Details</h4>
                                <div class="space-y-2">
                                    <p class="text-gray-900 font-medium">{{ $quotation->customer->display_name }}</p>
                                    <div class="space-y-2">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-envelope w-4 mr-2"></i>
                                        {{ $quotation->customer->customer_email }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-phone w-4 mr-2"></i>
                                        {{ $quotation->customer->contact_number1 }}
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Timeline</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Quotation Date</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $quotation->quotation_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Status</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $quotation->status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Quotation Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Quotation Items</h3>
                        <div class="flex items-center space-x-6 text-sm text-gray-600">
                            <span>{{ $quotation->details->count() }} items</span>
                            @if($quotation->layout_fees > 0)
                            <span>₱{{ number_format($quotation->layout_fees, 2) }} layout fee</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout Price</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($quotation->details as $detail)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $detail->item_type === 'Product' ? 'text-blue-800' : 'text-green-800' }}">
                                        {{ $detail->item_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $detail->item_name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->quantity }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->unit ? $detail->unit->unit_name : '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->size }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($detail->price, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->layout)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-green-800">
                                            <i class="fas fa-check mr-1"></i>Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-gray-800">
                                            <i class="fas fa-times mr-1"></i>No
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->layout && $detail->layout_price > 0)
                                        ₱{{ number_format($detail->layout_price, 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-box text-4xl mb-2"></i>
                                    <p>No items found for this quotation.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quotation Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quotation Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. of items: </span>
                            <span class="font-medium">{{ $quotation->details->sum('quantity') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Amount:</span>
                            <span class="font-medium">₱{{ number_format($quotation->base_amount, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">VAT (12%):</span>
                            <span class="font-medium">₱{{ number_format($quotation->vat_amount, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sub Total:</span>
                            <span class="font-medium">₱{{ number_format($quotation->sub_total, 2) }}</span>
                        </div>
                       
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Discount:</span>
                            <div class="text-right">
                                <div class="font-medium text-green-600">-₱{{ number_format($quotation->quotation_discount_amount, 2) }}</div>
                                @if($quotation->quotation_discount_info)
                                    <div class="text-xs text-gray-500">
                                        @if($quotation->quotation_discount_info['type'] === 'percentage')
                                            {{ $quotation->quotation_discount_info['percentage'] }}% off
                                        @else
                                            ₱{{ number_format($quotation->quotation_discount_info['amount'], 2) }} off
                                        @endif
                                        @if($quotation->quotation_discount_info['rule_name'])
                                            ({{ $quotation->quotation_discount_info['rule_name'] }})
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Layout Fees: </span>
                            <span class="font-medium">₱{{ number_format($quotation->layout_fees, 2) }}</span>
                        </div>
                        
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-bold">
                            <span>TOTAL AMOUNT: </span>
                            <span>₱{{ number_format($quotation->final_total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($quotation->notes)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700">{{ $quotation->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Terms and Conditions -->
            @if($quotation->terms_and_conditions)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Terms and Conditions</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700">{{ $quotation->terms_and_conditions }}</p>
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
                    <a href="{{ route('admin.quotations.edit', $quotation) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Edit Quotation
                    </a>
                    @if($quotation->status === 'Pending')
                    <form action="{{ route('admin.quotations.status', $quotation) }}" method="POST" class="w-full">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Closed">
                        <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                            Close Quotation
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.quotations.status', $quotation) }}" method="POST" class="w-full">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Pending">
                        <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                            Reopen Quotation
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.quotations.archive', $quotation) }}" 
                          onsubmit="return confirm('Are you sure you want to archive this quotation?')" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                            Archive Quotation
                        </button>
                    </form>
                </div>
            </div>

        
        </div>
    </div>
</div>
@endsection
