@extends('layouts.admin')

@section('title', 'Services')
@section('page-title', 'Service Management')
@section('page-description', 'Manage your service offerings and pricing')

@section('content')
<div class="space-y-6" x-data="{ 
    serviceModal: false, 
    editModal: false, 
    editingService: null,
    requiresLayout: false,
    toggleLayoutPrice() {
        this.requiresLayout = !this.requiresLayout;
        const layoutPriceInput = document.getElementById('layout_price');
        if (layoutPriceInput) {
            layoutPriceInput.disabled = !this.requiresLayout;
            if (!this.requiresLayout) {
                layoutPriceInput.value = '0';
            }
        }
    },
    toggleEditLayoutPrice() {
        if (this.editingService) {
            this.editingService.requires_layout = !this.editingService.requires_layout;
            const editLayoutPriceInput = document.getElementById('edit_layout_price');
            if (editLayoutPriceInput) {
                editLayoutPriceInput.disabled = !this.editingService.requires_layout;
                if (!this.editingService.requires_layout) {
                    this.editingService.layout_price = 0;
                }
            }
        }
    }
}">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <button @click="serviceModal = true" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Service
                </button>
            @endif
        </div>
        
        <!-- Search and Archive Toggle -->
        <div class="flex items-center space-x-4">
            <x-archive-toggle :showArchived="$showArchived" :route="route('admin.services.index')" />
            
            <form method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search services..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.services.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $service)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
                <!-- Service Content -->
                <div class="p-6">
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-500">#{{ str_pad($service->service_id, 3, '0', STR_PAD_LEFT) }}</span>
                            @if($service->category)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $service->category->category_color }}20; color: {{ $service->category->category_color }};">
                                    {{ $service->category->category_name }}
                                </span>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $service->service_name }}</h3>
                        @if($service->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($service->description, 80) }}</p>
                        @endif
                    </div>

                    <!-- Price -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-xs text-gray-500">Base Fee</span>
                            <div class="text-2xl font-bold text-maroon">₱{{ number_format($service->base_fee, 2) }}</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <div class="flex items-center space-x-2">
                            @if($showArchived)
                                <x-archive-actions 
                                    :item="$service" 
                                    :archiveRoute="'admin.services.archive'" 
                                    :restoreRoute="'admin.services.restore'" 
                                    :showRestore="true" />
                            @else
                                <x-archive-actions 
                                    :item="$service" 
                                    :archiveRoute="'admin.services.archive'" 
                                    :restoreRoute="'admin.services.restore'" 
                                    :showRestore="false" />
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">
                            Updated {{ $service->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
                <div class="text-gray-400">
                    <i class="fas fa-cogs text-6xl mb-4"></i>
                    <p class="text-xl font-medium mb-2">No services found</p>
                    <p class="text-gray-500 mb-4">Add your first service offering</p>
                    <button @click="serviceModal = true" class="inline-flex items-center px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Service
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($services->hasPages())
        <div class="bg-white rounded-lg shadow p-4">
            {{ $services->links() }}
        </div>
    @endif

    <!-- Add Service Modal -->
    <div x-show="serviceModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="serviceModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Add New Service</h3>
                <button @click="serviceModal = false" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-6">
                @csrf

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Service Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">Service Name *</label>
                            <input type="text" name="service_name" id="service_name" value="{{ old('service_name') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('service_name') border-red-500 @enderror"
                                   placeholder="Enter service name">
                            @error('service_name')
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
                                    <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Choose the service category</p>
                        </div>
                        
                        <div>
                            <label for="base_fee" class="block text-sm font-medium text-gray-700 mb-1">Base Fee (₱) *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="base_fee" id="base_fee" value="{{ old('base_fee') }}" step="0.01" min="0" required
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('base_fee') border-red-500 @enderror"
                                       placeholder="0.00">
                            </div>
                            @error('base_fee')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Enter 0 for variable pricing services</p>
                        </div>

                        <div>
                            <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                                <input type="checkbox" name="requires_layout" id="requires_layout" value="1" {{ old('requires_layout') ? 'checked' : '' }}
                                       class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded" @change="toggleLayoutPrice()">
                                <div>
                                    <label for="requires_layout" class="text-sm font-medium text-gray-900 cursor-pointer">
                                        Requires Layout Design
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Check if this service needs layout design services</p>
                                </div>
                            </div>
                            @error('requires_layout')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="layout_price" class="block text-sm font-medium text-gray-700 mb-1">Layout Price (₱)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="layout_price" id="layout_price" value="{{ old('layout_price') }}" step="0.01" min="0"
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_price') border-red-500 @enderror"
                                       placeholder="0.00" :disabled="!requiresLayout">
                            </div>
                            @error('layout_price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Additional cost for layout design services</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="layout_description" class="block text-sm font-medium text-gray-700 mb-1">Layout Description</label>
                            <textarea name="layout_description" id="layout_description" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_description') border-red-500 @enderror"
                                      placeholder="Describe the layout design requirements...">{{ old('layout_description') }}</textarea>
                            @error('layout_description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                                      placeholder="Detailed description of the service, what's included, delivery time, etc...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Service Guidelines -->
                <div class="mb-8 bg-blue-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Service Guidelines</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-palette"></i>
                            <span>Design services: Layout fee ~₱400</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock"></i>
                            <span>Rush jobs: +50% of total order</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-tools"></i>
                            <span>Consider complexity and time required</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-handshake"></i>
                            <span>Include consultation if applicable</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                    <a href="{{ route('admin.services.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Service
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="editModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Edit Service</h3>
                <button @click="editModal = false" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form x-bind:action="editingService ? `/admin/services/${editingService.service_id}` : '#'" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="edit_service_name" class="block text-sm font-medium text-gray-700 mb-1">Service Name *</label>
                        <input type="text" name="service_name" id="edit_service_name" x-model="editingService ? editingService.service_name : ''" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>

                    <!-- Category Selection -->
                    <div>
                        <label for="edit_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" id="edit_category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">Select a category</option>
                            @foreach(\App\Models\Category::where('is_active', true)->orderBy('category_name')->get() as $category)
                                <option value="{{ $category->category_id }}" 
                                        x-bind:selected="editingService && editingService.category_id == {{ $category->category_id }}">
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Choose the service category</p>
                    </div>

                    <div>
                        <label for="edit_base_fee" class="block text-sm font-medium text-gray-700 mb-1">Base Fee (₱) *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="base_fee" id="edit_base_fee" x-model="editingService ? editingService.base_fee : ''" step="0.01" min="0" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="requires_layout" id="edit_requires_layout" value="1" 
                                   x-model="editingService ? editingService.requires_layout : false"
                                   class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded" @change="toggleEditLayoutPrice()">
                            <label for="edit_requires_layout" class="text-sm font-medium text-gray-700 cursor-pointer">
                                Requires Layout Design
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Check if this service needs layout design services</p>
                    </div>

                    <div>
                        <label for="edit_layout_price" class="block text-sm font-medium text-gray-700 mb-1">Layout Price (₱)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="layout_price" id="edit_layout_price" x-model="editingService ? editingService.layout_price : 0" step="0.01" min="0"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                   placeholder="0.00" :disabled="!editingService || !editingService.requires_layout">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="edit_layout_description" class="block text-sm font-medium text-gray-700 mb-1">Layout Description</label>
                        <textarea name="layout_description" id="edit_layout_description" x-model="editingService ? editingService.layout_description : ''" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                  placeholder="Describe the layout design requirements..."></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="edit_description" x-model="editingService ? editingService.description : ''" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                  placeholder="Detailed description of the service..."></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" @click="editModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Service</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this service? This action cannot be undone.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                        Delete
                    </button>
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editService(service) {
    this.editingService = service;
    this.editModal = true;
}

// Delete confirmation functionality
let serviceToDelete = null;

function confirmDelete(serviceId) {
    serviceToDelete = serviceId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    serviceToDelete = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (serviceToDelete) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/services/${serviceToDelete}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<style>
[x-cloak] { display: none !important; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection