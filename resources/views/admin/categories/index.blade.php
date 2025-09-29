@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories Management')
@section('page-description', 'Manage product and service categories')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Categories</h2>
                <a href="{{ route('admin.categories.create') }}" 
                   class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Category
                </a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search categories..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                </div>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </form>
        </div>

        <!-- Categories Grid -->
        <div class="p-6">
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 rounded-full" style="background-color: {{ $category->category_color }}"></div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $category->category_name }}</h3>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.show', $category) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                          class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            @if($category->category_description)
                                <p class="text-gray-600 text-sm mb-3">{{ $category->category_description }}</p>
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
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first category.</p>
                    <a href="{{ route('admin.categories.create') }}" 
                       class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-dark transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Category
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
