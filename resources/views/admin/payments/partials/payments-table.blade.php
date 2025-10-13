<div id="paymentsTableContainer">
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
                                @if($order->customer->customer_email)
                                    <div class="text-xs text-gray-400">{{ $order->customer->customer_email }}</div>
                                @endif
                            @elseif($orderStatus === 'deleted' && $order && $order->customer)
                                <div class="text-sm font-medium text-gray-900 text-orange-600">{{ $order->customer->display_name }} (Deleted)</div>
                                <div class="text-sm text-gray-500">{{ $order->customer->contact_number1 }}</div>
                                @if($order->customer->customer_email)
                                    <div class="text-xs text-gray-400">{{ $order->customer->customer_email }}</div>
                                @endif
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
                                        bg-indigo-100 text-indigo-800
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->balance > 0)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    Partial Payment
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Paid in Full
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors" 
                                   title="View Payment"
                                   onclick="event.stopPropagation();">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.payments.edit', $payment) }}" 
                                   class="text-green-600 hover:text-green-800 transition-colors" 
                                   title="Edit Payment"
                                   onclick="event.stopPropagation();">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($showArchived)
                                    <form method="POST" action="{{ route('admin.payments.restore', $payment) }}" 
                                          class="inline" 
                                          onsubmit="return confirm('Restore this payment?');"
                                          onclick="event.stopPropagation();">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 transition-colors" title="Restore Payment">
                                            <i class="fas fa-rotate-left"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.payments.archive', $payment) }}" 
                                          class="inline" 
                                          onsubmit="return confirm('Archive this payment? It will be moved to archives.');"
                                          onclick="event.stopPropagation();">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-800 transition-colors" title="Archive Payment">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    @if(request('search'))
                        {{-- Don't show empty state when searching --}}
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No payments found</h3>
                                    <p class="text-gray-500">Get started by creating a new payment.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
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
