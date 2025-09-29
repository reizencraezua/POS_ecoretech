@extends('layouts.admin')

@section('title', 'Create Layout Fee')
@section('page-title', 'Create New Layout Fee Setting')
@section('page-description', 'Add a new layout fee setting for orders')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.layout-fees.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Create Layout Fee Setting</h2>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.layout-fees.store') }}" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Setting Name -->
                <div>
                    <label for="setting_name" class="block text-sm font-medium text-gray-700 mb-2">Setting Name *</label>
                    <input type="text" name="setting_name" id="setting_name" value="{{ old('setting_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('setting_name') border-red-500 @enderror"
                           placeholder="e.g., Standard Layout Fee, Premium Layout Fee">
                    @error('setting_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Layout Fee Type -->
                <div>
                    <label for="layout_fee_type" class="block text-sm font-medium text-gray-700 mb-2">Fee Type *</label>
                    <select name="layout_fee_type" id="layout_fee_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_fee_type') border-red-500 @enderror"
                            onchange="toggleFeeInput()">
                        <option value="">Select Fee Type</option>
                        <option value="fixed" {{ old('layout_fee_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ old('layout_fee_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                    @error('layout_fee_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Layout Fee Amount -->
                <div>
                    <label for="layout_fee_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        <span id="amount-label">Fee Amount *</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="layout_fee_amount" id="layout_fee_amount" value="{{ old('layout_fee_amount') }}" required
                               step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_fee_amount') border-red-500 @enderror"
                               placeholder="Enter fee amount">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span id="amount-suffix" class="text-gray-500 sm:text-sm">₱</span>
                        </div>
                    </div>
                    @error('layout_fee_amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                              placeholder="Enter description for this layout fee setting">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-maroon focus:ring-maroon">
                        <span class="ml-2 text-sm text-gray-700">Set as Active (will deactivate other settings)</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.layout-fees.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Layout Fee
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFeeInput() {
    const type = document.getElementById('layout_fee_type').value;
    const amountLabel = document.getElementById('amount-label');
    const amountSuffix = document.getElementById('amount-suffix');
    const amountInput = document.getElementById('layout_fee_amount');
    
    if (type === 'percentage') {
        amountLabel.textContent = 'Percentage *';
        amountSuffix.textContent = '%';
        amountInput.placeholder = 'Enter percentage (e.g., 5 for 5%)';
        amountInput.max = '100';
    } else {
        amountLabel.textContent = 'Fee Amount *';
        amountSuffix.textContent = '₱';
        amountInput.placeholder = 'Enter fee amount';
        amountInput.removeAttribute('max');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFeeInput();
});
</script>
@endsection
