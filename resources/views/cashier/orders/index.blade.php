@extends('layouts.cashier')

@section('title', 'Job Orders')
@section('page-title', 'Job Orders Management')
@section('page-description', 'Manage customer job orders and track progress')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.orders.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Create Job Order
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order ID or customer..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Status</option>
                    <option value="On-Process" {{ request('status') == 'On-Process' ? 'selected' : '' }}>On-Process</option>
                    <option value="Designing" {{ request('status') == 'Designing' ? 'selected' : '' }}>Designing</option>
                    <option value="Production" {{ request('status') == 'Production' ? 'selected' : '' }}>Production</option>
                    <option value="For Releasing" {{ request('status') == 'For Releasing' ? 'selected' : '' }}>For Releasing</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <optgroup label="Due Dates">
                        <option value="due_today" {{ request('status') == 'due_today' ? 'selected' : '' }}>Due Today</option>
                        <option value="due_tomorrow" {{ request('status') == 'due_tomorrow' ? 'selected' : '' }}>Due Tomorrow</option>
                        <option value="due_3_days" {{ request('status') == 'due_3_days' ? 'selected' : '' }}>Due in 3 Days</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </optgroup>
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('status') || request('start_date') || request('end_date'))
                    <a href="{{ route('cashier.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('cashier.orders.show', $order) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}</div>
                            @if($order->customer->business_name)
                                <div class="text-sm text-gray-500">{{ $order->customer->business_name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->employee->employee_firstname }} {{ $order->employee->employee_lastname }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->order_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->order_status === 'On-Process') bg-blue-100 text-blue-800
                                @elseif($order->order_status === 'Designing') bg-purple-100 text-purple-800
                                @elseif($order->order_status === 'Production') bg-yellow-100 text-yellow-800
                                @elseif($order->order_status === 'For Releasing') bg-orange-100 text-orange-800
                                @elseif($order->order_status === 'Completed') bg-green-100 text-green-800
                                @elseif($order->order_status === 'Cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->order_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                @if($order->creator)
                                    {{ $order->creator->name }}
                                @else
                                    Admin
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                @if($order->creator)
                                    {{ $order->created_at->diffForHumans() }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                            <div class="flex items-center space-x-2">
                                <!-- Edit Button -->
                                <a href="{{ route('cashier.orders.edit', $order) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 transition-colors" 
                                   title="Edit Order">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if($order->order_status !== 'Completed' && $order->order_status !== 'Cancelled')
                                    <!-- Status Update Dropdown -->
                                    <form method="POST" action="{{ route('cashier.orders.status', $order) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" 
                                                class="text-xs border-0 bg-transparent focus:ring-0">
                                            <option value="">Update Status</option>
                                            <option value="On-Process" {{ $order->order_status === 'On-Process' ? 'selected' : '' }}>On-Process</option>
                                            <option value="Designing" {{ $order->order_status === 'Designing' ? 'selected' : '' }}>Designing</option>
                                            <option value="Production" {{ $order->order_status === 'Production' ? 'selected' : '' }}>Production</option>
                                            <option value="For Releasing" {{ $order->order_status === 'For Releasing' ? 'selected' : '' }}>For Releasing</option>
                                            <option value="Completed" {{ $order->order_status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Cancelled" {{ $order->order_status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                    
                                    <!-- Void Button -->
                                    <button onclick="openVoidModal({{ $order->order_id }})" 
                                            class="text-red-600 hover:text-red-800 transition-colors" 
                                            title="Void Order">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No job orders found</p>
                                <p class="text-sm">Get started by creating a new job order.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Void Order Modal -->
<div id="voidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="mt-2 text-center">
                <h3 class="text-lg font-medium text-gray-900">Void Order</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        This action will void the order and move it to archives. This action cannot be undone.
                    </p>
                </div>
            </div>
            <form id="voidForm" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-1">Admin Password *</label>
                    <input type="password" name="admin_password" id="admin_password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Enter admin password">
                </div>
                <div class="mb-4">
                    <label for="void_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Voiding *</label>
                    <textarea name="void_reason" id="void_reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Enter reason for voiding this order"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeVoidModal()" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        Void Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openVoidModal(orderId) {
    document.getElementById('voidForm').action = `/cashier/orders/${orderId}/void`;
    document.getElementById('voidModal').classList.remove('hidden');
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
