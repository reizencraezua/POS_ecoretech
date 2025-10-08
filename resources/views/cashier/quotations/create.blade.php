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
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                    <select name="customer_id" id="customer_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_id') border-red-500 @enderror">
                        <option value="">Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}
                                @if($customer->business_name) - {{ $customer->business_name }} @endif
                            </option>
                        @endforeach
                    </select>
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
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Quotation Items</h3>
                <button type="button" id="add-item" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-1"></i>
                    Add Item
                </button>
            </div>
            
            <div id="items-container" class="space-y-4">
                <!-- Items will be added dynamically -->
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('cashier.quotations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                Cancel
            </a>
            <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Create Quotation
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 0;
    
    // Add item button
    document.getElementById('add-item').addEventListener('click', function() {
        addItemRow();
    });
    
    function addItemRow() {
        itemCount++;
        const container = document.getElementById('items-container');
        const itemRow = document.createElement('div');
        itemRow.className = 'border border-gray-200 rounded-lg p-4';
        itemRow.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="items[${itemCount}][type]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon" onchange="loadItems(this, ${itemCount})">
                        <option value="">Select Type</option>
                        <option value="product">Product</option>
                        <option value="service">Service</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                    <select name="items[${itemCount}][id]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon" required>
                        <option value="">Select Item</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="items[${itemCount}][quantity]" min="1" value="1" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select name="items[${itemCount}][unit]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->unit_code }}">{{ $unit->unit_name }} ({{ $unit->unit_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <input type="number" name="items[${itemCount}][price]" step="0.01" min="0" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Layout</label>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="items[${itemCount}][layout]" value="1" class="rounded border-gray-300 text-maroon focus:ring-maroon">
                        <input type="number" name="items[${itemCount}][layoutPrice]" step="0.01" min="0" placeholder="Layout Price"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                </div>
                <div>
                    <button type="button" onclick="removeItem(this)" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(itemRow);
    }
    
    // Load items based on type
    window.loadItems = function(selectElement, itemIndex) {
        const type = selectElement.value;
        const itemSelect = selectElement.parentElement.nextElementSibling.querySelector('select');
        
        // Clear existing options
        itemSelect.innerHTML = '<option value="">Select Item</option>';
        
        if (type === 'product') {
            @foreach($products as $product)
                itemSelect.innerHTML += '<option value="{{ $product->product_id }}" data-price="{{ $product->product_price }}">{{ $product->product_name }}</option>';
            @endforeach
        } else if (type === 'service') {
            @foreach($services as $service)
                itemSelect.innerHTML += '<option value="{{ $service->service_id }}" data-price="{{ $service->service_price }}">{{ $service->service_name }}</option>';
            @endforeach
        }
        
        // Auto-fill price when item is selected
        itemSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const priceInput = this.parentElement.nextElementSibling.nextElementSibling.querySelector('input[name*="[price]"]');
            if (selectedOption.dataset.price) {
                priceInput.value = selectedOption.dataset.price;
            }
        });
    };
    
    // Remove item
    window.removeItem = function(button) {
        button.closest('.border').remove();
    };
    
    // Add first item by default
    addItemRow();
});
</script>
@endsection
