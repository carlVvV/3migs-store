<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Print</title>
    <style>
        @media print {
            .no-print { display: none; }
            @page { margin: 0.5in; }
        }
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { margin-bottom: 20px; }
        .header-left { float: left; width: 50%; }
        .header-right { float: right; width: 45%; text-align: right; }
        .clear { clear: both; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #2d3748; color: white; padding: 8px; text-align: left; font-weight: bold; border: 1px solid #4a5568; }
        td { padding: 6px; border: 1px solid #cbd5e0; }
        .text-right { text-align: right; }
        .section-title { font-size: 13px; font-weight: bold; margin: 15px 0 8px 0; color: #2d3748; }
        .stats-box { border: 1px solid #cbd5e0; padding: 10px; margin-bottom: 15px; }
        .totals-box { border: 2px solid #2d3748; padding: 10px; margin-top: 20px; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.print();
        });
    </script>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; padding: 10px; background: #f7fafc; border-bottom: 2px solid #cbd5e0;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2d3748; color: white; border: none; cursor: pointer; border-radius: 4px;">Print</button>
    </div>

    <!-- Header Section -->
    <div class="header">
        <div class="header-left">
            <div style="font-weight: bold; font-size: 13px;">COMPANY NAME</div>
            <div>3MIGs E-commerce</div>
            <div style="margin-top: 15px; font-weight: bold; font-size: 13px;">EMPLOYEE</div>
            <div>{{ auth()->user()->name ?? 'Admin' }}</div>
            <div style="font-weight: bold; font-size: 13px;">EMPLOYEE ID</div>
            <div>{{ auth()->user()->id ?? 'N/A' }}</div>
        </div>
        <div class="header-right">
            <div style="font-weight: bold; font-size: 13px;">DATE SUBMITTED</div>
            <div>{{ $salesReport['report_date'] ?? now()->format('m/d/Y') }}</div>
            @if($salesReport['start_date'])
            <div style="margin-top: 15px; font-weight: bold; font-size: 13px;">REPORT BEGINS</div>
            <div>{{ \Carbon\Carbon::parse($salesReport['start_date'])->format('m/d/Y') }}</div>
            @endif
            @if($salesReport['end_date'])
            <div style="font-weight: bold; font-size: 13px;">REPORT ENDS</div>
            <div>{{ \Carbon\Carbon::parse($salesReport['end_date'])->format('m/d/Y') }}</div>
            @endif
        </div>
        <div class="clear"></div>
    </div>

    <!-- Sales Report Title -->
    <div class="section-title">{{ strtoupper($salesReport['format_label'] ?? 'Monthly') }} SALES REPORT</div>
    
    <!-- Sales by Period Table -->
    @if($salesReport['sales_by_period']->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">
                    {{ $salesReport['format'] === 'daily' ? 'DATE' : ($salesReport['format'] === 'weekly' ? 'WEEK' : 'MONTH') }}
                </th>
                <th class="text-right" style="width: 20%;">ORDERS</th>
                <th class="text-right" style="width: 25%;">REVENUE</th>
                <th class="text-right" style="width: 25%;">AVG ORDER VALUE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesReport['sales_by_period'] as $period)
            <tr>
                <td>
                    @if($salesReport['format'] === 'daily')
                        {{ \Carbon\Carbon::parse($period->period)->format('M d, Y') }}
                    @elseif($salesReport['format'] === 'weekly')
                        Week of {{ \Carbon\Carbon::parse($period->period)->format('M d, Y') }}
                    @else
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $period->period)->format('F Y') }}
                    @endif
                </td>
                <td class="text-right">{{ number_format($period->orders_count) }}</td>
                <td class="text-right">₱{{ number_format($period->revenue, 2) }}</td>
                <td class="text-right">₱{{ number_format($period->orders_count > 0 ? ($period->revenue / $period->orders_count) : 0, 2) }}</td>
            </tr>
            @endforeach
            
            <!-- Totals Row -->
            <tr style="background-color: #f7fafc; font-weight: bold;">
                <td>TOTAL</td>
                <td class="text-right">{{ number_format($salesReport['total_orders']) }}</td>
                <td class="text-right">₱{{ number_format($salesReport['total_revenue'], 2) }}</td>
                <td class="text-right">₱{{ number_format($salesReport['average_order_value'], 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Key Statistics -->
    <div class="stats-box">
        <div class="section-title" style="margin-top: 0;">KEY STATISTICS</div>
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 33%;"><strong>TOTAL REVENUE:</strong> ₱{{ number_format($salesReport['total_revenue'], 2) }}</td>
                <td style="border: none; width: 33%;"><strong>TOTAL ORDERS:</strong> {{ number_format($salesReport['total_orders']) }}</td>
                <td style="border: none;"><strong>AVG ORDER VALUE:</strong> ₱{{ number_format($salesReport['average_order_value'], 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Top Customers -->
    @if($salesReport['top_customers']->count() > 0)
    <div class="section-title">TOP CUSTOMERS</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">CUSTOMER</th>
                <th style="width: 35%;">EMAIL</th>
                <th class="text-right" style="width: 12%;">ORDERS</th>
                <th class="text-right" style="width: 13%;">TOTAL SPENT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesReport['top_customers'] as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td class="text-right">{{ $customer->orders_count ?? 0 }}</td>
                <td class="text-right">₱{{ number_format($customer->total_spent ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Best Selling Products -->
    @if($salesReport['best_sellers']->count() > 0)
    <div class="section-title">BEST SELLING PRODUCTS</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">PRODUCT</th>
                <th class="text-right" style="width: 25%;">QUANTITY SOLD</th>
                <th class="text-right" style="width: 25%;">TOTAL SALES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesReport['best_sellers'] as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td class="text-right">{{ number_format($product->total_qty) }}</td>
                <td class="text-right">₱{{ number_format($product->total_sales, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div style="margin-top: 20px; font-size: 9px; color: #718096; text-align: center;">
        Report generated on {{ now()->format('m/d/Y H:i:s') }} | Format: {{ $salesReport['format_label'] ?? 'Monthly' }}
    </div>
</body>
</html>
