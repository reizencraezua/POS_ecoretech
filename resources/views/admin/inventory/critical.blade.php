@extends('layouts.admin')

@section('title', 'Critical Level Inventory')
@section('page-title', 'Critical Level Inventory')
@section('page-description', 'Items that are at or below critical level')

@section('header-actions')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Inventory</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Critical Level Alert -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Critical Level Alert
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>The following inventory items are at or below their critical level and need immediate attention.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Items Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Critical Level Items ({{ $inventories->count() }})</h3>
            <p class="text-sm text-gray-600">Items that need restocking</p>
        </div>
        
        @if($inventories->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inventory ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Critical Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inventories as $inventory)
                    <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.inventory.show', $inventory) }}'">
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
                                <span class="text-sm font-bold text-red-600">{{ $inventory->stocks }}</span>
                                @if($inventory->unit)
                                    <span class="text-sm text-gray-500 ml-1">{{ $inventory->unit }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $inventory->critical_level }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $inventory->supplier ? $inventory->supplier->company_name : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $inventory->last_updated ? $inventory->last_updated->format('M d, Y') : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button onclick="event.stopPropagation(); openAddStockModal({{ $inventory->id }}, '{{ $inventory->name }}')" 
                                        class="text-blue-600 hover:text-blue-900" title="Add Stock">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <a href="{{ route('admin.inventory.edit', $inventory) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit" onclick="event.stopPropagation();">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">
            <i class="fas fa-check-circle text-4xl mb-4 text-green-400"></i>
            <p class="text-lg font-medium text-green-600">All Good!</p>
            <p class="text-sm">No items are at critical level</p>
        </div>
        @endif
    </div>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Add Stock</h3>
                <p class="text-sm text-gray-600" id="addStockItemName"></p>
            </div>
            <form method="POST" id="addStockForm">
                @csrf
                <div class="px-6 py-4">
                    <div>
                        <label for="addQuantity" class="block text-sm font-medium text-gray-700">Quantity to Add *</label>
                        <input type="number" name="quantity" id="addQuantity" required min="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closeAddStockModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddStockModal(inventoryId, itemName) {
    document.getElementById('addStockItemName').textContent = itemName;
    document.getElementById('addStockForm').action = `/admin/inventory/${inventoryId}/add-stock`;
    document.getElementById('addStockModal').classList.remove('hidden');
}

function closeAddStockModal() {
    document.getElementById('addStockModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-gray-600')) {
        closeAddStockModal();
    }
});
</script>
@endsection
