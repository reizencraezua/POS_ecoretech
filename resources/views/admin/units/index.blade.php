@extends('layouts.admin')

@section('title', 'Units')
@section('page-title', 'Units Management')
@section('page-description', 'Manage measurement units for products and services')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Archive Toggle -->
    <x-archive-toggle :showArchived="$showArchived" :route="route('admin.units.index')" />
    
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Units</h2>
                @if(!$showArchived)
                    <a href="{{ route('admin.units.create') }}" 
                       class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-dark transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Unit
                    </a>
                @endif
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search units..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                </div>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </form>
        </div>

        <!-- Units Table -->
        <div class="overflow-x-auto">
            @if($units->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Services</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($units as $unit)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $unit->unit_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $unit->unit_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $unit->description ?: 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $unit->products->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $unit->services->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($unit->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$unit" 
                                            :archiveRoute="'admin.units.archive'" 
                                            :restoreRoute="'admin.units.restore'" 
                                            :showRestore="true" />
                                    @else
                                        <x-archive-actions 
                                            :item="$unit" 
                                            :archiveRoute="'admin.units.archive'" 
                                            :restoreRoute="'admin.units.restore'" 
                                            :showRestore="false" />
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $units->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-balance-scale text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No units found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first unit.</p>
                    <a href="{{ route('admin.units.create') }}" 
                       class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-dark transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Unit
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
