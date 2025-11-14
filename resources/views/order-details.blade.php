<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order Details - 3Migs Gowns & Barong</title>
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
                        <a href="{{ route('orders') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">My Orders</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Order Details</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Order Details Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->order_number }}</h1>
                        <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 text-sm rounded-full 
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        <p class="text-xl font-bold text-gray-800 mt-2">₱{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Order Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-3">Customer Information</h3>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Name:</span> {{ $order->user->name ?? 'N/A' }}</p>
                                <p><span class="font-medium">Email:</span> {{ $order->user->email ?? 'N/A' }}</p>
                                <p><span class="font-medium">Phone:</span> {{ $order->user->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-3">Payment Information</h3>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Method:</span> {{ ucfirst($order->payment_method) }}</p>
                                <p><span class="font-medium">Status:</span> {{ ucfirst($order->payment_status ?? 'Pending') }}</p>
                                <p><span class="font-medium">Order Date:</span> {{ $order->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-3">Order Summary</h3>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Order Number:</span> #{{ $order->order_number }}</p>
                                <p><span class="font-medium">Items:</span> {{ $order->orderItems->count() }} item(s)</p>
                                <p><span class="font-medium">Total:</span> ₱{{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-3">Shipping Address</h3>
                            <div class="text-sm text-gray-700">
                                @php
                                    $shippingAddress = is_string($order->shipping_address) ? json_decode($order->shipping_address, true) : $order->shipping_address;
                                @endphp
                                @if($shippingAddress)
                                    <p class="font-medium">{{ $shippingAddress['full_name'] ?? 'N/A' }}</p>
                                    @if(isset($shippingAddress['company_name']) && $shippingAddress['company_name'])
                                        <p>{{ $shippingAddress['company_name'] }}</p>
                                    @endif
                                    <p>{{ $shippingAddress['street_address'] ?? 'N/A' }}</p>
                                    @if(isset($shippingAddress['apartment']) && $shippingAddress['apartment'])
                                        <p>{{ $shippingAddress['apartment'] }}</p>
                                    @endif
                                    <p>{{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['province'] ?? 'N/A' }}</p>
                                    <p>{{ $shippingAddress['postal_code'] ?? 'N/A' }}</p>
                                    <p class="font-medium">{{ $shippingAddress['phone'] ?? 'N/A' }}</p>
                                @else
                                    <p>No shipping address provided</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-3">Billing Address</h3>
                            <div class="text-sm text-gray-700">
                                @php
                                    $billingAddress = is_string($order->billing_address) ? json_decode($order->billing_address, true) : $order->billing_address;
                                @endphp
                                @if($billingAddress)
                                    <p class="font-medium">{{ $billingAddress['full_name'] ?? 'N/A' }}</p>
                                    @if(isset($billingAddress['company_name']) && $billingAddress['company_name'])
                                        <p>{{ $billingAddress['company_name'] }}</p>
                                    @endif
                                    <p>{{ $billingAddress['street_address'] ?? 'N/A' }}</p>
                                    @if(isset($billingAddress['apartment']) && $billingAddress['apartment'])
                                        <p>{{ $billingAddress['apartment'] }}</p>
                                    @endif
                                    <p>{{ $billingAddress['city'] ?? 'N/A' }}, {{ $billingAddress['province'] ?? 'N/A' }}</p>
                                    <p>{{ $billingAddress['postal_code'] ?? 'N/A' }}</p>
                                    <p class="font-medium">{{ $billingAddress['phone'] ?? 'N/A' }}</p>
                                @else
                                    <p>No billing address provided</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="p-4 bg-white border rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-4">Order Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 w-3/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-4 py-3 w-2/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-4 py-3 w-1/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                        <th class="px-4 py-3 w-1/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                        <th class="px-4 py-3 w-1/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 w-2/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-3 w-2/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        @if($order->status === 'delivered')
                                        <th class="px-4 py-3 w-2/12 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($order->orderItems as $item)
                                    @php
                                        $hasCustomMeasurements = !empty($item->custom_measurements) && 
                                            is_array($item->custom_measurements) &&
                                            !empty(array_filter($item->custom_measurements, function($val) {
                                                return !empty($val) && trim($val) !== '';
                                            }));
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-sm">
                                            <div>
                                                <span class="text-blue-700">{{ $item->product_name ?? ($item->product->name ?? 'Item') }}</span>
                                                @if($hasCustomMeasurements)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                                        <i class="fas fa-ruler-combined mr-1 text-xs"></i>Custom Size
                                                    </span>
                                                @endif
                                                @if($hasCustomMeasurements)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        @php
                                                            $parts = [];
                                                            if (!empty($item->custom_measurements['shoulder'])) $parts[] = 'Shoulder: ' . $item->custom_measurements['shoulder'] . '"';
                                                            if (!empty($item->custom_measurements['chest'])) $parts[] = 'Chest: ' . $item->custom_measurements['chest'] . '"';
                                                            if (!empty($item->custom_measurements['sleeve'])) $parts[] = 'Sleeve: ' . $item->custom_measurements['sleeve'] . '"';
                                                            if (!empty($item->custom_measurements['waist'])) $parts[] = 'Waist: ' . $item->custom_measurements['waist'] . '"';
                                                            if (!empty($item->custom_measurements['notes'])) $parts[] = 'Notes: ' . $item->custom_measurements['notes'];
                                                        @endphp
                                                        {{ implode(' • ', $parts) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->product_sku ?? ($item->product->sku ?? '—') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->size ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->color ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 font-medium">₱{{ number_format($item->total_price, 2) }}</td>
                                        @if($order->status === 'delivered')
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $hasReviewed = \App\Models\Review::where('user_id', auth()->id())
                                                    ->where('product_id', $item->product_id)
                                                    ->where('order_id', $order->id)
                                                    ->exists();
                                            @endphp
                                            @if($hasReviewed)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Reviewed
                                                </span>
                                            @else
                                                <button onclick="openReviewModal({{ $item->product_id }}, {{ $order->id }}, '{{ $item->product_name ?? ($item->product->name ?? 'Item') }}')" 
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                                    <i class="fas fa-star mr-1"></i>
                                                    Review
                                                </button>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-3">Order Notes</h3>
                            <p class="text-sm text-gray-700">{{ $order->notes ?? 'No notes provided' }}</p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-3">Order Summary</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-medium text-gray-800">₱{{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Shipping:</span>
                                    <span class="font-medium text-gray-800">₱{{ number_format($order->shipping_fee, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Discount:</span>
                                    <span class="font-medium text-gray-800">₱{{ number_format($order->discount, 2) }}</span>
                                </div>
                                @if($order->tax_amount)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tax:</span>
                                    <span class="font-medium text-gray-800">₱{{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between border-t border-gray-300 pt-2 mt-2">
                                    <span class="text-base font-semibold text-gray-800">Grand Total:</span>
                                    <span class="text-base font-bold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center">
                <a href="{{ route('orders') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Orders
                </a>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Print Order
                    </button>
                    @if($order->status === 'pending' && $order->payment_status === 'pending')
                    <button class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors pay-order-btn" data-order-id="{{ $order->id }}">
                        <i class="fas fa-credit-card mr-2"></i>
                        Pay Now
                    </button>
                    @endif
                    @if($order->status === 'pending' || $order->status === 'processing')
                    <button class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Cancel Order
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')

    <script>
        // Print functionality
        function printOrder() {
            window.print();
        }

        // Payment functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Pay Now button functionality
            document.querySelectorAll('.pay-order-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    startBuxCheckout(orderId);
                });
            });
        });

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

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Review modal variables
        let currentProductId = null;
        let currentOrderId = null;
        let selectedRating = 0;

        // Review modal functions
        function openReviewModal(productId, orderId, productName) {
            currentProductId = productId;
            currentOrderId = orderId;
            selectedRating = 0;
            
            // Create modal if it doesn't exist
            if (!document.getElementById('reviewModal')) {
                createReviewModal();
            }
            
            document.getElementById('reviewProductName').textContent = productName;
            document.getElementById('reviewText').value = '';
            document.getElementById('reviewModal').classList.remove('hidden');
            
            // Reset stars
            resetStars();
        }

        function createReviewModal() {
            const modalHTML = `
                <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <!-- Modal Header -->
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Write a Review</h3>
                                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-4">Product: <span id="reviewProductName" class="font-medium"></span></p>
                                
                                <!-- Star Rating -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex space-x-1" id="starRating">
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="1">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="2">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="3">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="4">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="5">
                                            <i class="far fa-star"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Click to rate</p>
                                </div>

                                <!-- Review Text -->
                                <div class="mb-4">
                                    <label for="reviewText" class="block text-sm font-medium text-gray-700 mb-2">Review (Optional)</label>
                                    <textarea id="reviewText" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Share your experience with this product..."></textarea>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end space-x-3">
                                <button onclick="closeReviewModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                                    Cancel
                                </button>
                                <button onclick="submitReview()" id="submitReviewBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Submit Review
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            setupStarRating();
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (modal) {
                modal.classList.add('hidden');
            }
            currentProductId = null;
            currentOrderId = null;
            selectedRating = 0;
        }

        function setupStarRating() {
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach((btn, index) => {
                btn.addEventListener('click', function() {
                    selectedRating = parseInt(this.getAttribute('data-rating'));
                    updateStars(selectedRating);
                });

                btn.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    updateStars(rating);
                });
            });

            // Reset stars when mouse leaves the rating area
            const starRating = document.getElementById('starRating');
            if (starRating) {
                starRating.addEventListener('mouseleave', function() {
                    updateStars(selectedRating);
                });
            }
        }

        function updateStars(rating) {
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach((btn, index) => {
                const starIcon = btn.querySelector('i');
                if (index < rating) {
                    starIcon.className = 'fas fa-star text-yellow-400';
                } else {
                    starIcon.className = 'far fa-star text-gray-300';
                }
            });
        }

        function resetStars() {
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach(btn => {
                const starIcon = btn.querySelector('i');
                starIcon.className = 'far fa-star text-gray-300';
            });
        }

        async function submitReview() {
            if (selectedRating === 0) {
                alert('Please select a rating');
                return;
            }

            const reviewText = document.getElementById('reviewText').value.trim();
            const submitBtn = document.getElementById('submitReviewBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
            submitBtn.disabled = true;

            try {
                // Submit review
                const response = await fetch('/api/v1/reviews', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: currentProductId,
                        order_id: currentOrderId,
                        rating: selectedRating,
                        review_text: reviewText
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Review submitted successfully!', 'success');
                    closeReviewModal();
                    // Reload the page to show updated review status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Failed to submit review');
                }
            } catch (error) {
                console.error('Review submission error:', error);
                showNotification('Failed to submit review. Please try again.', 'error');
                
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    </script>
</body>
</html>

            } catch (error) {
                console.error('Payment processing error:', error);
                showNotification('Failed to process payment. Please try again.', 'error');
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Review modal variables
        let currentProductId = null;
        let currentOrderId = null;
        let selectedRating = 0;

        // Review modal functions
        function openReviewModal(productId, orderId, productName) {
            currentProductId = productId;
            currentOrderId = orderId;
            selectedRating = 0;
            
            // Create modal if it doesn't exist
            if (!document.getElementById('reviewModal')) {
                createReviewModal();
            }
            
            document.getElementById('reviewProductName').textContent = productName;
            document.getElementById('reviewText').value = '';
            document.getElementById('reviewModal').classList.remove('hidden');
            
            // Reset stars
            resetStars();
        }

        function createReviewModal() {
            const modalHTML = `
                <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <!-- Modal Header -->
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Write a Review</h3>
                                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-4">Product: <span id="reviewProductName" class="font-medium"></span></p>
                                
                                <!-- Star Rating -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex space-x-1" id="starRating">
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="1">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="2">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="3">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="4">
                                            <i class="far fa-star"></i>
                                        </button>
                                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 text-2xl" data-rating="5">
                                            <i class="far fa-star"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Click to rate</p>
                                </div>

                                <!-- Review Text -->
                                <div class="mb-4">
                                    <label for="reviewText" class="block text-sm font-medium text-gray-700 mb-2">Review (Optional)</label>
                                    <textarea id="reviewText" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Share your experience with this product..."></textarea>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end space-x-3">
                                <button onclick="closeReviewModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                                    Cancel
                                </button>
                                <button onclick="submitReview()" id="submitReviewBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Submit Review
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            setupStarRating();
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (modal) {
                modal.classList.add('hidden');
            }
            currentProductId = null;
            currentOrderId = null;
            selectedRating = 0;
        }

        function setupStarRating() {
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach((btn, index) => {
                btn.addEventListener('click', function() {
                    selectedRating = parseInt(this.getAttribute('data-rating'));
                    updateStars(selectedRating);
                });

                btn.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    updateStars(rating);
                });
            });

            // Reset stars when mouse leaves the rating area
            const starRating = document.getElementById('starRating');
            if (starRating) {
                starRating.addEventListener('mouseleave', function() {
                    updateStars(selectedRating);
                });
            }
        }

        function updateStars(rating) {
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach((btn, index) => {
                const starIcon = btn.querySelector('i');
                if (index < rating) {
                    starIcon.className = 'fas fa-star text-yellow-400';
                } else {
                    starIcon.className = 'far fa-star text-gray-300';
                }
            });
        }

        function resetStars() {
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach(btn => {
                const starIcon = btn.querySelector('i');
                starIcon.className = 'far fa-star text-gray-300';
            });
        }

        async function submitReview() {
            if (selectedRating === 0) {
                alert('Please select a rating');
                return;
            }

            const reviewText = document.getElementById('reviewText').value.trim();
            const submitBtn = document.getElementById('submitReviewBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
            submitBtn.disabled = true;

            try {
                // Submit review
                const response = await fetch('/api/v1/reviews', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: currentProductId,
                        order_id: currentOrderId,
                        rating: selectedRating,
                        review_text: reviewText
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Review submitted successfully!', 'success');
                    closeReviewModal();
                    // Reload the page to show updated review status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Failed to submit review');
                }
            } catch (error) {
                console.error('Review submission error:', error);
                showNotification('Failed to submit review. Please try again.', 'error');
                
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    </script>
</body>
</html>
