@extends('layouts.admin')

@section('title', 'Edit Inventory Item')
@section('page-title', 'Edit Inventory Item')
@section('page-description', 'Update inventory item information')

@section('header-actions')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.inventories.show', $inventory) }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Details</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Inventory Item</h3>
            <p class="text-sm text-gray-600">Update the information for {{ $inventory->name }}</p>
        </div>
        
        <form method="POST" action="{{ route('admin.inventories.update', $inventory) }}">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $inventory->name) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="inventory_id" class="block text-sm font-medium text-gray-700">Inventory ID</label>
                        <input type="text" id="inventory_id" value="{{ $inventory->inventory_id }}" disabled
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                        <p class="mt-1 text-xs text-gray-500">Inventory ID cannot be changed</p>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon @error('description') border-red-300 @enderror">{{ old('description', $inventory->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="critical_level" class="block text-sm font-medium text-gray-700">Critical Level *</label>
                        <input type="number" name="critical_level" id="critical_level" value="{{ old('critical_level', $inventory->critical_level) }}" required min="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon @error('critical_level') border-red-300 @enderror">
                        @error('critical_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', $inventory->unit) }}" placeholder="e.g., pieces, kg, meters"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon @error('unit') border-red-300 @enderror">
                        @error('unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                </div>

                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                    <select name="supplier_id" id="supplier_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon @error('supplier_id') border-red-300 @enderror">
                        <option value="">Select Supplier</option>
                        @foreach(\App\Models\Supplier::all() as $supplier)
                            <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id', $inventory->supplier_id) == $supplier->supplier_id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $inventory->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active (uncheck to deactivate this inventory item)
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <a href="{{ route('admin.inventories.show', $inventory) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark">
                    Update Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
