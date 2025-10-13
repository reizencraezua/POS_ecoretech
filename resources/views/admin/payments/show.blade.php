@extends('layouts.admin')

@section('title', 'Payment Details')
@section('page-title', 'Payment #' . $payment->payment_id)
@section('page-description', 'View detailed payment information and transaction details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.payments.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Payment #{{ $payment->payment_id }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $payment->payment_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-receipt mr-1"></i>{{ $payment->receipt_number }}</span>
                            <span><i class="fas fa-credit-card mr-1"></i>{{ $payment->payment_method }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">₱{{ number_format($payment->amount_paid, 2) }}</div>
                        <div class="text-sm text-gray-600">Amount Paid</div>
                    </div>
                    @if($payment->change > 0)
                    <div class="text-right">
                        <div class="text-xl font-semibold text-blue-600">₱{{ number_format($payment->change, 2) }}</div>
                        <div class="text-sm text-gray-600">Change</div>
                    </div>
                    @endif
                    @if($payment->balance > 0)
                    <div class="text-right">
                        <div class="text-lg font-semibold text-red-600">-₱{{ number_format($payment->balance, 2) }}</div>
                        <div class="text-sm text-gray-600">Balance</div>
                    </div>
                    @else
                    <div class="text-right">
                        <div class="text-lg font-semibold text-green-600">Complete</div>
                        <div class="text-sm text-gray-600">Payment Status</div>
                    </div>
                    @endif
                    <span class="px-3 py-1 rounded-md text-sm font-medium 
                        @if($payment->balance > 0) bg-red-100 text-red-800 @else bg-green-100 text-green-800 @endif">
                        {{ $payment->balance > 0 ? 'Partial' : 'Complete' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-3 space-y-4">
            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Payment Details</h4>
                                <div class="space-y-2">
                                    <p class="text-gray-900 font-medium">{{ $payment->payment_method }}</p>
                                    <p class="text-sm text-gray-600">{{ $payment->payment_term ?? 'N/A' }}</p>
                                    @if($payment->reference_number && in_array(strtolower($payment->payment_method), ['gcash', 'bank transfer']))
                                    <p class="text-sm text-blue-600">{{ $payment->reference_number }}</p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Receipt Information</h4>
                                <div class="space-y-2">
                                    <p class="text-gray-900 font-medium">{{ $payment->receipt_number }}</p>
                                    <p class="text-sm text-gray-600">{{ $payment->payment_date->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Order Information</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Order Number</span>
                                        <span class="text-sm font-medium text-gray-900">#{{ $payment->order_id }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Customer</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $payment->order->customer->customer_firstname }} {{ $payment->order->customer->customer_lastname }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Order Date</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $payment->order->order_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Payment Summary</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Amount Paid</span>
                                        <span class="text-sm font-medium text-gray-900">₱{{ number_format($payment->amount_paid, 2) }}</span>
                                    </div>
                                    @if($payment->change > 0)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Change</span>
                                        <span class="text-sm font-medium text-blue-600">₱{{ number_format($payment->change, 2) }}</span>
                                    </div>
                                    @endif
                                    @if($payment->balance > 0)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Balance</span>
                                        <span class="text-sm font-medium text-red-600">-₱{{ number_format($payment->balance, 2) }}</span>
                                    </div>
                                    @else
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Status</span>
                                        <span class="text-sm font-medium text-green-600">Complete</span>
                                    </div>
                                    @endif
                                    @php
                                        $deadlineDate = \Carbon\Carbon::parse($payment->order->deadline_date);
                                        $today = \Carbon\Carbon::today();
                                        $daysUntilDeadline = $today->diffInDays($deadlineDate, false);
                                    @endphp
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Deadline</span>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium text-gray-900">{{ $payment->order->deadline_date->format('M d, Y') }}</span>
                                            @if($daysUntilDeadline <= 3 && $daysUntilDeadline >= 0)
                                                <i class="fas fa-exclamation-triangle text-yellow-500 animate-pulse" title="Due in {{ $daysUntilDeadline }} day(s)"></i>
                                            @elseif($daysUntilDeadline < 0)
                                                <i class="fas fa-exclamation-triangle text-red-500 animate-pulse" title="Overdue by {{ abs($daysUntilDeadline) }} day(s)"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Remarks -->
            @if($payment->remarks)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Remarks</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700">{{ $payment->remarks }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('admin.payments.edit', $payment) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Edit Payment
                    </a>
                    <button onclick="printReceipt({{ $payment->payment_id }})" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center">
                        Print Receipt
                    </button>
                    <form action="{{ route('admin.payments.archive', $payment) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 px-3 py-2 rounded text-sm transition-colors inline-flex items-center justify-center"
                                onclick="return confirm('Are you sure you want to archive this payment?')">
                            Archive Payment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Record Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-maroon"></i>
                        Record Information
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Payment ID</p>
                        <p class="text-sm font-mono text-gray-900 bg-gray-50 px-2 py-1 rounded">
                            #{{ $payment->payment_id }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Created</p>
                        <p class="text-sm text-gray-900">{{ $payment->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Last Updated</p>
                        <p class="text-sm text-gray-900">{{ $payment->updated_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-maroon"></i>
                        Payment Status
                    </h3>
                </div>
                <div class="p-4">
                    @if($payment->balance > 0)
                        <div class="text-center">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <p class="text-sm font-medium text-red-600">Partial Payment</p>
                            <p class="text-xs text-gray-500">Balance: -₱{{ number_format($payment->balance, 2) }}</p>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <p class="text-sm font-medium text-green-600">Payment Complete</p>
                            <p class="text-xs text-gray-500">Fully paid</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReceipt(paymentId) {
    window.open(`/admin/payments/${paymentId}/print`, '_blank');
}
</script>
@endsection
