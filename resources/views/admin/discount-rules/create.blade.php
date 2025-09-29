@extends('layouts.admin')

@section('title', 'Create Discount Rule')
@section('page-title', 'Create New Discount Rule')
@section('page-description', 'Create a new quantity-based discount rule')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.discount-rules.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Create Discount Rule</h2>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.discount-rules.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Rule Name -->
                <div class="md:col-span-2">
                    <label for="rule_name" class="block text-sm font-medium text-gray-700 mb-2">Rule Name *</label>
                    <input type="text" name="rule_name" id="rule_name" value="{{ old('rule_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('rule_name') border-red-500 @enderror"
                           placeholder="e.g., Bulk Order Discount">
                    @error('rule_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                              placeholder="Optional description for this discount rule">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity Range -->
                <div>
                    <label for="min_quantity" class="block text-sm font-medium text-gray-700 mb-2">Minimum Quantity *</label>
                    <input type="number" name="min_quantity" id="min_quantity" value="{{ old('min_quantity', 1) }}" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('min_quantity') border-red-500 @enderror">
                    @error('min_quantity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_quantity" class="block text-sm font-medium text-gray-700 mb-2">Maximum Quantity</label>
                    <input type="number" name="max_quantity" id="max_quantity" value="{{ old('max_quantity') }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('max_quantity') border-red-500 @enderror"
                           placeholder="Leave empty for unlimited">
                    @error('max_quantity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount Type -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Discount Type *</label>
                    <div class="flex space-x-6">
                        <label class="flex items-center">
                            <input type="radio" name="discount_type" value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'checked' : '' }}
                                   class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300" onchange="toggleDiscountFields()">
                            <span class="ml-2 text-sm text-gray-700">Percentage</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="discount_type" value="fixed" {{ old('discount_type') === 'fixed' ? 'checked' : '' }}
                                   class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300" onchange="toggleDiscountFields()">
                            <span class="ml-2 text-sm text-gray-700">Fixed Amount</span>
                        </label>
                    </div>
                    @error('discount_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount Percentage -->
                <div id="percentage-field">
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage *</label>
                    <div class="relative">
                        <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage') }}" 
                               min="0" max="100" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('discount_percentage') border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    @error('discount_percentage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount Amount -->
                <div id="amount-field" style="display: none;">
                    <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-2">Discount Amount *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₱</span>
                        </div>
                        <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount') }}" 
                               min="0" step="0.01"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('discount_amount') border-red-500 @enderror">
                    </div>
                    @error('discount_amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <!-- Valid From -->
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">Valid From</label>
                    <input type="date" name="valid_from" id="valid_from" value="{{ old('valid_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('valid_from') border-red-500 @enderror">
                    @error('valid_from')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valid Until -->
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('valid_until') border-red-500 @enderror">
                    @error('valid_until')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.discount-rules.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    Create Discount Rule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDiscountFields() {
    const percentageField = document.getElementById('percentage-field');
    const amountField = document.getElementById('amount-field');
    const percentageRadio = document.querySelector('input[name="discount_type"][value="percentage"]');
    const amountRadio = document.querySelector('input[name="discount_type"][value="fixed"]');
    
    if (percentageRadio.checked) {
        percentageField.style.display = 'block';
        amountField.style.display = 'none';
        document.getElementById('discount_percentage').required = true;
        document.getElementById('discount_amount').required = false;
    } else {
        percentageField.style.display = 'none';
        amountField.style.display = 'block';
        document.getElementById('discount_percentage').required = false;
        document.getElementById('discount_amount').required = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDiscountFields();
});
</script>
@endsection
