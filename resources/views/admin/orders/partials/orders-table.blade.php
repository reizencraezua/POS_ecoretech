<div id="ordersTableContainer">
    <div class="px-6 py-3 bg-blue-50 border-b border-blue-200">
        <p class="text-sm text-blue-700">
            <i class="fas fa-info-circle mr-2"></i>
            Click on any order row to view details
        </p>
    </div>
    <div class="overflow-x-auto">
        <table id="ordersTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Info</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>   
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 {{ $order->order_status !== 'Voided' ? 'cursor-pointer group' : 'cursor-default' }}" 
                        @if($order->order_status !== 'Voided') onclick="window.location.href='{{ route('admin.orders.show', $order) }}'" @endif>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                               <div class="ml-4">
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</div>
                                        <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $order->customer->display_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="space-y-1">
                                <div>
                                    <span class="text-gray-500">Order:</span> {{ $order->order_date->format('M d, Y') }}
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-500">Due:</span> 
                                    <span class="ml-1 {{ $order->deadline_date->isPast() && $order->order_status !== 'Completed' ? 'text-red-600 font-medium' : '' }}">
                                        {{ $order->deadline_date->format('M d, Y') }}
                                    </span>
                                    @if($order->deadline_date->isPast() && $order->order_status !== 'Completed')
                                        <i class="fas fa-exclamation-triangle text-red-500 ml-1 text-xs"></i>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">₱{{ number_format($order->final_total_amount, 2) }}</div>
                            <div class="text-sm text-gray-500">{{ $order->details->count() }} item(s)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $totalPaid = $order->total_paid ?? 0;
                                $remainingBalance = $order->final_total_amount - $totalPaid;
                                $paymentPercentage = $order->final_total_amount > 0 ? ($totalPaid / $order->final_total_amount) * 100 : 0;
                            @endphp
                            <div class="space-y-1">
                                <div class="text-sm">
                                    <span class="font-medium text-green-600">₱{{ number_format($totalPaid, 2) }}</span>
                                    <span class="text-gray-500">paid</span>
                                </div>
                                @if($remainingBalance > 0)
                                    <div class="text-sm">
                                        <span class="font-medium text-red-600">₱{{ number_format($remainingBalance, 2) }}</span>
                                        <span class="text-gray-500">due</span>
                                    </div>
                                @endif
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $paymentPercentage }}%"></div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    @switch($order->order_status)
                                        @case('On-Process')
                                           text-blue-800
                                            @break
                                        @case('Designing')
                                            text-purple-800
                                            @break
                                        @case('Production')
                                            text-yellow-800
                                            @break
                                        @case('For Releasing')
                                            text-orange-800
                                            @break
                                        @case('Completed')
                                            text-green-800
                                            @break
                                        @case('Cancelled')
                                            text-red-800
                                            @break
                                        @case('Voided')
                                            text-gray-800
                                            @break
                                        @default
                                            text-gray-800
                                    @endswitch
                                ">
                                    {{ $order->order_status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                @if($order->creator)
                                    @if($order->creator->employee)
                                        EMP00{{ $order->creator->employee->employee_id }} : {{ $order->creator->employee->employee_firstname }}
                                    @else
                                        {{ $order->creator->name }}
                                    @endif
                                @else
                                    Admin
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                @if($order->creator)
                                    {{ $order->created_at->diffForHumans() }}
                                @endif
                            </div>
                            @if($order->order_status === 'Voided')
                                <div class="text-xs text-red-600 mt-1">
                                    <i class="fas fa-ban mr-1"></i>
                                    Voided by {{ $order->voidedBy ? ($order->voidedBy->employee ? 'EMP' . $order->voidedBy->employee->employee_id . ' : ' . $order->voidedBy->employee->employee_firstname : $order->voidedBy->name) : 'Admin' }}
                                    @if($order->voided_at)
                                        - {{ $order->voided_at->diffForHumans() }}
                                    @endif
                                </div>
                                @if($order->void_reason)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Reason: {{ Str::limit($order->void_reason, 50) }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if($order->order_status !== 'Voided')
                                    @if($order->payments()->count() > 0)
                                        <!-- Edit Order Disabled -->
                                        <span class="text-gray-400 cursor-not-allowed" title="Cannot edit order with existing payments">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    @else
                                        <!-- Edit Order -->
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="text-red-600 hover:text-red-800 transition-colors" title="Edit Order" onclick="event.stopPropagation();">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                @endif
                                
                                @if(isset($showArchived) && $showArchived)
                                    <!-- Restore Order -->
                                    <form method="POST" action="{{ route('admin.orders.restore', $order->order_id) }}" onsubmit="return confirm('Restore this order?');" class="inline" onclick="event.stopPropagation();">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-800 transition-colors" title="Restore Order">
                                            <i class="fas fa-rotate-left"></i>
                                        </button>
                                    </form>
                                @else
                                    <!-- Archive Order -->
                                    <form method="POST" action="{{ route('admin.orders.archive', $order) }}" onsubmit="return confirm('Archive this order? It will be moved to archives.');" class="inline" onclick="event.stopPropagation();">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-800 transition-colors" title="Archive Order">
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
                                <div class="text-gray-400">
                                    <i class="fas fa-shopping-cart text-6xl mb-4"></i>
                                    <p class="text-xl font-medium mb-2">No orders found</p>
                                    <p class="text-gray-500">Create your first job order to get started</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="bg-white px-6 py-3 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    @endif
</div>
