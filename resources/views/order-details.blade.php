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
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-blue-700">{{ $item->product_name ?? ($item->product->name ?? 'Item') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->product_sku ?? ($item->product->sku ?? '—') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->size ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->color ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 font-medium">₱{{ number_format($item->total_price, 2) }}</td>
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
    </script>
</body>
</html>
