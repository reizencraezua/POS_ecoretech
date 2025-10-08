@extends('layouts.cashier')

@section('title', 'Create Job Order')
@section('page-title', 'Create Job Order')
@section('page-description', 'Create a new job order for customer')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Orders
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('cashier.orders.store') }}" class="space-y-6">
        @csrf
        
        <!-- Customer Information -->
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
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned Employee *</label>
                    <select name="employee_id" id="employee_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_id') border-red-500 @enderror">
                        <option value="">Select an employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->employee_id }}" {{ old('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                {{ $employee->employee_firstname }} {{ $employee->employee_lastname }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Order Date *</label>
                    <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_date') border-red-500 @enderror">
                    @error('order_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Delivery Date</label>
                    <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('delivery_date') border-red-500 @enderror">
                    @error('delivery_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
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
            <a href="{{ route('cashier.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                Cancel
            </a>
            <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Create Job Order
            </button>
        </div>
    </form>
</div>

<script>
    let itemCount = 0;

    // Add item function
    document.getElementById('add-item').addEventListener('click', function() {
        addItem();
    });

    function addItem() {
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
                    <select name="items[${itemCount}][unit]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon" required>
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
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                const priceInput = this.parentElement.nextElementSibling.nextElementSibling.nextElementSibling.querySelector('input[name*="[price]"]');
                priceInput.value = price;
            }
        });
    }

    // Remove item function
    window.removeItem = function(button) {
        button.closest('.border').remove();
    }

    // Add first item on page load
    document.addEventListener('DOMContentLoaded', function() {
        addItem();
    });
</script>
@endsection
