@extends('layouts.cashier')

@section('title', 'Payments')
@section('page-title', 'Payment Management')
@section('page-description', 'Manage customer payments and track payment records')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.payments.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Record Payment
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Payment Filters</h3>
            <div class="flex items-center space-x-2">
                <button onclick="openFilterModal()" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-filter mr-2"></i>
                    Advanced Filters
                </button>
                <button onclick="openSummaryModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Payment Summary
                </button>
            </div>
        </div>
        
        <form method="GET" class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by payment ID or customer..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('payment_method') || request('start_date') || request('end_date'))
                    <a href="{{ route('cashier.payments.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request('payment_method') || request('start_date') || request('end_date'))
        <div class="mt-4 flex flex-wrap gap-2">
            @if(request('payment_method'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                    {{ request('payment_method') }}
                    <button onclick="removeFilter('payment_method')" class="ml-2 text-blue-600 hover:text-blue-400">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            @endif
            @if(request('start_date') && request('end_date'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    {{ request('start_date') }} to {{ request('end_date') }}
                    <button onclick="removeFilter('date_range')" class="ml-2 text-green-600 hover:text-green-400">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            @endif
        </div>
        @endif
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->payment_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $payment->order->order_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $payment->order->customer->customer_firstname }} {{ $payment->order->customer->customer_lastname }}</div>
                            @if($payment->order->customer->business_name)
                                <div class="text-sm text-gray-500">{{ $payment->order->customer->business_name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div class="flex items-center space-x-2">
                                <span>₱{{ number_format($payment->amount_paid, 2) }}</span>
                                @php
                                    $deadlineDate = \Carbon\Carbon::parse($payment->order->deadline_date);
                                    $today = \Carbon\Carbon::today();
                                    $daysUntilDeadline = $today->diffInDays($deadlineDate, false);
                                @endphp
                                @if($daysUntilDeadline <= 3 && $daysUntilDeadline >= 0)
                                    <i class="fas fa-exclamation-triangle text-yellow-500 animate-pulse" title="Due in {{ $daysUntilDeadline }} day(s)"></i>
                                @elseif($daysUntilDeadline < 0)
                                    <i class="fas fa-exclamation-triangle text-red-500 animate-pulse" title="Overdue by {{ abs($daysUntilDeadline) }} day(s)"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($payment->payment_method === 'Cash') bg-green-100 text-green-800
                                @elseif($payment->payment_method === 'Check') bg-blue-100 text-blue-800
                                @elseif($payment->payment_method === 'GCash') bg-purple-100 text-purple-800
                                @elseif($payment->payment_method === 'PayMaya') bg-pink-100 text-pink-800
                                @elseif($payment->payment_method === 'Bank Transfer') bg-indigo-100 text-indigo-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $payment->payment_method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->payment_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->payment_reference ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('cashier.payments.show', $payment) }}" 
                                   class="text-maroon hover:text-maroon-dark" title="View Payment">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cashier.payments.edit', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit Payment">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="printReceipt({{ $payment->payment_id }})" 
                                        class="text-green-600 hover:text-green-900" 
                                        title="Print Receipt">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-credit-card text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No payments found</p>
                                <p class="text-sm">Get started by recording a new payment.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Advanced Payment Filters</h3>
                <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="filterForm" method="GET" action="{{ route('cashier.payments.index') }}">
                <div class="space-y-4">
                    <!-- Date Range Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="setDateRange('today')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Today</button>
                            <button type="button" onclick="setDateRange('yesterday')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Yesterday</button>
                            <button type="button" onclick="setDateRange('this_week')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">This Week</button>
                            <button type="button" onclick="setDateRange('last_week')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Last Week</button>
                            <button type="button" onclick="setDateRange('this_month')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">This Month</button>
                            <button type="button" onclick="setDateRange('last_month')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Last Month</button>
                            <button type="button" onclick="setDateRange('this_year')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">This Year</button>
                            <button type="button" onclick="setDateRange('last_year')" class="date-range-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Last Year</button>
                        </div>
                    </div>

                    <!-- Custom Date Range -->
                    <div>
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
                            <option value="Check" {{ request('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
                            <option value="GCash" {{ request('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                            <option value="PayMaya" {{ request('payment_method') == 'PayMaya' ? 'selected' : '' }}>PayMaya</option>
                            <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
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
    </div>
</div>

<!-- Summary Modal -->
<div id="summaryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Payment Summary</h3>
                <button onclick="closeSummaryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="summaryContent">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Loading payment summary...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReceipt(paymentId) {
    // Open receipt in new window for printing
    const printWindow = window.open(`/cashier/payments/${paymentId}/print`, '_blank', 'width=800,height=600');
    printWindow.focus();
}

function openFilterModal() {
    document.getElementById('filterModal').classList.remove('hidden');
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}

function openSummaryModal() {
    document.getElementById('summaryModal').classList.remove('hidden');
    loadPaymentSummary();
}

function closeSummaryModal() {
    document.getElementById('summaryModal').classList.add('hidden');
}

function setDateRange(range) {
    const today = new Date();
    let startDate, endDate;
    
    // Remove active class from all buttons
    document.querySelectorAll('.date-range-btn').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('border-gray-300', 'hover:bg-gray-50');
    });
    
    // Add active class to clicked button
    event.target.classList.add('bg-maroon', 'text-white');
    event.target.classList.remove('border-gray-300', 'hover:bg-gray-50');
    
    switch(range) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = yesterday.toISOString().split('T')[0];
            break;
        case 'this_week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            startDate = startOfWeek.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'last_week':
            const lastWeekStart = new Date(today);
            lastWeekStart.setDate(today.getDate() - today.getDay() - 7);
            const lastWeekEnd = new Date(today);
            lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
            startDate = lastWeekStart.toISOString().split('T')[0];
            endDate = lastWeekEnd.toISOString().split('T')[0];
            break;
        case 'this_month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'last_month':
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            startDate = lastMonth.toISOString().split('T')[0];
            endDate = lastMonthEnd.toISOString().split('T')[0];
            break;
        case 'this_year':
            startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'last_year':
            const lastYear = new Date(today.getFullYear() - 1, 0, 1);
            const lastYearEnd = new Date(today.getFullYear() - 1, 11, 31);
            startDate = lastYear.toISOString().split('T')[0];
            endDate = lastYearEnd.toISOString().split('T')[0];
            break;
    }
    
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
}

function loadPaymentSummary() {
    const urlParams = new URLSearchParams(window.location.search);
    const params = {
        start_date: urlParams.get('start_date') || '',
        end_date: urlParams.get('end_date') || '',
        payment_method: urlParams.get('payment_method') || ''
    };
    
    fetch(`/cashier/payments/summary?${new URLSearchParams(params)}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('summaryContent').innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm text-blue-600 font-medium">Total Payments</div>
                            <div class="text-2xl font-bold text-blue-800">₱${data.total_amount.toLocaleString()}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm text-green-600 font-medium">Payment Count</div>
                            <div class="text-2xl font-bold text-green-800">${data.payment_count}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-sm text-purple-600 font-medium">Average Payment</div>
                            <div class="text-2xl font-bold text-purple-800">₱${data.average_payment.toLocaleString()}</div>
                        </div>
                    </div>
                    
                    <div class="bg-white border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-4">Payment Methods Breakdown</h4>
                        <div class="space-y-3">
                            ${data.payment_methods.map(method => `
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 rounded-full" style="background-color: ${method.color}"></div>
                                        <span class="font-medium">${method.method}</span>
                                        <span class="text-sm text-gray-500">(${method.count} payments)</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold">₱${method.amount.toLocaleString()}</div>
                                        <div class="text-sm text-gray-500">${method.percentage}%</div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('summaryContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400 mb-4"></i>
                    <p class="text-red-500">Error loading payment summary</p>
                </div>
            `;
        });
}
</script>
@endsection
