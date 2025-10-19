@extends('layouts.admin')

@section('title', 'Inventory Management')
@section('page-title', 'Inventory Management')
@section('page-description', $showArchived ? 'View archived inventory items' : 'Manage your inventory items and track stock levels')


@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Items</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $inventories->total() }}</p>
                </div>
                <div class="p-3 bg-blue-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-boxes text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Critical Level</p>
                    <p class="text-3xl font-bold text-red-600">{{ $criticalInventories }}</p>
                </div>
                <div class="p-3 bg-red-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Items</p>
                    <p class="text-3xl font-bold text-green-600">{{ $inventories->where('is_active', true)->count() }}</p>
                </div>
                <div class="p-3 bg-green-500 bg-opacity-10 rounded-full">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="flex items-center justify-between">
        <!-- Action Buttons (Left Side) -->
        <div class="flex items-center space-x-4">
            <button onclick="openCreateModal()" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Item
            </button>

            <a href="{{ route('admin.inventories.critical') }}" class="flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Critical Level ({{ $criticalInventories }})</span>
            </a>
        </div>

        <!-- Search and Filters (Right Side) -->
        <div class="flex items-center space-x-4">
            <!-- Archive Toggle -->
            <a href="{{ route('admin.inventories.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>

            <!-- Search -->
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search inventory items..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon w-80">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>


    <!-- Inventory Table -->
    <div id="inventoriesTableContainer" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Inventory Items</h3>
            <p class="text-sm text-gray-600">Manage your inventory items and stock levels</p>
        </div>
        
        <div class="overflow-x-auto">
            <table id="inventoriesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inventory ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stocks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inventories as $inventory)
                    <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.inventories.show', $inventory) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ $inventory->inventory_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ $inventory->name }}</div>
                                <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                            </div>
                            @if($inventory->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($inventory->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">{{ $inventory->stocks }}</span>
                                @if($inventory->unit)
                                    <span class="text-sm text-gray-500 ml-1">{{ $inventory->unit }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $inventory->stock_in }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $inventory->supplier ? $inventory->supplier->supplier_name : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'Normal' => 'text-green-800',
                                    'Critical' => 'text-red-800',
                                    'Inactive' => 'text-gray-800'
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$inventory->status] }}">
                                {{ $inventory->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $inventory->last_updated ? $inventory->last_updated->format('M d, Y') : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if($showArchived)
                                    <x-archive-actions 
                                        :item="$inventory" 
                                        :archiveRoute="'admin.inventories.archive'" 
                                        :restoreRoute="'admin.inventories.restore'" 
                                        :editRoute="'admin.inventories.edit'"
                                        :showRestore="true" />
                                @else
                                    <x-archive-actions 
                                        :item="$inventory" 
                                        :archiveRoute="'admin.inventories.archive'" 
                                        :restoreRoute="'admin.inventories.restore'" 
                                        :editRoute="'admin.inventories.edit'"
                                        :showRestore="false" />
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            @if(request('search'))
                                <i class="fas fa-search text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">No inventory items found</p>
                                <p class="text-sm">No items match your search for "{{ request('search') }}"</p>
                            @elseif($showArchived)
                                <i class="fas fa-archive text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">No archived inventory items</p>
                                <p class="text-sm">All inventory items are currently active</p>
                            @else
                                <i class="fas fa-boxes text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">No inventory items found</p>
                                <p class="text-sm">Start by adding your first inventory item</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inventories->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $inventories->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Inventory Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Add New Inventory Item</h3>
            </div>
            <form method="POST" action="{{ route('admin.inventories.store') }}">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" name="name" id="name" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stocks" class="block text-sm font-medium text-gray-700">Initial Stock *</label>
                            <input type="number" name="stocks" id="stocks" required min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label for="critical_level" class="block text-sm font-medium text-gray-700">Critical Level *</label>
                            <input type="number" name="critical_level" id="critical_level" required min="1" value="5"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                        </div>
                    </div>
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                        <select name="supplier_id" id="supplier_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                            <option value="">Select Supplier</option>
                            @foreach(\App\Models\Supplier::all() as $supplier)
                                <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                            <input type="text" name="unit" id="unit" placeholder="e.g., pieces, kg, meters"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark">
                        Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}


// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-gray-600')) {
        closeCreateModal();
    }
});

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const inventoriesTable = document.getElementById('inventoriesTable');
    
    if (searchInput && inventoriesTable) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = inventoriesTable.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection
