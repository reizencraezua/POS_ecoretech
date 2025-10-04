@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories Management')
@section('page-description', 'Manage product and service categories')

@section('content')
<div class="space-y-6">
    
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <a href="{{ route('admin.categories.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Category
                </a>
            @endif
        </div>
        
        <!-- Search -->
        <div class="flex items-center space-x-4">
            <form method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">

            @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer" 
                             onclick="window.location.href='{{ route('admin.categories.show', $category) }}'">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 rounded-full" style="background-color: {{ $category->category_color }}"></div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $category->category_name }}</h3>
                                </div>
                                <div class="flex space-x-2" onclick="event.stopPropagation();">
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$category" 
                                            :archiveRoute="'admin.categories.archive'" 
                                            :restoreRoute="'admin.categories.restore'" 
                                            :showRestore="true" />
                                    @else
                                        <x-archive-actions 
                                            :item="$category" 
                                            :archiveRoute="'admin.categories.archive'" 
                                            :restoreRoute="'admin.categories.restore'" 
                                            :showRestore="false" />
                                    @endif
                                </div>
                            </div>
                            
                            @if($category->category_description)
                                <p class="text-gray-600 text-sm mb-3 truncate">{{ $category->category_description }}</p>
                            @endif
                            
                            <!-- Size Organization -->
                            @if($category->sizes->count() > 0)
                                <div class="mb-3">
                                    <div class="text-xs font-medium text-gray-500 mb-2">Available Sizes:</div>
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $sizesByUnit = $category->sizes->groupBy('unit.unit_name');
                                        @endphp
                                        @foreach($sizesByUnit as $unitName => $unitSizes)
                                            <div class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                                {{ $unitName }} ({{ $unitSizes->count() }})
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-box mr-1"></i>
                                    {{ $category->products->count() }} products
                                </span>
                                <span>
                                    <i class="fas fa-cogs mr-1"></i>
                                    {{ $category->services->count() }} services
                                </span>
                                <span>
                                    <i class="fas fa-ruler mr-1"></i>
                                    {{ $category->sizes->count() }} sizes
                                </span>
                                <span class="flex items-center">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($categories->hasPages())
                    <div class="mt-6">
                        {{ $categories->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400">
                        <i class="fas fa-tags text-6xl mb-4"></i>
                        <p class="text-xl font-medium mb-2">No categories found</p>
                        <p class="text-gray-500 mb-4">
                            @if(request('search'))
                                No categories match your search criteria.
                            @else
                                Get started by creating your first category.
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection