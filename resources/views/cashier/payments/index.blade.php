@extends('layouts.cashier')

@section('title', 'Payments')
@section('page-title', 'Payment Management')
@section('page-description', 'Track and manage customer payments')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <a href="{{ route('cashier.payments.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Record Payment
                </a>
            @endif
          
        </div>
        
        <!-- Search and Archive Toggle -->
        <div class="flex items-center space-x-4">
          
            
            <!-- Search -->
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search payments..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon w-80">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto" id="paymentsTable">
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
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('cashier.payments.show', $payment) }}'">
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
                                            <i class="fas fa-exclamation-triangle text-yellow-500" title="Due in {{ $daysUntilDeadline }} day(s)"></i>
                                        @elseif($daysUntilDeadline < 0)
                                            <i class="fas fa-exclamation-triangle text-red-500" title="Overdue by {{ abs($daysUntilDeadline) }} day(s)"></i>
                                        @endif
                                    @endif
                                </div>
                                @if($payment->change > 0)
                                    <div class="text-sm text-gray-500">Change: ₱{{ number_format($payment->change, 2) }}</div>
                                @endif
                                @if($payment->balance > 0)
                                    <div class="text-sm text-red-600 font-medium">Balance: -₱{{ number_format($payment->balance, 2) }}</div>
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
                                            class="text-blue-600 hover:text-blue-900" 
                                        title="Print Receipt">
                                    <i class="fas fa-print"></i>
                                </button>
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



<script>
function printReceipt(paymentId) {
    // Open receipt in new window for printing
    const printWindow = window.open(`/cashier/payments/${paymentId}/print`, '_blank', 'width=800,height=600');
    printWindow.focus();
}

function printPaymentSummary() {
    // Get current filter values
    const dateRange = document.getElementById('date_range').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const paymentMethod = document.getElementById('payment_method').value;
    const paymentStatus = document.getElementById('payment_status').value;
    
    // Build query parameters for the print URL
    const params = new URLSearchParams();
    if (dateRange) params.append('date_range', dateRange);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (paymentMethod) params.append('payment_method', paymentMethod);
    if (paymentStatus) params.append('payment_status', paymentStatus);
    
    // Open print window with filtered data
    const printWindow = window.open(`/cashier/payments/print-summary?${params.toString()}`, '_blank', 'width=800,height=600');
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
            <i class="fas fa-circle text-2xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Loading filtered payments...</p>
        </div>
    `;
    
    // Update summary cards to loading state
    document.getElementById('totalAmount').textContent = 'Loading...';
    document.getElementById('paymentMethodsSummary').innerHTML = '<div class="text-xs text-gray-500">Loading...</div>';
    
    // Fetch filtered payments
    fetch(`{{ route('cashier.payments.index') }}?${params.toString()}`)
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
                last7Days.setDate(today.getDate() - 7);
                startDate = last7Days.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_30_days':
                const last30Days = new Date(today);
                last30Days.setDate(today.getDate() - 30);
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

<!-- Simple Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const paymentsTable = document.getElementById('paymentsTable');
    
    if (searchInput && paymentsTable) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = paymentsTable.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection
