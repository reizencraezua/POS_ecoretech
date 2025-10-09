@extends('layouts.cashier')

@section('title', 'Schedule Delivery')
@section('page-title', 'Schedule New Delivery')
@section('page-description', 'Schedule a delivery for an order')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.deliveries.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Deliveries
    </a>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('cashier.deliveries.store') }}" class="space-y-6">
        @csrf
        
        <!-- Order Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
            <div>
                <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Select Order *</label>
                <select name="order_id" id="order_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-cashier-blue focus:border-cashier-blue @error('order_id') border-red-500 @enderror">
                    <option value="">Select an order</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->order_id }}" 
                                {{ (old('order_id', $selectedOrder ? $selectedOrder->order_id : '') == $order->order_id) ? 'selected' : '' }}>
                            {{ $order->order_id }} - {{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}
                            @if($order->customer->business_name) ({{ $order->customer->business_name }}) @endif
                            - ₱{{ number_format($order->total_amount, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('order_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Only orders without existing deliveries are shown</p>
            </div>
        </div>

        <!-- Delivery Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Details</h3>
            <div class="space-y-4">
                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Delivery Date *</label>
                    <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-cashier-blue focus:border-cashier-blue @error('delivery_date') border-red-500 @enderror">
                    @error('delivery_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned Employee</label>
                    <select name="employee_id" id="employee_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-cashier-blue focus:border-cashier-blue @error('employee_id') border-red-500 @enderror">
                        <option value="">Select Employee (Optional)</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->employee_id }}" {{ old('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                {{ $employee->full_name }} - {{ $employee->job->job_title ?? 'No Job Title' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                    <textarea name="delivery_address" id="delivery_address" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-cashier-blue focus:border-cashier-blue @error('delivery_address') border-red-500 @enderror"
                              placeholder="Enter complete delivery address">{{ old('delivery_address', $selectedOrder ? $selectedOrder->customer->address : '') }}</textarea>
                    @error('delivery_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="delivery_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact Number *</label>
                    <input type="text" name="delivery_contact" id="delivery_contact" value="{{ old('delivery_contact', $selectedOrder ? $selectedOrder->customer->contact_number1 : '') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-cashier-blue focus:border-cashier-blue @error('delivery_contact') border-red-500 @enderror"
                           placeholder="Enter contact number">
                    @error('delivery_contact')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="delivery_notes" class="block text-sm font-medium text-gray-700 mb-1">Delivery Notes</label>
                    <textarea name="delivery_notes" id="delivery_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-cashier-blue focus:border-cashier-blue @error('delivery_notes') border-red-500 @enderror"
                              placeholder="Enter any special delivery instructions">{{ old('delivery_notes') }}</textarea>
                    @error('delivery_notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('cashier.deliveries.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                Cancel
            </a>
            <button type="submit" class="bg-cashier-blue hover:bg-cashier-blue-dark text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Schedule Delivery
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSelect = document.getElementById('order_id');
    const addressField = document.getElementById('delivery_address');
    const contactField = document.getElementById('delivery_contact');
    
    // Store order data for auto-population
    const orderData = {
        @foreach($orders as $order)
        {{ $order->order_id }}: {
            address: @json($order->customer->address ?? ''),
            contact: @json($order->customer->contact_number1 ?? '')
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
            
            // Auto-populate contact if empty
            if (!contactField.value.trim()) {
                contactField.value = order.contact;
            }
        }
    });
    
    // Auto-populate on page load if order is pre-selected
    @if($selectedOrder)
        const selectedOrderId = {{ $selectedOrder->order_id }};
        if (orderData[selectedOrderId]) {
            const order = orderData[selectedOrderId];
            addressField.value = order.address;
            contactField.value = order.contact;
        }
    @endif
});
</script>
@endsection
