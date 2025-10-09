@extends('layouts.cashier')

@section('title', 'Create Quotation')
@section('page-title', 'Create New Quotation')
@section('page-description', 'Create a new quotation for a customer')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.quotations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Quotations
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('cashier.quotations.store') }}" class="space-y-6">
        @csrf
        
        <!-- Customer Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                    <x-customer-search :selectedCustomer="old('customer_id') ? \App\Models\Customer::find(old('customer_id')) : null" 
                                       placeholder="Search customers..." 
                                       :required="true" />
                    @error('customer_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Quotation Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quotation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-1">Quotation Date *</label>
                    <input type="date" name="quotation_date" id="quotation_date" value="{{ old('quotation_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('quotation_date') border-red-500 @enderror">
                    @error('quotation_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">Valid Until *</label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('valid_until') border-red-500 @enderror">
                    @error('valid_until')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quotation Items</h3>
            <x-product-service-selector :selectedItems="old('items', [])" type="both" />
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between items-center">
            <x-pos-calculator targetInput="total_amount" />
            <div class="flex space-x-4">
                <a href="{{ route('cashier.quotations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Create Quotation
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
