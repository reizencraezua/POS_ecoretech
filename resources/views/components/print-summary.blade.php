<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary Report - Ecoretech POS</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        /* Header Styles */
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 25px;
            margin-bottom: 30px;
            background: #fff;
            padding: 30px 20px;
            border: 2px solid #000;
        }

        .company-name {
            font-size: 28px;
            font-weight: 700;
            color: #000;
            margin-bottom: 8px;
        }

        .report-title {
            font-size: 20px;
            color: #000;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .report-date {
            font-size: 14px;
            color: #000;
            font-style: italic;
        }

        /* Filter Section */
        .filters {
            background: #fff;
            padding: 20px;
            margin-bottom: 25px;
            border: 2px solid #000;
        }

        .filters h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #000;
            font-weight: 600;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 12px;
        }

        .filter-item {
            background: #fff;
            padding: 12px 15px;
            border: 1px solid #000;
        }

        .filter-label {
            font-weight: 600;
            color: #000;
            margin-right: 8px;
        }

        /* Summary Section */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 35px;
        }

        .summary-card {
            text-align: center;
            padding: 15px 0;
        }

        .summary-card h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .summary-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #000;
        }

        /* Payment Methods Section */
        .payment-methods {
            margin-bottom: 35px;
        }

        .payment-methods h3 {
            color: #000;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .method-card {
            text-align: center;
            padding: 15px 0;
            border-bottom: 1px solid #000;
        }

        .method-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .method-name {
            font-weight: 600;
            color: #000;
            font-size: 16px;
        }

        .method-count {
            color: #000;
            font-size: 12px;
        }

        .method-amount {
            color: #000;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .method-percentage {
            color: #000;
            font-size: 13px;
            font-weight: 500;
        }

        /* Payment Status Section */
        .payment-status {
            margin-bottom: 35px;
        }

        .payment-status h3 {
            color: #000;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .status-card {
            text-align: center;
            padding: 15px 0;
            border-bottom: 1px solid #000;
        }

        .status-card.complete {
            border-left: 3px solid #000;
            padding-left: 10px;
        }

        .status-card.partial {
            border-left: 3px solid #000;
            padding-left: 10px;
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .status-name {
            font-weight: 600;
            color: #000;
            font-size: 16px;
        }

        .status-count {
            color: #000;
            font-size: 12px;
        }

        .status-amount {
            color: #000;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .status-percentage {
            color: #000;
            font-size: 13px;
            font-weight: 500;
        }

        /* Table Styles */
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background: white;
            border: 2px solid #000;
        }

        .payments-table th,
        .payments-table td {
            padding: 15px 12px;
            text-align: left;
            border: 1px solid #000;
        }

        .payments-table th {
            background: #000;
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payments-table tr:nth-child(even) {
            background: #fff;
        }

        .payments-table tr:nth-child(odd) {
            background: #f0f0f0;
        }

        /* Badge Styles */
        .status-badge {
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
        }

        .status-complete {
            color: #000;
        }

        .status-partial {
            color: #000;
        }

        .method-badge {
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
        }

        .method-cash { 
            color: #000; 
        }
        .method-gcash { 
            color: #000; 
        }
        .method-bank { 
            color: #000; 
        }
        .method-check { 
            color: #000; 
        }
        .method-card { 
            color: #000; 
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #000;
            font-size: 13px;
            border-top: 2px solid #000;
            padding-top: 25px;
        }

        .footer p {
            margin-bottom: 5px;
        }

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #000;
            margin: 20px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .no-data h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #000;
        }

        .no-data p {
            font-size: 16px;
        }

        /* Print Styles */
        @media print {
            body { 
                margin: 0; 
                padding: 15px;
                font-size: 12px;
            }
            
            .header { 
                page-break-inside: avoid; 
                margin-bottom: 20px;
            }
            
            .summary-cards { 
                page-break-inside: avoid; 
                margin-bottom: 20px;
            }
            
            .payment-methods { 
                page-break-inside: avoid; 
                margin-bottom: 20px;
            }
            
            .payment-status {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }
            
            .payments-table {
                page-break-inside: auto;
            }
            
            .payments-table thead {
                display: table-header-group;
            }
            
            .payments-table tr {
                page-break-inside: avoid;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .summary-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }
            
            .methods-grid,
            .status-grid {
                grid-template-columns: 1fr;
            }
            
            .payments-table {
                font-size: 12px;
            }
            
            .payments-table th,
            .payments-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="company-name">Ecoretech POS System</div>
        <div class="report-title">Payment Summary Report</div>
        <div class="report-date">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
    </div>

    <!-- Filter Summary Section -->
    <div class="filters">
        <h3>Report Filters Applied:</h3>
        <div class="filter-grid">
            @if(isset($filters['Start Date']) || isset($filters['End Date']))
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

    <!-- Summary Statistics Section -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Payment Count</h3>
            <div class="value">{{ $paymentCount }}</div>
        </div>
        <div class="summary-card">
            <h3>Total Amount</h3>
            <div class="value">₱{{ number_format($totalAmount, 2) }}</div>
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


    <!-- Payment Methods Breakdown Section -->
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

    <!-- Payments Table Section -->
    @if($payments->count() > 0)
    <table class="payments-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt #</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td>{{ $payment->receipt_number }}</td>
                    <td>#{{ str_pad($payment->order_id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $payment->order->customer->display_name ?? 'N/A' }}</td>
                    <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                    <td>
                        <span class="method-badge method-{{ strtolower(str_replace(' ', '', $payment->payment_method)) }}">
                            {{ $payment->payment_method }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $payment->balance > 0 ? 'partial' : 'complete' }}">
                            {{ $payment->balance > 0 ? 'Partial' : 'Complete' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <h3>No payments found for the selected filters</h3>
        <p>Please adjust your filter criteria and try again.</p>
    </div>
    @endif

    <!-- Footer Section -->
    <div class="footer">
        <p>This report was generated by the Ecoretech POS System</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>

    <!-- Auto-print Script -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>