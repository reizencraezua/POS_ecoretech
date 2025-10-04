@extends('layouts.admin')

@section('title', 'Inventory Details')
@section('page-title', 'Inventory Details')
@section('page-description', 'View detailed information about inventory item')

@section('header-actions')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Inventory</span>
    </a>
    <a href="{{ route('admin.inventory.edit', $inventory) }}" class="flex items-center gap-2 px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
        <i class="fas fa-edit"></i>
        <span>Edit Item</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Item Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Item Information</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Inventory ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inventory->inventory_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inventory->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inventory->description ?: 'No description' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $inventory->supplier ? $inventory->supplier->company_name : 'No supplier assigned' }}
                            </dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Stock</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="text-2xl font-bold {{ $inventory->isCriticalLevel() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $inventory->stocks }}
                                </span>
                                @if($inventory->unit)
                                    <span class="text-sm text-gray-500 ml-1">{{ $inventory->unit }}</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Stock In</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inventory->stock_in }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Critical Level</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inventory->critical_level }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Status -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Stock Status</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $inventory->isCriticalLevel() ? 'text-red-600' : 'text-green-600' }}">
                        {{ $inventory->stocks }}
                    </div>
                    <div class="text-sm text-gray-500">Current Stock</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $inventory->stock_in }}</div>
                    <div class="text-sm text-gray-500">Total Stock In</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600">{{ $inventory->stock_in - $inventory->stocks }}</div>
                    <div class="text-sm text-gray-500">Total Used</div>
                </div>
            </div>
            
            @if($inventory->isCriticalLevel())
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                    <span class="text-sm font-medium text-red-800">This item is at critical level!</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stock Usage History -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Stock Usage History</h3>
        </div>
        <div class="px-6 py-4">
            @if($inventory->stockUsages->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Used</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inventory->stockUsages->sortByDesc('used_at') as $usage)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $usage->used_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $usage->quantity_used }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $usage->purpose ?: 'Not specified' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $usage->used_by ?: 'Not specified' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
                <p class="text-lg font-medium">No usage history</p>
                <p class="text-sm">This item hasn't been used yet</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        </div>
        <div class="px-6 py-4">
            <div class="flex flex-wrap gap-4">
                <button onclick="openAddStockModal({{ $inventory->id }}, '{{ $inventory->name }}')" 
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Add Stock</span>
                </button>
                <button onclick="openUseStockModal({{ $inventory->id }}, '{{ $inventory->name }}', {{ $inventory->stocks }})" 
                        class="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-minus"></i>
                    <span>Use Stock</span>
                </button>
                <a href="{{ route('admin.inventory.edit', $inventory) }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-edit"></i>
                    <span>Edit Item</span>
                </a>
            </div>
        </div>
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

<!-- Use Stock Modal -->
<div id="useStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Use Stock</h3>
                <p class="text-sm text-gray-600" id="useStockItemName"></p>
                <p class="text-sm text-gray-500">Available: <span id="useStockAvailable"></span></p>
            </div>
            <form method="POST" id="useStockForm">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="useQuantity" class="block text-sm font-medium text-gray-700">Quantity to Use *</label>
                        <input type="number" name="quantity_used" id="useQuantity" required min="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                        <input type="text" name="purpose" id="purpose" placeholder="What is this used for?"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="used_by" class="block text-sm font-medium text-gray-700">Used By</label>
                        <input type="text" name="used_by" id="used_by" placeholder="Who used this?"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-maroon focus:border-maroon">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closeUseStockModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        Use Stock
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

function openUseStockModal(inventoryId, itemName, available) {
    document.getElementById('useStockItemName').textContent = itemName;
    document.getElementById('useStockAvailable').textContent = available;
    document.getElementById('useQuantity').max = available;
    document.getElementById('useStockForm').action = `/admin/inventory/${inventoryId}/use-stock`;
    document.getElementById('useStockModal').classList.remove('hidden');
}

function closeUseStockModal() {
    document.getElementById('useStockModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-gray-600')) {
        closeAddStockModal();
        closeUseStockModal();
    }
});
</script>
@endsection
