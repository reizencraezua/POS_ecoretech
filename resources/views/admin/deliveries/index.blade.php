@extends('layouts.admin')

@section('title', 'Deliveries')
@section('page-title', 'Delivery Management')
@section('page-description', 'Manage delivery schedules and track delivery status')


@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <a href="{{ route('admin.deliveries.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Schedule Delivery
                </a>
            @endif
        </div>
        
        <!-- Search and Archive Toggle -->
        <div class="flex items-center space-x-4">
            <!-- Archive Toggle -->
            <a href="{{ route('admin.deliveries.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            
            <!-- Search and Filters -->
            <form method="GET" class="flex items-center space-x-2" id="searchForm">
                <div class="relative">
                    <input type="text" 
                           id="instantSearchInput" 
                           data-instant-search="true"
                           data-container="deliveriesTableContainer"
                           data-loading="searchLoading"
                           value="{{ request('search') }}" 
                           placeholder="Search deliveries..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <div id="searchLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon" >
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.deliveries.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

        <!-- Deliveries Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($deliveries as $delivery)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.deliveries.show', $delivery) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Order #{{ $delivery->order_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-maroon text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        {{ substr($delivery->order->customer->customer_firstname, 0, 1) }}{{ substr($delivery->order->customer->customer_lastname, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $delivery->order->customer->customer_firstname }} {{ $delivery->order->customer->customer_lastname }}</div>
                                        <div class="text-sm text-gray-500">{{ $delivery->order->customer->contact_number1 ?? 'No contact' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $delivery->delivery_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($delivery->status == 'scheduled') bg-blue-100 text-blue-800
                                    @elseif($delivery->status == 'in_transit') bg-yellow-100 text-yellow-800
                                    @elseif($delivery->status == 'delivered') bg-green-100 text-green-800
                                    @elseif($delivery->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($delivery->employee)
                                    <div class="text-sm font-medium text-gray-900">{{ $delivery->employee->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $delivery->employee->job->job_title ?? 'No Job Title' }}</div>
                                @elseif($delivery->driver_name)
                                    <div class="text-sm font-medium text-gray-900">{{ $delivery->driver_name }}</div>
                                    <div class="text-sm text-gray-500">External Driver</div>
                                @else
                                    <span class="text-sm text-gray-500">Not Assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center space-x-3">
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$delivery" 
                                            :archiveRoute="'admin.deliveries.archive'" 
                                            :restoreRoute="'admin.deliveries.restore'" 
                                            :editRoute="'admin.deliveries.edit'"
                                            :showRestore="true" />
                                    @else
                                        <x-archive-actions 
                                            :item="$delivery" 
                                            :archiveRoute="'admin.deliveries.archive'" 
                                            :restoreRoute="'admin.deliveries.restore'" 
                                            :editRoute="'admin.deliveries.edit'"
                                            :showRestore="false" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-truck text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No deliveries found</p>
                                    <p class="text-sm">Schedule your first delivery to get started</p>
                                    @if(!$showArchived)
                                        <a href="{{ route('admin.deliveries.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center mt-4">
                                            <i class="fas fa-plus mr-2"></i>
                                            Schedule Delivery
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($deliveries->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $deliveries->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
<script src="{{ asset('js/instant-search.js') }}"></script>
@endsection
