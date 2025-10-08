<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10mm;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .customer-info {
            margin-bottom: 15px;
            padding: 8px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .customer-name {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .customer-details {
            font-size: 10px;
            color: #666;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .items-table th,
        .items-table td {
            padding: 4px 2px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        
        .items-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table .qty {
            text-align: center;
            width: 15%;
        }
        
        .items-table .item {
            width: 45%;
        }
        
        .items-table .price {
            text-align: right;
            width: 20%;
        }
        
        .items-table .total {
            text-align: right;
            width: 20%;
        }
        
        .totals {
            margin-top: 15px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .payment-info {
            margin-top: 15px;
            padding: 8px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .print-date {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 5mm;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div class="company-name">ECORETECH PRINTING SERVICES</div>
        <div class="company-address">123 Business Street, City, Province 1234</div>
        <div class="company-address">Tel: (02) 123-4567 | Email: info@ecoretech.com</div>
        <div class="receipt-title">OFFICIAL RECEIPT</div>
    </div>
    
    <div class="receipt-info">
        <div>
            <strong>Receipt #:</strong> {{ $payment->receipt_number }}
        </div>
        <div>
            <strong>Date:</strong> {{ $payment->payment_date->format('M d, Y') }}
        </div>
    </div>
    
    <div class="customer-info">
        <div class="customer-name">{{ $payment->order->customer->display_name }}</div>
        <div class="customer-details">
            @if($payment->order->customer->business_name)
                {{ $payment->order->customer->business_name }}<br>
            @endif
            @if($payment->order->customer->contact_number1)
                Tel: {{ $payment->order->customer->contact_number1 }}<br>
            @endif
            @if($payment->order->customer->customer_address)
                {{ $payment->order->customer->customer_address }}
            @endif
        </div>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th class="qty">Qty</th>
                <th class="item">Item Description</th>
                <th class="price">Unit Price</th>
                <th class="total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payment->order->details as $detail)
            <tr>
                <td class="qty">{{ $detail->quantity }}</td>
                <td class="item">
                    @if($detail->product)
                        {{ $detail->product->product_name }}
                    @elseif($detail->service)
                        {{ $detail->service->service_name }}
                    @endif
                    @if($detail->size)
                        ({{ $detail->size }})
                    @endif
                    @if($detail->layout)
                        <br><small>+ Layout Design</small>
                    @endif
                </td>
                <td class="price">₱{{ number_format($detail->price, 2) }}</td>
                <td class="total">₱{{ number_format($detail->subtotal, 2) }}</td>
            </tr>
            @if($detail->layout && $detail->layout_price > 0)
            <tr>
                <td class="qty">1</td>
                <td class="item">Layout Design Fee</td>
                <td class="price">₱{{ number_format($detail->layout_price, 2) }}</td>
                <td class="total">₱{{ number_format($detail->layout_price, 2) }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    
    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>₱{{ number_format($payment->order->sub_total, 2) }}</span>
        </div>
        <div class="total-row">
            <span>VAT (12%):</span>
            <span>₱{{ number_format($payment->order->vat_amount, 2) }}</span>
        </div>
        @if($payment->order->order_discount_amount > 0)
        <div class="total-row">
            <span>Discount:</span>
            <span>-₱{{ number_format($payment->order->order_discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="total-row final">
            <span>TOTAL AMOUNT:</span>
            <span>₱{{ number_format($payment->order->final_total_amount, 2) }}</span>
        </div>
    </div>
    
    <div class="payment-info">
        <div class="payment-row">
            <span><strong>Payment Method:</strong></span>
            <span>{{ $payment->payment_method }}</span>
        </div>
        <div class="payment-row">
            <span><strong>Payment Term:</strong></span>
            <span>{{ $payment->payment_term ?? 'N/A' }}</span>
        </div>
        <div class="payment-row">
            <span><strong>Amount Paid:</strong></span>
            <span>₱{{ number_format($payment->amount_paid, 2) }}</span>
        </div>
        @if($payment->change > 0)
        <div class="payment-row">
            <span><strong>Change:</strong></span>
            <span>₱{{ number_format($payment->change, 2) }}</span>
        </div>
        @endif
        @if($payment->balance > 0)
        <div class="payment-row">
            <span><strong>Remaining Balance:</strong></span>
            <span>₱{{ number_format($payment->balance, 2) }}</span>
        </div>
        @endif
        @if($payment->reference_number)
        <div class="payment-row">
            <span><strong>Reference #:</strong></span>
            <span>{{ $payment->reference_number }}</span>
        </div>
        @endif
    </div>
    
    @if($payment->remarks)
    <div style="margin-top: 10px; padding: 5px; background: #f0f0f0; font-size: 10px;">
        <strong>Remarks:</strong> {{ $payment->remarks }}
    </div>
    @endif
    
    <div class="footer">
        <div class="print-date">Printed on: {{ now()->format('M d, Y g:i A') }}</div>
        <div>Thank you for your business!</div>
        <div style="margin-top: 10px; font-size: 9px;">
            This is a computer-generated receipt.<br>
            No signature required.
        </div>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
