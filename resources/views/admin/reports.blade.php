@extends('layouts.admin')

@section('title', 'Reports - Admin Dashboard')
@section('page-title', 'Reports')

@section('content')
<div class="space-y-6">
    <!-- Reports Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900">₱{{ number_format($salesReport['total_revenue'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $salesReport['total_orders'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Order Value</dt>
                            <dd class="text-lg font-medium text-gray-900">₱{{ number_format($salesReport['average_order_value'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-orange-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $salesReport['total_customers'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Filters -->
    @include('admin.report-filters')

    <!-- Print & Export Toolbar -->
    <div class="flex items-center justify-end gap-3 mb-6">
        <a href="/admin/reports/export" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm">
            <i class="fas fa-download mr-2"></i>
            Export CSV
        </a>
        <a href="/admin/reports/print{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" target="_blank"
           class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition-colors text-sm">
            <i class="fas fa-print mr-2"></i>
            Print
        </a>
    </div>

    <!-- Sales Chart -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                @php
                    $formatLabels = [
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly'
                    ];
                    $currentFormat = $salesReport['format'] ?? 'monthly';
                    $formatLabel = $formatLabels[$currentFormat] ?? 'Monthly';
                @endphp
                <h3 class="text-lg leading-6 font-medium text-gray-900">Sales & Orders ({{ $formatLabel }})</h3>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-green-100 text-green-800 text-xs font-medium">Revenue: ₱{{ number_format($salesReport['total_revenue'], 2) }}</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-100 text-blue-800 text-xs font-medium">Orders: {{ number_format($salesReport['total_orders']) }}</span>
                </div>
            </div>
            <div class="relative" style="height: 320px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function(){
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;
            const data = @json($salesReport['sales_by_period']);
            const format = @json($salesReport['format'] ?? 'monthly');
            
            // Format labels based on format type
            const labels = data.map(item => {
                const period = item.period;
                if (format === 'daily') {
                    // Format: YYYY-MM-DD -> MMM DD, YYYY
                    const date = new Date(period + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                } else if (format === 'weekly') {
                    // Format: YYYY-MM-DD (week start) -> Week of MMM DD, YYYY
                    const date = new Date(period + 'T00:00:00');
                    return 'Week of ' + date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                } else {
                    // Format: YYYY-MM -> MMM YYYY
                    const [year, month] = period.split('-');
                    const date = new Date(year, month - 1, 1);
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }
            });
            
            const revenues = data.map(m => Number(m.revenue));
            const orders = data.map(m => Number(m.orders_count));
            const maxOrders = orders.length ? Math.max(...orders) : 0;
            const step = Math.max(1, Math.round(maxOrders / 5));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Revenue (₱)',
                            data: revenues,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            yAxisID: 'y',
                            borderWidth: 1,
                        },
                        {
                            label: 'Orders',
                            data: orders,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            yAxisID: 'y1',
                            borderWidth: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            ticks: {
                                callback: (val) => '₱' + Number(val).toLocaleString(),
                            },
                            grid: { drawOnChartArea: true }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { precision: 0, stepSize: step }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: {
                                maxRotation: format === 'daily' ? 45 : 0,
                                minRotation: format === 'daily' ? 45 : 0
                            }
                        }
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            callbacks: {
                                label: function(ctx){
                                    if (ctx.dataset.label.includes('Revenue')) {
                                        return ctx.dataset.label + ': ₱' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                    }
                                    return ctx.dataset.label + ': ' + ctx.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>

    <!-- Top Customers -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Top Customers</h3>
            @if($salesReport['top_customers']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesReport['top_customers'] as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $customer->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $customer->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $customer->orders_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($customer->total_spent ?? 0, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No customer data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sales Trend Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @php
                $formatLabels = [
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly'
                ];
                $currentFormat = $salesReport['format'] ?? 'monthly';
                $formatLabel = $formatLabels[$currentFormat] ?? 'Monthly';
            @endphp
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ $formatLabel }} Sales Trend</h3>
            @if($salesReport['sales_by_period']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $formatLabel === 'Daily' ? 'Date' : ($formatLabel === 'Weekly' ? 'Week' : 'Month') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesReport['sales_by_period'] as $period)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($currentFormat === 'daily')
                                        {{ \Carbon\Carbon::parse($period->period)->format('M d, Y') }}
                                    @elseif($currentFormat === 'weekly')
                                        Week of {{ \Carbon\Carbon::parse($period->period)->format('M d, Y') }}
                                    @else
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $period->period)->format('M Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $period->orders_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($period->revenue, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="text-green-600">+0%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-chart-line text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No {{ strtolower($formatLabel) }} sales data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Weekly Sales Report -->
    @include('admin.weekly-sales-report')

    <!-- Best Sellers -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Best Selling Products</h3>
            @if($salesReport['best_sellers']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesReport['best_sellers'] as $row)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->total_qty }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($row->total_sales, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No best sellers data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Custom Design Orders Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-palette text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Custom Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $salesReport['custom_orders_summary']['total_custom_orders'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Custom Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900">₱{{ number_format($salesReport['custom_orders_summary']['total_custom_revenue'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Custom</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $salesReport['custom_orders_summary']['pending_custom_orders'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Design Orders -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Custom Design Orders</h3>
            @if($salesReport['custom_orders']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fabric</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesReport['custom_orders'] as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->user->name ?? 'Guest' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($order->fabric) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($order->color) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-palette text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No custom design orders available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Custom Orders by Fabric -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Custom Orders by Fabric</h3>
            @if($salesReport['custom_orders_summary']['custom_orders_by_fabric']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fabric Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesReport['custom_orders_summary']['custom_orders_by_fabric'] as $fabric)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ ucfirst($fabric->fabric) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $fabric->count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($fabric->revenue, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-cut text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No fabric data available</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

 
