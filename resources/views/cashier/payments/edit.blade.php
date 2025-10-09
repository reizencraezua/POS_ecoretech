@extends('layouts.cashier')

@section('title', 'Edit Payment')
@section('page-title', 'Edit Payment')
@section('page-description', 'Update payment information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Payment Information</h3>
            <p class="text-sm text-gray-600 mt-1">Admin password required to edit payment data</p>
        </div>
        
        <form method="POST" action="{{ route('cashier.payments.update', $payment) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Admin Password Verification -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-lock text-yellow-600 mr-2"></i>
                    <h4 class="text-sm font-medium text-yellow-800">Admin Password Required</h4>
                </div>
                <p class="text-sm text-yellow-700 mt-1">Please enter the admin password to edit this payment.</p>
                
                <div class="mt-3">
                    <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-1">Admin Password *</label>
                    <input type="password" name="admin_password" id="admin_password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('admin_password') border-red-500 @enderror">
                    @error('admin_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Information -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-gray-900">Payment Information</h4>
                    
                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                        <select name="order_id" id="order_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_id') border-red-500 @enderror">
                            <option value="">Select Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->order_id }}" 
                                        {{ old('order_id', $payment->order_id) == $order->order_id ? 'selected' : '' }}>
                                    Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }} - {{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }} (₱{{ number_format($order->final_total_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_method') border-red-500 @enderror">
                            <option value="">Select Method</option>
                            <option value="Cash" {{ old('payment_method', $payment->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Check" {{ old('payment_method', $payment->payment_method) == 'Check' ? 'selected' : '' }}>Check</option>
                            <option value="GCash" {{ old('payment_method', $payment->payment_method) == 'GCash' ? 'selected' : '' }}>GCash</option>
                            <option value="PayMaya" {{ old('payment_method', $payment->payment_method) == 'PayMaya' ? 'selected' : '' }}>PayMaya</option>
                            <option value="Bank Transfer" {{ old('payment_method', $payment->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                        @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0.01" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('amount_paid') border-red-500 @enderror"
                                   value="{{ old('amount_paid', $payment->amount_paid) }}">
                        </div>
                        @error('amount_paid')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-gray-900">Additional Information</h4>
                    
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" id="payment_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_date') border-red-500 @enderror"
                               value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}">
                        @error('payment_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                        <input type="text" name="payment_reference" id="payment_reference"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_reference') border-red-500 @enderror"
                               value="{{ old('payment_reference', $payment->payment_reference) }}"
                               placeholder="Transaction ID, check number, etc.">
                        @error('payment_reference')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="payment_notes" id="payment_notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_notes') border-red-500 @enderror"
                                  placeholder="Additional notes about this payment...">{{ old('payment_notes', $payment->payment_notes) }}</textarea>
                        @error('payment_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('cashier.payments.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                    Update Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
