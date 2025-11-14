<!-- Report Filters -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Filter Reports</h3>
        </div>
        
        <form method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Quick Period Selector -->
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Quick Period</label>
                <select name="period" 
                        id="period"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        onchange="applyQuickPeriod(this.value)">
                    <option value="">Select Period</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="3months" {{ request('period') == '3months' ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="6months" {{ request('period') == '6months' ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Last Year</option>
                    <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>All Time</option>
                </select>
            </div>

            <!-- Format Selector -->
            <div>
                <label for="format" class="block text-sm font-medium text-gray-700 mb-2">View Format</label>
                <select name="format" 
                        id="format"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="daily" {{ request('format') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('format') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('format') == 'monthly' || !request('format') ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-4 flex items-center gap-2 mt-3">
                <button type="submit" 
                        class="inline-flex items-center px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Apply Filters
                </button>
                <a href="{{ route('admin.reports') }}" 
                   class="inline-flex items-center px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Reset Filters
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function applyQuickPeriod(period) {
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const today = new Date();
    
    let fromDate, toDate;
    
    switch(period) {
        case 'today':
            fromDate = today.toISOString().split('T')[0];
            toDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            fromDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7).toISOString().split('T')[0];
            toDate = today.toISOString().split('T')[0];
            break;
        case 'month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            toDate = today.toISOString().split('T')[0];
            break;
        case 'last_month':
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            fromDate = lastMonth.toISOString().split('T')[0];
            toDate = lastMonthEnd.toISOString().split('T')[0];
            break;
        case '3months':
            fromDate = new Date(today.getFullYear(), today.getMonth() - 3, today.getDate()).toISOString().split('T')[0];
            toDate = today.toISOString().split('T')[0];
            break;
        case '6months':
            fromDate = new Date(today.getFullYear(), today.getMonth() - 6, today.getDate()).toISOString().split('T')[0];
            toDate = today.toISOString().split('T')[0];
            break;
        case 'year':
            fromDate = new Date(today.getFullYear(), today.getMonth() - 12, today.getDate()).toISOString().split('T')[0];
            toDate = today.toISOString().split('T')[0];
            break;
        case 'all':
            fromDate = null;
            toDate = null;
            break;
    }
    
    if (fromDate) dateFromInput.value = fromDate;
    if (toDate) dateToInput.value = toDate;
}
</script>

