@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')
@section('page-description', 'Update product information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.products.show', $product) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Product</h2>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Update product information below
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Product Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Product Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                        <input type="text" name="product_name" id="product_name" value="{{ old('product_name', $product->product_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('product_name') border-red-500 @enderror">
                        @error('product_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category Selection -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" id="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('category_id') border-red-500 @enderror">
                            <option value="">Select a category</option>
                            @foreach(\App\Models\Category::where('is_active', true)->orderBy('category_name')->get() as $category)
                                <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Choose the product category</p>
                    </div>
                    
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">Base Price (₱) *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="base_price" id="base_price" value="{{ old('base_price', $product->base_price) }}" step="0.01" min="0" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('base_price') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('base_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="layout_price" class="block text-sm font-medium text-gray-700 mb-1">Layout Price (₱)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="layout_price" id="layout_price" value="{{ old('layout_price', $product->layout_price) }}" step="0.01" min="0"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_price') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('layout_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="product_description" class="block text-sm font-medium text-gray-700 mb-1">Product Description</label>
                    <textarea name="product_description" id="product_description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('product_description') border-red-500 @enderror"
                              placeholder="Describe the product features, specifications, etc...">{{ old('product_description', $product->product_description) }}</textarea>
                    @error('product_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Layout Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Layout Information</h3>
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="requires_layout" id="requires_layout" value="1" {{ old('requires_layout', $product->requires_layout) ? 'checked' : '' }}
                               class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded">
                        <div>
                            <label for="requires_layout" class="text-sm font-medium text-gray-900 cursor-pointer">
                                Requires Layout Design
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Check if this product needs layout design services</p>
                        </div>
                    </div>
                    
                    <div>
                        <label for="layout_description" class="block text-sm font-medium text-gray-700 mb-1">Layout Description</label>
                        <textarea name="layout_description" id="layout_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_description') border-red-500 @enderror"
                                  placeholder="Describe the layout design requirements...">{{ old('layout_description', $product->layout_description) }}</textarea>
                        @error('layout_description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

           
            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.products.show', $product) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
