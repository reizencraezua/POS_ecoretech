@extends('layouts.admin')

@section('title', 'Create Discount Rule')
@section('page-title', 'Create New Discount Rule')
@section('page-description', 'Create a new quantity-based discount rule')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.discount-rules.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Create Discount Rule</h2>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.discount-rules.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Rule Name -->
                    <div>
                        <label for="rule_name" class="block text-sm font-medium text-gray-700 mb-2">Rule Name *</label>
                        <input type="text" name="rule_name" id="rule_name" value="{{ old('rule_name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('rule_name') border-red-500 @enderror"
                               placeholder="e.g., Bulk Order Discount">
                        @error('rule_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                                  placeholder="Describe when this discount rule applies...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Range *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="min_quantity" class="block text-xs text-gray-600 mb-1">Minimum</label>
                                <input type="number" name="min_quantity" id="min_quantity" value="{{ old('min_quantity', 1) }}" min="1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('min_quantity') border-red-500 @enderror">
                                @error('min_quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="max_quantity" class="block text-xs text-gray-600 mb-1">Maximum (optional)</label>
                                <input type="number" name="max_quantity" id="max_quantity" value="{{ old('max_quantity') }}" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('max_quantity') border-red-500 @enderror"
                                       placeholder="No limit">
                                @error('max_quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Validity Period -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Validity Period (Optional)</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="valid_from" class="block text-xs text-gray-600 mb-1">Valid From</label>
                                <input type="date" name="valid_from" id="valid_from" value="{{ old('valid_from') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('valid_from') border-red-500 @enderror">
                                @error('valid_from')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="valid_until" class="block text-xs text-gray-600 mb-1">Valid Until</label>
                                <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('valid_until') border-red-500 @enderror">
                                @error('valid_until')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Discount Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center p-3 border rounded-lg cursor-pointer
                                @if(old('discount_type', 'percentage') === 'percentage') border-maroon bg-maroon-50
                                @else border-gray-300 hover:border-gray-400
                                @endif">
                                <input type="radio" name="discount_type" value="percentage" 
                                       {{ old('discount_type', 'percentage') === 'percentage' ? 'checked' : '' }}
                                       class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300"
                                       onchange="toggleDiscountFields()">
                                <div class="ml-2">
                                    <div class="text-sm font-medium text-gray-900">Percentage</div>
                                    <div class="text-xs text-gray-500">% based</div>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-3 border rounded-lg cursor-pointer
                                @if(old('discount_type') === 'fixed') border-maroon bg-maroon-50
                                @else border-gray-300 hover:border-gray-400
                                @endif">
                                <input type="radio" name="discount_type" value="fixed" 
                                       {{ old('discount_type') === 'fixed' ? 'checked' : '' }}
                                       class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300"
                                       onchange="toggleDiscountFields()">
                                <div class="ml-2">
                                    <div class="text-sm font-medium text-gray-900">Fixed Amount</div>
                                    <div class="text-xs text-gray-500">₱ amount</div>
                                </div>
                            </label>
                        </div>
                        @error('discount_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Discount Values -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="discount_percentage" class="block text-xs text-gray-600 mb-1">Percentage (%)</label>
                                <div class="relative">
                                    <input type="number" name="discount_percentage" id="discount_percentage" 
                                           value="{{ old('discount_percentage') }}" 
                                           min="0" max="100" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('discount_percentage') border-red-500 @enderror"
                                           placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">%</span>
                                    </div>
                                </div>
                                @error('discount_percentage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="discount_amount" class="block text-xs text-gray-600 mb-1">Amount (₱)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">₱</span>
                                    </div>
                                    <input type="number" name="discount_amount" id="discount_amount" 
                                           value="{{ old('discount_amount') }}" 
                                           min="0" step="0.01"
                                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('discount_amount') border-red-500 @enderror"
                                           placeholder="0.00">
                                </div>
                                @error('discount_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded">
                            <div>
                                <label for="is_active" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Active Rule
                                </label>
                                <p class="text-xs text-gray-500">Enable this discount rule</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6 mt-6">
                <a href="{{ route('admin.discount-rules.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Rule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDiscountFields() {
    const percentageRadio = document.querySelector('input[name="discount_type"][value="percentage"]');
    const amountRadio = document.querySelector('input[name="discount_type"][value="fixed"]');
    const percentageField = document.getElementById('discount_percentage');
    const amountField = document.getElementById('discount_amount');
    
    if (percentageRadio.checked) {
        percentageField.required = true;
        percentageField.disabled = false;
        amountField.required = false;
        amountField.disabled = true;
        amountField.value = '';
    } else {
        percentageField.required = false;
        percentageField.disabled = true;
        amountField.required = true;
        amountField.disabled = false;
        percentageField.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDiscountFields();
});
</script>
@endsection
