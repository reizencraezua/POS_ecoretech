@extends('layouts.cashier')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')
@section('page-description', 'Update customer information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Customer Information</h3>
            <p class="text-sm text-gray-600 mt-1">Admin password required to edit customer data</p>
        </div>
        
        <form method="POST" action="{{ route('cashier.customers.update', $customer) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Admin Password Verification -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-lock text-yellow-600 mr-2"></i>
                    <h4 class="text-sm font-medium text-yellow-800">Admin Password Required</h4>
                </div>
                <p class="text-sm text-yellow-700 mt-1">Please enter the admin password to edit this customer's information.</p>
                
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
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-gray-900">Personal Information</h4>
                    
                    <div>
                        <label for="customer_firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="customer_firstname" id="customer_firstname" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_firstname') border-red-500 @enderror"
                               value="{{ old('customer_firstname', $customer->customer_firstname) }}">
                        @error('customer_firstname')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customer_lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="customer_lastname" id="customer_lastname" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_lastname') border-red-500 @enderror"
                               value="{{ old('customer_lastname', $customer->customer_lastname) }}">
                        @error('customer_lastname')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                        <input type="text" name="business_name" id="business_name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('business_name') border-red-500 @enderror"
                               value="{{ old('business_name', $customer->business_name) }}">
                        @error('business_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-gray-900">Contact Information</h4>
                    
                    <div>
                        <label for="contact_number1" class="block text-sm font-medium text-gray-700 mb-1">Primary Contact *</label>
                        <input type="text" name="contact_number1" id="contact_number1" required
                               pattern="[0-9]{11}" maxlength="11" minlength="11"
                               title="Contact number must be exactly 11 digits"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('contact_number1') border-red-500 @enderror"
                               value="{{ old('contact_number1', $customer->contact_number1) }}">
                        @error('contact_number1')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="contact_number2" class="block text-sm font-medium text-gray-700 mb-1">Secondary Contact</label>
                        <input type="text" name="contact_number2" id="contact_number2"
                               pattern="[0-9]{11}" maxlength="11" minlength="11"
                               title="Contact number must be exactly 11 digits"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('contact_number2') border-red-500 @enderror"
                               value="{{ old('contact_number2', $customer->contact_number2) }}">
                        @error('contact_number2')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('email') border-red-500 @enderror"
                               value="{{ old('email', $customer->email) }}">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Address Information -->
            <div class="mt-6 space-y-4">
                <h4 class="text-md font-medium text-gray-900">Address Information</h4>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                    <textarea name="address" id="address" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('address') border-red-500 @enderror">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" name="city" id="city" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('city') border-red-500 @enderror"
                               value="{{ old('city', $customer->city) }}">
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Province *</label>
                        <input type="text" name="province" id="province" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('province') border-red-500 @enderror"
                               value="{{ old('province', $customer->province) }}">
                        @error('province')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon @error('postal_code') border-red-500 @enderror"
                               value="{{ old('postal_code', $customer->postal_code) }}">
                        @error('postal_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('cashier.customers.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
