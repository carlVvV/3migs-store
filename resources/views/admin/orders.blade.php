@extends('layouts.admin')

@section('title', 'Orders - Admin Dashboard')
@section('page-title', 'Orders')

@section('content')
<div class="space-y-6">
    <!-- Orders Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $orders->total() }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $orders->where('status', 'pending')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $orders->where('status', 'delivered')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Cancelled Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $orders->where('status', 'cancelled')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Filter Orders</h3>
            <form method="GET" action="{{ route('admin.orders') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">All Orders</h3>
            
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if(isset($order->orderItems))
                                        Order #{{ $order->order_number ?? $order->id }}
                                    @else
                                        Custom #{{ $order->order_number ?? $order->id }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Guest' }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(isset($order->orderItems))
                                        {{ $order->orderItems->count() }} item(s)
                                    @else
                                        Custom Barong ({{ $order->quantity }})
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($order->total_amount ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->status ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(isset($order->orderItems))
                                        <button class="text-blue-600 hover:text-blue-900 mr-3 view-order-btn" data-order-id="{{ $order->id }}">View</button>
                                        <button class="text-green-600 hover:text-green-900 mr-3 update-order-btn" data-order-id="{{ $order->id }}" data-order-status="{{ $order->status }}" data-payment-status="{{ $order->payment_status ?? 'pending' }}">Update</button>
                                    @else
                                        <button class="text-blue-600 hover:text-blue-900 mr-3 view-custom-order-btn" data-order-id="{{ $order->id }}">View</button>
                                        <button class="text-green-600 hover:text-green-900 mr-3 update-custom-order-btn" data-order-id="{{ $order->id }}" data-order-status="{{ $order->status }}" data-payment-status="{{ $order->payment_status ?? 'pending' }}">Update</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-shopping-cart text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Orders Found</h3>
                    <p class="text-gray-500">There are no orders matching your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = createOrGetAdminOrderModal();

    document.querySelectorAll('.view-order-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const orderId = this.getAttribute('data-order-id');
            await openAdminOrderModal(modal, orderId);
        });
    });

    document.querySelectorAll('.update-order-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const orderId = this.getAttribute('data-order-id');
            const currentStatus = this.getAttribute('data-order-status') || 'pending';
            const currentPayment = this.getAttribute('data-payment-status') || 'pending';
            await openAdminOrderUpdateModal(orderId, currentStatus, currentPayment);
        });
    });
});

function createOrGetAdminOrderModal() {
    let modal = document.getElementById('admin-order-details-modal');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'admin-order-details-modal';
    modal.className = 'fixed inset-0 z-50 hidden';
    modal.innerHTML = `
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="absolute inset-0 flex items-start md:items-center justify-center p-4 md:p-8">
            <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <h3 id="adm-order-title" class="text-lg font-semibold text-gray-800">Order Details</h3>
                        <p id="adm-order-subtitle" class="text-sm text-gray-500"></p>
                    </div>
                    <button id="adm-order-close" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div id="adm-order-body" class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                    <div id="adm-order-loading" class="py-10 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading order...
                    </div>
                    <div id="adm-order-content" class="hidden space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Customer</h4>
                                <p id="adm-order-customer-name" class="text-sm text-gray-800">-</p>
                                <p id="adm-order-customer-email" class="text-sm text-gray-600">-</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Payment</h4>
                                <p id="adm-order-payment" class="text-sm text-gray-800">-</p>
                                <p id="adm-order-status" class="text-sm"></p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Placed</h4>
                                <p id="adm-order-date" class="text-sm text-gray-800">-</p>
                                <p id="adm-order-number" class="text-sm text-gray-600">-</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Shipping Address</h4>
                                <p id="adm-order-ship" class="text-sm text-gray-700">-</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded">
                                <h4 class="font-semibold text-gray-700 mb-2">Billing Address</h4>
                                <p id="adm-order-bill" class="text-sm text-gray-700">-</p>
                            </div>
                        </div>

                        <div class="p-4 bg-white border rounded">
                            <h4 class="font-semibold text-gray-700 mb-3">Items</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adm-order-items" class="bg-white divide-y divide-gray-200"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex flex-col items-end space-y-1">
                            <div class="text-sm text-gray-600">Subtotal: <span id="adm-order-subtotal" class="font-medium text-gray-800">₱0.00</span></div>
                            <div class="text-sm text-gray-600">Shipping: <span id="adm-order-shipping" class="font-medium text-gray-800">₱0.00</span></div>
                            <div class="text-base text-gray-800 font-semibold">Grand Total: <span id="adm-order-total" class="text-gray-900">₱0.00</span></div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end">
                    <button id="adm-order-close-bottom" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-gray-700">Close</button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    function close() {
        modal.classList.add('hidden');
    }
    modal.querySelector('#adm-order-close').addEventListener('click', close);
    modal.querySelector('#adm-order-close-bottom').addEventListener('click', close);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });

    return modal;
}

async function openAdminOrderModal(modal, orderId) {
    const loading = modal.querySelector('#adm-order-loading');
    const content = modal.querySelector('#adm-order-content');
    const title = modal.querySelector('#adm-order-title');
    const subtitle = modal.querySelector('#adm-order-subtitle');

    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    try {
        const res = await fetch(`/api/v1/orders/${orderId}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data || data.success === false) throw new Error('Failed to load');
        const order = data.data || data.order || data;

        // Header
        title.textContent = `Order #${order.order_number ?? order.id}`;
        subtitle.textContent = order.created_at ? new Date(order.created_at).toLocaleString() : '';

        // Top cards
        modal.querySelector('#adm-order-customer-name').textContent = (order.user && (order.user.name || order.user.full_name)) || 'Guest';
        modal.querySelector('#adm-order-customer-email').textContent = (order.user && order.user.email) || 'N/A';

        modal.querySelector('#adm-order-payment').textContent = formatText(order.payment_method);
        modal.querySelector('#adm-order-status').innerHTML = renderStatusPill(order.status);
        modal.querySelector('#adm-order-date').textContent = order.created_at ? new Date(order.created_at).toLocaleString() : '—';
        modal.querySelector('#adm-order-number').textContent = `#${order.order_number ?? order.id}`;

        // Addresses (fetch asynchronously)
        const shippingAddr = await formatAddress(order.shipping_address);
        const billingAddr = await formatAddress(order.billing_address);
        
        modal.querySelector('#adm-order-ship').textContent = shippingAddr;
        modal.querySelector('#adm-order-bill').textContent = billingAddr;

        // Items
        const itemsTbody = modal.querySelector('#adm-order-items');
        itemsTbody.innerHTML = '';
        let subtotal = 0;
        (order.order_items || order.items || []).forEach(it => {
            const unit = toNumber(it.unit_price ?? it.price ?? 0);
            const qty = toNumber(it.quantity ?? 0);
            const line = unit * qty;
            subtotal += line;
            const tr = document.createElement('tr');
            const productData = it.product ? encodeURIComponent(JSON.stringify(it.product)) : '';
            // Check if item has custom measurements
            const hasCustomMeasurements = it.custom_measurements && 
                typeof it.custom_measurements === 'object' &&
                Object.keys(it.custom_measurements).some(k => it.custom_measurements[k] && String(it.custom_measurements[k]).trim() !== '');
            
            // Format custom measurements
            let customMeasurementsHtml = '';
            if (hasCustomMeasurements) {
                const measurements = it.custom_measurements;
                const parts = [];
                if (measurements.shoulder && String(measurements.shoulder).trim()) parts.push(`Shoulder: ${escapeHtml(String(measurements.shoulder))}"`);
                if (measurements.chest && String(measurements.chest).trim()) parts.push(`Chest: ${escapeHtml(String(measurements.chest))}"`);
                if (measurements.sleeve && String(measurements.sleeve).trim()) parts.push(`Sleeve Length: ${escapeHtml(String(measurements.sleeve))}"`);
                if (measurements.waist && String(measurements.waist).trim()) parts.push(`Waist: ${escapeHtml(String(measurements.waist))}"`);
                if (measurements.notes && String(measurements.notes).trim()) parts.push(`Notes: ${escapeHtml(String(measurements.notes))}`);
                customMeasurementsHtml = parts.length > 0 ? `<div class="text-xs text-gray-500 mt-1">${parts.join(' • ')}</div>` : '';
            }
            
            const productName = escapeHtml((it.product && it.product.name) || it.name || 'Item');
            const customSizeBadge = hasCustomMeasurements ? 
                `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                    <i class="fas fa-ruler-combined mr-1 text-xs"></i>Custom Size
                </span>` : '';
            
            tr.innerHTML = `
                <td class="px-4 py-2 text-sm text-gray-800">
                    <div>
                        <button class="text-blue-600 hover:underline adm-item-product-btn" data-product="${productData}">
                            ${productName}${customSizeBadge}
                        </button>
                        ${customMeasurementsHtml}
                    </div>
                </td>
                <td class="px-4 py-2 text-sm text-gray-600">${qty}</td>
                <td class="px-4 py-2 text-sm text-gray-600">₱${formatMoney(unit)}</td>
                <td class="px-4 py-2 text-sm text-gray-800 font-medium">₱${formatMoney(line)}</td>
            `;
            itemsTbody.appendChild(tr);
            const btn = tr.querySelector('.adm-item-product-btn');
            if (btn && productData) {
                btn.addEventListener('click', () => {
                    try {
                        const prod = JSON.parse(decodeURIComponent(btn.getAttribute('data-product')));
                        openAdminProductQuickView(prod);
                    } catch (e) {
                        console.error('Failed to parse product data', e);
                    }
                });
            }
        });

        const shippingFee = toNumber(order.shipping_fee ?? 0);
        const grandTotal = toNumber(order.total_amount ?? subtotal + shippingFee);
        modal.querySelector('#adm-order-subtotal').textContent = `₱${formatMoney(subtotal)}`;
        modal.querySelector('#adm-order-shipping').textContent = `₱${formatMoney(shippingFee)}`;
        modal.querySelector('#adm-order-total').textContent = `₱${formatMoney(grandTotal)}`;

        loading.classList.add('hidden');
        content.classList.remove('hidden');
    } catch (e) {
        loading.innerHTML = '<span class="text-red-600">Failed to load order details.</span>';
        console.error(e);
    }
}

function openAdminOrderUpdateModal(orderId, currentStatus, currentPayment) {
    let modal = document.getElementById('admin-order-update-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'admin-order-update-modal';
        modal.className = 'fixed inset-0 z-50 hidden';
        modal.innerHTML = `
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="bg-white w-full max-w-md rounded-lg shadow-xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">Update Order Status</h3>
                        <button id="adm-upd-close" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="adm-upd-status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <select id="adm-upd-payment" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div id="adm-upd-error" class="hidden text-sm text-red-600"></div>
                    </div>
                    <div class="px-6 py-4 border-t flex justify-end space-x-2">
                        <button id="adm-upd-cancel" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-gray-700">Cancel</button>
                        <button id="adm-upd-save" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                    </div>
                </div>
            </div>`;

        document.body.appendChild(modal);
        const close = () => modal.classList.add('hidden');
        modal.querySelector('#adm-upd-close').addEventListener('click', close);
        modal.querySelector('#adm-upd-cancel').addEventListener('click', close);
        modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
    }

    const statusSel = modal.querySelector('#adm-upd-status');
    const paySel = modal.querySelector('#adm-upd-payment');
    const errBox = modal.querySelector('#adm-upd-error');
    statusSel.value = (currentStatus || 'pending').toLowerCase();
    paySel.value = (currentPayment || 'pending').toLowerCase();
    errBox.classList.add('hidden');
    errBox.textContent = '';

    modal.classList.remove('hidden');

    modal.querySelector('#adm-upd-save').onclick = async () => {
        const status = statusSel.value;
        const payment_status = paySel.value;
        try {
            const res = await fetch(`{{ route('admin.orders.update-status', ['id' => '__ID__']) }}`.replace('__ID__', orderId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ status, payment_status }),
            });
            const data = await res.json();
            if (!res.ok || data.success === false) {
                throw new Error(data.message || 'Failed to update');
            }
            modal.classList.add('hidden');
            // Refresh page to reflect changes
            window.location.reload();
        } catch (e) {
            errBox.textContent = e.message || 'Failed to update order';
            errBox.classList.remove('hidden');
        }
    };
}

function openAdminProductQuickView(product) {
    let modal = document.getElementById('admin-product-quickview-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'admin-product-quickview-modal';
        modal.className = 'fixed inset-0 z-50 hidden';
        modal.innerHTML = `
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">Product Quick View</h3>
                        <button id="adm-pqv-close" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex flex-col md:flex-row md:space-x-6">
                            <div class="md:w-1/3 mb-4 md:mb-0">
                                <img id="adm-pqv-image" src="" alt="Product image" class="w-full h-48 object-cover rounded border" />
                            </div>
                            <div class="md:w-2/3 space-y-2">
                                <div class="text-xl font-semibold text-gray-800" id="adm-pqv-name">-</div>
                                <div class="text-sm text-gray-600">SKU: <span id="adm-pqv-sku">-</span></div>
                                <div class="text-sm text-gray-600">Brand: <span id="adm-pqv-brand">-</span></div>
                                <div class="text-sm text-gray-600">Category: <span id="adm-pqv-category">-</span></div>
                                <div class="text-lg font-semibold text-gray-900">₱<span id="adm-pqv-price">0.00</span></div>
                                <div class="text-sm text-gray-600">Stock: <span id="adm-pqv-stock">0</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t flex justify-end space-x-2">
                        <a id="adm-pqv-view-admin" href="#" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-gray-700">Open in Products</a>
                        <button id="adm-pqv-close-bottom" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Close</button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modal);
        const close = () => modal.classList.add('hidden');
        modal.querySelector('#adm-pqv-close').addEventListener('click', close);
        modal.querySelector('#adm-pqv-close-bottom').addEventListener('click', close);
        modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
    }

    const img = modal.querySelector('#adm-pqv-image');
    const nameEl = modal.querySelector('#adm-pqv-name');
    const skuEl = modal.querySelector('#adm-pqv-sku');
    const brandEl = modal.querySelector('#adm-pqv-brand');
    const categoryEl = modal.querySelector('#adm-pqv-category');
    const priceEl = modal.querySelector('#adm-pqv-price');
    const stockEl = modal.querySelector('#adm-pqv-stock');
    const viewAdmin = modal.querySelector('#adm-pqv-view-admin');

    const imgUrl = (product && (product.cover_image || (product.images && product.images[0]))) || '{{ asset('images/placeholder.jpg') }}';
    img.src = imgUrl;
    img.alt = product?.name || 'Product image';
    nameEl.textContent = product?.name || 'Product';
    skuEl.textContent = product?.sku || '—';
    brandEl.textContent = product?.brand?.name || '—';
    categoryEl.textContent = product?.category?.name || '—';
    const price = (product?.current_price ?? product?.base_price ?? 0);
    priceEl.textContent = formatMoney(price);
    stockEl.textContent = (product?.stock ?? 0);

    // Link to admin products (index); direct edit if id known
    const editUrl = product?.id ? `{{ route('admin.products') }}?edit=${'__PID__'}`.replace('__PID__', product.id) : `{{ route('admin.products') }}`;
    viewAdmin.href = editUrl;

    modal.classList.remove('hidden');
}

async function formatAddress(raw) {
    try {
        const addr = raw && typeof raw === 'string' ? (JSON.parse(raw) || {}) : (raw || {});
        
        // Fetch names for city, province, region, barangay if they are numeric codes
        let cityName = addr.city || '';
        let provinceName = addr.province || '';
        let regionName = addr.region || '';
        let barangayName = addr.barangay || '';
        
        // Check if values are numeric codes and fetch names from API
        if (isNumeric(cityName)) {
            try {
                const res = await fetch(`/api/v1/psgc/cities/${cityName}`);
                const data = await res.json();
                if (data.success && data.data && data.data.name) {
                    cityName = data.data.name;
                }
            } catch (e) {
                // Keep the code if API fails
            }
        }
        
        if (isNumeric(provinceName)) {
            try {
                const res = await fetch(`/api/v1/psgc/provinces/${provinceName}`);
                const data = await res.json();
                if (data.success && data.data && data.data.name) {
                    provinceName = data.data.name;
                }
            } catch (e) {
                // Keep the code if API fails
            }
        }
        
        if (isNumeric(regionName)) {
            try {
                const res = await fetch('/api/v1/psgc/regions');
                const data = await res.json();
                if (data.success && data.data && Array.isArray(data.data)) {
                    const region = data.data.find(r => r.code === regionName);
                    if (region && region.name) {
                        regionName = region.name;
                    }
                }
            } catch (e) {
                // Keep the code if API fails
            }
        }
        
        if (isNumeric(barangayName)) {
            try {
                const res = await fetch(`/api/v1/psgc/barangays/${barangayName}`);
                const data = await res.json();
                if (data.success && data.data && data.data.name) {
                    barangayName = data.data.name;
                }
            } catch (e) {
                // Keep the code if API fails
            }
        }
        
        const parts = [
            addr.name || addr.full_name,
            barangayName,
            addr.line1 || addr.address,
            addr.line2,
            cityName,
            provinceName,
            regionName,
            addr.postal_code,
            addr.country
        ].filter(Boolean);
        
        return parts.length ? parts.join(', ') : 'N/A';
    } catch {
        return 'N/A';
    }
}

function isNumeric(str) {
    return !isNaN(str) && str && str.match(/^\d+$/);
}

function formatText(s) {
    if (!s) return 'N/A';
    return String(s).replace(/_/g, ' ').replace(/\b\w/g, m => m.toUpperCase());
}

function renderStatusPill(status) {
    const s = (status || '').toLowerCase();
    let cls = 'bg-gray-100 text-gray-800';
    if (s === 'completed') cls = 'bg-green-100 text-green-800';
    else if (s === 'pending') cls = 'bg-yellow-100 text-yellow-800';
    else if (s === 'processing') cls = 'bg-blue-100 text-blue-800';
    else if (s === 'shipped') cls = 'bg-purple-100 text-purple-800';
    else if (s === 'cancelled') cls = 'bg-red-100 text-red-800';
    return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${s ? s.charAt(0).toUpperCase() + s.slice(1) : 'Unknown'}</span>`;
}

function toNumber(v) {
    const n = Number(v);
    return isFinite(n) ? n : 0;
}

function formatMoney(n) {
    return toNumber(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escapeHtml(s) {
    return String(s).replace(/[&<>"]|'/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));
}
</script>
@endpush
