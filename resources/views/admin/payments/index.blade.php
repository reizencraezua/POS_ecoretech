@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payment Management')
@section('page-description', 'Track and manage customer payments')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <a href="{{ route('admin.payments.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Record Payment
                </a>
            @endif
            <button onclick="openFilterModal()" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Advanced Filters
            </button>
        </div>
        
        <!-- Search and Archive Toggle -->
        <div class="flex items-center space-x-4">
            <!-- Archive Toggle -->
            <a href="{{ route('admin.payments.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            
            <!-- Search -->
            <form method="GET" id="searchForm" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search payments..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                @if(request('search') || request('method') || request('date_range') || request('start_date') || request('end_date'))
                    <a href="{{ route('admin.payments.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.payments.show', $payment) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-receipt text-green-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $payment->receipt_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $orderStatus = $payment->getOrderStatus();
                                    $order = $payment->orderWithTrashed()->first();
                                @endphp
                                @if($orderStatus === 'active')
                                    <div class="text-sm font-medium text-gray-900">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-sm text-gray-500">₱{{ number_format($order->final_total_amount, 2) }} total</div>
                                @elseif($orderStatus === 'deleted')
                                    <div class="text-sm font-medium text-gray-900 text-orange-600">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }} (Deleted)</div>
                                    <div class="text-sm text-gray-500">₱{{ number_format($order->final_total_amount, 2) }} total</div>
                                @else
                                    <div class="text-sm font-medium text-gray-900 text-red-600">Order Not Found</div>
                                    <div class="text-sm text-gray-500">Payment ID: {{ $payment->payment_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($orderStatus === 'active' && $order && $order->customer)
                                    <div class="text-sm font-medium text-gray-900">{{ $order->customer->display_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->customer->contact_number1 }}</div>
                                @elseif($orderStatus === 'deleted' && $order && $order->customer)
                                    <div class="text-sm font-medium text-gray-900 text-orange-600">{{ $order->customer->display_name }} (Deleted)</div>
                                    <div class="text-sm text-gray-500">{{ $order->customer->contact_number1 }}</div>
                                @else
                                    <div class="text-sm font-medium text-gray-900 text-red-600">Customer Not Found</div>
                                    <div class="text-sm text-gray-500">Payment ID: {{ $payment->payment_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @switch($payment->payment_method)
                                        @case('Cash')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('GCash')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @case('Bank Transfer')
                                            bg-purple-100 text-purple-800
                                            @break
                                        @case('Check')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @case('Credit Card')
                                            bg-gray-100 text-gray-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="text-lg font-bold text-maroon">₱{{ number_format($payment->amount_paid, 2) }}</div>
                                    @if($orderStatus === 'active' && $order && $order->deadline_date)
                                        @php
                                            $deadlineDate = \Carbon\Carbon::parse($order->deadline_date);
                                            $today = \Carbon\Carbon::today();
                                            $daysUntilDeadline = $today->diffInDays($deadlineDate, false);
                                        @endphp
                                        @if($daysUntilDeadline <= 3 && $daysUntilDeadline >= 0)
                                            <i class="fas fa-exclamation-triangle text-yellow-500 animate-pulse" title="Due in {{ $daysUntilDeadline }} day(s)"></i>
                                        @elseif($daysUntilDeadline < 0)
                                            <i class="fas fa-exclamation-triangle text-red-500 animate-pulse" title="Overdue by {{ abs($daysUntilDeadline) }} day(s)"></i>
                                        @endif
                                    @endif
                                </div>
                                @if($payment->change > 0)
                                    <div class="text-sm text-gray-500">Change: ₱{{ number_format($payment->change, 2) }}</div>
                                @endif
                                @if($payment->balance > 0)
                                    <div class="text-sm text-red-600 font-medium">Balance: ₱{{ number_format($payment->balance, 2) }}</div>
                                @else
                                    <div class="text-sm text-green-600 font-medium">Fully Paid</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->balance > 0)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Partial</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Complete</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center space-x-3">
                                    <!-- Print Button -->
                                <button onclick="printReceipt({{ $payment->payment_id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors" 
                                        title="Print Receipt">
                                    <i class="fas fa-print"></i>
                                </button>
                                
                                    <!-- Edit Button -->
                                   
                                    
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$payment" 
                                            :archiveRoute="'admin.payments.archive'" 
                                            :restoreRoute="'admin.payments.restore'" 
                                            :editRoute="'admin.payments.edit'"
                                            :showRestore="true" />
                                    @else
                                        <x-archive-actions 
                                            :item="$payment" 
                                            :archiveRoute="'admin.payments.archive'" 
                                            :restoreRoute="'admin.payments.restore'" 
                                            :editRoute="'admin.payments.edit'"
                                            :showRestore="false" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-credit-card text-6xl mb-4"></i>
                                    <p class="text-xl font-medium mb-2">No payments found</p>
                                    <p class="text-gray-500">Payment records will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Advanced Payment Filters & Summary</h3>
                <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Filters Section -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Filters</h4>
            <form id="filterForm" method="GET" action="{{ route('admin.payments.index') }}">
                <div class="space-y-4">
                    <!-- Date Range Selection -->

                            <!-- Date Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                                <select name="date_range" id="date_range" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    <option value="">All Time</option>
                                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                    <option value="last_30_days" {{ request('date_range') == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                    <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 Months</option>
                                    <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                    <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                    </div>

                            <!-- Custom Date Range (shown when "Custom Range" is selected) -->
                            <div id="custom_date_range_div" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Custom Date Range</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-xs text-gray-500 mb-1">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs text-gray-500 mb-1">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Filter -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" id="payment_method" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">All Payment Methods</option>
                            <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="GCash" {{ request('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                            <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Check" {{ request('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
                            <option value="Credit Card" {{ request('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                    </div>

                    <!-- Payment Status Filter -->
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                        <select name="payment_status" id="payment_status" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">All Status</option>
                            <option value="complete" {{ request('payment_status') == 'complete' ? 'selected' : '' }}>Complete</option>
                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>

                    <!-- Summary Cards -->
                    <div class="mb-4">
                        <div class="bg-blue-50 p-3 rounded-lg mb-3">
                            <div class="text-sm text-blue-600 font-medium">Total Amount</div>
                            <div id="totalAmount" class="text-lg font-bold text-blue-800">₱0.00</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <div class="text-sm text-green-600 font-medium mb-2">Payment Methods</div>
                            <div id="paymentMethodsSummary" class="space-y-1">
                                <div class="text-xs text-gray-500">No payments found</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeFilterModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
                </div>

                <!-- Payments List Section -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Filtered Payments</h4>
                    
                    <div id="filteredPaymentsList" class="bg-gray-50 rounded-lg p-4 max-h-100 overflow-y-auto">
                        @if($payments->count() > 0)
                            <div class="space-y-3">
                                @foreach($payments->take(10) as $payment)
                                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-receipt text-green-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $payment->receipt_number }}</div>
                                                    <div class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-bold text-maroon">₱{{ number_format($payment->amount_paid, 2) }}</div>
                                                <div class="text-xs text-gray-500">{{ $payment->payment_method }}</div>
                                            </div>
                                        </div>
                                        @if($payment->balance > 0)
                                            <div class="mt-2 text-xs text-red-600">
                                                Balance: ₱{{ number_format($payment->balance, 2) }}
                                            </div>
                                        @else
                                            <div class="mt-2 text-xs text-green-600">
                                                Fully Paid
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
            </div>
                        @else
                <div class="text-center py-8">
                                <i class="fas fa-credit-card text-2xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">No payments found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
function printReceipt(paymentId) {
    // Open receipt in new window for printing
    const printWindow = window.open(`/admin/payments/${paymentId}/print`, '_blank', 'width=800,height=600');
    printWindow.focus();
}

function openFilterModal() {
    document.getElementById('filterModal').classList.remove('hidden');
    
    // Initialize summary with current data
    updateSummaryFromCurrentData();
}

function updateSummaryFromCurrentData() {
    // Calculate totals from current page data
    const paymentsTable = document.querySelector('tbody');
    if (paymentsTable) {
        const allRows = Array.from(paymentsTable.querySelectorAll('tr'));
        
        // Filter out the "No payments found" row
        const allPayments = allRows.filter(row => {
            const hasColspan = row.querySelector('td[colspan]');
            return !hasColspan; // Exclude rows with colspan (like "No payments found")
        });
        
        let totalAmount = 0;
        let paymentMethods = {};
        
        allPayments.forEach(paymentRow => {
            const amountCell = paymentRow.querySelector('td:nth-child(5)');
            const methodCell = paymentRow.querySelector('td:nth-child(4)');
            
            if (amountCell) {
                const amountText = amountCell.querySelector('.text-lg.font-bold')?.textContent || '₱0.00';
                const amount = parseFloat(amountText.replace('₱', '').replace(',', '')) || 0;
                totalAmount += amount;
            }
            
            if (methodCell) {
                const method = methodCell.textContent.trim();
                if (method) {
                    paymentMethods[method] = (paymentMethods[method] || 0) + 1;
                }
            }
        });
        
        // Update summary cards
        document.getElementById('totalAmount').textContent = `₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        // Update payment methods summary
        const methodsSummary = document.getElementById('paymentMethodsSummary');
        if (Object.keys(paymentMethods).length > 0) {
            methodsSummary.innerHTML = Object.entries(paymentMethods)
                .map(([method, count]) => 
                    `<div class="flex justify-between items-center text-xs">
                        <span class="text-gray-700">${method}:</span>
                        <span class="font-medium text-green-800">${count}</span>
                    </div>`
                ).join('');
        } else {
            methodsSummary.innerHTML = '<div class="text-xs text-gray-500">No payments found</div>';
        }
    }
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}



function updateFilteredPayments() {
    const dateRange = document.getElementById('date_range').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const paymentMethod = document.getElementById('payment_method').value;
    const paymentStatus = document.getElementById('payment_status').value;
    
    // Build query parameters
    const params = new URLSearchParams();
    if (dateRange) params.append('date_range', dateRange);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (paymentMethod) params.append('payment_method', paymentMethod);
    if (paymentStatus) params.append('payment_status', paymentStatus);
    
    // Show loading state
    document.getElementById('filteredPaymentsList').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Loading filtered payments...</p>
        </div>
    `;
    
    // Update summary cards to loading state
    document.getElementById('totalAmount').textContent = 'Loading...';
    document.getElementById('paymentMethodsSummary').innerHTML = '<div class="text-xs text-gray-500">Loading...</div>';
    
    // Fetch filtered payments
    fetch(`{{ route('admin.payments.index') }}?${params.toString()}`)
        .then(response => response.text())
        .then(html => {
            // Create a temporary DOM element to parse the response
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extract the payments from the main table
            const paymentsTable = tempDiv.querySelector('tbody');
            if (paymentsTable) {
                const allRows = Array.from(paymentsTable.querySelectorAll('tr'));
                
                // Filter out the "No payments found" row
                const allPayments = allRows.filter(row => {
                    const hasColspan = row.querySelector('td[colspan]');
                    return !hasColspan; // Exclude rows with colspan (like "No payments found")
                });
                
                const displayPayments = allPayments.slice(0, 10);
                
                // Calculate totals and payment methods from all payments
                let totalAmount = 0;
                let paymentMethods = {};
                
                allPayments.forEach(paymentRow => {
                    const amountCell = paymentRow.querySelector('td:nth-child(5)');
                    const methodCell = paymentRow.querySelector('td:nth-child(4)');
                    
                    if (amountCell) {
                        const amountText = amountCell.querySelector('.text-lg.font-bold')?.textContent || '₱0.00';
                        const amount = parseFloat(amountText.replace('₱', '').replace(',', '')) || 0;
                        totalAmount += amount;
                    }
                    
                    if (methodCell) {
                        const method = methodCell.textContent.trim();
                        if (method) {
                            paymentMethods[method] = (paymentMethods[method] || 0) + 1;
                        }
                    }
                });
                
                // Update summary cards
                document.getElementById('totalAmount').textContent = `₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                
                // Update payment methods summary
                const methodsSummary = document.getElementById('paymentMethodsSummary');
                if (Object.keys(paymentMethods).length > 0) {
                    methodsSummary.innerHTML = Object.entries(paymentMethods)
                        .map(([method, count]) => 
                            `<div class="flex justify-between items-center text-xs">
                                <span class="text-gray-700">${method}:</span>
                                <span class="font-medium text-green-800">${count}</span>
                            </div>`
                        ).join('');
                } else {
                    methodsSummary.innerHTML = '<div class="text-xs text-gray-500">No payments found</div>';
                }
                
                if (displayPayments.length > 0) {
                    const paymentsHTML = displayPayments.map(paymentRow => {
                        const receiptCell = paymentRow.querySelector('td:first-child');
                        const amountCell = paymentRow.querySelector('td:nth-child(5)');
                        
                        if (receiptCell && amountCell) {
                            const receiptInfo = receiptCell.querySelector('div');
                            const receiptNumber = receiptInfo?.querySelector('.text-sm.font-medium')?.textContent || 'N/A';
                            const receiptDate = receiptInfo?.querySelector('.text-sm.text-gray-500')?.textContent || 'N/A';
                            const amount = amountCell.querySelector('.text-lg.font-bold')?.textContent || '₱0.00';
                            const balance = amountCell.querySelector('.text-sm.text-red-600')?.textContent || 
                                         amountCell.querySelector('.text-sm.text-green-600')?.textContent || '';
                            
                            return `
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-receipt text-green-600 text-sm"></i>
                        </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">${receiptNumber}</div>
                                                <div class="text-xs text-gray-500">${receiptDate}</div>
                        </div>
                    </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-maroon">${amount}</div>
                                    </div>
                                    </div>
                                    ${balance ? `<div class="mt-2 text-xs ${balance.includes('Fully Paid') ? 'text-green-600' : 'text-red-600'}">${balance}</div>` : ''}
                                </div>
                            `;
                        }
                        return '';
                    }).filter(html => html).join('');
                    
                    document.getElementById('filteredPaymentsList').innerHTML = `
                        <div class="space-y-3">
                            ${paymentsHTML}
                        </div>
                    `;
                } else {
                    document.getElementById('filteredPaymentsList').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-credit-card text-2xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No payments found for the selected filters</p>
                </div>
            `;
                }
            }
        })
        .catch(error => {
            console.error('Error loading filtered payments:', error);
            document.getElementById('filteredPaymentsList').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400 mb-4"></i>
                    <p class="text-red-500">Error loading payments</p>
                </div>
            `;
            
            // Reset summary cards on error
            document.getElementById('totalAmount').textContent = 'Error';
            document.getElementById('totalCount').textContent = 'Error';
        });
}

// Add event listeners for filter changes
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = ['start_date', 'end_date', 'payment_method', 'payment_status'];
    
    filterInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('change', updateFilteredPayments);
            input.addEventListener('input', updateFilteredPayments);
        }
    });
    
    // Add date range dropdown handler
    const dateRangeSelect = document.getElementById('date_range');
    if (dateRangeSelect) {
        dateRangeSelect.addEventListener('change', handleDateRangeChange);
    }
    
    // Add auto-search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500); // 500ms delay to avoid too many requests
        });
    }
});

function handleDateRangeChange() {
    const dateRange = document.getElementById('date_range').value;
    const customDateRangeDiv = document.getElementById('custom_date_range_div');
    
    if (dateRange === 'custom') {
        customDateRangeDiv.classList.remove('hidden');
    } else {
        customDateRangeDiv.classList.add('hidden');
        
        // Set dates based on selected range
        let startDate = '';
        let endDate = '';
        
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        switch(dateRange) {
            case 'today':
                startDate = today.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'yesterday':
                startDate = yesterday.toISOString().split('T')[0];
                endDate = yesterday.toISOString().split('T')[0];
                break;
            case 'last_7_days':
                const last7Days = new Date(today);
                last7Days.setDate(last7Days.getDate() - 7);
                startDate = last7Days.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_30_days':
                const last30Days = new Date(today);
                last30Days.setDate(last30Days.getDate() - 30);
                startDate = last30Days.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_3_months':
                const last3Months = new Date(today);
                last3Months.setMonth(last3Months.getMonth() - 3);
                startDate = last3Months.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'this_year':
                const thisYear = new Date(today.getFullYear(), 0, 1);
                startDate = thisYear.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
        }
        
        // Update the hidden date inputs
        document.getElementById('start_date').value = startDate;
        document.getElementById('end_date').value = endDate;
    }
    
    // Update filtered payments
    updateFilteredPayments();
}
</script>
@endsection