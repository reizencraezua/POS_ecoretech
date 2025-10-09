@extends('layouts.admin')

@section('title', 'Layout Fees')
@section('page-title', 'Layout Fee Settings')
@section('page-description', 'Manage layout fee settings for orders')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Layout Fee Settings</h2>
                <a href="{{ route('admin.layout-fees.create') }}" 
                   class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Layout Fee
                </a>
            </div>
        </div>

        <!-- Layout Fees Table -->
        <div class="overflow-x-auto">
            @if($settings->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Setting Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($settings as $setting)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $setting->setting_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($setting->layout_fee_type === 'percentage')
                                            {{ $setting->layout_fee_amount }}%
                                        @else
                                            ₱{{ number_format($setting->layout_fee_amount, 2) }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $setting->layout_fee_type === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($setting->layout_fee_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $setting->description ?: 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($setting->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if(!$setting->is_active)
                                            <form method="POST" action="{{ route('admin.layout-fees.activate', $setting) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-play mr-1"></i>Activate
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.layout-fees.show', $setting) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('admin.layout-fees.edit', $setting) }}" 
                                           class="text-red-600 hover:text-red-800">Edit</a>
                                        <form method="POST" action="{{ route('admin.layout-fees.archive', $setting) }}" 
                                              class="inline" onsubmit="return confirm('Are you sure you want to archive this layout fee setting?')">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-900">Archive</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $settings->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calculator text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No layout fee settings found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first layout fee setting.</p>
                    <a href="{{ route('admin.layout-fees.create') }}" 
                       class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-dark transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Layout Fee
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
