@extends('layouts.admin')

@section('title', 'Layout Fee Details')
@section('page-title', 'Layout Fee Details')
@section('page-description', 'View layout fee setting information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.layout-fees.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $layoutFee->setting_name }}</h2>
                </div>
                <div class="flex space-x-2">
                    @if(!$layoutFee->is_active)
                        <form method="POST" action="{{ route('admin.layout-fees.activate', $layoutFee) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-2"></i>Activate
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.layout-fees.edit', $layoutFee) }}" 
                       class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Layout Fee Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Setting Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Setting Name</dt>
                            <dd class="text-sm text-gray-900">{{ $layoutFee->setting_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fee Type</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $layoutFee->layout_fee_type === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($layoutFee->layout_fee_type) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fee Amount</dt>
                            <dd class="text-sm text-gray-900">
                                @if($layoutFee->layout_fee_type === 'percentage')
                                    {{ $layoutFee->layout_fee_amount }}%
                                @else
                                    ₱{{ number_format($layoutFee->layout_fee_amount, 2) }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="text-sm text-gray-900">{{ $layoutFee->description ?: 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd>
                                @if($layoutFee->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timestamps</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $layoutFee->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $layoutFee->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Fee Calculation Examples -->
            <div class="bg-blue-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fee Calculation Examples</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Order Total: ₱1,000</h4>
                        <p class="text-sm text-gray-600">
                            Layout Fee: 
                            @if($layoutFee->layout_fee_type === 'percentage')
                                ₱{{ number_format(($layoutFee->layout_fee_amount / 100) * 1000, 2) }}
                            @else
                                ₱{{ number_format($layoutFee->layout_fee_amount, 2) }}
                            @endif
                        </p>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Order Total: ₱5,000</h4>
                        <p class="text-sm text-gray-600">
                            Layout Fee: 
                            @if($layoutFee->layout_fee_type === 'percentage')
                                ₱{{ number_format(($layoutFee->layout_fee_amount / 100) * 5000, 2) }}
                            @else
                                ₱{{ number_format($layoutFee->layout_fee_amount, 2) }}
                            @endif
                        </p>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Order Total: ₱10,000</h4>
                        <p class="text-sm text-gray-600">
                            Layout Fee: 
                            @if($layoutFee->layout_fee_type === 'percentage')
                                ₱{{ number_format(($layoutFee->layout_fee_amount / 100) * 10000, 2) }}
                            @else
                                ₱{{ number_format($layoutFee->layout_fee_amount, 2) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.layout-fees.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to List
                </a>
                <a href="{{ route('admin.layout-fees.edit', $layoutFee) }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Setting
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
