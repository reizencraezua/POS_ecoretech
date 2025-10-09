@extends('layouts.admin')

@section('title', 'Suppliers')
@section('page-title', 'Supplier Management')
@section('page-description', 'Manage your suppliers and vendor information')

@section('content')
<div class="space-y-6" x-data="{ supplierModal: false, editModal: false, editingSupplier: null }">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <button @click="supplierModal = true" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Supplier
                </button>
            @endif
        </div>
        
        <!-- Search and Archive Toggle -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.suppliers.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            
            <form method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search suppliers..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.suppliers.show', $supplier) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-maroon text-white rounded-full flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($supplier->supplier_name, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ $supplier->supplier_name }}</div>
                                            <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                                        </div>
                                        <div class="text-sm text-gray-500">#{{ str_pad($supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $supplier->supplier_email ?? 'No email' }}</div>
                                <div class="text-sm text-gray-500">{{ $supplier->supplier_contact }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $supplier->supplier_address }}">
                                    {{ $supplier->supplier_address }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $supplier->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $supplier->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <div class="flex items-center space-x-2">
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$supplier" 
                                            :archiveRoute="'admin.suppliers.archive'" 
                                            :restoreRoute="'admin.suppliers.restore'" 
                                            :showRestore="true" />
                                    @else
                                        <button @click="editingSupplier = {{ $supplier->toJson() }}; editModal = true" 
                                                class="text-red-600 hover:text-red-800 transition-colors" 
                                                title="Edit Supplier"
                                                onclick="event.stopPropagation();">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <x-archive-actions 
                                            :item="$supplier" 
                                            :archiveRoute="'admin.suppliers.archive'" 
                                            :restoreRoute="'admin.suppliers.restore'" 
                                            :showRestore="false" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-truck text-6xl mb-4"></i>
                                    <p class="text-xl font-medium mb-2">No suppliers found</p>
                                    <p class="text-gray-500 mb-6">
                                        @if(request('search'))
                                            No suppliers match your search criteria
                                        @else
                                            Add your first supplier to get started
                                        @endif
                                    </p>
                                    @if(!request('search') && !$showArchived)
                                        <button @click="supplierModal = true" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-maroon-dark transition-colors inline-flex items-center">
                                            <i class="fas fa-plus mr-2"></i>Add Supplier
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($suppliers->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    <!-- Add Supplier Modal -->
    <div x-show="supplierModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="supplierModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Add New Supplier</h3>
                <button @click="supplierModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-6">
                @csrf

                <!-- Supplier Information -->
                <div class="space-y-6">
                    <!-- Supplier Name -->
                    <div>
                        <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Supplier Name *
                        </label>
                        <input type="text" 
                               name="supplier_name" 
                               id="supplier_name" 
                               value="{{ old('supplier_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_name') border-red-500 @enderror"
                               placeholder="Enter supplier or company name"
                               required>
                        @error('supplier_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <label for="supplier_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address
                            </label>
                            <input type="email" 
                                   name="supplier_email" 
                                   id="supplier_email" 
                                   value="{{ old('supplier_email') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_email') border-red-500 @enderror"
                                   placeholder="supplier@example.com">
                            @error('supplier_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact -->
                        <div>
                            <label for="supplier_contact" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Number *
                            </label>
                            <input type="text" 
                                   name="supplier_contact" 
                                   id="supplier_contact" 
                                   value="{{ old('supplier_contact') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_contact') border-red-500 @enderror"
                                   placeholder="09XX-XXX-XXXX"
                                   maxlength="11"
                                   pattern="[0-9]{11}"
                                   required>
                            @error('supplier_contact')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="supplier_address" class="block text-sm font-medium text-gray-700 mb-1">
                            Address *
                        </label>
                        <textarea name="supplier_address" 
                                  id="supplier_address" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_address') border-red-500 @enderror"
                                  placeholder="Enter complete business address"
                                  required>{{ old('supplier_address') }}</textarea>
                        @error('supplier_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                    <button type="button" @click="supplierModal = false" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="editModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Edit Supplier</h3>
                <button @click="editModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" :action="`/admin/suppliers/${editingSupplier?.supplier_id}`" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Supplier Information -->
                <div class="space-y-6">
                    <!-- Supplier Name -->
                    <div>
                        <label for="edit_supplier_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Supplier Name *
                        </label>
                        <input type="text" 
                               name="supplier_name" 
                               id="edit_supplier_name" 
                               x-model="editingSupplier?.supplier_name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_name') border-red-500 @enderror"
                               placeholder="Enter supplier or company name"
                               required>
                        @error('supplier_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <label for="edit_supplier_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address
                            </label>
                            <input type="email" 
                                   name="supplier_email" 
                                   id="edit_supplier_email" 
                                   x-model="editingSupplier?.supplier_email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_email') border-red-500 @enderror"
                                   placeholder="supplier@example.com">
                            @error('supplier_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact -->
                        <div>
                            <label for="edit_supplier_contact" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Number *
                            </label>
                            <input type="text" 
                                   name="supplier_contact" 
                                   id="edit_supplier_contact" 
                                   x-model="editingSupplier?.supplier_contact"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_contact') border-red-500 @enderror"
                                   placeholder="09XX-XXX-XXXX"
                                   maxlength="11"
                                   pattern="[0-9]{11}"
                                   required>
                            @error('supplier_contact')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="edit_supplier_address" class="block text-sm font-medium text-gray-700 mb-1">
                            Address *
                        </label>
                        <textarea name="supplier_address" 
                                  id="edit_supplier_address" 
                                  rows="3"
                                  x-model="editingSupplier?.supplier_address"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('supplier_address') border-red-500 @enderror"
                                  placeholder="Enter complete business address"
                                  required></textarea>
                        @error('supplier_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                    <button type="button" @click="editModal = false" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reopen modal if there are validation errors
            Alpine.store('supplierModal', true);
        });
    </script>
    @endif
</div>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection