@extends('layouts.cashier')

@section('title', 'Job Orders')
@section('page-title', 'Job Order Management')
@section('page-description', 'Track and manage all job orders')

@section('header-actions')
<form method="GET" action="{{ route('cashier.orders.index') }}" class="flex items-end gap-3">
    <div>
        <label for="start_date" class="block text-xs font-medium text-gray-600 mb-1">Start date</label>
        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
               class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
    </div>
    <div>
        <label for="end_date" class="block text-xs font-medium text-gray-600 mb-1">End date</label>
        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
               class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-md">Filter</button>
        <a href="{{ route('cashier.orders.index') }}" class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
    </div>
</form>
@endsection

@section('content')
<div class="space-y-6">

     <!-- Order Statistics -->
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-clock text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">On-Process</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->where('order_status', 'On-Process')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-cogs text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Production</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->where('order_status', 'Production')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-paint-brush text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Designing</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->where('order_status', 'Designing')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-upload text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">For Releasing</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->where('order_status', 'For Releasing')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->where('order_status', 'Completed')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('cashier.orders.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Job Order
            </a>
        </div>
        
        <!-- Search -->
        <div class="flex items-center space-x-4">
                <div class="relative">
                <input type="text" id="searchInput" placeholder="Search orders..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon w-80">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
        </div>
    </div>

   


    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-3 bg-blue-50 border-b border-blue-200">
            <p class="text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                Click on any order row to view details
            </p>
        </div>
        <div class="overflow-x-auto" id="ordersTable">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>   
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-blue-50 hover:shadow-sm {{ $order->order_status !== 'Voided' ? 'cursor-pointer group' : 'cursor-default' }}" 
                            @if($order->order_status !== 'Voided') onclick="window.location.href='{{ route('cashier.orders.show', $order) }}'" @endif>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-maroon text-white rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</div>
                                            <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600"></i>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $order->customer->display_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="space-y-1">
                                    <div>
                                        <span class="text-gray-500">Order:</span> {{ $order->order_date->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-500">Due:</span> 
                                        <span class="ml-1 {{ $order->deadline_date->isPast() && $order->order_status !== 'Completed' ? 'text-red-600 font-medium' : '' }}">
                                            {{ $order->deadline_date->format('M d, Y') }}
                                        </span>
                                        @if($order->deadline_date->isPast() && $order->order_status !== 'Completed')
                                            <i class="fas fa-exclamation-triangle text-red-500 ml-1 text-xs"></i>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($order->final_total_amount, 2) }}</div>
                                <div class="text-sm text-gray-500">{{ $order->details->count() }} item(s)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalPaid = $order->total_paid ?? 0;
                                    $remainingBalance = $order->final_total_amount - $totalPaid;
                                    $paymentPercentage = $order->final_total_amount > 0 ? ($totalPaid / $order->final_total_amount) * 100 : 0;
                                @endphp
                                <div class="space-y-1">
                                    <div class="text-sm">
                                        <span class="font-medium text-green-600">₱{{ number_format($totalPaid, 2) }}</span>
                                        <span class="text-gray-500">paid</span>
                                    </div>
                                    @if($remainingBalance > 0)
                                        <div class="text-sm">
                                            <span class="font-medium text-red-600">₱{{ number_format($remainingBalance, 2) }}</span>
                                            <span class="text-gray-500">due</span>
                                        </div>
                                    @endif
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $paymentPercentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @switch($order->order_status)
                                            @case('On-Process')
                                                text-blue-800
                                                @break
                                            @case('Designing')
                                                text-purple-800
                                                @break
                                            @case('Production')
                                                text-yellow-800
                                                @break
                                            @case('For Releasing')
                                                text-orange-800
                                                @break
                                            @case('Completed')
                                                text-green-800
                                                @break
                                            @case('Cancelled')
                                                text-red-800
                                                @break
                                            @case('Voided')
                                                text-gray-800
                                                @break
                                            @default
                                                text-gray-800
                                        @endswitch
                                    ">
                                        {{ $order->order_status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($order->creator)
                                        @if($order->creator->employee)
                                            EMP00{{ $order->creator->employee->employee_id }} : {{ $order->creator->employee->employee_firstname }}
                                        @else
                                            {{ $order->creator->name }}
                                        @endif
                                    @else
                                        Admin
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($order->creator)
                                        {{ $order->created_at->diffForHumans() }}
                                    @endif
                                </div>
                                @if($order->order_status === 'Voided')
                                    <div class="text-xs text-red-600 mt-1">
                                        <i class="fas fa-ban mr-1"></i>
                                        Voided by {{ $order->voidedBy ? ($order->voidedBy->employee ? 'EMP' . $order->voidedBy->employee->employee_id . ' : ' . $order->voidedBy->employee->employee_firstname : $order->voidedBy->name) : 'Admin' }}
                                        @if($order->voided_at)
                                            - {{ $order->voided_at->diffForHumans() }}
                                        @endif
                                    </div>
                                    @if($order->void_reason)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Reason: {{ Str::limit($order->void_reason, 50) }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($order->order_status !== 'Voided')
                                        @if($order->payments()->count() > 0)
                                            <!-- Edit Order Disabled -->
                                            <span class="text-gray-400 cursor-not-allowed" title="Cannot edit order with existing payments">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @else
                                            <!-- Edit Order -->
                                            <a href="{{ route('cashier.orders.edit', $order) }}" class="text-blue-600 hover:text-blue-800" title="Edit Order" onclick="event.stopPropagation();">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    @endif
                                    
                                    @if($order->order_status !== 'Completed' && $order->order_status !== 'Cancelled' && $order->order_status !== 'Voided')
                                        <!-- Void Order -->
                                        <button onclick="event.stopPropagation(); openVoidModal({{ $order->order_id }});" class="text-red-600 hover:text-red-800" title="Void Order">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-shopping-cart text-6xl mb-4"></i>
                                    <p class="text-xl font-medium mb-2">No orders found</p>
                                    <p class="text-gray-500">Create your first job order to get started</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Void Order Confirmation Modal -->
<div id="voidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-4">Void Order Confirmation</h3>
            <p class="text-sm text-gray-500 text-center mb-6">This action will permanently void the order. This cannot be undone.</p>
            
            <form id="voidForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <!-- Admin Password -->
                    <div>
                        <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">System Administrator Password *</label>
                        <input type="password" name="admin_password" id="admin_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter system administrator password">
                        <p class="text-xs text-gray-500 mt-1">Enter the system administrator's password to void this order</p>
                    </div>
                    
                    <!-- Void Reason -->
                    <div>
                        <label for="void_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Voiding *</label>
                        <textarea name="void_reason" id="void_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Enter reason for voiding this order"></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <button type="button" onclick="closeVoidModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        <i class="fas fa-ban mr-2"></i>Void Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alpine.js for dropdown functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Simple Search Script -->
<script>
function showPaymentError() {
    alert('Cannot edit due to payment exist');
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const ordersTable = document.getElementById('ordersTable');
    
    if (searchInput && ordersTable) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = ordersTable.querySelectorAll('tbody tr');
            
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

<script>
function openVoidModal(orderId) {
    document.getElementById('voidForm').action = '{{ route("cashier.orders.void", ":id") }}'.replace(':id', orderId);
    document.getElementById('voidModal').classList.remove('hidden');
    document.getElementById('admin_password').focus();
}

function closeVoidModal() {
    document.getElementById('voidModal').classList.add('hidden');
    document.getElementById('voidForm').reset();
}

// Close modal when clicking outside
document.getElementById('voidModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVoidModal();
    }
});
</script>
@endsection
