@extends('layouts.admin')

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
                    <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
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
                    <span class="px-3 py-1 rounded-md text-sm font-medium bg-gray-100 text-gray-800">
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
                                        {{ $detail->item_type === 'Product' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $detail->item_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $detail->item_type === 'Product' ? $detail->product->product_name : $detail->service->service_name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->quantity }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->unit }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->size }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($detail->price, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->layout)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
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
                    <a href="{{ route('admin.orders.edit', $order) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Edit Order
                    </a>
                    <button type="button" onclick="openPaymentModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Add Payment
                    </button>
                    <a href="{{ route('admin.deliveries.create', ['order_id' => $order->order_id]) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Add Delivery
                    </a>
                    @if($order->order_status !== 'Completed' && $order->order_status !== 'Cancelled')
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="w-full">
                        @csrf
                        @method('PATCH')
                        <select name="order_status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500 @error('order_status') border-red-500 @enderror">
                            <option value="">Update Status</option>
                            <option value="On-Process">On-Process</option>
                            <option value="Designing">Designing</option>
                            <option value="Production">Production</option>
                            <option value="For Releasing">For Releasing</option>
                            <option value="Completed" {{ !$order->isFullyPaid() ? 'disabled' : '' }}>Completed{{ !$order->isFullyPaid() ? ' (Must be fully paid)' : '' }}</option>
                            <option value="Cancelled">Cancelled</option>
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
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
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
                                        <p class="text-sm font-medium text-gray-900">{{ $delivery->delivery_status }}</p>
                                        <p class="text-xs text-gray-500">{{ $delivery->delivery_date->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    @if($delivery->delivery_status === 'Delivered') bg-green-100 text-green-800
                                    @elseif($delivery->delivery_status === 'In Transit') bg-yellow-100 text-yellow-800
                                    @elseif($delivery->delivery_status === 'Pending') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $delivery->delivery_status }}
                                </span>
                            </div>
                            
                            <div class="text-xs text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $delivery->delivery_address }}
                            </div>
                            
                            @if($delivery->delivery_notes)
                            <div class="text-xs text-gray-600 bg-white rounded p-2 border">
                                <i class="fas fa-comment mr-1"></i>
                                {{ $delivery->delivery_notes }}
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
            <form id="paymentForm" method="POST" action="{{ route('admin.payments.store') }}" class="mt-4">
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
                    
                    <!-- Reference Number (for GCash and Bank Transfer) -->
                    <div id="reference_number_field" style="display: none;">
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                               placeholder="Transaction ID, reference number, etc."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('reference_number') border-red-500 @enderror">
                        @error('reference_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    
                    <!-- Payment Term -->
                    <div>
                        <label for="payment_term" class="block text-sm font-medium text-gray-700 mb-1">Payment Term *</label>
                        <select name="payment_term" id="payment_term" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_term') border-red-500 @enderror">
                            <option value="">Select Term</option>
                            @if(!$order->payments->where('payment_term', 'Downpayment')->count())
                            <option value="Downpayment" {{ old('payment_term') == 'Downpayment' ? 'selected' : '' }}>Downpayment</option>
                            @endif
                            <option value="Initial" {{ old('payment_term') == 'Initial' ? 'selected' : '' }}>Initial</option>
                            <option value="Full" {{ old('payment_term') == 'Full' ? 'selected' : '' }}>Full</option>
                        </select>
                        @error('payment_term')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Amount -->
                    <div>
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0" max="{{ $order->remaining_balance }}" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('amount_paid') border-red-500 @enderror"
                                   placeholder="0.00" value="{{ old('amount_paid') }}">
                        </div>
                        <div id="downpayment_info" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm" style="display: none;">
                            <div class="flex items-center text-blue-700 mb-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span class="font-medium">Downpayment Information</span>
                            </div>
                            <div class="text-blue-800">
                                <div class="flex justify-between items-center">
                                    <span>Total Amount:</span>
                                    <span id="final_total_amount" class="font-semibold">₱0.00</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Required Downpayment (50%):</span>
                                    <span id="downpayment_amount_display" class="font-bold text-lg">₱0.00</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Maximum: ₱{{ number_format($order->remaining_balance, 2) }}</p>
                        @error('amount_paid')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Remarks -->
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('remarks') border-red-500 @enderror"
                                  placeholder="Optional notes for this payment...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                    <button type="button" onclick="closePaymentModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                        Add Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
    // Set max amount to remaining balance
    document.getElementById('amount_paid').max = {{ $order->remaining_balance }};
    // Initialize field visibility
    toggleReferenceField();
    toggleDownpaymentInfo();
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    // Reset form
    document.querySelector('#paymentModal form').reset();
    document.getElementById('payment_date').value = '{{ now()->format('Y-m-d') }}';
}

// Function to toggle reference number field visibility
function toggleReferenceField() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const referenceNumberField = document.getElementById('reference_number_field');
    
    if (paymentMethodSelect && referenceNumberField) {
        const selectedMethod = paymentMethodSelect.value;
        if (selectedMethod === 'GCash' || selectedMethod === 'Bank Transfer') {
            referenceNumberField.style.display = 'block';
        } else {
            referenceNumberField.style.display = 'none';
        }
    }
}

// Function to toggle downpayment info visibility
function toggleDownpaymentInfo() {
    const paymentTermSelect = document.getElementById('payment_term');
    const downpaymentInfo = document.getElementById('downpayment_info');
    const totalAmount = {{ $order->total_amount }};
    const hasDownpayment = {{ $order->payments->where('payment_term', 'Downpayment')->count() ? 'true' : 'false' }};
    
    if (paymentTermSelect && downpaymentInfo) {
        const selectedTerm = paymentTermSelect.value;
        
        // Only show downpayment info if downpayment option is available and selected
        if (selectedTerm === 'Downpayment' && !hasDownpayment) {
            const expectedDownpayment = totalAmount * 0.5;
            
            // Update display elements
            document.getElementById('final_total_amount').textContent = `₱${totalAmount.toFixed(2)}`;
            document.getElementById('downpayment_amount_display').textContent = `₱${expectedDownpayment.toFixed(2)}`;
            
            downpaymentInfo.style.display = 'block';
        } else {
            downpaymentInfo.style.display = 'none';
        }
    }
}

// Function to validate downpayment amount
function validateDownpayment() {
    const paymentTermSelect = document.getElementById('payment_term');
    const amountInput = document.getElementById('amount_paid');
    const downpaymentInfo = document.getElementById('downpayment_info');
    const totalAmount = {{ $order->total_amount }};
    const hasDownpayment = {{ $order->payments->where('payment_term', 'Downpayment')->count() ? 'true' : 'false' }};
    
    if (paymentTermSelect && amountInput && downpaymentInfo) {
        const selectedTerm = paymentTermSelect.value;
        const enteredAmount = parseFloat(amountInput.value);
        
        // Only validate downpayment if it's available and selected
        if (selectedTerm === 'Downpayment' && !hasDownpayment) {
            const expectedDownpayment = totalAmount * 0.5;
            const tolerance = 0.01;
            
            if (enteredAmount && Math.abs(enteredAmount - expectedDownpayment) > tolerance) {
                amountInput.classList.add('border-red-500');
                amountInput.classList.remove('border-gray-300');
                downpaymentInfo.classList.remove('bg-blue-50', 'border-blue-200', 'text-blue-700');
                downpaymentInfo.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
            } else {
                amountInput.classList.remove('border-red-500');
                amountInput.classList.add('border-gray-300');
                downpaymentInfo.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
                downpaymentInfo.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-700');
            }
        } else {
            amountInput.classList.remove('border-red-500');
            amountInput.classList.add('border-gray-300');
            downpaymentInfo.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
            downpaymentInfo.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-700');
        }
    }
}

// Close modal when clicking outside
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Add event listeners for payment method and term changes
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentTermSelect = document.getElementById('payment_term');
    const amountInput = document.getElementById('amount_paid');
    
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', toggleReferenceField);
    }
    
    if (paymentTermSelect) {
        paymentTermSelect.addEventListener('change', toggleDownpaymentInfo);
    }
    
    if (amountInput) {
        amountInput.addEventListener('input', validateDownpayment);
    }
});

// Handle form submission with AJAX and receipt printing
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('amount_paid').value);
    const remainingBalance = {{ $order->remaining_balance }};
    const paymentTerm = document.getElementById('payment_term').value;
    const totalAmount = {{ $order->total_amount }};
    
    if (amount > remainingBalance) {
        alert('Payment amount cannot exceed remaining balance of ₱' + remainingBalance.toFixed(2));
        return false;
    }
    
    // Validate downpayment amount (only if downpayment is available)
    const hasDownpayment = {{ $order->payments->where('payment_term', 'Downpayment')->count() ? 'true' : 'false' }};
    if (paymentTerm === 'Downpayment' && !hasDownpayment) {
        const expectedDownpayment = totalAmount * 0.5;
        const tolerance = 0.01;
        
        if (Math.abs(amount - expectedDownpayment) > tolerance) {
            alert('Downpayment must be exactly 50% of the total amount (₱' + expectedDownpayment.toFixed(2) + ')');
            return false;
        }
    }
    
    // Disable submit button to prevent double submission
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    
    
    // Submit form via AJAX
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Payment successful - close modal
            closePaymentModal();
            
            // Show success message
            showNotification('Payment added successfully!', 'success');
            
            // Reload page to update payment information
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Show error message
            showNotification(data.message || 'Error adding payment. Please try again.', 'error');
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding payment. Please try again.', 'error');
        submitBtn.disabled = false;
    });
});


// Function to show notifications
function showNotification(message, type = 'info') {
    // Remove any existing notifications first
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
