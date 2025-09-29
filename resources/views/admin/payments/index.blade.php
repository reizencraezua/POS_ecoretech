@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payment Management')
@section('page-description', 'Track and manage customer payments')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.payments.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Record Payment
            </a>
        </div>
        
        <!-- Search and Filters -->
        <div class="flex items-center space-x-4">
            <form method="GET" class="flex items-center space-x-2">
                <select name="method" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Methods</option>
                    <option value="Cash" {{ request('method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="GCash" {{ request('method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                    <option value="Bank Transfer" {{ request('method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="Check" {{ request('method') == 'Check' ? 'selected' : '' }}>Check</option>
                    <option value="Credit Card" {{ request('method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payments..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('method'))
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
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
                                <div class="text-sm font-medium text-gray-900">Order #{{ str_pad($payment->order->order_id, 5, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-sm text-gray-500">₱{{ number_format($payment->order->total_amount, 2) }} total</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->order->customer->display_name }}</div>
                                <div class="text-sm text-gray-500">{{ $payment->order->customer->contact_number1 }}</div>
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
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($payment->amount_paid, 2) }}</div>
                                @if($payment->change > 0)
                                    <div class="text-sm text-gray-500">Change: ₱{{ number_format($payment->change, 2) }}</div>
                                @endif
                                @if($payment->balance > 0)
                                    <div class="text-sm text-red-600">Balance: ₱{{ number_format($payment->balance, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->balance > 0)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Partial</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Complete</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="printReceipt({{ $payment->payment_id }})" class="text-maroon hover:text-maroon-dark transition-colors">
                                    <i class="fas fa-print"></i>
                                </button>
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
    const printWindow = window.open(`/admin/payments/${paymentId}/print`, '_blank', 'width=800,height=600');
    printWindow.focus();
}
</script>
@endsection