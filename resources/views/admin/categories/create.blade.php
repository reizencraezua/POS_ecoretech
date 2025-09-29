@extends('layouts.admin')

@section('title', 'Create Category')
@section('page-title', 'Create New Category')
@section('page-description', 'Add a new category for products and services')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-8 py-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Create Category</h2>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.categories.store') }}" class="p-8">
            @csrf
            
            <div class="space-y-6">
                <!-- Category Name -->
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                    <input type="text" name="category_name" id="category_name" value="{{ old('category_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('category_name') border-red-500 @enderror"
                           placeholder="Enter category name">
                    @error('category_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Description -->
                <div>
                    <label for="category_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="category_description" id="category_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('category_description') border-red-500 @enderror"
                              placeholder="Enter category description">{{ old('category_description') }}</textarea>
                    @error('category_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Color -->
                <div>
                    <label for="category_color" class="block text-sm font-medium text-gray-700 mb-2">Category Color *</label>
                    <div class="flex items-center space-x-4">
                        <input type="color" name="category_color" id="category_color" value="{{ old('category_color', '#3B82F6') }}" required
                               class="w-12 h-10 border border-gray-300 rounded-md cursor-pointer @error('category_color') border-red-500 @enderror">
                        <input type="text" value="{{ old('category_color', '#3B82F6') }}" 
                               class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                               readonly>
                    </div>
                    @error('category_color')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Size Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Applicable Sizes</label>
                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-md p-4">
                        @php
                            $sizesByUnit = $sizes->groupBy('unit.unit_name');
                        @endphp
                        
                        @foreach($sizesByUnit as $unitName => $unitSizes)
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-ruler mr-2 text-maroon"></i>
                                    {{ $unitName }}
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-8 gap-2">
                                    @foreach($unitSizes as $size)
                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded border border-gray-100">
                                        <input type="checkbox" name="size_ids[]" value="{{ $size->size_id }}" 
                                               {{ in_array($size->size_id, old('size_ids', [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-maroon focus:ring-maroon">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-700">{{ $size->size_name }}</span>
                                            <span class="text-xs text-gray-500">{{ $size->size_value }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('size_ids')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Select the sizes that are applicable to products in this category</p>
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
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('category_color').addEventListener('change', function() {
    this.nextElementSibling.value = this.value;
});
</script>
@endsection
