# Reports Documentation

## Overview
The Reports module provides comprehensive sales analytics and reporting functionality for administrators. It includes visual charts, detailed tables, filtering capabilities, and export options.

## Access
- **URL**: `/admin/reports`
- **Route Name**: `admin.reports`
- **Authentication**: Admin role required
- **Controller**: `App\Http\Controllers\AdminController@reports`

---

## Features

### 1. Dashboard Overview Cards
Displays four key metrics at the top of the reports page:

- **Total Revenue** (₱) - Sum of all completed/delivered/shipped/processing orders
- **Total Orders** - Count of all orders in the selected period
- **Average Order Value** (₱) - Total revenue divided by total orders
- **Total Customers** - Count of unique customers who placed orders

### 2. Report Filters
Located below the overview cards, allows filtering by:

#### Date Range Filters
- **Date From**: Start date (YYYY-MM-DD format)
- **Date To**: End date (YYYY-MM-DD format)

#### Quick Period Selector
Pre-configured date ranges:
- **Today**: Current day only
- **This Week**: Last 7 days
- **This Month**: From start of current month to today
- **Last Month**: Previous month (full month)
- **Last 3 Months**: Last 90 days
- **Last 6 Months**: Last 180 days
- **Last Year**: Last 365 days
- **All Time**: No date restrictions

#### Order Status Filter
Filter orders by status:
- **All Status**: Includes all statuses
- **Completed**: Completed orders only
- **Delivered**: Delivered orders only
- **Shipped**: Shipped orders only
- **Processing**: Processing orders only
- **Pending**: Pending orders only

**Note**: By default, the system filters for `completed`, `delivered`, `shipped`, and `processing` statuses. Only these statuses are included in revenue calculations unless explicitly filtered.

---

## Report Sections

### 1. Sales & Orders Chart (Monthly)
- **Type**: Bar chart (Chart.js)
- **Data**: Monthly sales revenue and order counts
- **Time Range**: Last 12 months (or filtered period)
- **Dual Y-Axis**: 
  - Left axis: Revenue in Philippine Peso (₱)
  - Right axis: Order count
- **Visualization**: Green bars for revenue, blue bars for orders

### 2. Top Customers Table
Shows the top 10 customers by total spending:
- **Columns**:
  - Customer Name
  - Email Address
  - Total Orders Count
  - Total Amount Spent (₱)
- **Sorting**: Ordered by total spent (descending)
- **Filtering**: Respects date range and status filters

### 3. Monthly Sales Trend Table
Displays month-by-month breakdown:
- **Columns**:
  - Month (YYYY-MM format)
  - Orders Count
  - Revenue (₱)
  - Growth (currently shows +0% - placeholder)
- **Data Source**: Combined from regular orders and custom design orders
- **Time Range**: Last 12 months or filtered period

### 4. Weekly Sales Report
Shows product sales by week for the last 3 months:
- **Columns**:
  - Week (ISO week format: YYYY-W##)
  - Product Name
  - SKU
  - Total Quantity Sold
  - Total Sales (₱)
- **Grouping**: By product and week
- **Export**: Can be exported to CSV via button
- **Default Period**: Last 3 months (if no date filter applied)

### 5. Best Selling Products
Top 10 products by quantity sold:
- **Columns**:
  - Product Name
  - Total Quantity Sold
  - Total Sales Revenue (₱)
- **Sorting**: Ordered by total quantity (descending)
- **Filtering**: Respects date range and status filters

### 6. Custom Design Orders Overview
Three summary cards:
- **Custom Orders**: Total count of custom design orders
- **Custom Revenue**: Total revenue from custom orders
- **Pending Custom**: Count of pending custom orders

### 7. Recent Custom Design Orders Table
Shows the 20 most recent custom design orders:
- **Columns**:
  - Order Number
  - Customer Name
  - Fabric Type
  - Color
  - Quantity
  - Total Amount (₱)
  - Status (with color-coded badges)
  - Date Created
- **Status Badges**:
  - Completed: Green
  - Pending: Yellow
  - Processing: Blue
  - Shipped: Purple
  - Cancelled: Red
  - Other: Gray

### 8. Custom Orders by Fabric
Breakdown of custom orders grouped by fabric type:
- **Columns**:
  - Fabric Type
  - Orders Count
  - Total Revenue (₱)
- **Sorting**: Ordered by count (descending)

---

## Export & Print Features

### CSV Export
- **URL**: `/admin/reports/export`
- **Route Name**: `admin.reports.export`
- **Method**: GET
- **File Format**: CSV with UTF-8 BOM encoding
- **Filename**: `sales_report_YYYY-MM-DD_HHMMSS.csv`
- **Columns Exported**:
  - Order ID
  - Item No (SKU)
  - Item Name
  - Item Description
  - Price (unit price)
  - Quantity
  - Amount (total price)
- **Filtering**: Respects all applied filters (date range, status)
- **Default Period**: Last 3 months if no date filter specified

### Print View
- **URL**: `/admin/reports/print`
- **Route Name**: `admin.reports.print`
- **Method**: GET
- **Features**:
  - Print-optimized layout
  - Last 7 days daily sales breakdown
  - Category-by-day sales matrix
  - Product sales summary
  - Month-to-date and previous month comparisons
  - Year-to-date summary
- **Opens in**: New browser tab/window

---

## Data Sources

### Order Types Included
1. **Regular Orders** (`orders` table)
   - Standard product orders
   - Includes order items from `order_items` table
   - Linked to `barong_products` table

2. **Custom Design Orders** (`custom_design_orders` table)
   - Custom-made barong orders
   - Includes fabric, color, and quantity details
   - Linked to `users` table for customer information

### Database Tables Used
- `orders` - Main order records
- `order_items` - Individual items in orders
- `barong_products` - Product catalog
- `custom_design_orders` - Custom order records
- `users` - Customer information
- `categories` - Product categories (for grouping)

---

## Technical Details

### Query Optimization
- Uses PostgreSQL-compatible date functions (`TO_CHAR` for date formatting)
- Efficient joins with proper indexing
- Aggregations performed at database level
- Caching not implemented (real-time data)

### Status Filtering Logic
```php
// Default statuses included in reports
$statusFilter = ['completed', 'delivered', 'shipped', 'processing'];

// If specific status selected, only that status is included
if ($status !== 'all') {
    $statusFilter = [$status];
}
```

### Date Range Handling
- If both `date_from` and `date_to` are provided, uses custom range
- If `period` is selected, calculates dates automatically
- If no dates provided, defaults to last 3 months for weekly sales
- Monthly chart shows last 12 months regardless of filters

### Revenue Calculation
```php
$total_revenue = $regular_revenue + $custom_revenue;
// Where:
// $regular_revenue = sum of orders.total_amount (filtered)
// $custom_revenue = sum of custom_design_orders.total_amount (filtered)
```

---

## Usage Examples

### View Reports for Last Month
1. Navigate to `/admin/reports`
2. Select "Last Month" from Quick Period dropdown
3. Click "Apply Filters"
4. View all sections updated with last month's data

### Export Sales Data for Specific Date Range
1. Navigate to `/admin/reports`
2. Enter "Date From": `2024-01-01`
3. Enter "Date To": `2024-01-31`
4. Select "Completed" from Order Status dropdown
5. Click "Apply Filters"
6. Click "Export CSV" button
7. File downloads with filtered data

### Print Weekly Report
1. Navigate to `/admin/reports`
2. Apply desired filters (optional)
3. Click "Print" button
4. Print dialog opens with optimized layout

### View Top Customers
1. Navigate to `/admin/reports`
2. Scroll to "Top Customers" section
3. Table shows top 10 customers by spending
4. Data respects current filters

---

## API Endpoints

### Reports Page
```
GET /admin/reports
Query Parameters:
  - date_from (optional): YYYY-MM-DD
  - date_to (optional): YYYY-MM-DD
  - period (optional): today|week|month|last_month|3months|6months|year|all
  - status (optional): all|completed|delivered|shipped|processing|pending
```

### Export CSV
```
GET /admin/reports/export
Query Parameters: Same as reports page
Response: CSV file download
```

### Print View
```
GET /admin/reports/print
Query Parameters: None (uses default last 7 days)
Response: HTML print view
```

---

## Troubleshooting

### No Data Showing
- **Check Date Filters**: Ensure date range includes orders
- **Check Status Filter**: Default includes completed/delivered/shipped/processing
- **Check Database**: Verify orders exist in database with correct statuses
- **Check Permissions**: Ensure admin role is active

### Chart Not Displaying
- **Check JavaScript Console**: Look for Chart.js errors
- **Verify Data**: Ensure `sales_by_month` has data
- **Browser Compatibility**: Requires modern browser with JavaScript enabled

### Export File Empty
- **Check Filters**: Too restrictive filters may result in no data
- **Check Date Range**: Ensure orders exist in selected period
- **Check Status**: Ensure selected status has matching orders

### Performance Issues
- **Large Date Ranges**: Very large date ranges may be slow
- **Database Indexing**: Ensure proper indexes on `orders.created_at` and `orders.status`
- **Query Optimization**: Consider adding database indexes if queries are slow

---

## Future Enhancements (Potential)

1. **Real-time Updates**: WebSocket integration for live sales data
2. **Email Reports**: Scheduled email delivery of reports
3. **PDF Export**: Generate PDF reports in addition to CSV
4. **Advanced Analytics**: Trend analysis, forecasting, comparisons
5. **Custom Date Ranges**: Calendar picker for custom ranges
6. **Saved Filters**: Save and reuse filter presets
7. **Dashboard Widgets**: Customizable report widgets
8. **Export Formats**: Excel, JSON, XML export options
9. **Scheduled Reports**: Automated report generation
10. **Data Visualization**: Additional chart types (pie, line, area)

---

## Related Files

### Controllers
- `app/Http/Controllers/AdminController.php`
  - `reports()` - Main reports page
  - `reportsPrint()` - Print view
  - `exportReports()` - CSV export

### Views
- `resources/views/admin/reports.blade.php` - Main reports page
- `resources/views/admin/report-filters.blade.php` - Filter section
- `resources/views/admin/reports-print.blade.php` - Print view
- `resources/views/admin/weekly-sales-report.blade.php` - Weekly sales section

### Routes
- `routes/web.php` - Route definitions

### Models
- `App\Models\Order` - Regular orders
- `App\Models\CustomDesignOrder` - Custom orders
- `App\Models\User` - Customers
- `App\Models\BarongProduct` - Products

---

## Version History
- **Initial Version**: Basic sales reporting with filters
- **Current Version**: Includes custom orders, weekly reports, export functionality

---

## Support
For issues or questions about the Reports module, contact the development team or refer to the main project documentation.

