    /**
     * Print-friendly reports view with daily sales data
     */
    public function reportsPrint(Request $request)
    {
        $reportStartDate = now()->subDays(6)->startOfDay();
        $reportEndDate = now()->endOfDay();
        
        // Get daily sales for last 7 days
        $dailySales = [];
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $daysOfWeek[$date->dayOfWeek];
            $dateStr = $date->format('Y-m-d');
            
            $orders = Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
                ->whereDate('created_at', $dateStr)
                ->get();
            
            $dailySales[] = [
                'date' => $dateStr,
                'day_name' => $dayName,
                'revenue' => $orders->sum('total_amount'),
                'orders' => $orders->count(),
            ];
        }
        
        // Product sales by category
        $productSales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('barong_products', 'order_items.product_id', '=', 'barong_products.id')
            ->leftJoin('categories', 'barong_products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.total_price) as total_sales')
            )
            ->whereIn('orders.status', ['completed', 'delivered', 'shipped', 'processing'])
            ->where('orders.created_at', '>=', $reportStartDate)
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->get();
        
        // Statistics
        $totalRevenue = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->where('created_at', '>=', $reportStartDate)
            ->where('created_at', '<=', $reportEndDate)
            ->sum('total_amount');
        
        $totalOrders = (int) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->where('created_at', '>=', $reportStartDate)
            ->where('created_at', '<=', $reportEndDate)
            ->count();
        
        $totalProducts = BarongProduct::count();
        $totalCustomers = User::where('role', '!=', 'admin')->count();
        
        // Monthly totals
        $monthToDate = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        
        $previousMonth = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');
        
        $yearToDate = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        
        $salesReport = [
            'report_start_date' => $reportStartDate,
            'report_end_date' => $reportEndDate,
            'daily_sales' => $dailySales,
            'product_sales' => $productSales,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'total_customers' => $totalCustomers,
            'month_to_date' => $monthToDate,
            'previous_month' => $previousMonth,
            'year_to_date' => $yearToDate,
        ];

        return view('admin.reports-print', compact('salesReport'));
    }
