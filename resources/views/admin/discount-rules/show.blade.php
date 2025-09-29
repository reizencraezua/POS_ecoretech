@extends('layouts.admin')

@section('title', 'Discount Rule Details')
@section('page-title', 'Discount Rule Details')
@section('page-description', 'View detailed information about this discount rule')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.discount-rules.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $discountRule->rule_name }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($discountRule->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @if($discountRule->is_active)
                                    <i class="fas fa-check mr-1"></i>Active
                                @else
                                    <i class="fas fa-times mr-1"></i>Inactive
                                @endif
                            </span>
                            <span><i class="fas fa-tag mr-1"></i>{{ ucfirst($discountRule->discount_type) }} Discount</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.discount-rules.edit', $discountRule) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Rule
                    </a>
                    <form method="POST" action="{{ route('admin.discount-rules.destroy', $discountRule) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this discount rule?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rule Information -->
        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Rule Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Rule Name</label>
                            <p class="text-gray-900 font-medium">{{ $discountRule->rule_name }}</p>
                        </div>
                        
                        @if($discountRule->description)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Description</label>
                            <p class="text-gray-900 font-medium">{{ $discountRule->description }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="text-gray-900 font-medium">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($discountRule->is_active) bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($discountRule->is_active)
                                        <i class="fas fa-check mr-1"></i>Active
                                    @else
                                        <i class="fas fa-times mr-1"></i>Inactive
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quantity Range -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quantity Range</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Minimum Quantity</label>
                            <p class="text-gray-900 font-medium">{{ $discountRule->min_quantity }} {{ $discountRule->min_quantity == 1 ? 'item' : 'items' }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Maximum Quantity</label>
                            <p class="text-gray-900 font-medium">
                                @if($discountRule->max_quantity)
                                    {{ $discountRule->max_quantity }} {{ $discountRule->max_quantity == 1 ? 'item' : 'items' }}
                                @else
                                    <span class="text-gray-500">No limit</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discount Information -->
        <div class="space-y-6">
            <!-- Discount Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Discount Details</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Discount Type</label>
                            <p class="text-gray-900 font-medium">{{ ucfirst($discountRule->discount_type) }}</p>
                        </div>
                        
                        @if($discountRule->discount_type === 'percentage')
                        <div>
                            <label class="text-sm font-medium text-gray-500">Discount Percentage</label>
                            <p class="text-gray-900 font-medium text-2xl text-green-600">{{ $discountRule->discount_percentage }}%</p>
                        </div>
                        @else
                        <div>
                            <label class="text-sm font-medium text-gray-500">Discount Amount</label>
                            <p class="text-gray-900 font-medium text-2xl text-green-600">₱{{ number_format($discountRule->discount_amount, 2) }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Validity Period -->
            @if($discountRule->valid_from || $discountRule->valid_until)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Validity Period</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if($discountRule->valid_from)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Valid From</label>
                            <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($discountRule->valid_from)->format('M d, Y') }}</p>
                        </div>
                        @endif
                        
                        @if($discountRule->valid_until)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Valid Until</label>
                            <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($discountRule->valid_until)->format('M d, Y') }}</p>
                        </div>
                        @endif
                        
                        @if($discountRule->valid_from && $discountRule->valid_until)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Duration</label>
                            <p class="text-gray-900 font-medium">
                                {{ \Carbon\Carbon::parse($discountRule->valid_from)->diffInDays(\Carbon\Carbon::parse($discountRule->valid_until)) + 1 }} days
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.discount-rules.toggle-status', $discountRule) }}" class="inline">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm font-medium rounded-md transition-colors
                                @if($discountRule->is_active)
                                    bg-red-50 text-red-700 hover:bg-red-100
                                @else
                                    bg-green-50 text-green-700 hover:bg-green-100
                                @endif">
                                @if($discountRule->is_active)
                                    <i class="fas fa-pause mr-2"></i>Deactivate Rule
                                @else
                                    <i class="fas fa-play mr-2"></i>Activate Rule
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
