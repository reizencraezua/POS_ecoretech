@extends('layouts.admin')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')
@section('page-description', 'Update customer information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Customer</h2>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Update customer information below
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="customer_firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="customer_firstname" id="customer_firstname" value="{{ old('customer_firstname', $customer->customer_firstname) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_firstname') border-red-500 @enderror">
                        @error('customer_firstname')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customer_middlename" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        <input type="text" name="customer_middlename" id="customer_middlename" value="{{ old('customer_middlename', $customer->customer_middlename) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_middlename') border-red-500 @enderror">
                        @error('customer_middlename')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customer_lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="customer_lastname" id="customer_lastname" value="{{ old('customer_lastname', $customer->customer_lastname) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_lastname') border-red-500 @enderror">
                        @error('customer_lastname')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                    <textarea name="customer_address" id="customer_address" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_address') border-red-500 @enderror">{{ old('customer_address', $customer->customer_address) }}</textarea>
                    @error('customer_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-6">
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', $customer->customer_email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_email') border-red-500 @enderror">
                    @error('customer_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Business Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Business Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $customer->business_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('business_name') border-red-500 @enderror">
                        @error('business_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="tin" class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                        <input type="text" name="tin" id="tin" value="{{ old('tin', $customer->tin) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('tin') border-red-500 @enderror">
                        @error('tin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
                    <textarea name="payment_terms" id="payment_terms" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_terms') border-red-500 @enderror">{{ old('payment_terms', $customer->payment_terms) }}</textarea>
                    @error('payment_terms')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_person1" class="block text-sm font-medium text-gray-700 mb-1">Primary Contact Person *</label>
                        <input type="text" name="contact_person1" id="contact_person1" value="{{ old('contact_person1', $customer->contact_person1) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('contact_person1') border-red-500 @enderror">
                        @error('contact_person1')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="contact_number1" class="block text-sm font-medium text-gray-700 mb-1">Primary Contact Number *</label>
                        <input type="text" name="contact_number1" id="contact_number1" value="{{ old('contact_number1', $customer->contact_number1) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('contact_number1') border-red-500 @enderror"
                               placeholder="09XX-XXX-XXXX"
                               maxlength="11"
                               pattern="[0-9]{11}">
                        @error('contact_number1')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="contact_person2" class="block text-sm font-medium text-gray-700 mb-1">Secondary Contact Person</label>
                        <input type="text" name="contact_person2" id="contact_person2" value="{{ old('contact_person2', $customer->contact_person2) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('contact_person2') border-red-500 @enderror">
                        @error('contact_person2')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="contact_number2" class="block text-sm font-medium text-gray-700 mb-1">Secondary Contact Number</label>
                        <input type="text" name="contact_number2" id="contact_number2" value="{{ old('contact_number2', $customer->contact_number2) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('contact_number2') border-red-500 @enderror"
                               placeholder="09XX-XXX-XXXX"
                               maxlength="11"
                               pattern="[0-9]{11}">
                        @error('contact_number2')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
