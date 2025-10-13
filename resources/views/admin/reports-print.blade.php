<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Print</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            .print-container { padding: 0 !important; }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-open print dialog
            window.print();
        });
    </script>
    </head>
<body class="bg-white print-container">
    <div class="container mx-auto px-6 py-6">
        <div class="flex items-center justify-between mb-6 no-print">
            <h1 class="text-2xl font-bold text-gray-900">Reports (Print Preview)</h1>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900">Print</button>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="border rounded p-4">
                <div class="text-sm text-gray-500">Total Revenue</div>
                <div class="text-xl font-bold">₱{{ number_format($salesReport['total_revenue'], 2) }}</div>
            </div>
            <div class="border rounded p-4">
                <div class="text-sm text-gray-500">Total Orders</div>
                <div class="text-xl font-bold">{{ $salesReport['total_orders'] }}</div>
            </div>
            <div class="border rounded p-4">
                <div class="text-sm text-gray-500">Average Order Value</div>
                <div class="text-xl font-bold">₱{{ number_format($salesReport['average_order_value'], 2) }}</div>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-3">Top Customers</h2>
        <table class="w-full text-sm border mb-8">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left p-2 border">Customer</th>
                    <th class="text-left p-2 border">Email</th>
                    <th class="text-left p-2 border">Orders</th>
                    <th class="text-left p-2 border">Total Spent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesReport['top_customers'] as $c)
                    <tr>
                        <td class="p-2 border">{{ $c->name }}</td>
                        <td class="p-2 border">{{ $c->email }}</td>
                        <td class="p-2 border">{{ $c->orders_count ?? 0 }}</td>
                        <td class="p-2 border">₱{{ number_format($c->total_spent ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-500 border">No data</td></tr>
                @endforelse
            </tbody>
        </table>

        <h2 class="text-xl font-semibold mb-3">Monthly Sales</h2>
        <table class="w-full text-sm border">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left p-2 border">Month</th>
                    <th class="text-left p-2 border">Orders</th>
                    <th class="text-left p-2 border">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesReport['sales_by_month'] as $m)
                    <tr>
                        <td class="p-2 border">{{ $m->month }}</td>
                        <td class="p-2 border">{{ $m->orders_count }}</td>
                        <td class="p-2 border">₱{{ number_format($m->revenue ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-4 text-center text-gray-500 border">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>


