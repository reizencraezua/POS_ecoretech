@extends('layouts.admin')

@section('title', 'Discount Rule Details')
@section('page-title', $discountRule->rule_name)
@section('page-description', 'View detailed information about this discount rule')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.discount-rules.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">BACK TO DISCOUNT RULES</h1>
               
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                @if($discountRule->is_active) bg-green-100 text-green-800
                @else bg-red-100 text-red-800
                @endif">
                @if($discountRule->is_active)
                    <i class="fas fa-check-circle mr-1.5"></i>Active
                @else
                    <i class="fas fa-times-circle mr-1.5"></i>Inactive
                @endif
            </span>
            <a href="{{ route('admin.discount-rules.edit', $discountRule) }}" 
               class="bg-maroon hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form method="POST" action="{{ route('admin.discount-rules.archive', $discountRule) }}" 
                  class="inline" onsubmit="return confirm('Are you sure you want to archive this discount rule?')">
                @csrf
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-archive mr-2"></i>Archive
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-3 gap-6">
        <!-- Discount Value -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="mb-2 text-xs text-gray-600 uppercase tracking-wide">Discount Value</div>
                @if($discountRule->discount_type === 'percentage')
                    <div class="text-4xl font-bold text-gray-900 mb-1">{{ $discountRule->discount_percentage }}%</div>
                    <div class="text-xs text-gray-600">Percentage off</div>
                @else
                    <div class="text-4xl font-bold text-gray-900 mb-1">₱{{ number_format($discountRule->discount_amount, 2) }}</div>
                    <div class="text-xs text-gray-600">Fixed amount off</div>
                @endif
            </div>
        </div>

        <!-- Quantity Requirements -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Quantity Requirements</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="text-center p-3 bg-gray-50 rounded border border-gray-200">
                    <div class="text-2xl font-bold text-gray-900">{{ $discountRule->min_quantity }}</div>
                    <div class="text-xs text-gray-600 mt-1">Min Qty</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded border border-gray-200">
                    <div class="text-2xl font-bold text-gray-900">
                        @if($discountRule->max_quantity)
                            {{ $discountRule->max_quantity }}
                        @else
                            ∞
                        @endif
                    </div>
                    <div class="text-xs text-gray-600 mt-1">Max Qty</div>
                </div>
            </div>
        </div>

        <!-- Validity Period -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Validity Period</h3>
            @if($discountRule->valid_from || $discountRule->valid_until)
                <div class="space-y-2 text-sm">
                    @if($discountRule->valid_from)
                    <div class="flex justify-between">
                        <span class="text-gray-600">From</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($discountRule->valid_from)->format('M d, Y') }}</span>
                    </div>
                    @endif
                    @if($discountRule->valid_until)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Until</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($discountRule->valid_until)->format('M d, Y') }}</span>
                    </div>
                    @endif
                    @if($discountRule->valid_from && $discountRule->valid_until)
                    <div class="pt-2 border-t border-gray-200 text-center">
                        <div class="text-lg font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($discountRule->valid_from)->diffInDays(\Carbon\Carbon::parse($discountRule->valid_until)) + 1 }}
                        </div>
                        <div class="text-xs text-gray-600">days duration</div>
                    </div>
                    @endif
                </div>
            @else
                <div class="text-center py-4 text-sm text-gray-500">No time limit</div>
            @endif
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-2 gap-6 mt-6">
        <!-- Rule Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Rule Information</h2>
            <div class="space-y-2.5">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Rule Name</span>
                    <span class="text-sm font-medium text-gray-900">{{ $discountRule->rule_name }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Discount Type</span>
                    <span class="text-sm font-medium text-gray-900">{{ ucfirst($discountRule->discount_type) }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                        @if($discountRule->is_active) bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        @if($discountRule->is_active)
                            <i class="fas fa-check mr-1"></i>Active
                        @else
                            <i class="fas fa-times mr-1"></i>Inactive
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">Created</span>
                    <span class="text-sm text-gray-900">{{ $discountRule->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <form method="POST" action="{{ route('admin.discount-rules.toggle-status', $discountRule) }}">
                @csrf
                <button type="submit" class="w-full px-4 py-3 text-sm font-medium rounded-lg transition-colors
                    @if($discountRule->is_active)
                        bg-red-50 text-red-700 hover:bg-red-100 border border-red-200
                    @else
                        bg-green-50 text-green-700 hover:bg-green-100 border border-green-200
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
@endsection