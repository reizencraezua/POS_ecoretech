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

<!-- Pagination -->
@if($payments->hasPages())
    <div class="bg-white px-6 py-3 border-t border-gray-200">
        {{ $payments->links() }}
    </div>
@endif
