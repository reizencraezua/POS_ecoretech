@extends('layouts.admin')

@section('title', 'Quotations')
@section('page-title', 'Quotation Management')
@section('page-description', 'Manage customer quotations and proposals')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.quotations.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Quotation
            </a>
        </div>

        <!-- Search and Filters -->
        <div class="flex items-center space-x-4">
            <form method="GET" class="flex items-center space-x-2">
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quotations..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('admin.quotations.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Quotations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($quotations as $quotation)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow border border-gray-200">
            <!-- Card Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Quote #{{ str_pad($quotation->quotation_id, 5, '0', STR_PAD_LEFT) }}</h3>
                        <p class="text-sm text-gray-600">{{ $quotation->quotation_date->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($quotation->status === 'Pending')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-green-100 text-green-800
                                @endif
                            ">
                            {{ $quotation->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Content -->
            <div class="p-4">
                <div class="space-y-3">
                    <!-- Customer Info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-maroon text-white rounded-full flex items-center justify-center text-sm font-bold">
                            {{ substr($quotation->customer->customer_firstname, 0, 1) }}{{ substr($quotation->customer->customer_lastname, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $quotation->customer->display_name }}</p>
                            <p class="text-xs text-gray-500">{{ $quotation->customer->contact_number1 }}</p>
                        </div>
                    </div>

                    <!-- Items Count -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Items:</span>
                        <span class="font-medium">{{ $quotation->details->count() }} item(s)</span>
                    </div>

                    <!-- Total Amount -->
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="text-lg font-bold text-maroon">₱{{ number_format($quotation->total_amount, 2) }}</span>
                    </div>

                    <!-- Notes Preview -->
                    @if($quotation->notes)
                    <div class="text-xs text-gray-500">
                        <p class="truncate">{{ \Illuminate\Support\Str::limit($quotation->notes, 50) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card Actions -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.quotations.show', $quotation) }}" class="text-blue-600 hover:text-blue-800 text-sm transition-colors">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="{{ route('admin.quotations.edit', $quotation) }}" class="text-maroon hover:text-maroon-dark text-sm transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>

                @if($quotation->status === 'Pending')
                <form method="POST" action="{{ route('admin.quotations.status', $quotation) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Closed">
                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm transition-colors">
                        <i class="fas fa-check mr-1"></i>Close
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
            <div class="text-gray-400">
                <i class="fas fa-file-alt text-6xl mb-4"></i>
                <p class="text-xl font-medium mb-2">No quotations found</p>
                <p class="text-gray-500 mb-4">Create your first quotation to get started</p>
                <a href="{{ route('admin.quotations.create') }}" class="inline-flex items-center px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Quotation
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($quotations->hasPages())
    <div class="bg-white rounded-lg shadow p-4">
        {{ $quotations->links() }}
    </div>
    @endif
</div>
@endsection