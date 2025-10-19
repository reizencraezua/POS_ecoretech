@extends('layouts.admin')

@section('title', 'Transaction Logs')
@section('page-title', 'Transaction Logs')
@section('page-description', 'View edit history for all transactions')

@section('header-actions')
<div class="flex items-center gap-4">
     <!-- Filter Button -->
     <button onclick="openFilterModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon">
        <i class="fas fa-filter mr-2"></i>
        Filter
        @if(request('transaction_type') || request('action') || request('start_date') || request('end_date') || request('user_id'))
            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-maroon text-white">
                {{ collect([request('transaction_type'), request('action'), request('start_date'), request('end_date'), request('user_id')])->filter()->count() }}
            </span>
        @endif
    </button>
    <!-- Search Field -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
        <input type="text" 
               id="quickSearch" 
               placeholder="Search logs..." 
               value="{{ request('search') }}"
               class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-maroon focus:border-maroon sm:text-sm"
               onkeypress="handleQuickSearch(event)">
    </div>

</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Filter Transaction Logs</h3>
                <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.logs.index') }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Transaction Type Filter -->
                    <div>
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                        <select name="transaction_type" id="transaction_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">All Types</option>
                            @foreach($transactionTypes as $value => $label)
                                <option value="{{ $value }}" {{ request('transaction_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Filter -->
                    <div>
                        <label for="action" class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <select name="action" id="action" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">All Actions</option>
                            @foreach($actions as $value => $label)
                                <option value="{{ $value }}" {{ request('action') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User Filter -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Edited By</label>
                        <select name="user_id" id="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">All Users</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Search by transaction name, ID, or user..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>

                    <!-- Date Range Filters -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeFilterModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-maroon hover:bg-maroon-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon">
                        <i class="fas fa-search mr-2"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openFilterModal() {
    document.getElementById('filterModal').classList.remove('hidden');
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}

// Handle quick search
function handleQuickSearch(event) {
    if (event.key === 'Enter') {
        const searchValue = document.getElementById('quickSearch').value;
        
        // Get current URL parameters
        const url = new URL(window.location);
        
        // Update search parameter
        if (searchValue.trim()) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }
        
        // Redirect to new URL
        window.location.href = url.toString();
    }
}

// Close modal when clicking outside
document.getElementById('filterModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFilterModal();
    }
});
</script>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Status -->
    @if(request('start_date') || request('end_date') || request('transaction_type') || request('action') || request('user_id') || request('search'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-filter text-blue-600 mr-2"></i>
                <span class="text-sm font-medium text-blue-800">Filtered Results</span>
                <span class="text-sm text-blue-600 ml-2">
                    @if(request('start_date') && request('end_date'))
                        {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                    @elseif(request('start_date'))
                        From {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }}
                    @elseif(request('end_date'))
                        Until {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                    @endif
                    @if(request('transaction_type'))
                        • {{ ucfirst(request('transaction_type')) }}
                    @endif
                    @if(request('action'))
                        • {{ ucfirst(request('action')) }}
                    @endif
                </span>
            </div>
            <a href="{{ route('admin.logs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <i class="fas fa-times mr-1"></i>Clear Filters
            </a>
        </div>
    </div>
    @endif


    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">

        @if($logs->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date and Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.logs.show', $log) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @switch($log->transaction_type)
                                        @case('quotation')
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-file-lines text-blue-600 text-sm"></i>
                                            </div>
                                            @break
                                        @case('order')
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-clipboard-check text-green-600 text-sm"></i>
                                            </div>
                                            @break
                                        @case('payment')
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-credit-card text-yellow-600 text-sm"></i>
                                            </div>
                                            @break
                                        @case('delivery')
                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-truck text-purple-600 text-sm"></i>
                                            </div>
                                            @break
                                        @default
                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-file text-gray-600 text-sm"></i>
                                            </div>
                                    @endswitch
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($log->transaction_type === 'payment')
                                            @if($log->changes && isset($log->changes['created']['receipt_number']))
                                                Payment - {{ $log->changes['created']['receipt_number'] }}
                                            @elseif($log->changes && isset($log->changes['updated']['receipt_number']))
                                                Payment - {{ $log->changes['updated']['receipt_number'] }}
                                            @else
                                                {{ $log->transaction_name }}
                                            @endif
                                        @else
                                            {{ $log->transaction_name }}
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ ucfirst($log->transaction_type) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                                    @case('converted_to_order')
                                        text-purple-800
                                        @break
                                    @default
                                        text-gray-800
                                @endswitch
                            ">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $log->edited_by }}</div>
                            @if($log->user)
                                <div class="text-sm text-gray-500">{{ $log->user->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->formatted_date_time }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No transaction logs found</h3>
            <p class="text-gray-500">Transaction logs will appear here when changes are made to quotations, orders, payments, or deliveries.</p>
        </div>
        @endif
    </div>
</div>
@endsection
