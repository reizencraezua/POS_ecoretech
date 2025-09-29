@extends('layouts.admin')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service')
@section('page-description', 'Update service information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.services.show', $service) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Service</h2>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Update service information below
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Service Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Service Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">Service Name *</label>
                        <input type="text" name="service_name" id="service_name" value="{{ old('service_name', $service->service_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('service_name') border-red-500 @enderror">
                        @error('service_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="base_fee" class="block text-sm font-medium text-gray-700 mb-1">Base Fee (₱) *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="base_fee" id="base_fee" value="{{ old('base_fee', $service->base_fee) }}" step="0.01" min="0" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('base_fee') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('base_fee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="layout_price" class="block text-sm font-medium text-gray-700 mb-1">Layout Price (₱)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="layout_price" id="layout_price" value="{{ old('layout_price', $service->layout_price) }}" step="0.01" min="0"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_price') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('layout_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                              placeholder="Describe the service features, what's included, delivery time, etc...">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Layout Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Layout Information</h3>
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="requires_layout" id="requires_layout" value="1" {{ old('requires_layout', $service->requires_layout) ? 'checked' : '' }}
                               class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded">
                        <div>
                            <label for="requires_layout" class="text-sm font-medium text-gray-900 cursor-pointer">
                                Requires Layout Design
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Check if this service needs layout design services</p>
                        </div>
                    </div>
                    
                    <div>
                        <label for="layout_description" class="block text-sm font-medium text-gray-700 mb-1">Layout Description</label>
                        <textarea name="layout_description" id="layout_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_description') border-red-500 @enderror"
                                  placeholder="Describe the layout design requirements...">{{ old('layout_description', $service->layout_description) }}</textarea>
                        @error('layout_description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.services.show', $service) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
