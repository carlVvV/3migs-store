<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Orders - 3Migs Gowns & Barong</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">My Orders</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">My Orders</h1>
                    <p class="text-gray-600 mt-2">Track and manage your orders</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->orders()->count() }}</div>
                        <div class="text-sm text-gray-600">Total Orders</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ Auth::user()->orders()->where('status', 'completed')->count() }}</div>
                        <div class="text-sm text-gray-600">Completed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <span class="text-sm font-medium text-gray-700">Filter by status:</span>
                    <div class="flex space-x-2">
                        <button class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" data-status="all">All</button>
                        <button class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800" data-status="pending">Pending</button>
                        <button class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800" data-status="processing">Processing</button>
                        <button class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800" data-status="completed">Completed</button>
                        <button class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800" data-status="cancelled">Cancelled</button>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Sort by:</span>
                    <select id="sort-select" class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="latest">Latest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="amount-high">Amount: High to Low</option>
                        <option value="amount-low">Amount: Low to High</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div id="orders-container">
            @if($allOrders->count() > 0)
                @foreach($allOrders as $order)
                <div class="order-card bg-white rounded-lg shadow-md p-6 mb-6" data-status="{{ $order->status }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                @if(isset($order->orderItems))
                                    <i class="fas fa-box text-gray-600"></i>
                                @else
                                    <i class="fas fa-palette text-gray-600"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    @if(isset($order->orderItems))
                                        Order #{{ $order->order_number }}
                                    @else
                                        Custom Order #{{ $order->order_number }}
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-gray-800">₱{{ number_format($order->total_amount, 2) }}</p>
                            <span class="inline-block px-3 py-1 text-sm rounded-full 
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="space-y-3 mb-4">
                        @if(isset($order->orderItems))
                            @foreach($order->orderItems as $item)
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    @if($item->product && $item->product->cover_image)
                                        <img src="{{ $item->product->cover_image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-image text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">{{ $item->product->name ?? 'Product' }}</h4>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                    @if($item->size)
                                        <p class="text-sm text-gray-600">Size: {{ $item->size }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">₱{{ number_format($item->total_price ?? ($item->unit_price * $item->quantity), 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <!-- Custom Design Order Display -->
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    @if($order->reference_image)
                                        <img src="{{ Storage::url($order->reference_image) }}" alt="Custom Barong" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-palette text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">Custom Barong</h4>
                                    <p class="text-sm text-gray-600">Fabric: {{ ucfirst($order->fabric) }}</p>
                                    <p class="text-sm text-gray-600">Color: {{ ucfirst($order->color) }}</p>
                                    <p class="text-sm text-gray-600">Quantity: {{ $order->quantity }}</p>
                                    @if($order->embroidery && $order->embroidery !== 'none')
                                        <p class="text-sm text-gray-600">Embroidery: {{ ucfirst($order->embroidery) }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">₱{{ number_format($order->total_amount, 2) }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Order Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                        <!-- Customer Details -->
                        <div>
                            <h5 class="font-semibold text-gray-700 mb-2">Customer Details</h5>
                            @php
                                $user = $order->user ?? auth()->user();
                            @endphp
                            <dl class="text-sm text-gray-700 space-y-1">
                                <div class="flex justify-between"><dt class="font-medium text-gray-600">Name</dt><dd class="text-gray-800">{{ $user->name ?? ($order->billing_address['full_name'] ?? 'N/A') }}</dd></div>
                                <div class="flex justify-between"><dt class="font-medium text-gray-600">Email</dt><dd class="text-gray-800 break-all">{{ $user->email ?? ($order->billing_address['email'] ?? 'N/A') }}</dd></div>
                                <div class="flex justify-between"><dt class="font-medium text-gray-600">Phone</dt><dd class="text-gray-800">{{ $user->phone ?? ($order->billing_address['phone'] ?? 'N/A') }}</dd></div>
                                @php
                                    // Fallback address on user
                                    $uaddrRaw = $user->address ?? null;
                                    $uaddr = is_array($uaddrRaw) ? $uaddrRaw : (json_decode($uaddrRaw, true) ?: []);
                                    $uaddrFormatted = collect([
                                        $uaddr['line1'] ?? ($uaddr['street'] ?? null),
                                        $uaddr['line2'] ?? null,
                                        $uaddr['city'] ?? null,
                                        $uaddr['province'] ?? null,
                                        $uaddr['postal_code'] ?? null,
                                        $uaddr['country'] ?? null,
                                    ])->filter()->join(', ');
                                @endphp
                                @if($uaddrFormatted)
                                <div class="flex justify-between"><dt class="font-medium text-gray-600">Account Address</dt><dd class="text-gray-800 text-right ml-4">{{ $uaddrFormatted }}</dd></div>
                                @endif
                            </dl>
                        </div>

                        <!-- Shipping Address -->
                        <div>
                            <h5 class="font-semibold text-gray-700 mb-2">Shipping Address</h5>
                            @php
                                $raw = $order->shipping_address ?? [];
                                $addr = is_array($raw) ? $raw : (json_decode($raw, true) ?: []);
                                
                                // Use the stored names directly - no API calls here
                                $cityName = $addr['city'] ?? '';
                                $provinceName = $addr['province'] ?? '';
                                $regionName = $addr['region'] ?? '';
                                $barangayName = $addr['barangay'] ?? '';
                                
                                $formatted = collect([
                                    $addr['name'] ?? ($user->name ?? null),
                                    $barangayName,
                                    $addr['line1'] ?? null,
                                    $addr['line2'] ?? null,
                                    $cityName,
                                    $provinceName,
                                    $regionName,
                                    $addr['postal_code'] ?? null,
                                    $addr['country'] ?? 'Philippines',
                                ])->filter()->join(', ');
                            @endphp
                            <p class="text-sm text-gray-600" data-shipping-address="{{ json_encode($addr) }}">{{ $formatted ?: 'N/A' }}</p>
                        </div>

                        <!-- Billing Address / Payment -->
                        <div>
                            <h5 class="font-semibold text-gray-700 mb-2">Billing & Payment</h5>
                            @php
                                $braw = $order->billing_address ?? [];
                                $baddr = is_array($braw) ? $braw : (json_decode($braw, true) ?: []);
                                
                                // Use the stored names directly - no API calls here
                                $bcityName = $baddr['city'] ?? '';
                                $bprovinceName = $baddr['province'] ?? '';
                                $bregionName = $baddr['region'] ?? '';
                                $bbarangayName = $baddr['barangay'] ?? '';
                                
                                $bformatted = collect([
                                    $baddr['name'] ?? ($user->name ?? null),
                                    $bbarangayName,
                                    $baddr['line1'] ?? null,
                                    $baddr['line2'] ?? null,
                                    $bcityName,
                                    $bprovinceName,
                                    $bregionName,
                                    $baddr['postal_code'] ?? null,
                                    $baddr['country'] ?? 'Philippines',
                                ])->filter()->join(', ');
                            @endphp
                            <p class="text-sm text-gray-600"><span class="font-medium text-gray-700">Payment:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-medium text-gray-700">Billing:</span> 
                                <span data-billing-address="{{ json_encode($baddr) }}">{{ $bformatted ?: 'N/A' }}</span>
                            </p>
                        </div>
                    </div>

                    @if($order->notes)
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <h5 class="font-semibold text-gray-700 mb-2">Order Notes</h5>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                    @endif

                    <!-- Order Actions -->
                    <div class="flex justify-end space-x-4">
                        @if(isset($order->orderItems))
                            <!-- Regular Order Actions -->
                            <a href="{{ route('orders.details', $order->id) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </a>
                            @if($order->status === 'pending')
                            <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 cancel-order-btn" data-order-id="{{ $order->id }}">
                                <i class="fas fa-times mr-2"></i>Cancel Order
                            </button>
                            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 pay-order-btn" data-order-id="{{ $order->id }}">
                                <i class="fas fa-credit-card mr-2"></i>Pay Now
                            </button>
                            @endif
                            @if($order->status === 'delivered' || $order->status === 'completed')
                            <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 review-order-btn" data-order-id="{{ $order->id }}" data-product-id="{{ $order->orderItems->first()->product_id ?? '' }}">
                                <i class="fas fa-star mr-2"></i>Write Review
                            </button>
                            @endif
                        @else
                            <!-- Custom Design Order Actions -->
                            <button class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors view-custom-order-btn" data-order-id="{{ $order->id }}">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </button>
                            @if($order->status === 'pending')
                            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 pay-custom-order-btn" data-order-id="{{ $order->id }}">
                                <i class="fas fa-credit-card mr-2"></i>Pay Now
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-box text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No orders yet</h3>
                    <p class="text-gray-600 mb-6">Start shopping to see your orders here</p>
                    <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 inline-block">
                        <i class="fas fa-shopping-bag mr-2"></i>Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile dropdown functionality
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profileMenu');

        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function() {
                profileMenu.classList.add('hidden');
            });

            profileMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Filter functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const orderCards = document.querySelectorAll('.order-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const status = this.getAttribute('data-status');
                
                // Update button styles
                filterBtns.forEach(b => {
                    b.classList.remove('bg-blue-100', 'text-blue-800');
                    b.classList.add('bg-gray-100', 'text-gray-800');
                });
                this.classList.remove('bg-gray-100', 'text-gray-800');
                this.classList.add('bg-blue-100', 'text-blue-800');

                // Filter orders
                orderCards.forEach(card => {
                    if (status === 'all' || card.getAttribute('data-status') === status) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Sort functionality
        const sortSelect = document.getElementById('sort-select');
        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            const container = document.getElementById('orders-container');
            const cards = Array.from(container.querySelectorAll('.order-card'));

            cards.sort((a, b) => {
                switch(sortBy) {
                    case 'latest':
                        return new Date(b.querySelector('p').textContent.split('Placed on ')[1]) - new Date(a.querySelector('p').textContent.split('Placed on ')[1]);
                    case 'oldest':
                        return new Date(a.querySelector('p').textContent.split('Placed on ')[1]) - new Date(b.querySelector('p').textContent.split('Placed on ')[1]);
                    case 'amount-high':
                        return parseFloat(b.querySelector('.text-xl').textContent.replace('₱', '').replace(',', '')) - parseFloat(a.querySelector('.text-xl').textContent.replace('₱', '').replace(',', ''));
                    case 'amount-low':
                        return parseFloat(a.querySelector('.text-xl').textContent.replace('₱', '').replace(',', '')) - parseFloat(b.querySelector('.text-xl').textContent.replace('₱', '').replace(',', ''));
                    default:
                        return 0;
                }
            });

            cards.forEach(card => container.appendChild(card));
        });

        // Cancel order functionality (popup)
        document.querySelectorAll('.cancel-order-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                openCancelOrderModal(orderId);
            });
        });

        // View order details functionality
        // Pay order functionality
        document.querySelectorAll('.pay-order-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                startBuxCheckout(orderId);
            });
        });

        // Review order functionality
        document.querySelectorAll('.review-order-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                const productId = this.getAttribute('data-product-id');
                openReviewModal(orderId, productId);
            });
        });

        function cancelOrder(orderId) {
            fetch(`/api/v1/orders/${orderId}/cancel`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Order cancelled successfully', 'success');
                    location.reload();
                } else {
                    showNotification(data.message || 'Failed to cancel order', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        function openCancelOrderModal(orderId) {
            let modal = document.getElementById('order-cancel-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'order-cancel-modal';
                modal.className = 'fixed inset-0 z-50 hidden';
                modal.innerHTML = `
                    <div class=\"absolute inset-0 bg-black bg-opacity-20\"></div>
                    <div class=\"absolute inset-0 flex items-center justify-center p-4\">
                        <div class=\"bg-white w-full max-w-md rounded-lg shadow-xl overflow-hidden\">
                            <div class=\"px-6 py-4 border-b\">
                                <h3 class=\"text-lg font-semibold text-gray-800\">Cancel Order</h3>
                            </div>
                            <div class=\"px-6 py-5\">
                                <p class=\"text-sm text-gray-700\">Are you sure you want to cancel this order? This action cannot be undone.</p>
                                <div id=\"ord-cancel-error\" class=\"hidden mt-3 text-sm text-red-600\"></div>
                            </div>
                            <div class=\"px-6 py-4 border-t flex justify-end space-x-2\">
                                <button id=\"ord-cancel-close\" class=\"px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-gray-700\">No, keep order</button>
                                <button id=\"ord-cancel-confirm\" class=\"px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700\">Yes, cancel</button>
                            </div>
                        </div>
                    </div>`;
                document.body.appendChild(modal);
                const close = () => modal.classList.add('hidden');
                modal.querySelector('#ord-cancel-close').addEventListener('click', close);
                modal.addEventListener('click', e => { if (e.target === modal) close(); });
            }
            const errBox = modal.querySelector('#ord-cancel-error');
            errBox.classList.add('hidden');
            errBox.textContent = '';
            modal.classList.remove('hidden');
            modal.querySelector('#ord-cancel-confirm').onclick = async () => {
                try {
                    await cancelOrder(orderId);
                    modal.classList.add('hidden');
                } catch (e) {
                    errBox.textContent = e?.message || 'Failed to cancel order';
                    errBox.classList.remove('hidden');
                }
            };
        }

        function viewOrderDetails(orderId) {
            const modal = createOrGetOrderModal();
            const loading = modal.querySelector('#ord-loading');
            const content = modal.querySelector('#ord-content');
            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');

            fetch(`/api/v1/orders/${orderId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(async r => {
                if (!r.ok) {
                    const text = await r.text();
                    throw new Error(`HTTP ${r.status}: ${text.substring(0,200)}`);
                }
                return r.json();
            })
            .then(async data => {
                if (!data || data.success === false) throw new Error('Failed to load');
                const order = data.data || data;
                await renderOrderIntoModal(order);
                loading.classList.add('hidden');
                content.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                loading.innerHTML = '<span class="text-red-600">Failed to load order details. Please make sure you are logged in and try again.</span>';
            });
        }

        function createOrGetOrderModal() {
            let modal = document.getElementById('order-details-modal');
            if (modal) return modal;
            modal = document.createElement('div');
            modal.id = 'order-details-modal';
            modal.className = 'fixed inset-0 z-50 hidden';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="bg-white w-full max-w-5xl h-[75vh] rounded-lg shadow-xl overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
                            <div>
                                <h3 id="ord-title" class="text-lg font-semibold text-gray-800">Order Details</h3>
                                <p id="ord-subtitle" class="text-sm text-gray-500"></p>
                            </div>
                            <button id="ord-close" class="text-gray-500 hover:text-gray-700 p-2"><i class="fas fa-times text-lg"></i></button>
                        </div>
                        <div class="px-6 py-4 flex-1 overflow-y-auto">
                            <div id="ord-loading" class="py-10 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Loading...</div>
                            <div id="ord-content" class="hidden space-y-4">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div class="p-3 bg-gray-50 rounded">
                                        <h4 class="font-semibold text-gray-700 mb-2">Customer</h4>
                                        <p id="ord-cust-name" class="text-sm text-gray-800">-</p>
                                        <p id="ord-cust-email" class="text-sm text-gray-600">-</p>
                                        <p id="ord-cust-phone" class="text-sm text-gray-600"></p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded">
                                        <h4 class="font-semibold text-gray-700 mb-2">Payment</h4>
                                        <p id="ord-payment" class="text-sm text-gray-800">-</p>
                                        <p id="ord-status" class="text-sm"></p>
                                        <p id="ord-payment-status" class="text-xs text-gray-500"></p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded">
                                        <h4 class="font-semibold text-gray-700 mb-2">Placed</h4>
                                        <p id="ord-date" class="text-sm text-gray-800">-</p>
                                        <p id="ord-number" class="text-sm text-gray-600">-</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="p-3 bg-gray-50 rounded">
                                        <h4 class="font-semibold text-gray-700 mb-2">Shipping Address</h4>
                                        <p id="ord-ship" class="text-sm text-gray-700">-</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded">
                                        <h4 class="font-semibold text-gray-700 mb-2">Billing Address</h4>
                                        <p id="ord-bill" class="text-sm text-gray-700">-</p>
                                    </div>
                                </div>
                                <div class="p-3 bg-white border rounded">
                                    <h4 class="font-semibold text-gray-700 mb-2">Items</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full table-fixed divide-y divide-gray-200 text-sm">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 w-3/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                                    <th class="px-3 py-2 w-2/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                                    <th class="px-3 py-2 w-1/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                                    <th class="px-3 py-2 w-1/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                                    <th class="px-3 py-2 w-1/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                                    <th class="px-3 py-2 w-2/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                                    <th class="px-3 py-2 w-2/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ord-items" class="bg-white divide-y divide-gray-200"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="p-3 bg-gray-50 rounded">
                                        <h4 class="font-semibold text-gray-700 mb-2">Order Notes</h4>
                                        <p id="ord-notes" class="text-sm text-gray-700">—</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded">
                                        <h4 class="font-semibold text-gray-700 mb-2">Summary</h4>
                                        <div class="text-sm text-gray-600">Subtotal: <span id="ord-subtotal" class="font-medium text-gray-800">₱0.00</span></div>
                                        <div class="text-sm text-gray-600">Shipping: <span id="ord-shipping" class="font-medium text-gray-800">₱0.00</span></div>
                                        <div class="text-sm text-gray-600">Discount: <span id="ord-discount" class="font-medium text-gray-800">₱0.00</span></div>
                                        <div class="text-sm text-gray-600">Tax: <span id="ord-tax" class="font-medium text-gray-800">₱0.00</span></div>
                                        <div class="mt-1 text-base text-gray-800 font-semibold">Grand Total: <span id="ord-total" class="text-gray-900">₱0.00</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t flex justify-end flex-shrink-0">
                            <button id="ord-close-bottom" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-gray-700">Close</button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modal);
            const close = () => modal.classList.add('hidden');
            modal.querySelector('#ord-close').addEventListener('click', close);
            modal.querySelector('#ord-close-bottom').addEventListener('click', close);
            modal.addEventListener('click', e => { if (e.target === modal) close(); });
            return modal;
        }

        // Utility functions
        async function formatAddress(address) {
            if (!address) return 'Not provided';
            if (typeof address === 'string') {
                try {
                    address = JSON.parse(address);
                } catch (e) {
                    return address; // Return as-is if not JSON
                }
            }
            if (typeof address === 'object' && address !== null) {
                const parts = [];
                if (address.name || address.full_name) parts.push(address.name || address.full_name);
                if (address.line1 || address.address) parts.push(address.line1 || address.address);
                if (address.line2) parts.push(address.line2);
                
                // Fetch names for city, province, region, barangay if they are numeric codes
                let cityName = address.city || '';
                let provinceName = address.province || '';
                let regionName = address.region || '';
                let barangayName = address.barangay || '';
                
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
                
                if (barangayName) parts.push(barangayName);
                if (cityName) parts.push(cityName);
                if (provinceName) parts.push(provinceName);
                if (regionName) parts.push(regionName);
                
                if (address.postal_code || address.zip) parts.push(address.postal_code || address.zip);
                if (address.country) parts.push(address.country);
                if (address.phone) parts.push(`Phone: ${address.phone}`);
                return parts.join(', ') || 'Address not available';
            }
            return 'Address not available';
        }

        function formatText(text) {
            if (!text) return '—';
            return text.toString().replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        function formatMoney(amount) {
            return parseFloat(amount || 0).toFixed(2);
        }

        function toNumber(value) {
            return parseFloat(value || 0);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function renderStatusPill(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'processing': 'bg-blue-100 text-blue-800',
                'shipped': 'bg-purple-100 text-purple-800',
                'delivered': 'bg-green-100 text-green-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800',
                'refunded': 'bg-gray-100 text-gray-800'
            };
            const colorClass = colors[status] || 'bg-gray-100 text-gray-800';
            return `<span class="px-2 py-1 text-xs font-semibold rounded-full ${colorClass}">${formatText(status)}</span>`;
        }

        function inferPhone(order) {
            if (order.user && order.user.phone) return order.user.phone;
            if (order.shipping_address) {
                const addr = typeof order.shipping_address === 'string' ? 
                    JSON.parse(order.shipping_address || '{}') : order.shipping_address;
                if (addr && addr.phone) return addr.phone;
            }
            if (order.billing_address) {
                const addr = typeof order.billing_address === 'string' ? 
                    JSON.parse(order.billing_address || '{}') : order.billing_address;
                if (addr && addr.phone) return addr.phone;
            }
            return 'Not provided';
        }

        async function renderOrderIntoModal(order) {
            const modal = document.getElementById('order-details-modal');
            modal.querySelector('#ord-title').textContent = `Order #${order.order_number ?? order.id}`;
            modal.querySelector('#ord-subtitle').textContent = order.created_at ? new Date(order.created_at).toLocaleString() : '';
            modal.querySelector('#ord-cust-name').textContent = (order.user && (order.user.name || order.user.full_name)) || 'Guest';
            modal.querySelector('#ord-cust-email').textContent = (order.user && order.user.email) || 'N/A';
            modal.querySelector('#ord-payment').textContent = formatText(order.payment_method);
            modal.querySelector('#ord-status').innerHTML = renderStatusPill(order.status);
            modal.querySelector('#ord-payment-status').textContent = order.payment_status ? `Payment: ${formatText(order.payment_status)}` : '';
            modal.querySelector('#ord-date').textContent = order.created_at ? new Date(order.created_at).toLocaleString() : '—';
            modal.querySelector('#ord-number').textContent = `#${order.order_number ?? order.id}`;
            
            // Fetch and format addresses
            const shippingAddr = await formatAddress(order.shipping_address);
            const billingAddr = await formatAddress(order.billing_address);
            
            modal.querySelector('#ord-ship').textContent = shippingAddr;
            modal.querySelector('#ord-bill').textContent = billingAddr;
            modal.querySelector('#ord-cust-phone').textContent = inferPhone(order);
            modal.querySelector('#ord-notes').textContent = order.notes || '—';

            const tbody = modal.querySelector('#ord-items');
            tbody.innerHTML = '';
            let subtotal = 0;
            (order.order_items || order.items || []).forEach(it => {
                const unit = toNumber(it.unit_price ?? it.price ?? 0);
                const qty = toNumber(it.quantity ?? 0);
                const line = unit * qty;
                subtotal += line;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-3 py-2 text-sm text-blue-700">${escapeHtml((it.product && it.product.name) || it.name || 'Item')}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">${escapeHtml(it.product_sku || (it.product && it.product.sku) || '—')}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">${escapeHtml(it.size || '—')}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">${escapeHtml(it.color || '—')}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">${qty}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">₱${formatMoney(unit)}</td>
                    <td class="px-3 py-2 text-sm text-gray-800 font-medium">₱${formatMoney(line)}</td>
                `;
                tbody.appendChild(tr);
            });
            const shippingFee = toNumber(order.shipping_fee ?? order.shipping_amount ?? 0);
            const discount = toNumber(order.discount ?? order.discount_amount ?? 0);
            const tax = toNumber(order.tax_amount ?? 0);
            const grand = toNumber(order.total_amount ?? (subtotal + shippingFee - discount + tax));
            modal.querySelector('#ord-subtotal').textContent = `₱${formatMoney(subtotal)}`;
            modal.querySelector('#ord-shipping').textContent = `₱${formatMoney(shippingFee)}`;
            const discountEl = modal.querySelector('#ord-discount');
            if (discountEl) discountEl.textContent = `₱${formatMoney(discount)}`;
            const taxEl = modal.querySelector('#ord-tax');
            if (taxEl) taxEl.textContent = `₱${formatMoney(tax)}`;
            modal.querySelector('#ord-total').textContent = `₱${formatMoney(grand)}`;
        }

        function openReviewModal(orderId, productId) {
            let modal = document.getElementById('review-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'review-modal';
                modal.className = 'fixed inset-0 z-50 hidden';
                modal.innerHTML = `
                    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <div class="bg-white w-full max-w-md rounded-lg shadow-xl overflow-hidden">
                            <div class="px-6 py-4 border-b flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800">Write a Review</h3>
                                <button id="review-close" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <div class="px-6 py-5 space-y-4">
                                <!-- Star Rating -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex space-x-1" id="star-rating">
                                        <i class="far fa-star text-2xl text-gray-300 hover:text-yellow-400 cursor-pointer" data-rating="1"></i>
                                        <i class="far fa-star text-2xl text-gray-300 hover:text-yellow-400 cursor-pointer" data-rating="2"></i>
                                        <i class="far fa-star text-2xl text-gray-300 hover:text-yellow-400 cursor-pointer" data-rating="3"></i>
                                        <i class="far fa-star text-2xl text-gray-300 hover:text-yellow-400 cursor-pointer" data-rating="4"></i>
                                        <i class="far fa-star text-2xl text-gray-300 hover:text-yellow-400 cursor-pointer" data-rating="5"></i>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Click on a star to rate</p>
                                </div>
                                
                                <!-- Comment -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                                    <textarea id="review-comment" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Share your experience with this product..."></textarea>
                                </div>
                                
                                <!-- Error Message -->
                                <div id="review-error" class="hidden text-sm text-red-600"></div>
                            </div>
                            <div class="px-6 py-4 border-t flex justify-end space-x-2">
                                <button id="review-cancel" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-gray-700">Cancel</button>
                                <button id="review-submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit Review</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                modal.querySelector('#review-close').addEventListener('click', () => modal.classList.add('hidden'));
                modal.querySelector('#review-cancel').addEventListener('click', () => modal.classList.add('hidden'));
            }
            
            // Setup star rating
            let currentRating = 0;
            const stars = modal.querySelectorAll('#star-rating i');
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    currentRating = parseInt(this.getAttribute('data-rating'));
                    updateStarDisplay(stars, currentRating);
                });
                star.addEventListener('mouseenter', function() {
                    const hoverRating = parseInt(this.getAttribute('data-rating'));
                    updateStarDisplay(stars, hoverRating);
                });
            });
            document.getElementById('star-rating').addEventListener('mouseleave', function() {
                updateStarDisplay(stars, currentRating);
            });
            
            function updateStarDisplay(stars, rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('far');
                        star.classList.add('fas', 'text-yellow-400');
                    } else {
                        star.classList.remove('fas', 'text-yellow-400');
                        star.classList.add('far', 'text-gray-300');
                    }
                });
            }
            
            // Reset form
            document.getElementById('review-comment').value = '';
            document.getElementById('review-error').classList.add('hidden');
            currentRating = 0;
            updateStarDisplay(stars, 0);
            
            // Setup submit handler
            modal.querySelector('#review-submit').onclick = async () => {
                const comment = document.getElementById('review-comment').value.trim();
                const errorDiv = document.getElementById('review-error');
                
                if (currentRating === 0) {
                    errorDiv.textContent = 'Please select a rating';
                    errorDiv.classList.remove('hidden');
                    return;
                }
                
                if (!comment) {
                    errorDiv.textContent = 'Please write a comment';
                    errorDiv.classList.remove('hidden');
                    return;
                }
                
                try {
                    const response = await fetch('/api/v1/reviews', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            product_id: productId,
                            rating: currentRating,
                            review_text: comment
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        showNotification('Review submitted successfully!', 'success');
                        modal.classList.add('hidden');
                        // Reload to update the page
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        errorDiv.textContent = data.message || 'Failed to submit review';
                        errorDiv.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Review submission error:', error);
                    errorDiv.textContent = 'An error occurred. Please try again.';
                    errorDiv.classList.remove('hidden');
                }
            };
            
            modal.classList.remove('hidden');
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function updateCartCount() {
            fetch('/api/v1/cart')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            // Use items array length or fallback to 0
                            cartCount.textContent = data.items ? data.items.length : 0;
                        }
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }

        async function startBuxCheckout(orderId) {
            try {
                // Show loading state
                const payBtn = document.querySelector(`[data-order-id="${orderId}"]`);
                const originalText = payBtn.innerHTML;
                payBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                payBtn.disabled = true;

                // Try to create Bux checkout URL
                const res = await fetch(`/api/v1/orders/${orderId}/bux-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await res.json();
                
                if (res.ok && data.success) {
                    // Bux API working - redirect to checkout
                    const url = data.data.checkout_url || data.data.url || data.data.redirect_url;
                    if (url) {
                        window.location.href = url;
                        return;
                    }
                }

                // Bux API failed - simulate GCash payment for testing
                console.log('Bux API failed, simulating GCash payment...');
                await simulateGCashPayment(orderId);
                
            } catch (e) {
                console.error(e);
                // If Bux fails, simulate GCash payment for testing
                console.log('Bux API error, simulating GCash payment...');
                await simulateGCashPayment(orderId);
            }
        }

        async function simulateGCashPayment(orderId) {
            try {
                // Show GCash simulation modal
                showGCashModal(orderId);
            } catch (e) {
                console.error('GCash simulation failed:', e);
                showNotification('Payment simulation failed', 'error');
            }
        }

        function showGCashModal(orderId) {
            // Create modal HTML
            const modalHTML = `
                <div id="gcash-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-mobile-alt text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">GCash Payment</h3>
                            <p class="text-gray-600 mb-4">Simulating GCash payment process...</p>
                            <div class="space-y-3">
                                <button id="simulate-success" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700">
                                    <i class="fas fa-check mr-2"></i>Simulate Successful Payment
                                </button>
                                <button id="simulate-failed" class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700">
                                    <i class="fas fa-times mr-2"></i>Simulate Failed Payment
                                </button>
                                <button id="cancel-payment" class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to page
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Add event listeners
            document.getElementById('simulate-success').onclick = () => processPaymentResult(orderId, 'paid');
            document.getElementById('simulate-failed').onclick = () => processPaymentResult(orderId, 'failed');
            document.getElementById('cancel-payment').onclick = () => closeGCashModal();
        }

        function closeGCashModal() {
            const modal = document.getElementById('gcash-modal');
            if (modal) {
                modal.remove();
            }
        }

        async function processPaymentResult(orderId, status) {
            try {
                closeGCashModal();
                
                // Show processing message
                showNotification('Processing payment...', 'info');
                
                // Update order status via API
                const response = await fetch(`/api/v1/orders/${orderId}/update-payment-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        payment_status: status,
                        transaction_id: status === 'paid' ? 'GCASH_' + Date.now() : null,
                        paid_at: status === 'paid' ? new Date().toISOString() : null
                    })
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    if (status === 'paid') {
                        showNotification('Payment successful! Order status updated.', 'success');
                        // Reload page to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showNotification('Payment failed. Please try again.', 'error');
                    }
                } else {
                    throw new Error(result.message || 'Failed to update payment status');
                }
                
            } catch (error) {
                console.error('Payment processing error:', error);
                showNotification('Failed to process payment. Please try again.', 'error');
            }
        }

        // Initialize cart count on page load
        updateCartCount();
        
        // Fetch and replace address codes with names after page loads
        loadAddressNames();
    });
    
    async function loadAddressNames() {
        // Find all address containers with data attributes
        const shippingContainers = document.querySelectorAll('[data-shipping-address]');
        const billingContainers = document.querySelectorAll('[data-billing-address]');
        
        // Process shipping addresses
        for (const container of shippingContainers) {
            try {
                const addressData = JSON.parse(container.dataset.shippingAddress);
                if (addressData && (isNumeric(addressData.city) || isNumeric(addressData.province) || isNumeric(addressData.region) || isNumeric(addressData.barangay))) {
                    const formatted = await formatAddressInOrder(addressData);
                    if (formatted) {
                        container.textContent = formatted;
                    }
                }
            } catch (e) {
                console.error('Error formatting shipping address:', e);
            }
        }
        
        // Process billing addresses
        for (const container of billingContainers) {
            try {
                const addressData = JSON.parse(container.dataset.billingAddress);
                if (addressData && (isNumeric(addressData.city) || isNumeric(addressData.province) || isNumeric(addressData.region) || isNumeric(addressData.barangay))) {
                    const formatted = await formatAddressInOrder(addressData);
                    if (formatted) {
                        container.textContent = formatted;
                    }
                }
            } catch (e) {
                console.error('Error formatting billing address:', e);
            }
        }
    }
    
    async function formatAddressInOrder(address) {
        if (!address) return 'Not provided';
        if (typeof address === 'string') {
            try {
                address = JSON.parse(address);
            } catch (e) {
                return address;
            }
        }
        if (typeof address === 'object' && address !== null) {
            const parts = [];
            if (address.name || address.full_name) parts.push(address.name || address.full_name);
            if (address.line1 || address.address) parts.push(address.line1 || address.address);
            if (address.line2) parts.push(address.line2);
            
            // Fetch names for city, province, region, barangay if they are numeric codes
            let cityName = address.city || '';
            let provinceName = address.province || '';
            let regionName = address.region || '';
            let barangayName = address.barangay || '';
            
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
            
            if (barangayName) parts.push(barangayName);
            if (cityName) parts.push(cityName);
            if (provinceName) parts.push(provinceName);
            if (regionName) parts.push(regionName);
            
            if (address.postal_code || address.zip) parts.push(address.postal_code || address.zip);
            if (address.country) parts.push(address.country);
            if (address.phone) parts.push(`Phone: ${address.phone}`);
            return parts.join(', ') || 'Address not available';
        }
        return 'Address not available';
    }
    
    function isNumeric(str) {
        return !isNaN(str) && str.match(/^\d+$/);
    }
    </script>
    
    @include('layouts.footer')
</body>
</html>
