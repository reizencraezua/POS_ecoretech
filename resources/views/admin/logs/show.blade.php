@extends('layouts.admin')

@section('title', 'Log Details')
@section('page-title', 'Transaction Log Details')
@section('page-description', 'Detailed view of transaction changes')

@section('header-actions')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.logs.index') }}" 
       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Logs
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Log Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @switch($log->transaction_type)
                        @case('quotation')
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-lines text-blue-600 text-xl"></i>
                            </div>
                            @break
                        @case('order')
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-green-600 text-xl"></i>
                            </div>
                            @break
                        @case('payment')
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-credit-card text-yellow-600 text-xl"></i>
                            </div>
                            @break
                        @case('delivery')
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-truck text-purple-600 text-xl"></i>
                            </div>
                            @break
                        @default
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file text-gray-600 text-xl"></i>
                            </div>
                    @endswitch
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $log->transaction_name }}</h2>
                    <p class="text-sm text-gray-600">{{ ucfirst($log->transaction_type) }} • {{ $log->formatted_transaction_id }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @switch($log->action)
                        @case('created')
                            text-green-800
                            @break
                        @case('updated')
                            text-blue-800
                            @break
                        @case('deleted')
                            text-red-800
                            @break
                        @case('status_changed')
                            text-yellow-800
                            @break
                        @default
                            text-gray-800
                    @endswitch
                ">
                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Log Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Transaction Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($log->transaction_type) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Transaction ID</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900">{{ $log->formatted_transaction_id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Action Performed</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->formatted_date_time }}</dd>
                </div>
               
            </dl>
        </div>

        <!-- User Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Edited By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->edited_by }}</dd>
                </div>
                @if($log->user)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($log->user->role) }}</dd>
                </div>
                @endif
             
            </dl>
        </div>
    </div>

    <!-- Changes Details -->
    @if($log->changes && count($log->changes) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Changes Made</h3>
        <div class="space-y-6">
            @foreach($log->changes as $field => $change)
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-4 text-lg">{{ ucfirst(str_replace('_', ' ', $field)) }}</h4>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if(isset($change['old']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-3 flex items-center">
                            <i class="fas fa-arrow-left text-red-500 mr-2"></i>
                            Previous Details
                        </dt>
                        <dd class="text-sm text-gray-900 bg-red-50 p-4 rounded-lg border-l-4 border-red-200">
                            @if(is_array($change['old']))
                                @if($field === 'converted_to')
                                    <!-- Display quotation conversion data -->
                                    <div class="space-y-3">
                                        @foreach($change['old'] as $key => $value)
                                        <div class="bg-white p-3 rounded border">
                                            <div class="flex justify-between items-start">
                                                <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-right max-w-xs">
                                                    @if(is_numeric($value) && strpos($value, '.') !== false)
                                                        ₱{{ number_format($value, 2) }}
                                                    @elseif(in_array($key, ['order_date', 'deadline_date']))
                                                        {{ \Carbon\Carbon::parse($value)->format('M d, Y g:i A') }}
                                                    @elseif($key === 'order_id')
                                                        <span class="font-mono text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $value }}</span>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @elseif($field === 'details' && is_array($change['old']))
                                    <!-- Display order details in a user-friendly format -->
                                    <div class="space-y-3">
                                        @foreach($change['old'] as $index => $detail)
                                        <div class="bg-white p-3 rounded border">
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <div><span class="font-medium">Item ID:</span> {{ $detail['order_detail_id'] ?? 'N/A' }}</div>
                                                <div><span class="font-medium">Quantity:</span> {{ $detail['quantity'] ?? 'N/A' }}</div>
                                                <div><span class="font-medium">Size:</span> {{ $detail['size'] ?? 'N/A' }}</div>
                                                <div><span class="font-medium">Price:</span> ₱{{ number_format($detail['price'] ?? 0, 2) }}</div>
                                                <div><span class="font-medium">Subtotal:</span> ₱{{ number_format($detail['subtotal'] ?? 0, 2) }}</div>
                                                <div><span class="font-medium">VAT:</span> ₱{{ number_format($detail['vat'] ?? 0, 2) }}</div>
                                                <div><span class="font-medium">Layout:</span> {{ $detail['layout'] ? 'Yes' : 'No' }}</div>
                                                @if($detail['layout'] && isset($detail['layout_price']))
                                                <div><span class="font-medium">Layout Price:</span> ₱{{ number_format($detail['layout_price'], 2) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @elseif($field === 'created' || $field === 'updated' || $field === 'deleted')
                                    <!-- Display transaction creation/update/deletion data -->
                                    <div class="space-y-3">
                                        @foreach($change['old'] as $key => $value)
                                        <div class="bg-white p-3 rounded border">
                                            <div class="flex justify-between items-start">
                                                <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-right max-w-xs">
                                                    @if(is_numeric($value) && strpos($value, '.') !== false)
                                                        ₱{{ number_format($value, 2) }}
                                                    @elseif(in_array($key, ['created_at', 'updated_at', 'payment_date', 'order_date', 'deadline_date', 'delivery_date', 'quotation_date', 'valid_until']))
                                                        {{ \Carbon\Carbon::parse($value)->format('M d, Y g:i A') }}
                                                    @elseif($key === 'status' || $key === 'order_status' || $key === 'payment_term')
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ $value }}</span>
                                                    @elseif($key === 'layout')
                                                        <span class="px-2 py-1 {{ $value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-xs">
                                                            {{ $value ? 'Yes' : 'No' }}
                                                        </span>
                                                    @elseif($key === 'receipt_number' || $key === 'reference_number')
                                                        <span class="font-mono text-sm">{{ $value }}</span>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Display other arrays in a more readable format -->
                                    <div class="space-y-2">
                                        @foreach($change['old'] as $key => $value)
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            <span>
                                                @if(is_numeric($value) && strpos($value, '.') !== false)
                                                    ₱{{ number_format($value, 2) }}
                                                @elseif(in_array($key, ['created_at', 'updated_at', 'payment_date', 'order_date', 'deadline_date', 'delivery_date']))
                                                    {{ \Carbon\Carbon::parse($value)->format('M d, Y g:i A') }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                {{ $change['old'] }}
                            @endif
                        </dd>
                    </div>
                    @endif
                    @if(isset($change['new']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-3 flex items-center">
                            <i class="fas fa-arrow-right text-green-500 mr-2"></i>
                            New Details
                        </dt>
                        <dd class="text-sm text-gray-900 bg-green-50 p-4 rounded-lg border-l-4 border-green-200">
                            @if(is_array($change['new']))
                                @if($field === 'converted_to')
                                    <!-- Display quotation conversion data -->
                                    <div class="space-y-3">
                                        @foreach($change['new'] as $key => $value)
                                        <div class="bg-white p-3 rounded border">
                                            <div class="flex justify-between items-start">
                                                <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-right max-w-xs">
                                                    @if(is_numeric($value) && strpos($value, '.') !== false)
                                                        ₱{{ number_format($value, 2) }}
                                                    @elseif(in_array($key, ['order_date', 'deadline_date']))
                                                        {{ \Carbon\Carbon::parse($value)->format('M d, Y g:i A') }}
                                                    @elseif($key === 'order_id')
                                                        <span class="font-mono text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $value }}</span>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @elseif($field === 'details' && is_array($change['new']))
                                    <!-- Display order details in a user-friendly format -->
                                    <div class="space-y-3">
                                        @foreach($change['new'] as $index => $detail)
                                        <div class="bg-white p-3 rounded border">
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <div><span class="font-medium">Item ID:</span> {{ $detail['order_detail_id'] ?? 'N/A' }}</div>
                                                <div><span class="font-medium">Quantity:</span> {{ $detail['quantity'] ?? 'N/A' }}</div>
                                                <div><span class="font-medium">Size:</span> {{ $detail['size'] ?? 'N/A' }}</div>
                                                <div><span class="font-medium">Price:</span> ₱{{ number_format($detail['price'] ?? 0, 2) }}</div>
                                                <div><span class="font-medium">Subtotal:</span> ₱{{ number_format($detail['subtotal'] ?? 0, 2) }}</div>
                                                <div><span class="font-medium">VAT:</span> ₱{{ number_format($detail['vat'] ?? 0, 2) }}</div>
                                                <div><span class="font-medium">Layout:</span> {{ $detail['layout'] ? 'Yes' : 'No' }}</div>
                                                @if($detail['layout'] && isset($detail['layout_price']))
                                                <div><span class="font-medium">Layout Price:</span> ₱{{ number_format($detail['layout_price'], 2) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @elseif($field === 'created' || $field === 'updated' || $field === 'deleted')
                                    <!-- Display transaction creation/update/deletion data -->
                                    <div class="space-y-3">
                                        @foreach($change['new'] as $key => $value)
                                        <div class="bg-white p-3 rounded border">
                                            <div class="flex justify-between items-start">
                                                <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-right max-w-xs">
                                                    @if(is_numeric($value) && strpos($value, '.') !== false)
                                                        ₱{{ number_format($value, 2) }}
                                                    @elseif(in_array($key, ['created_at', 'updated_at', 'payment_date', 'order_date', 'deadline_date', 'delivery_date', 'quotation_date', 'valid_until']))
                                                        {{ \Carbon\Carbon::parse($value)->format('M d, Y g:i A') }}
                                                    @elseif($key === 'status' || $key === 'order_status' || $key === 'payment_term')
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ $value }}</span>
                                                    @elseif($key === 'layout')
                                                        <span class="px-2 py-1 {{ $value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-xs">
                                                            {{ $value ? 'Yes' : 'No' }}
                                                        </span>
                                                    @elseif($key === 'receipt_number' || $key === 'reference_number')
                                                        <span class="font-mono text-sm">{{ $value }}</span>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Display other arrays in a more readable format -->
                                    <div class="space-y-2">
                                        @foreach($change['new'] as $key => $value)
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            <span>
                                                @if(is_numeric($value) && strpos($value, '.') !== false)
                                                    ₱{{ number_format($value, 2) }}
                                                @elseif(in_array($key, ['created_at', 'updated_at', 'payment_date', 'order_date', 'deadline_date', 'delivery_date']))
                                                    {{ \Carbon\Carbon::parse($value)->format('M d, Y g:i A') }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                {{ $change['new'] }}
                            @endif
                        </dd>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Changes Made</h3>
        <div class="text-center py-8">
            <i class="fas fa-info-circle text-3xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No detailed changes recorded for this action.</p>
        </div>
    </div>
    @endif

</div>
@endsection
