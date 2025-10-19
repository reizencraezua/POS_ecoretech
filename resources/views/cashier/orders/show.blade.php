@extends('layouts.cashier')

@section('title', 'Order Details')
@section('page-title', 'Order #' . $order->order_id)
@section('page-description', 'View detailed information about this job order')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cashier.orders.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
            <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Order #{{ $order->order_id }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $order->order_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-flag-checkered mr-1"></i>{{ $order->deadline_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-user mr-1"></i>{{ $order->customer->display_name }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">₱{{ number_format($order->final_total_amount, 2) }}</div>
                        <div class="text-sm text-gray-600">Total Amount</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-semibold text-gray-700">₱{{ number_format($order->total_paid, 2) }}</div>
                        <div class="text-sm text-gray-600">Total Paid</div>
            </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold {{ $order->remaining_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₱{{ number_format($order->remaining_balance, 2) }}
                        </div>
                        <div class="text-sm text-gray-600">Balance</div>
                    </div>
                 <span id="statusBadge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
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
                     @endswitch">
                    {{ $order->order_status }}
                </span>
            </div>
        </div>
    </div>
    </div>


    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-3 space-y-4">
            <!-- Order Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Order Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Customer Details</h4>
                                <div class="space-y-2">
                                    <p class="text-gray-900 font-medium">{{ $order->customer->display_name }}</p>
                @if($order->customer->business_name)
                                        <p class="text-sm text-gray-600">{{ $order->customer->business_name }}</p>
                                    @endif
                                </div>
                    </div>
                    <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Production Team</h4>
                                <div class="space-y-2">
                                    <p class="text-gray-900 font-medium">{{ $order->employee->full_name }}</p>
                                    @if($order->employee->job)
                                        <p class="text-sm text-gray-600">{{ $order->employee->job->job_title }}</p>
                @endif
                                    @if($order->layout_employee_id)
                                        <div class="pt-2 border-t border-gray-100">
                                            <p class="text-sm text-gray-500">Layout Designer</p>
                                            <p class="text-gray-900 font-medium">{{ $order->layoutEmployee->full_name }}</p>
                                            @if($order->layoutEmployee->job)
                                                <p class="text-sm text-gray-600">{{ $order->layoutEmployee->job->job_title }}</p>
                @endif
                    </div>
                @endif
            </div>
        </div>
                </div>
                        <div class="space-y-6">
                <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Timeline</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Order Date</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $order->order_date->format('M d, Y') }}</span>
                </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Deadline</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $order->deadline_date->format('M d, Y') }}</span>
                    </div>
                                    
                </div>
                    </div>
                        </div>
                        <div class="space-y-6">
                        <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Order Summary</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Items</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $order->details->count() }} items</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Payment Progress</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $order->final_total_amount > 0 ? round(($order->total_paid / $order->final_total_amount) * 100) : 0 }}%
                                        </span>
                                    </div>
                                    <div class="pt-2 border-t border-gray-100">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-gray-600 h-2 rounded-full" style="width: {{ $order->final_total_amount > 0 ? round(($order->total_paid / $order->final_total_amount) * 100) : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
        </div>
    </div>


    <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
                        <div class="flex items-center space-x-6 text-sm text-gray-600">
                            <span>{{ $order->details->count() }} items</span>
                            @if($order->layout_design_fee > 0)
                            <span>₱{{ number_format($order->layout_design_fee, 2) }} layout fee</span>
                            @endif
                        </div>
                    </div>
                </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout Price</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($order->details as $detail)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $detail->item_type === 'Product' ? 'text-blue-800' : 'text-green-800' }}">
                                        {{ $detail->item_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                        @if($detail->item_type === 'Product')
                                            {{ $detail->product ? $detail->product->product_name : 'Product Not Found' }}
                                        @else
                                            {{ $detail->service ? $detail->service->service_name : 'Service Not Found' }}
                                @endif
                            </div>
                        </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->quantity }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->unit ? $detail->unit->unit_name : 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->size }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($detail->price, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @if($detail->layout)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-green-800">
                                            <i class="fas fa-check mr-1"></i>Yes
                                </span>
                            @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-gray-800">
                                            <i class="fas fa-times mr-1"></i>No
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->layout && $detail->layout_price > 0)
                                        ₱{{ number_format($detail->layout_price, 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                            @endif
                        </td>
                               
                            @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-box text-4xl mb-2"></i>
                                    <p>No items found for this order.</p>
                        </td>
                    </tr>
                            @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. of items: </span>
                            <span class="font-medium">{{ $order->details->sum('quantity') }}</span>
                        </div>
                        
                       

                <div class="flex justify-between">
                            <span class="text-gray-600">Base Amount:</span>
                            <span class="font-medium">₱{{ number_format($order->base_amount, 2) }}</span>
                </div>
                        
                       
                        
                <div class="flex justify-between">
                            <span class="text-gray-600">VAT (12%):</span>
                            <span class="font-medium">₱{{ number_format($order->vat_amount, 2) }}</span>
                </div>
                        
                    <div class="flex justify-between">
                            <span class="text-gray-600">Sub Total:</span>
                            <span class="font-medium">₱{{ number_format($order->sub_total, 2) }}</span>
                    </div>
                       
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Discount:</span>
                            <div class="text-right">
                                <div class="font-medium text-green-600">-₱{{ number_format($order->order_discount_amount, 2) }}</div>
                                @if($order->order_discount_info)
                                    <div class="text-xs text-gray-500">
                                        @if($order->order_discount_info['type'] === 'percentage')
                                            {{ $order->order_discount_info['percentage'] }}% off
                                        @else
                                            ₱{{ number_format($order->order_discount_info['amount'], 2) }} off
                                        @endif
                                        @if($order->order_discount_info['rule_name'])
                                            ({{ $order->order_discount_info['rule_name'] }})
                @endif
                    </div>
                @endif
            </div>
                        </div>

                    <div class="flex justify-between">
                            <span class="text-gray-600">Layout Fees: </span>
                            <span class="font-medium">₱{{ number_format($order->layout_fees, 2) }}</span>
                        </div>
                        
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-bold">
                            <span>TOTAL AMOUNT: </span>
                            <span>₱{{ number_format($order->final_total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    @if($order->payments()->count() > 0)
                        <button type="button" disabled class="w-full bg-gray-100 text-gray-400 px-3 py-2 rounded text-sm cursor-not-allowed inline-flex items-center justify-center" title="Cannot edit order with existing payments">
                            <i class="fas fa-lock mr-2"></i>
                            Edit Order (Disabled)
                        </button>
                    @else
                        <a href="{{ route('cashier.orders.edit', $order) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Order
                        </a>
                    @endif
                    <button type="button" onclick="openPaymentModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Add Payment
                    </button>
                    <a href="{{ route('cashier.deliveries.create', ['order_id' => $order->order_id]) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Add Delivery
                    </a>
                    @if($order->order_status !== 'Completed' && $order->order_status !== 'Cancelled' && $order->order_status !== 'Voided')
                    <form method="POST" action="{{ route('cashier.orders.status', $order) }}" class="w-full">
                        @csrf
                        @method('PATCH')
                        <select name="order_status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_status') border-red-500 @enderror">
                            <option value="">Update Status</option>
                            <option value="On-Process" {{ $order->order_status === 'On-Process' ? 'selected' : '' }}>On-Process</option>
                            <option value="Designing" {{ $order->order_status === 'Designing' ? 'selected' : '' }}>Designing</option>
                            <option value="Production" {{ $order->order_status === 'Production' ? 'selected' : '' }}>Production</option>
                            <option value="For Releasing" {{ $order->order_status === 'For Releasing' ? 'selected' : '' }}>For Releasing</option>
                            <option value="Completed" {{ $order->order_status === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ $order->order_status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('order_status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </form>
                    @endif
        </div>
    </div>

            <!-- Payment History -->
    @if($order->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 flex items-center">
            <i class="fas fa-credit-card mr-2 text-maroon"></i>
            Payment History
        </h3>
                        <span class="text-xs text-gray-500">{{ $order->payments->count() }} payment(s)</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-4 max-h-80 overflow-y-auto">
                    @foreach($order->payments as $payment)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-maroon">₱{{ number_format($payment->amount_paid, 2) }}</p>
                                        <p class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-blue-800">
                            {{ $payment->payment_term }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $payment->payment_method }}</p>
                                </div>
                            </div>
                            
                            @if($payment->reference_number)
                            <div class="flex items-center space-x-2 text-xs text-gray-600 mb-2">
                                <i class="fas fa-hashtag"></i>
                                <span>Reference: {{ $payment->reference_number }}</span>
                            </div>
                            @endif
                            
                            @if($payment->remarks)
                            <div class="text-xs text-gray-600 bg-white rounded p-2 border">
                                <i class="fas fa-comment mr-1"></i>
                                {{ $payment->remarks }}
                            </div>
                            @endif
                            
                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                <span class="text-xs text-gray-500">Receipt: {{ $payment->receipt_number }}</span>
                                <span class="text-xs text-gray-500">
                                    @if($payment->change > 0)
                                        Change: ₱{{ number_format($payment->change, 2) }}
                                    @else
                                        Balance: ₱{{ number_format($payment->balance, 2) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                    </div>
        </div>
    </div>
    @endif

            <!-- Delivery History -->
            @if($order->deliveries->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-truck mr-2 text-maroon"></i>
                            Delivery History
                        </h3>
                        <span class="text-xs text-gray-500">{{ $order->deliveries->count() }} delivery(s)</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-4 max-h-80 overflow-y-auto">
                        @foreach($order->deliveries as $delivery)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-truck text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</p>
                                        <p class="text-xs text-gray-500">{{ $delivery->delivery_date->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    @if($delivery->status === 'delivered') text-green-800
                                    @elseif($delivery->status === 'in_transit') text-yellow-800
                                    @elseif($delivery->status === 'scheduled') text-blue-800
                                    @else text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </div>
                            
                            <div class="text-xs text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $delivery->delivery_address }}
</div>

                            @if($delivery->notes)
                            <div class="text-xs text-gray-600 bg-white rounded p-2 border">
                                <i class="fas fa-comment mr-1"></i>
                                {{ $delivery->notes }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Add Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form id="paymentForm" method="POST" action="{{ route('cashier.payments.store') }}" class="mt-4">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                
                <div class="space-y-4">
                    <!-- Payment Date (Hidden) -->
                    <input type="hidden" name="payment_date" id="payment_date" value="{{ now()->format('Y-m-d') }}">
                    
                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_method') border-red-500 @enderror">
                            <option value="">Select Method</option>
                            <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="GCash" {{ old('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                            <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Check" {{ old('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
                            <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                        @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Payment Term -->
                    <div>
                        <label for="payment_term" class="block text-sm font-medium text-gray-700 mb-1">Payment Term *</label>
                        <select name="payment_term" id="payment_term" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_term') border-red-500 @enderror">
                            <option value="">Select Term</option>
                            <option value="Full Payment" {{ old('payment_term') == 'Full Payment' ? 'selected' : '' }}>Full Payment</option>
                            <option value="Partial Payment" {{ old('payment_term') == 'Partial Payment' ? 'selected' : '' }}>Partial Payment</option>
                        </select>
                        @error('payment_term')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Amount Paid -->
                    <div>
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid *</label>
                        <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0" max="{{ $order->final_total_amount }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('amount_paid') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('amount_paid')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Order Total: ₱{{ number_format($order->final_total_amount, 2) }}</p>
                    </div>
                    
                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('reference_number') border-red-500 @enderror"
                               placeholder="Optional">
                        @error('reference_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Remarks -->
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('remarks') border-red-500 @enderror"
                                  placeholder="Optional">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <button type="button" onclick="closePaymentModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark">
                        <i class="fas fa-credit-card mr-2"></i>Add Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('amount_paid').focus();
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentForm').reset();
}

// Close modal when clicking outside
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>
@endsection