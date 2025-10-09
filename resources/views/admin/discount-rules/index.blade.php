@extends('layouts.admin')

@section('title', 'Discount Rules')
@section('page-title', 'Discount Rules Management')
@section('page-description', 'Manage quantity-based discount rules for orders')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Discount Rules</h2>
                    <p class="text-sm text-gray-600 mt-1">Manage quantity-based discounts for orders</p>
                </div>
                <div class="flex items-center space-x-4">
                    @if(!$showArchived)
                        <a href="{{ route('admin.discount-rules.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Add Discount Rule
                        </a>
                    @endif
                    <a href="{{ route('admin.discount-rules.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
                       class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-box-archive mr-2"></i>
                        {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Rules Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rule Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($discountRules as $rule)
                    <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.discount-rules.show', $rule) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ $rule->rule_name }}</div>
                                <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                            </div>
                            @if($rule->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($rule->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $rule->min_quantity }}+
                            @if($rule->max_quantity)
                                - {{ $rule->max_quantity }}
                            @else
                                items
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($rule->discount_type === 'percentage')
                                {{ $rule->discount_percentage }}%
                            @else
                                ₱{{ number_format($rule->discount_amount, 2) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $rule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($rule->valid_from || $rule->valid_until)
                                <div class="text-xs">
                                    @if($rule->valid_from)
                                        From: {{ $rule->valid_from->format('M d, Y') }}
                                    @endif
                                    @if($rule->valid_until)
                                        <br>Until: {{ $rule->valid_until->format('M d, Y') }}
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">No limit</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if(!$showArchived)
                                    <a href="{{ route('admin.discount-rules.edit', $rule) }}" class="text-maroon hover:text-maroon-dark" title="Edit Rule" onclick="event.stopPropagation();">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                
                                @if($showArchived)
                                    <!-- Restore Button -->
                                    <form method="POST" action="{{ route('admin.discount-rules.restore', $rule) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900 transition-colors" 
                                                title="Restore Rule"
                                                onclick="event.stopPropagation(); return confirm('Are you sure you want to restore this discount rule?')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @else
                                    <!-- Archive Button -->
                                    <form method="POST" action="{{ route('admin.discount-rules.archive', $rule) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-orange-600 hover:text-orange-900 transition-colors" 
                                                title="Archive Rule"
                                                onclick="event.stopPropagation(); return confirm('Are you sure you want to archive this discount rule?')">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-percentage text-4xl mb-2"></i>
                            <p>No discount rules found.</p>
                            <a href="{{ route('admin.discount-rules.create') }}" class="text-maroon hover:text-maroon-dark mt-2 inline-block">
                                Create your first discount rule
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
