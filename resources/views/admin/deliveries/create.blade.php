@extends('layouts.admin')

@section('title', 'Create Delivery')    
@section('page-title', 'Create Delivery')
@section('page-description', 'Create a new delivery schedule') 

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Back Button & Title -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.deliveries.index') }}" 
               class="p-2 text-gray-600 hover:text-maroon hover:bg-gray-100 rounded-lg transition-all">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Schedule Delivery</h1>
                <p class="text-sm text-gray-500">Create a new delivery schedule</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.deliveries.store') }}" method="POST">
            @csrf
            
            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 p-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Order Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                <select name="order_id" id="order_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_id') border-red-500 @enderror">
                                    <option value="">Select order</option>
                                    @foreach($orders as $order)
                                        <option value="{{ $order->order_id }}" 
                                                {{ (old('order_id', $selectedOrder ? $selectedOrder->order_id : '') == $order->order_id) ? 'selected' : '' }}>
                                            Order #{{ $order->order_id }} - {{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('order_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Delivery Date *</label>
                                <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('delivery_date') border-red-500 @enderror">
                                @error('delivery_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-1">Driver Name</label>
                                <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('driver_name') border-red-500 @enderror">
                                @error('driver_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="driver_contact" class="block text-sm font-medium text-gray-700 mb-1">Driver Contact</label>
                                <input type="tel" name="driver_contact" id="driver_contact" value="{{ old('driver_contact') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('driver_contact') border-red-500 @enderror">
                                @error('driver_contact')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle Column -->
                <div class="space-y-6">
                    <!-- Delivery Address -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Address</h3>
                        <div>
                            <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                            <textarea name="delivery_address" id="delivery_address" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('delivery_address') border-red-500 @enderror">{{ old('delivery_address', $selectedOrder ? $selectedOrder->customer->address : '') }}</textarea>
                            @error('delivery_address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Status & Fee -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status & Fee</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('status') border-red-500 @enderror">
                                    <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                    <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">Delivery Fee</label>
                                <input type="number" name="delivery_fee" id="delivery_fee" value="{{ old('delivery_fee', 0) }}" step="0.01" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('delivery_fee') border-red-500 @enderror">
                                @error('delivery_fee')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                <a href="{{ route('admin.deliveries.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                    Schedule Delivery
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSelect = document.getElementById('order_id');
    const addressField = document.getElementById('delivery_address');
    
    // Store order data for auto-population
    const orderData = {
        @foreach($orders as $order)
        {{ $order->order_id }}: {
            address: @json($order->customer->address ?? '')
        },
        @endforeach
    };
    
    // Handle order selection change
    orderSelect.addEventListener('change', function() {
        const selectedOrderId = this.value;
        
        if (selectedOrderId && orderData[selectedOrderId]) {
            const order = orderData[selectedOrderId];
            
            // Auto-populate address if empty
            if (!addressField.value.trim()) {
                addressField.value = order.address;
            }
        }
    });
    
    // Auto-populate on page load if order is pre-selected
    @if($selectedOrder)
        const selectedOrderId = {{ $selectedOrder->order_id }};
        if (orderData[selectedOrderId]) {
            const order = orderData[selectedOrderId];
            addressField.value = order.address;
        }
    @endif
});
</script>
@endsection
