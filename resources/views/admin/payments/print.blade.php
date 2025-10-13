<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->receipt_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
            padding: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.2;
            width: 80mm;
            max-width: 80mm;
            margin: 0;
            padding: 2mm;
            background: white;
        }
        
        .receipt-content {
            width: 100%;
            max-width: 80mm;
            margin: 0;
            padding: 0;
        }
        
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }
        .text-lg { font-size: 12px; }
        .text-xl { font-size: 13px; }
        .text-sm { font-size: 9px; }
        .text-xs { font-size: 8px; }
        .text-gray-900 { color: #111827; }
        .text-gray-600 { color: #4B5563; }
        .text-gray-500 { color: #6B7280; }
        .text-maroon { color: #800020; }
        .text-green-600 { color: #059669; }
        .text-red-600 { color: #DC2626; }
        .border-b { border-bottom: 1px dashed #000; }
        .border-t { border-top: 1px dashed #000; }
        .border-gray-300 { border-color: #000; }
        .pb-4 { padding-bottom: 6px; }
        .pt-4 { padding-top: 6px; }
        .pt-2 { padding-top: 3px; }
        .mb-4 { margin-bottom: 6px; }
        .mb-2 { margin-bottom: 3px; }
        .space-y-1 > * + * { margin-top: 1px; }
        .space-y-2 > * + * { margin-top: 2px; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-start { align-items: flex-start; }
        .flex-1 { flex: 1; }
        .text-right { text-align: right; }
        .rounded-lg { border-radius: 0; }
        .receipt-content { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="receipt-content">
        <!-- Header -->
        <div class="text-center border-b border-gray-300 pb-2 mb-2">
            <h1 class="text-lg font-bold text-gray-900">Ecoretech Printing Shop</h1>
            <p class="text-xs text-gray-600">Professional Printing Services</p>
            <p class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y - h:i A') }}</p>
        </div>

        <!-- Payment Information -->
        <div class="mb-2">
            <h2 class="font-semibold text-gray-900 mb-1 text-sm">Payment Details</h2>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Receipt #:</span>
                    <span class="font-medium">{{ $payment->receipt_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order #:</span>
                    <span class="font-medium">#{{ str_pad($payment->order->order_id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-medium">{{ $payment->order->customer->display_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="font-medium text-green-600">₱{{ number_format($payment->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-medium">{{ $payment->payment_method }}</span>
                </div>
                @if($payment->reference_number)
                <div class="flex justify-between">
                    <span class="text-gray-600">Reference:</span>
                    <span class="font-medium">{{ $payment->reference_number }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ $payment->payment_date->format('M d, Y - h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="mb-2">
            <h3 class="font-semibold text-gray-900 mb-1 text-sm">Order Summary</h3>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-medium">₱{{ number_format($payment->order->final_total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Previous Balance:</span>
                    <span class="font-medium text-red-600">-₱{{ number_format($payment->order->final_total_amount - ($payment->order->total_paid - $payment->amount_paid), 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="font-medium text-green-600">₱{{ number_format($payment->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Paid:</span>
                    <span class="font-medium">₱{{ number_format($payment->order->total_paid, 2) }}</span>
                </div>
                @if($payment->change > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Change:</span>
                    <span class="font-medium text-green-600">₱{{ number_format($payment->change, 2) }}</span>
                </div>
                @endif
                @if($payment->balance > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Remaining Balance:</span>
                    <span class="font-medium text-red-600">-₱{{ number_format($payment->balance, 2) }}</span>
                </div>
                @else
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium text-green-600">Fully Paid</span>
                </div>
                @endif
            </div>
        </div>

        @if($payment->remarks)
        <!-- Remarks -->
        <div class="mb-2">
            <h3 class="font-semibold text-gray-900 mb-1 text-sm">Remarks</h3>
            <div class="text-sm text-gray-600 bg-gray-50 p-2 rounded">
                {{ $payment->remarks }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="border-t border-gray-300 pt-2 text-center">
            <p class="text-xs text-gray-500 mb-1">Thank you for your business!</p>
            <p class="text-xs text-gray-500">For inquiries, contact us at:</p>
            <p class="text-xs text-gray-500 font-medium">(02) 123-4567 | info@ecoretech.com</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>