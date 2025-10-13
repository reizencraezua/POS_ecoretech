<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary Report - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #8B1538;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #8B1538;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        .report-date {
            font-size: 14px;
            color: #888;
        }
        .admin-badge {
            display: inline-block;
            background: #8B1538;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .filters {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
        }
        .filter-item {
            display: block;
            margin-bottom: 8px;
            padding: 8px;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #8B1538;
        }
        .filter-label {
            font-weight: bold;
            color: #8B1538;
        }
        .summary-cards {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .summary-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            min-width: 150px;
            margin: 5px;
            border-left: 4px solid #8B1538;
        }
        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #8B1538;
        }
        .payment-methods {
            margin-bottom: 30px;
        }
        .payment-methods h3 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .method-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #8B1538;
            text-align: center;
        }
        .method-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .method-name {
            font-weight: bold;
            color: #333;
        }
        .method-count {
            color: #666;
            font-size: 12px;
        }
        .method-amount {
            color: #8B1538;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 4px;
        }
        .method-percentage {
            color: #666;
            font-size: 12px;
        }
        .payment-status {
            margin-bottom: 30px;
        }
        .payment-status h3 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .status-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .status-card.complete {
            border-left: 4px solid #28a745;
        }
        .status-card.partial {
            border-left: 4px solid #dc3545;
        }
        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .status-name {
            font-weight: bold;
            color: #333;
        }
        .status-count {
            color: #666;
            font-size: 12px;
        }
        .status-amount {
            color: #8B1538;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 4px;
        }
        .status-percentage {
            color: #666;
            font-size: 12px;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .payments-table th,
        .payments-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .payments-table th {
            background: #8B1538;
            color: white;
            font-weight: bold;
        }
        .payments-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-complete {
            background: #d4edda;
            color: #155724;
        }
        .status-partial {
            background: #f8d7da;
            color: #721c24;
        }
        .method-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .method-cash { background: #d4edda; color: #155724; }
        .method-gcash { background: #cce5ff; color: #004085; }
        .method-bank { background: #e2e3f0; color: #383d41; }
        .method-check { background: #fff3cd; color: #856404; }
        .method-card { background: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            body { margin: 0; }
            .header { page-break-inside: avoid; }
            .summary-cards { page-break-inside: avoid; }
            .payment-methods { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Ecoretech POS System <span class="admin-badge">ADMIN</span></div>
        <div class="report-title">Payment Summary Report</div>
        <div class="report-date">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
    </div>

    <!-- Filter Summary -->
    <div class="filters">
        <h3>Report Filters Applied:</h3>
        <div class="filter-grid">
            @if(isset($filters['Date Range']))
                <div class="filter-item">
                    <span class="filter-label">Date Range:</span> {{ $filters['Date Range'] }}
                </div>
            @elseif(isset($filters['Start Date']) || isset($filters['End Date']))
                <div class="filter-item">
                    <span class="filter-label">Date Range:</span> 
                    {{ isset($filters['Start Date']) ? $filters['Start Date'] : 'All Time' }} 
                    @if(isset($filters['End Date']) && isset($filters['Start Date']))
                        to {{ $filters['End Date'] }}
                    @elseif(isset($filters['End Date']))
                        to {{ $filters['End Date'] }}
                    @endif
                </div>
            @endif
            @if(isset($filters['Payment Method']))
                <div class="filter-item">
                    <span class="filter-label">Payment Method:</span> {{ $filters['Payment Method'] }}
                </div>
            @endif
            @if(isset($filters['Payment Status']))
                <div class="filter-item">
                    <span class="filter-label">Payment Status:</span> {{ $filters['Payment Status'] }}
                </div>
            @endif
            @if(count($filters) == 0)
                <div class="filter-item">
                    <span class="filter-label">No filters applied - showing all payments</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Amount</h3>
            <div class="value">₱{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="summary-card">
            <h3>Payment Count</h3>
            <div class="value">{{ $paymentCount }}</div>
        </div>
        <div class="summary-card">
            <h3>Average Payment</h3>
            <div class="value">₱{{ number_format($averagePayment, 2) }}</div>
        </div>
        <div class="summary-card">
            <h3>Complete Payments</h3>
            <div class="value">{{ $payments->where('balance', 0)->count() }}</div>
        </div>
        <div class="summary-card">
            <h3>Partial Payments</h3>
            <div class="value">{{ $payments->where('balance', '>', 0)->count() }}</div>
        </div>
    </div>

    @if($paymentMethods->count() > 0)
    <div class="payment-methods">
        <h3>Payment Methods Breakdown</h3>
        <div class="methods-grid">
            @foreach($paymentMethods as $method)
                <div class="method-card">
                    <div class="method-header">
                        <span class="method-name">{{ $method['method'] }}</span>
                        <span class="method-count">{{ $method['count'] }} payments</span>
                    </div>
                    <div class="method-amount">₱{{ number_format($method['amount'], 2) }}</div>
                    <div class="method-percentage">{{ $method['percentage'] }}% of total</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Payment Status Breakdown -->
    <div class="payment-status">
        <h3>Payment Status Breakdown</h3>
        <div class="status-grid">
            <div class="status-card complete">
                <div class="status-header">
                    <span class="status-name">Complete Payments</span>
                    <span class="status-count">{{ $payments->where('balance', 0)->count() }} payments</span>
                </div>
                <div class="status-amount">₱{{ number_format($payments->where('balance', 0)->sum('amount_paid'), 2) }}</div>
                <div class="status-percentage">{{ $paymentCount > 0 ? round(($payments->where('balance', 0)->count() / $paymentCount) * 100, 1) : 0 }}% of total</div>
            </div>
            <div class="status-card partial">
                <div class="status-header">
                    <span class="status-name">Partial Payments</span>
                    <span class="status-count">{{ $payments->where('balance', '>', 0)->count() }} payments</span>
                </div>
                <div class="status-amount">₱{{ number_format($payments->where('balance', '>', 0)->sum('amount_paid'), 2) }}</div>
                <div class="status-percentage">{{ $paymentCount > 0 ? round(($payments->where('balance', '>', 0)->count() / $paymentCount) * 100, 1) : 0 }}% of total</div>
            </div>
        </div>
    </div>

    @if($payments->count() > 0)
    <table class="payments-table">
        <thead>
            <tr>
                <th>Receipt #</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Method</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->receipt_number }}</td>
                    <td>#{{ str_pad($payment->order_id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $payment->order->customer->display_name ?? 'N/A' }}</td>
                    <td>
                        <span class="method-badge method-{{ strtolower(str_replace(' ', '', $payment->payment_method)) }}">
                            {{ $payment->payment_method }}
                        </span>
                    </td>
                    <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $payment->balance > 0 ? 'partial' : 'complete' }}">
                            {{ $payment->balance > 0 ? 'Partial' : 'Complete' }}
                        </span>
                    </td>
                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; color: #666;">
        <h3>No payments found for the selected filters</h3>
        <p>Please adjust your filter criteria and try again.</p>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated by the Ecoretech POS System - Admin Panel</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
