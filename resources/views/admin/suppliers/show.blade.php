@extends('layouts.admin')

@section('title', 'Supplier Details')
@section('page-title', $supplier->supplier_name)
@section('page-description', 'View detailed information about this supplier')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.suppliers.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 rounded-full bg-maroon flex items-center justify-center">
                            <span class="text-white font-bold text-xl">
                                {{ strtoupper(substr($supplier->supplier_name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900">{{ $supplier->supplier_name }}</h2>
                            <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                                <span><i class="fas fa-hashtag mr-1"></i>#{{ str_pad($supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span><i class="fas fa-calendar mr-1"></i>{{ $supplier->created_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-phone mr-1"></i>{{ $supplier->supplier_contact }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-maroon-dark transition-colors inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i>Edit Supplier
                    </a>
                    <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" 
                          class="inline-block" onsubmit="return confirm('Are you sure you want to archive this supplier?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition-colors inline-flex items-center">
                            <i class="fas fa-archive mr-2"></i>Archive
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-3 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-address-book mr-2 text-maroon"></i>
                        Contact Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Contact Details -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Contact Details</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-phone text-maroon"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Phone Number</p>
                                            <a href="tel:{{ $supplier->supplier_contact }}" class="text-lg font-medium text-gray-900 hover:text-maroon transition-colors">
                                                {{ $supplier->supplier_contact }}
                                            </a>
                                        </div>
                                    </div>
                                    
                                    @if($supplier->supplier_email)
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-envelope text-maroon"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Email Address</p>
                                            <a href="mailto:{{ $supplier->supplier_email }}" class="text-lg font-medium text-gray-900 hover:text-maroon transition-colors">
                                                {{ $supplier->supplier_email }}
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Address</h4>
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4 mt-1">
                                        <i class="fas fa-map-marker-alt text-maroon"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Business Address</p>
                                        <p class="text-gray-900 whitespace-pre-line leading-relaxed">{{ $supplier->supplier_address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt mr-2 text-maroon"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($supplier->supplier_email)
                        <a href="mailto:{{ $supplier->supplier_email }}" 
                           class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                            <div class="w-10 h-10 bg-maroon text-white rounded-lg flex items-center justify-center mr-4 group-hover:bg-maroon-dark transition-colors">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Send Email</p>
                                <p class="text-sm text-gray-500">Contact via email</p>
                            </div>
                        </a>
                        @endif
                        
                        <a href="tel:{{ $supplier->supplier_contact }}" 
                           class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                            <div class="w-10 h-10 bg-maroon text-white rounded-lg flex items-center justify-center mr-4 group-hover:bg-maroon-dark transition-colors">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Call Supplier</p>
                                <p class="text-sm text-gray-500">Make a phone call</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" 
                           class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                            <div class="w-10 h-10 bg-maroon text-white rounded-lg flex items-center justify-center mr-4 group-hover:bg-maroon-dark transition-colors">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Edit Information</p>
                                <p class="text-sm text-gray-500">Update details</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Record Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-maroon"></i>
                        Record Information
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Supplier ID</p>
                        <p class="text-sm font-mono text-gray-900 bg-gray-50 px-2 py-1 rounded">
                            #{{ str_pad($supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Created</p>
                        <p class="text-sm text-gray-900">{{ $supplier->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $supplier->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Last Updated</p>
                        <p class="text-sm text-gray-900">{{ $supplier->updated_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $supplier->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Placeholder -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-maroon"></i>
                        Statistics
                    </h3>
                </div>
                <div class="p-4">
                    <div class="text-center text-gray-500">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-chart-line text-gray-400"></i>
                        </div>
                        <p class="text-xs">Statistics will be available when products are linked to this supplier.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection