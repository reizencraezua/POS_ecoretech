@extends('layouts.admin')

@section('title', 'Supplier Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 rounded-full bg-maroon flex items-center justify-center">
                        <span class="text-white font-bold text-xl">
                            {{ strtoupper(substr($supplier->supplier_name, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $supplier->supplier_name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">Supplier Details</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" 
                          class="inline-block" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                    <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Supplier Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Supplier Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                            <p class="text-lg text-gray-900 font-medium">{{ $supplier->supplier_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <p class="text-gray-900 flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-2"></i>
                                <a href="tel:{{ $supplier->supplier_contact }}" class="hover:text-maroon transition-colors">
                                    {{ $supplier->supplier_contact }}
                                </a>
                            </p>
                        </div>

                        @if($supplier->supplier_email)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <p class="text-gray-900 flex items-center">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    <a href="mailto:{{ $supplier->supplier_email }}" class="hover:text-maroon transition-colors">
                                        {{ $supplier->supplier_email }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <p class="text-gray-900 flex items-start">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-2 mt-1"></i>
                                <span class="whitespace-pre-line">{{ $supplier->supplier_address }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($supplier->supplier_email)
                        <a href="mailto:{{ $supplier->supplier_email }}" 
                           class="flex items-center w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-envelope text-gray-400 mr-3"></i>
                            Send Email
                        </a>
                    @endif
                    <a href="tel:{{ $supplier->supplier_contact }}" 
                       class="flex items-center w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-phone text-gray-400 mr-3"></i>
                        Call Supplier
                    </a>
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" 
                       class="flex items-center w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-edit text-gray-400 mr-3"></i>
                        Edit Information
                    </a>
                </div>
            </div>

            <!-- Record Information -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Record Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                        <p class="text-gray-900 flex items-center">
                            <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>
                            {{ $supplier->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $supplier->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                        <p class="text-gray-900 flex items-center">
                            <i class="fas fa-calendar-edit text-gray-400 mr-2"></i>
                            {{ $supplier->updated_at->format('M d, Y \a\t g:i A') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $supplier->updated_at->diffForHumans() }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier ID</label>
                        <p class="text-gray-900 font-mono text-sm">
                            #{{ str_pad($supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Future Enhancement: Statistics -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-chart-line text-4xl mb-2"></i>
                        <p class="text-sm">Statistics will be available when orders and products are linked to this supplier.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection