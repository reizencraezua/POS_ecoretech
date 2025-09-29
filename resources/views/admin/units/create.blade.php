@extends('layouts.admin')

@section('title', 'Create Unit')
@section('page-title', 'Create New Unit')
@section('page-description', 'Add a new measurement unit for products and services')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.units.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Create Unit</h2>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.units.store') }}" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Unit Name -->
                <div>
                    <label for="unit_name" class="block text-sm font-medium text-gray-700 mb-2">Unit Name *</label>
                    <input type="text" name="unit_name" id="unit_name" value="{{ old('unit_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('unit_name') border-red-500 @enderror"
                           placeholder="e.g., Pieces, Square Meters, Kilograms, etc.">
                    @error('unit_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit Code -->
                <div>
                    <label for="unit_code" class="block text-sm font-medium text-gray-700 mb-2">Unit Code</label>
                    <input type="text" name="unit_code" id="unit_code" value="{{ old('unit_code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('unit_code') border-red-500 @enderror"
                           placeholder="e.g., pcs, sqm, kg, etc.">
                    @error('unit_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                              placeholder="Enter unit description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-maroon focus:ring-maroon">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.units.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
