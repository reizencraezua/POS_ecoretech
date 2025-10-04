@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')
@section('page-description', 'Update category information')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-8 py-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Edit Category</h2>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="p-8"> 
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Category Name -->
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                    <input type="text" name="category_name" id="category_name" value="{{ old('category_name', $category->category_name) }}" required
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
                              placeholder="Enter category description">{{ old('category_description', $category->category_description) }}</textarea>
                    @error('category_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Color -->
                <div>
                    <label for="category_color" class="block text-sm font-medium text-gray-700 mb-2">Category Color *</label>
                    <div class="flex items-center space-x-4">
                        <input type="color" name="category_color" id="category_color" value="{{ old('category_color', $category->category_color) }}" required
                               class="w-12 h-10 border border-gray-300 rounded-md cursor-pointer @error('category_color') border-red-500 @enderror">
                        <input type="text" value="{{ old('category_color', $category->category_color) }}" 
                               class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                               readonly>
                    </div>
                    @error('category_color')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Size Group Selection -->
                <div>
                    <label for="size_group" class="block text-sm font-medium text-gray-700 mb-2">Size Group</label>
                    <select name="size_group" id="size_group" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('size_group') border-red-500 @enderror">
                        <option value="">Select a size group (optional)</option>
                        @foreach($sizeGroups as $group)
                            <option value="{{ $group }}" {{ old('size_group', $category->size_group) == $group ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $group)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('size_group')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Choose a size group to filter applicable sizes below</p>
                </div>

                <!-- Size Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Applicable Sizes</label>
                    <div id="size-selection-container" class="max-h-96 overflow-y-auto border border-gray-200 rounded-md p-4">
                        @php
                            $sizesByGroup = $sizes->groupBy('size_group');
                        @endphp
                        
                        @foreach($sizesByGroup as $groupName => $groupSizes)
                            <div class="size-group mb-6" data-group="{{ $groupName }}">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                        <i class="fas fa-tags mr-2 text-maroon"></i>
                                        {{ ucfirst(str_replace('_', ' ', $groupName)) }}
                                    </h4>
                                    <label class="flex items-center space-x-2 cursor-pointer text-sm text-gray-600 hover:text-gray-800">
                                        <input type="checkbox" 
                                               class="select-all-sizes rounded border-gray-300 text-maroon focus:ring-maroon" 
                                               data-group="{{ $groupName }}">
                                        <span>Select All</span>
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                    @foreach($groupSizes as $size)
                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded border border-gray-100">
                                        <input type="checkbox" name="size_ids[]" value="{{ $size->size_id }}" 
                                               {{ in_array($size->size_id, old('size_ids', $category->sizes->pluck('size_id')->toArray())) ? 'checked' : '' }}
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
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
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
                    <i class="fas fa-save mr-2"></i>Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('category_color');
    const sizeGroupSelect = document.getElementById('size_group');
    const sizeContainer = document.getElementById('size-selection-container');
    const sizeGroups = document.querySelectorAll('.size-group');

    // Color picker functionality
    colorInput.addEventListener('change', function() {
        this.nextElementSibling.value = this.value;
    });

    // Size group filtering
    sizeGroupSelect.addEventListener('change', function() {
        const selectedGroup = this.value;
        
        if (selectedGroup === '') {
            // Show all groups
            sizeGroups.forEach(group => {
                group.style.display = 'block';
            });
        } else {
            // Hide all groups first
            sizeGroups.forEach(group => {
                group.style.display = 'none';
            });
            
            // Show only the selected group
            const selectedGroupElement = document.querySelector(`[data-group="${selectedGroup}"]`);
            if (selectedGroupElement) {
                selectedGroupElement.style.display = 'block';
            }
        }
    });

    // Initialize with current or old value
    const currentSizeGroup = '{{ old("size_group", $category->size_group) }}';
    if (currentSizeGroup) {
        sizeGroupSelect.value = currentSizeGroup;
        sizeGroupSelect.dispatchEvent(new Event('change'));
    }

    // Select All functionality for size groups
    const selectAllCheckboxes = document.querySelectorAll('.select-all-sizes');
    selectAllCheckboxes.forEach(selectAllCheckbox => {
        selectAllCheckbox.addEventListener('change', function() {
            const groupName = this.getAttribute('data-group');
            const groupContainer = document.querySelector(`[data-group="${groupName}"]`);
            const sizeCheckboxes = groupContainer.querySelectorAll('input[name="size_ids[]"]');
            
            sizeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });

    // Update Select All checkbox when individual size checkboxes change
    const sizeCheckboxes = document.querySelectorAll('input[name="size_ids[]"]');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const groupContainer = this.closest('.size-group');
            const groupName = groupContainer.getAttribute('data-group');
            const selectAllCheckbox = document.querySelector(`[data-group="${groupName}"].select-all-sizes`);
            const groupSizeCheckboxes = groupContainer.querySelectorAll('input[name="size_ids[]"]');
            const checkedCount = groupContainer.querySelectorAll('input[name="size_ids[]"]:checked').length;
            
            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === groupSizeCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        });
    });

    // Initialize Select All checkboxes based on current selections
    function initializeSelectAllCheckboxes() {
        selectAllCheckboxes.forEach(selectAllCheckbox => {
            const groupName = selectAllCheckbox.getAttribute('data-group');
            const groupContainer = document.querySelector(`[data-group="${groupName}"]`);
            const groupSizeCheckboxes = groupContainer.querySelectorAll('input[name="size_ids[]"]');
            const checkedCount = groupContainer.querySelectorAll('input[name="size_ids[]"]:checked').length;
            
            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === groupSizeCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        });
    }

    // Initialize on page load
    initializeSelectAllCheckboxes();
});
</script>
@endsection
