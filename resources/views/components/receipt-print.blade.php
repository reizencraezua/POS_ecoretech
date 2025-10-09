@props(['order' => null, 'payment' => null, 'showPrintButton' => true])

<div x-data="receiptPrint()" class="space-y-4">
    @if($showPrintButton)
    <div class="flex justify-end no-print">
        <button @click="printReceipt()" 
                class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg flex items-center space-x-2">
            <i class="fas fa-print"></i>
            <span>Print Receipt</span>
        </button>
        <button onclick="window.print()" 
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 ml-2">
            <i class="fas fa-print"></i>
            <span>Print Page</span>
        </button>
    </div>
    @endif

    <!-- Receipt Content -->
    <div id="receipt-content" class="bg-white border border-gray-300 rounded-lg p-4 max-w-xs mx-auto" style="width: 80mm; max-width: 80mm;">
        <!-- Header -->
        <div class="text-center border-b border-gray-300 pb-2 mb-2">
            <h1 class="text-lg font-bold text-gray-900">Ecoretech Printing Shop</h1>
            <p class="text-xs text-gray-600">Professional Printing Services</p>
            <p class="text-xs text-gray-500">{{ now()->format('M d, Y - h:i A') }}</p>
        </div>

        @if($order)
        <!-- Order Information -->
        <div class="mb-2">
            <h2 class="font-semibold text-gray-900 mb-1 text-sm">Order Details</h2>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-medium">#{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-medium">{{ $order->customer->display_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ $order->order_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium text-green-600">{{ $order->order_status }}</span>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="mb-2">
            <h3 class="font-semibold text-gray-900 mb-1 text-sm">Items</h3>
            <div class="space-y-2">
                @foreach($order->details as $detail)
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $detail->product ? $detail->product->product_name : $detail->service->service_name }}</p>
                        <p class="text-xs text-gray-600">Qty: {{ $detail->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">₱{{ number_format($detail->unit_price, 2) }}</p>
                        <p class="text-xs text-gray-600">₱{{ number_format($detail->total_price, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Totals -->
        <div class="border-t border-gray-300 pt-2 mb-2">
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">₱{{ number_format($order->subtotal_amount, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Discount:</span>
                    <span class="font-medium text-red-600">-₱{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Tax:</span>
                    <span class="font-medium">₱{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2">
                    <span>Total:</span>
                    <span class="text-maroon">₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($payment)
        <!-- Payment Information -->
        <div class="mb-2">
            <h3 class="font-semibold text-gray-900 mb-1 text-sm">Payment Details</h3>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment ID:</span>
                    <span class="font-medium">#{{ str_pad($payment->payment_id, 5, '0', STR_PAD_LEFT) }}</span>
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
        @endif

        <!-- Footer -->
        <div class="border-t border-gray-300 pt-2 text-center">
            <p class="text-xs text-gray-500 mb-1">Thank you for your business!</p>
            <p class="text-xs text-gray-500">For inquiries, contact us at:</p>
            <p class="text-xs text-gray-500 font-medium">(02) 123-4567 | info@ecoretech.com</p>
        </div>
    </div>
</div>

<script>
function receiptPrint() {
    return {
        printReceipt() {
            try {
                const receiptContent = document.getElementById('receipt-content');
                if (!receiptContent) {
                    alert('Receipt content not found!');
                    return;
                }

                // Create a new window for printing
                const printWindow = window.open('', '_blank', 'width=300,height=600');
                
                if (!printWindow) {
                    alert('Please allow popups for this site to print receipts.');
                    return;
                }

                // Write the HTML content to the new window
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                        <head>
                            <title>Receipt - Ecoretech Printing Shop</title>
                            <meta charset="UTF-8">
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
                                ${receiptContent.innerHTML}
                            </div>
                        </body>
                    </html>
                `);
                
                printWindow.document.close();
                
                // Wait for content to load, then print
                printWindow.onload = function() {
                    setTimeout(() => {
                        printWindow.focus();
                        printWindow.print();
                        
                        // Close the window after printing (optional)
                        setTimeout(() => {
                            printWindow.close();
                        }, 1000);
                    }, 500);
                };
                
            } catch (error) {
                console.error('Print error:', error);
                alert('Error printing receipt: ' + error.message);
            }
        }
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    #receipt-content, #receipt-content * {
        visibility: visible;
    }
    
    #receipt-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm !important;
        max-width: 80mm !important;
        margin: 0 !important;
        padding: 2mm !important;
        font-family: 'Courier New', monospace !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        background: white !important;
    }
    
    .no-print {
        display: none !important;
    }
}
</style>