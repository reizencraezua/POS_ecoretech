@extends('layouts.admin')

@section('title', 'Payments for Order #'.str_pad($order->order_id, 5, '0', STR_PAD_LEFT))
@section('page-title', 'Order Payments')
@section('page-description', 'View all payments recorded for this order')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Order Information</h3>
                <p class="text-sm text-gray-600">Order #{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }} — {{ $order->customer->display_name }}</p>
            </div>
            <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Back to Order
            </a>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-500">Total Amount</p>
                <p class="font-bold text-maroon">₱{{ number_format($order->total_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Paid</p>
                <p class="font-medium text-gray-900">₱{{ number_format($order->total_paid, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Remaining Balance</p>
                <p class="font-medium text-gray-900">₱{{ number_format($order->remaining_balance, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-medium text-gray-900">{{ $order->order_status }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Payments</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($order->payments as $payment)
                        <tr>
                            <td class="px-4 py-3">{{ $payment->receipt_number }}</td>
                            <td class="px-4 py-3">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $payment->payment_method }}</td>
                            <td class="px-4 py-3">{{ $payment->payment_term }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-maroon">₱{{ number_format($payment->amount_paid, 2) }}</div>
                            </td>
                            <td class="px-4 py-3">₱{{ number_format($payment->change, 2) }}</td>
                            <td class="px-4 py-3">₱{{ number_format($payment->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No payments recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


