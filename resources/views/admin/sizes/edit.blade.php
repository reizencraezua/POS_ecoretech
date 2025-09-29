@extends('layouts.admin')

@section('title', 'Edit Size')
@section('page-title', 'Edit Size')
@section('page-description', 'Update size information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.sizes.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Edit Size</h2>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.sizes.update', $size) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Size Name -->
                <div>
                    <label for="size_name" class="block text-sm font-medium text-gray-700 mb-2">Size Name *</label>
                    <input type="text" name="size_name" id="size_name" value="{{ old('size_name', $size->size_name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('size_name') border-red-500 @enderror"
                           placeholder="e.g., Small, Medium, Large, A4, etc.">
                    @error('size_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Size Value -->
                <div>
                    <label for="size_value" class="block text-sm font-medium text-gray-700 mb-2">Size Value</label>
                    <input type="text" name="size_value" id="size_value" value="{{ old('size_value', $size->size_value) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('size_value') border-red-500 @enderror"
                           placeholder="e.g., 210 x 297 mm, Extra Small, etc.">
                    @error('size_value')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <select name="unit_id" id="unit_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('unit_id') border-red-500 @enderror">
                        <option value="">Select Unit (Optional)</option>
                        @foreach(\App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get() as $unit)
                            <option value="{{ $unit->unit_id }}" {{ old('unit_id', $size->unit_id) == $unit->unit_id ? 'selected' : '' }}>
                                {{ $unit->unit_name }} ({{ $unit->unit_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $size->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-maroon focus:ring-maroon">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.sizes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Size
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
