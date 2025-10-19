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
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Supplier
                    </a>
                    <form method="POST" action="{{ route('admin.suppliers.archive', $supplier) }}" 
                          class="inline-block" onsubmit="return confirm('Are you sure you want to archive this supplier?')">
                        @csrf
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-color">
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

            <!-- Transaction History -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-history mr-2 text-maroon"></i>
                            Transaction History
                        </h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">{{ $transactions->count() }} transactions</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($transactions->count() > 0)
                        <div class="space-y-4">
                            @foreach($transactions as $transaction)
                                <div class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                            @if($transaction['type'] === 'inventory') bg-blue-100 text-blue-600
                                            @elseif($transaction['type'] === 'stock_usage') bg-orange-100 text-orange-600
                                            @elseif($transaction['type'] === 'order') bg-green-100 text-green-600
                                            @else bg-gray-100 text-gray-600 @endif">
                                            @if($transaction['type'] === 'inventory')
                                                <i class="fas fa-boxes"></i>
                                            @elseif($transaction['type'] === 'stock_usage')
                                                <i class="fas fa-arrow-right"></i>
                                            @elseif($transaction['type'] === 'order')
                                                <i class="fas fa-shopping-cart"></i>
                                            @else
                                                <i class="fas fa-circle"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $transaction['description'] }}</h4>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($transaction['type'] === 'inventory') bg-blue-100 text-blue-800
                                                    @elseif($transaction['type'] === 'stock_usage') bg-orange-100 text-orange-800
                                                    @elseif($transaction['type'] === 'order') bg-green-100 text-green-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    @if($transaction['type'] === 'inventory') Stock In
                                                    @elseif($transaction['type'] === 'stock_usage') Stock Used
                                                    @elseif($transaction['type'] === 'order') Order
                                                    @else {{ ucfirst($transaction['type']) }} @endif
                                                </span>
                                                @if($transaction['status'])
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if($transaction['status'] === 'active' || $transaction['status'] === 'Completed') bg-green-100 text-green-800
                                                        @elseif($transaction['status'] === 'inactive' || $transaction['status'] === 'Cancelled') bg-red-100 text-red-800
                                                        @elseif($transaction['status'] === 'used') bg-blue-100 text-blue-800
                                                        @else bg-yellow-100 text-yellow-800 @endif">
                                                        {{ $transaction['status'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">{{ $transaction['details'] }}</p>
                                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                <span><i class="fas fa-calendar mr-1"></i>{{ $transaction['date']->format('M d, Y') }}</span>
                                                @if($transaction['quantity'])
                                                    <span><i class="fas fa-hashtag mr-1"></i>{{ number_format($transaction['quantity']) }} {{ $transaction['unit'] }}</span>
                                                @endif
                                                @if($transaction['amount'])
                                                    <span><i class="fas fa-peso-sign mr-1"></i>₱{{ number_format($transaction['amount'], 2) }}</span>
                                                @endif
                                                <span><i class="fas fa-tag mr-1"></i>{{ $transaction['reference'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-history text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Transactions Yet</h3>
                            <p class="text-gray-500">This supplier doesn't have any transactions yet. Transactions will appear here when:</p>
                            <ul class="text-sm text-gray-500 mt-2 space-y-1">
                                <li>• Inventory items are added from this supplier</li>
                                <li>• Stock is used in orders</li>
                                <li>• Products from this supplier are included in orders</li>
                            </ul>
                        </div>
                    @endif
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

            <!-- Transaction Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-maroon"></i>
                        Transaction Summary
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    @php
                        $inventoryCount = $transactions->where('type', 'inventory')->count();
                        $stockUsageCount = $transactions->where('type', 'stock_usage')->count();
                        $orderCount = $transactions->where('type', 'order')->count();
                        $totalAmount = $transactions->where('type', 'order')->sum('amount');
                    @endphp
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $inventoryCount }}</div>
                            <div class="text-xs text-gray-500">Stock Ins</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $stockUsageCount }}</div>
                            <div class="text-xs text-gray-500">Stock Used</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $orderCount }}</div>
                            <div class="text-xs text-gray-500">Orders</div>
                        </div>
                        
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</div>
@endsection