@extends('layouts.cashier')

@section('title', 'Order Details')
@section('page-title', 'Order Details')
@section('page-description', 'View detailed information about the job order')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Orders
    </a>
    @if($order->order_status !== 'Completed' && $order->order_status !== 'Cancelled' && $order->order_status !== 'Voided')
    <button onclick="openVoidModal({{ $order->order_id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
    <i class="fas fa-ban mr-2"></i>Void Order
    </button>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-sm text-gray-500">Created {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                
                
                <!-- Status Update (if not completed/cancelled) -->
                @if($order->order_status !== 'Completed' && $order->order_status !== 'Cancelled' && $order->order_status !== 'Voided')
                    <form id="statusUpdateForm" method="POST" action="{{ route('cashier.orders.status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center space-x-2">
                            <select name="order_status" id="statusSelect" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                <option value="">Select Status</option>
                                <option value="{{ \App\Models\Order::STATUS_ON_PROCESS }}" {{ $order->order_status === \App\Models\Order::STATUS_ON_PROCESS ? 'selected' : '' }}>On-Process</option>
                                <option value="{{ \App\Models\Order::STATUS_DESIGNING }}" {{ $order->order_status === \App\Models\Order::STATUS_DESIGNING ? 'selected' : '' }}>Designing</option>
                                <option value="{{ \App\Models\Order::STATUS_PRODUCTION }}" {{ $order->order_status === \App\Models\Order::STATUS_PRODUCTION ? 'selected' : '' }}>Production</option>
                                <option value="{{ \App\Models\Order::STATUS_FOR_RELEASING }}" {{ $order->order_status === \App\Models\Order::STATUS_FOR_RELEASING ? 'selected' : '' }}>For Releasing</option>
                                <option value="{{ \App\Models\Order::STATUS_COMPLETED }}" {{ $order->order_status === \App\Models\Order::STATUS_COMPLETED ? 'selected' : '' }}>Completed</option>
                                <option value="{{ \App\Models\Order::STATUS_CANCELLED }}" {{ $order->order_status === \App\Models\Order::STATUS_CANCELLED ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </form>
                @endif

                <!-- Status Badge -->
                <span id="statusBadge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @switch($order->order_status)
                        @case('On-Process')
                            bg-blue-100 text-blue-800
                            @break
                        @case('Designing')
                            bg-purple-100 text-purple-800
                            @break
                        @case('Production')
                            bg-yellow-100 text-yellow-800
                            @break
                        @case('For Releasing')
                            bg-orange-100 text-orange-800
                            @break
                        @case('Completed')
                            bg-green-100 text-green-800
                            @break
                        @case('Cancelled')
                            bg-red-100 text-red-800
                            @break
                        @case('Voided')
                            bg-gray-100 text-gray-800
                            @break
                        @default
                            bg-gray-100 text-gray-800
                    @endswitch">
                    {{ $order->order_status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Customer & Employee Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-maroon"></i>
                Customer Information
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Name</label>
                    <p class="text-sm text-gray-900">{{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}</p>
                </div>
                @if($order->customer->business_name)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Business Name</label>
                        <p class="text-sm text-gray-900">{{ $order->customer->business_name }}</p>
                    </div>
                @endif
                @if($order->customer->contact_number1)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Contact Number</label>
                        <p class="text-sm text-gray-900">{{ $order->customer->contact_number1 }}</p>
                    </div>
                @endif
                @if($order->customer->email)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-sm text-gray-900">{{ $order->customer->email }}</p>
                    </div>
                @endif
                @if($order->customer->address)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Address</label>
                        <p class="text-sm text-gray-900">{{ $order->customer->address }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Employee & Order Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-briefcase mr-2 text-maroon"></i>
                Order Information
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Assigned Employee</label>
                    <p class="text-sm text-gray-900">{{ $order->employee->employee_firstname }} {{ $order->employee->employee_lastname }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Order Date</label>
                    <p class="text-sm text-gray-900">{{ $order->order_date->format('M d, Y') }}</p>
                </div>
                @if($order->delivery_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Delivery Date</label>
                        <p class="text-sm text-gray-900">{{ $order->delivery_date->format('M d, Y') }}</p>
                    </div>
                @endif
                <div>
                    <label class="text-sm font-medium text-gray-500">Created By</label>
                    <p class="text-sm text-gray-900">
                        @if($order->creator)
                            @if($order->creator->employee)
                                EMP{{ $order->creator->employee->employee_id }} : {{ $order->creator->employee->employee_firstname }}
                            @else
                                {{ $order->creator->name }}
                            @endif
                        @else
                            Admin
                        @endif
                    </p>
                </div>
                @if($order->order_status === 'Voided')
                    <div>
                        <label class="text-sm font-medium text-gray-500">Voided By</label>
                        <p class="text-sm text-gray-900">
                            @if($order->voidedBy)
                                @if($order->voidedBy->employee)
                                    EMP{{ $order->voidedBy->employee->employee_id }} : {{ $order->voidedBy->employee->employee_firstname }}
                                @else
                                    {{ $order->voidedBy->name }}
                                @endif
                            @else
                                Admin
                            @endif
                        </p>
                    </div>
                    @if($order->voided_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Voided At</label>
                            <p class="text-sm text-gray-900">{{ $order->voided_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                    @if($order->void_reason)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Void Reason</label>
                            <p class="text-sm text-gray-900">{{ $order->void_reason }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-list mr-2 text-maroon"></i>
            Order Items
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->details as $detail)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                @if($detail->product)
                                    {{ $detail->product->product_name }}
                                @elseif($detail->service)
                                    {{ $detail->service->service_name }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($detail->product) bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                @if($detail->product) Product @else Service @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $detail->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $detail->unit ? $detail->unit->unit_name : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $detail->size ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₱{{ number_format($detail->price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($detail->layout)
                                <span class="text-green-600">
                                    <i class="fas fa-check mr-1"></i>
                                    ₱{{ number_format($detail->layout_price, 2) }}
                                </span>
                            @else
                                <span class="text-gray-400">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₱{{ number_format($detail->subtotal, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calculator mr-2 text-maroon"></i>
            Order Summary
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Subtotal:</span>
                    <span class="text-sm text-gray-900">₱{{ number_format($order->sub_total, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">VAT (12%):</span>
                    <span class="text-sm text-gray-900">₱{{ number_format($order->vat_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Base Amount:</span>
                    <span class="text-sm text-gray-900">₱{{ number_format($order->base_amount, 2) }}</span>
                </div>
                @if($order->order_discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Discount:</span>
                        <span class="text-sm text-green-600">-₱{{ number_format($order->order_discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($order->layout_fees > 0)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Layout Fees:</span>
                        <span class="text-sm text-gray-900">₱{{ number_format($order->layout_fees, 2) }}</span>
                    </div>
                @endif
            </div>
            <div class="space-y-3">
                <div class="border-t pt-3">
                    <div class="flex justify-between">
                        <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                        <span class="text-lg font-bold text-maroon">₱{{ number_format($order->final_total_amount, 2) }}</span>
                    </div>
                </div>
                @if($order->payments->count() > 0)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Total Paid:</span>
                        <span class="text-sm text-green-600">₱{{ number_format($order->total_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Remaining Balance:</span>
                        <span class="text-sm {{ $order->remaining_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₱{{ number_format($order->remaining_balance, 2) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payments -->
    @if($order->payments->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-credit-card mr-2 text-maroon"></i>
            Payment History
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->payment_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->payment_method }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->payment_term }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₱{{ number_format($payment->amount_paid, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->reference_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->remarks ?? 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelect');
    const statusBadge = document.getElementById('statusBadge');
    
    if (statusSelect && statusBadge) {
        // Handle status change with immediate visual feedback
        statusSelect.addEventListener('change', function() {
            const currentStatus = '{{ $order->order_status }}';
            
            // Only proceed if status is different and not empty
            if (this.value !== '' && this.value !== currentStatus) {
                // Update status badge immediately for better UX
                updateStatusBadge(this.value);
                
                // Update status immediately
                this.disabled = true;
            }
        });
    }
    
    function updateStatusBadge(status) {
        // Remove all existing classes
        statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium';
        
        // Add appropriate classes based on status
        switch(status) {
            case '{{ \App\Models\Order::STATUS_ON_PROCESS }}':
                statusBadge.classList.add('bg-blue-100', 'text-blue-800');
                break;
            case '{{ \App\Models\Order::STATUS_DESIGNING }}':
                statusBadge.classList.add('bg-purple-100', 'text-purple-800');
                break;
            case '{{ \App\Models\Order::STATUS_PRODUCTION }}':
                statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                break;
            case '{{ \App\Models\Order::STATUS_FOR_RELEASING }}':
                statusBadge.classList.add('bg-orange-100', 'text-orange-800');
                break;
            case '{{ \App\Models\Order::STATUS_COMPLETED }}':
                statusBadge.classList.add('bg-green-100', 'text-green-800');
                break;
            case '{{ \App\Models\Order::STATUS_CANCELLED }}':
                statusBadge.classList.add('bg-red-100', 'text-red-800');
                break;
            case 'Voided':
                statusBadge.classList.add('bg-gray-100', 'text-gray-800');
                break;
            default:
                statusBadge.classList.add('bg-gray-100', 'text-gray-800');
        }
        
        // Update the text content
        statusBadge.textContent = status;
    }
});
</script>

<!-- Void Order Confirmation Modal -->
<div id="voidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-4">Void Order Confirmation</h3>
            <p class="text-sm text-gray-500 text-center mb-6">This action will permanently void the order. This cannot be undone.</p>
            
            <form id="voidForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <!-- Admin Password -->
                    <div>
                        <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">System Administrator Password *</label>
                        <input type="password" name="admin_password" id="admin_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter system administrator password">
                        <p class="text-xs text-gray-500 mt-1">Enter the system administrator's password to void this order</p>
                    </div>
                    
                    <!-- Void Reason -->
                    <div>
                        <label for="void_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Voiding *</label>
                        <textarea name="void_reason" id="void_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Enter reason for voiding this order"></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <button type="button" onclick="closeVoidModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        <i class="fas fa-ban mr-2"></i>Void Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openVoidModal(orderId) {
    document.getElementById('voidForm').action = '{{ route("cashier.orders.void", ":id") }}'.replace(':id', orderId);
    document.getElementById('voidModal').classList.remove('hidden');
    document.getElementById('admin_password').focus();
}

function closeVoidModal() {
    document.getElementById('voidModal').classList.add('hidden');
    document.getElementById('voidForm').reset();
}

// Close modal when clicking outside
document.getElementById('voidModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVoidModal();
    }
});
</script>
@endsection
