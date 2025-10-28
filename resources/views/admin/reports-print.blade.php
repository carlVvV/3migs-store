<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report - Print</title>
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
        .notes-box { border: 1px solid #cbd5e0; padding: 10px; min-height: 80px; margin-top: 15px; }
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
            <div>{{ now()->format('m/d/Y') }}</div>
            <div style="margin-top: 15px; font-weight: bold; font-size: 13px;">REPORT BEGINS</div>
            <div>{{ $salesReport['report_start_date']->format('m/d/Y') }}</div>
            <div style="font-weight: bold; font-size: 13px;">REPORT ENDS</div>
            <div>{{ $salesReport['report_end_date']->format('m/d/Y') }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Daily Sales Report -->
    <div class="section-title">DAILY SALES REPORT</div>
    
    <!-- Sales by Category/Product -->
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">CATEGORY</th>
                @foreach($salesReport['daily_sales'] as $day)
                    <th class="text-right">{{ $day['day_name'] }}<br>{{ \Carbon\Carbon::parse($day['date'])->format('m/d/y') }}</th>
                @endforeach
                <th class="text-right">WEEKLY TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesReport['product_sales'] as $product)
            <tr>
                <td>{{ $product['category_name'] ?? ($product->category_name ?? 'Uncategorized') }}</td>
                @foreach($salesReport['daily_sales'] as $idx => $day)
                    @php
                        $val = is_array($product) ? ($product['daily'][$idx] ?? 0) : 0;
                    @endphp
                    <td class="text-right">₱{{ number_format($val, 2) }}</td>
                @endforeach
                <td class="text-right"><strong>₱{{ number_format(is_array($product) ? ($product['total_sales'] ?? 0) : ($product->total_sales ?? 0), 2) }}</strong></td>
            </tr>
            @endforeach
            
            <!-- Daily Totals Row -->
            <tr style="background-color: #f7fafc; font-weight: bold;">
                <td>DAILY TOTALS</td>
                @foreach($salesReport['daily_sales'] as $day)
                    <td class="text-right">₱{{ number_format($day['revenue'], 2) }}</td>
                @endforeach
                <td class="text-right">₱{{ number_format(array_sum(array_column($salesReport['daily_sales'], 'revenue')), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Key Statistics -->
    <div class="stats-box">
        <div class="section-title" style="margin-top: 0;">KEY STATISTICS</div>
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 33%;"><strong>TOTAL ORDERS:</strong> {{ number_format($salesReport['total_orders']) }}</td>
                <td style="border: none; width: 33%;"><strong>TOTAL PRODUCTS:</strong> {{ number_format($salesReport['total_products']) }}</td>
                <td style="border: none;"><strong>TOTAL CUSTOMERS:</strong> {{ number_format($salesReport['total_customers']) }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>AVERAGE ORDER VALUE:</strong> ₱{{ number_format($salesReport['total_orders'] > 0 ? ($salesReport['total_revenue'] / $salesReport['total_orders']) : 0, 2) }}</td>
                <td style="border: none;"></td>
                <td style="border: none;"></td>
            </tr>
        </table>
    </div>

    <!-- Notes Section -->
    <div class="notes-box">
        <div style="font-weight: bold; margin-bottom: 5px;">NOTES</div>
        <div style="min-height: 60px; color: #718096;">User to enter fields manually based upon prior data.</div>
    </div>

    <!-- Totals Section -->
    <div class="totals-box">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 50%; font-weight: bold;">MONTH-TO-DATE:</td>
                <td style="border: none; text-align: right; font-weight: bold;">₱{{ number_format($salesReport['month_to_date'], 2) }}</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">BUDGET AND VARIANCE:</td>
                <td style="border: none; text-align: right; font-weight: bold;">₱{{ number_format($salesReport['previous_month'], 2) }}</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">YEAR-TO-DATE:</td>
                <td style="border: none; text-align: right; font-weight: bold;">₱{{ number_format($salesReport['year_to_date'], 2) }}</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">PREVIOUS PERIOD:</td>
                <td style="border: none; text-align: right; font-weight: bold;">₱{{ number_format($salesReport['previous_month'], 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 20px; font-size: 9px; color: #718096; text-align: center;">
        Report generated on {{ now()->format('m/d/Y H:i:s') }}
    </div>
</body>
</html>