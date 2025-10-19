@extends('layouts.admin')

@section('title', 'Job Orders')
@section('page-title', 'Job Order Management')
@section('page-description', 'Track and manage all job orders')

@section('content')
<div class="space-y-6">

     <!-- Order Statistics -->
     @if(!isset($showArchived) || !$showArchived)
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
    @endif

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.orders.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Job Order
            </a>
        </div>
        
        <!-- Search and Filters -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            
            <form method="GET" class="flex items-center space-x-2" id="searchForm">
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon" onchange="document.getElementById('searchForm').submit();">
                    <option value="">All Status</option>
                    <option value="On-Process" {{ request('status') == 'On-Process' ? 'selected' : '' }}>On-Process</option>
                    <option value="Designing" {{ request('status') == 'Designing' ? 'selected' : '' }}>Designing</option>
                    <option value="Production" {{ request('status') == 'Production' ? 'selected' : '' }}>Production</option>
                    <option value="For Releasing" {{ request('status') == 'For Releasing' ? 'selected' : '' }}>For Releasing</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="Voided" {{ request('status') == 'Voided' ? 'selected' : '' }}>Voided</option>
                    <optgroup label="Due Dates">
                        <option value="due_today" {{ request('status') == 'due_today' ? 'selected' : '' }}>Due Today</option>
                        <option value="due_tomorrow" {{ request('status') == 'due_tomorrow' ? 'selected' : '' }}>Due Tomorrow</option>
                        <option value="due_3_days" {{ request('status') == 'due_3_days' ? 'selected' : '' }}>Due in 3 Days</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </optgroup>
                </select>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search orders..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon w-80">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <input type="hidden" name="archived" value="{{ (isset($showArchived) && $showArchived) ? 1 : 0 }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div id="ordersTableContainer" class="bg-white rounded-lg shadow overflow-hidden">
        @include('admin.orders.partials.orders-table')
    </div>
</div>

<!-- Alpine.js for dropdown functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
@endsection
