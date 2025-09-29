@extends('layouts.admin')

@section('title', 'Edit Quotation')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Quotation #{{ $quotation->quotation_id }}</h1>
            <p class="text-gray-600 mt-2">Update quotation details and items</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.quotations.update', $quotation) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Customer Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Customer Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                            <select name="customer_id" id="customer_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_id') border-red-500 @enderror">
                                <option value="">Select customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->customer_id }}" {{ old('customer_id', $quotation->customer_id) == $customer->customer_id ? 'selected' : '' }}>
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
                            <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-1">Quotation Date *</label>
                            <input type="date" name="quotation_date" id="quotation_date" value="{{ old('quotation_date', $quotation->quotation_date->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('quotation_date') border-red-500 @enderror">
                            @error('quotation_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Quotation Items</h3>
                    <div id="items-container">
                        @foreach($quotation->details as $index => $detail)
                        <div class="item-row border border-gray-200 rounded-lg p-4 mb-4" data-index="{{ $index }}">
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                                    <select name="items[{{ $index }}][type]" class="item-type w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                        <option value="product" {{ $detail->product_id ? 'selected' : '' }}>Product</option>
                                        <option value="service" {{ $detail->service_id ? 'selected' : '' }}>Service</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Item</label>
                                    <select name="items[{{ $index }}][id]" class="item-select w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                        <option value="">Select item</option>
                                        @if($detail->product_id)
                                            @foreach($products as $product)
                                                <option value="{{ $product->product_id }}" {{ $detail->product_id == $product->product_id ? 'selected' : '' }}>
                                                    {{ $product->product_name }}
                                                </option>
                                            @endforeach
                                        @elseif($detail->service_id)
                                            @foreach($services as $service)
                                                <option value="{{ $service->service_id }}" {{ $detail->service_id == $service->service_id ? 'selected' : '' }}>
                                                    {{ $service->service_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ $detail->quantity }}" min="1" class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                    <input type="number" name="items[{{ $index }}][price]" value="{{ $detail->price }}" step="0.01" min="0" class="item-price w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                </div>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="button" class="remove-item text-red-600 hover:text-red-800 text-sm">Remove Item</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-item" class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-700">
                        Add Item
                    </button>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Additional Information</h3>
                    <div class="space-y-6">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">{{ old('notes', $quotation->notes) }}</textarea>
                        </div>
                        <div>
                            <label for="terms_and_conditions" class="block text-sm font-medium text-gray-700 mb-1">Terms and Conditions</label>
                            <textarea name="terms_and_conditions" id="terms_and_conditions" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">{{ old('terms_and_conditions', $quotation->terms_and_conditions) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.quotations.show', $quotation) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-maroon text-white rounded-md hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-maroon">
                        Update Quotation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const products = @json($products);
    const services = @json($services);
    let itemIndex = {{ count($quotation->details) }};

    // Add item functionality
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const itemRow = document.createElement('div');
        itemRow.className = 'item-row border border-gray-200 rounded-lg p-4 mb-4';
        itemRow.setAttribute('data-index', itemIndex);
        
        itemRow.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                    <select name="items[${itemIndex}][type]" class="item-type w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        <option value="product">Product</option>
                        <option value="service">Service</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Item</label>
                    <select name="items[${itemIndex}][id]" class="item-select w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        <option value="">Select item</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <input type="number" name="items[${itemIndex}][price]" value="0" step="0.01" min="0" class="item-price w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                </div>
            </div>
            <div class="mt-2 flex justify-end">
                <button type="button" class="remove-item text-red-600 hover:text-red-800 text-sm">Remove Item</button>
            </div>
        `;
        
        container.appendChild(itemRow);
        itemIndex++;
        
        // Add event listeners to new row
        addItemEventListeners(itemRow);
    });

    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });

    // Add event listeners to existing items
    document.querySelectorAll('.item-row').forEach(addItemEventListeners);

    function addItemEventListeners(itemRow) {
        const typeSelect = itemRow.querySelector('.item-type');
        const itemSelect = itemRow.querySelector('.item-select');
        const priceInput = itemRow.querySelector('.item-price');

        typeSelect.addEventListener('change', function() {
            updateItemOptions(itemSelect, this.value);
        });

        itemSelect.addEventListener('change', function() {
            updateItemPrice(this, priceInput);
        });
    }

    function updateItemOptions(select, type) {
        select.innerHTML = '<option value="">Select item</option>';
        
        const items = type === 'product' ? products : services;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = type === 'product' ? item.product_id : item.service_id;
            option.textContent = type === 'product' ? item.product_name : item.service_name;
            select.appendChild(option);
        });
    }

    function updateItemPrice(select, priceInput) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value) {
            const type = select.closest('.item-row').querySelector('.item-type').value;
            const items = type === 'product' ? products : services;
            const item = items.find(item => 
                (type === 'product' ? item.product_id : item.service_id) == selectedOption.value
            );
            if (item) {
                priceInput.value = item.base_price;
            }
        }
    }
});
</script>
@endsection
