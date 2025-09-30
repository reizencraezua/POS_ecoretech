@extends('layouts.admin')

@section('title', 'Job Orders')
@section('page-title', 'Job Order Management')
@section('page-description', 'Track and manage all job orders')

@section('header-actions')
<form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-end gap-3">
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
        <input type="hidden" name="archived" value="{{ (isset($showArchived) && $showArchived) ? 1 : 0 }}">
        <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-md">Filter</button>
        <a href="{{ route('admin.orders.index', ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0]) }}" class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
    </div>
</form>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.orders.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Job Order
            </a>
            <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
        </div>
        
        <!-- Search and Filters -->
        <div class="flex items-center space-x-4">
            <form method="GET" class="flex items-center space-x-2">
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Status</option>
                    <option value="On-Process" {{ request('status') == 'On-Process' ? 'selected' : '' }}>On-Process</option>
                    <option value="Designing" {{ request('status') == 'Designing' ? 'selected' : '' }}>Designing</option>
                    <option value="Production" {{ request('status') == 'Production' ? 'selected' : '' }}>Production</option>
                    <option value="For Releasing" {{ request('status') == 'For Releasing' ? 'selected' : '' }}>For Releasing</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <input type="hidden" name="archived" value="{{ (isset($showArchived) && $showArchived) ? 1 : 0 }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('status') || request('start_date') || request('end_date') || request('archived'))
                    <a href="{{ route('admin.orders.index', ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0]) }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            
            <!-- Quick Status Actions -->
            <!-- @if(!isset($showArchived) || !$showArchived)
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Quick Actions:</span>
                    <div class="flex items-center space-x-1">
                        <a href="{{ route('admin.orders.index', ['status' => 'On-Process']) }}" 
                           class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition-colors">
                            On-Process
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'Production']) }}" 
                           class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200 transition-colors">
                            Production
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'Completed']) }}" 
                           class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200 transition-colors">
                            Completed
                        </a>
                    </div>
                </div>
            @endif -->
        </div>
    </div>

    <!-- Order Statistics -->
    @if(!isset($showArchived) || !$showArchived)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->where('order_status', 'Completed')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Cancelled</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orders->where('order_status', 'Cancelled')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Active Filters Summary -->
    @if(request('search') || request('status') || request('start_date') || request('end_date'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <i class="fas fa-filter text-blue-600"></i>
                <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                <div class="flex flex-wrap gap-2">
                    @if(request('search'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('admin.orders.index', array_merge(request()->except('search'), ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0])) }}" 
                               class="ml-1 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Status: {{ request('status') }}
                            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0])) }}" 
                               class="ml-1 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('start_date'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            From: {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }}
                            <a href="{{ route('admin.orders.index', array_merge(request()->except('start_date'), ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0])) }}" 
                               class="ml-1 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('end_date'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            To: {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                            <a href="{{ route('admin.orders.index', array_merge(request()->except('end_date'), ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0])) }}" 
                               class="ml-1 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.orders.index', ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0]) }}" 
               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Clear All Filters
            </a>
        </div>
    </div>
    @endif

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-3 bg-blue-50 border-b border-blue-200">
            <p class="text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                Click on any order row to view details
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.orders.show', $order) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-maroon text-white rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</div>
                                            <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $order->employee->full_name ?? 'Unassigned' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->customer->display_name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->customer->contact_number1 }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @switch($order->order_status)
                                            @case('On-Process')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('Designing')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('Production')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('For Releasing')
                                                bg-orange-100 text-orange-800
                                                @break
                                            @case('Completed')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('Cancelled')
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        {{ $order->order_status }}
                                    </span>
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
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</div>
                                <div class="text-sm text-gray-500">{{ $order->details->count() }} item(s)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalPaid = $order->total_paid ?? 0;
                                    $remainingBalance = $order->total_amount - $totalPaid;
                                    $paymentPercentage = $order->total_amount > 0 ? ($totalPaid / $order->total_amount) * 100 : 0;
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- Edit Order -->
                                    <a href="{{ route('admin.orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit Order" onclick="event.stopPropagation();">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if(isset($showArchived) && $showArchived)
                                        <!-- Restore Order -->
                                        <form method="POST" action="{{ route('admin.orders.restore', $order->order_id) }}" onsubmit="return confirm('Restore this order?');" class="inline" onclick="event.stopPropagation();">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-800 transition-colors" title="Restore Order">
                                                <i class="fas fa-rotate-left"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Status Management -->
                                        <div class="relative" x-data="{ open: false }" onclick="event.stopPropagation();">
                                            <button @click="open = !open" class="text-maroon hover:text-maroon-dark transition-colors" title="Change Status">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                                <div class="py-1">
                                                    <div class="px-4 py-2 text-xs font-medium text-gray-500 bg-gray-50">Change Status To:</div>
                                                    @foreach(['On-Process', 'Designing', 'Production', 'For Releasing', 'Completed', 'Cancelled'] as $status)
                                                        @if($status !== $order->order_status)
                                                            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="inline w-full">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="order_status" value="{{ $status }}">
                                                                <button type="submit" class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                                                                    <i class="fas fa-arrow-right mr-2"></i>{{ $status }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Archive Order -->
                                        <form method="POST" action="{{ route('admin.orders.archive', $order) }}" onsubmit="return confirm('Archive this order? It will be moved to archives.');" class="inline" onclick="event.stopPropagation();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-orange-600 hover:text-orange-800 transition-colors" title="Archive Order">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                        </form>
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

<!-- Alpine.js for dropdown functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
