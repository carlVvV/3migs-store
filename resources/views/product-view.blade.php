@extends('layouts.app')

@section('title', $product->name . ' - 3Migs Barong')

@section('content')
<div class="min-h-screen bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-gray-900 transition-colors">Account</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $product->category->name ?? 'Womens Barong' }}</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900 font-medium">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Panel - Product Images (1/3 width) -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Main Image -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="aspect-w-1 aspect-h-1">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-96 object-cover" id="main-image">
                        @else
                            <div class="w-full h-96 bg-gray-100 flex items-center justify-center">
                                <div class="text-center text-gray-400">
                                    <i class="fas fa-image text-6xl mb-4"></i>
                                    <p class="text-lg">No Image Available</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Thumbnail Images -->
                @if($product->images && count($product->images) > 1)
                <div class="grid grid-cols-2 gap-3">
                    @foreach($product->image_urls as $index => $imageUrl)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow {{ $product->cover_image == $product->images[$index] ? 'ring-2 ring-blue-500' : '' }}" onclick="changeMainImage('{{ $imageUrl }}', this)">
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-20 object-cover">
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Placeholder thumbnails -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-100 rounded-lg border border-gray-200 h-20 flex items-center justify-center">
                        <span class="text-gray-400 text-sm">thumb1</span>
                    </div>
                    <div class="bg-gray-100 rounded-lg border border-gray-200 h-20 flex items-center justify-center">
                        <span class="text-gray-400 text-sm">thumb2</span>
                    </div>
                    <div class="bg-gray-100 rounded-lg border border-gray-200 h-20 flex items-center justify-center">
                        <span class="text-gray-400 text-sm">thumb3</span>
                    </div>
                    <div class="bg-gray-100 rounded-lg border border-gray-200 h-20 flex items-center justify-center">
                        <span class="text-gray-400 text-sm">thumb4</span>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Right Panel - Product Details (2/3 width) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Title -->
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                
                
                
                <!-- Rating and Reviews -->
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= 4)
                                <i class="fas fa-star text-yellow-400"></i>
                            @elseif($i == 5)
                                <i class="fas fa-star-half-alt text-yellow-400"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-gray-600">(89 Reviews)</span>
                </div>
                
                <!-- Price -->
                <div class="mb-6">
                    <span class="text-4xl font-bold text-red-500">₱{{ number_format($product->current_price, 2) }}</span>
                </div>
                
                <!-- Description -->
                <div class="mb-6">
                    <p class="text-gray-600 text-lg leading-relaxed">
                        {{ $product->description ?: 'Tradition is timeless, not style. Forever embrace the Filipiniana where roots & modern meet.' }}
                    </p>
                </div>
                
                <!-- Colors -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Colours:</h3>
                    <select id="colorSelect" class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                        @if($product->colors && count($product->colors) > 0)
                            @foreach($product->colors as $index => $color)
                                <option value="{{ $color }}" {{ $index == 0 ? 'selected' : '' }}>{{ ucfirst($color) }}</option>
                            @endforeach
                        @else
                            <option value="black" selected>Black</option>
                            <option value="red">Red</option>
                            <option value="yellow">Yellow</option>
                            <option value="gray">Gray</option>
                        @endif
                    </select>
                </div>
                
                <!-- Size Selection -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-900">Size:</h3>
                        <button type="button" onclick="checkForUpdates()" 
                                class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Refresh Stock
                        </button>
                    </div>
                    <div class="flex space-x-3">
                        @php
                            $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                            $sizeStocks = $product->size_stocks ?? [];
                            
                            // Find the first available size, default to M if available
                            $defaultSize = 'M';
                            $selectedSize = null;
                            foreach($sizes as $size) {
                                if (($sizeStocks[$size] ?? 0) > 0) {
                                    if ($size === $defaultSize) {
                                        $selectedSize = $size;
                                        break;
                                    } elseif (!$selectedSize) {
                                        $selectedSize = $size;
                                    }
                                }
                            }
                            // If no size is available, don't select any
                            if (!$selectedSize) {
                                $selectedSize = $defaultSize; // Will be disabled anyway
                            }
                            
                            // Check if any size has stock
                            $hasAnyStock = false;
                            foreach($sizes as $size) {
                                if (($sizeStocks[$size] ?? 0) > 0) {
                                    $hasAnyStock = true;
                                    break;
                                }
                            }
                        @endphp
                        
                        @foreach($sizes as $size)
                            @php
                                $stock = $sizeStocks[$size] ?? 0;
                                $isAvailable = $stock > 0;
                                $isSelected = $size === $selectedSize;
                            @endphp
                            
                            <button class="px-6 py-3 border rounded-lg transition-colors size-btn {{ $isSelected ? 'bg-black text-white' : 'border-gray-300 text-gray-700 hover:border-gray-400' }} {{ !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                    data-size="{{ $size }}" 
                                    data-stock="{{ $stock }}"
                                    onclick="selectSize('{{ $size }}', this)"
                                    {{ !$isAvailable ? 'disabled' : '' }}
                                    title="{{ $isAvailable ? "Stock: {$stock}" : 'Out of stock' }}">
                                {{ $size }}
                                @if($isAvailable)
                                    <span class="text-xs ml-1">({{ $stock }})</span>
                                @else
                                    <span class="text-xs ml-1 text-red-500">(0)</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    
                    @if(count(array_filter($sizeStocks)) === 0)
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Out of Stock:</strong> This product is currently unavailable in all sizes.
                            </p>
                        </div>
                    @endif
                </div>
                
                @if ($hasAnyStock)
                    <!-- Quantity and Action Buttons -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button class="px-4 py-3 text-gray-600 hover:text-gray-800 transition-colors" onclick="decreaseQuantity()">-</button>
                            <span class="px-4 py-3 text-gray-900 font-medium" id="quantity">2</span>
                            <button class="px-4 py-3 text-gray-600 hover:text-gray-800 transition-colors" onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="space-y-3 mb-8">
                        <!-- Add to Cart Button -->
                        <button class="w-full bg-black hover:bg-gray-800 text-white py-3 rounded-lg font-medium transition-colors" onclick="addToCart()">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Add to Cart
                        </button>
                        
                        <!-- Buy Now Button -->
                        <button class="w-full bg-red-500 hover:bg-red-600 text-white py-4 rounded-lg font-bold text-lg transition-colors" onclick="buyNow()">
                            <i class="fas fa-bolt mr-2"></i>
                            Buy Now
                        </button>
                    </div>
                @else
                    <!-- Out of Stock Actions -->
                    <div class="mb-4">
                        <span class="inline-block bg-red-100 text-red-700 text-sm font-medium px-3 py-1 rounded">Out of stock</span>
                    </div>
                    <div class="space-y-3 mb-8">
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-medium transition-colors" onclick="addToWishlist()">
                            <i class="fas fa-heart mr-2"></i>
                            Add to Wishlist
                        </button>
                    </div>
                @endif
                
                <!-- Delivery and Return Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Free Delivery -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-bold text-gray-900 mb-2">Free Delivery</h4>
                        <p class="text-gray-600 text-sm">Enter your postal code for Delivery Availability</p>
                    </div>
                    
                    <!-- Return Delivery -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-bold text-gray-900 mb-2">Return Delivery</h4>
                        <p class="text-gray-600 text-sm">Free 30 Days Delivery Returns. Details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentQuantity = 2;
let selectedSize = '{{ $selectedSize }}'; // Default size (first available)
let lastUpdateTime = '{{ $product->updated_at->toISOString() }}';
let updateInterval;

function changeMainImage(imageSrc, element) {
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('ring-2', 'ring-blue-500');
    });
    
    // Add active class to clicked thumbnail
    element.classList.add('ring-2', 'ring-blue-500');
    
    // Change main image
    document.getElementById('main-image').src = imageSrc;
}

function increaseQuantity() {
    currentQuantity++;
    document.getElementById('quantity').textContent = currentQuantity;
}

function decreaseQuantity() {
    if (currentQuantity > 1) {
        currentQuantity--;
        document.getElementById('quantity').textContent = currentQuantity;
    }
}

function selectSize(size, element) {
    // Check if size is available
    const stock = parseInt(element.getAttribute('data-stock'));
    if (stock <= 0) {
        showError(`Size ${size} is out of stock`);
        return;
    }
    
    // Remove active class from all size buttons
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.classList.remove('bg-black', 'text-white');
        btn.classList.add('border', 'border-gray-300', 'text-gray-700');
    });
    
    // Add active class to selected button
    element.classList.remove('border', 'border-gray-300', 'text-gray-700');
    element.classList.add('bg-black', 'text-white');
    
    // Update selected size
    selectedSize = size;
    
    console.log('Selected size:', selectedSize, 'Stock:', stock);
}

// Real-time update functions
function checkForUpdates() {
    const productSlug = '{{ $product->slug }}';
    
    fetch(`/api/v1/product-data/${productSlug}/size-stocks`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.updated_at !== lastUpdateTime) {
                console.log('Product updated, refreshing size stocks...');
                updateSizeStocks(data.size_stocks);
                lastUpdateTime = data.updated_at;
            }
        })
        .catch(error => {
            console.log('Error checking for updates:', error);
        });
}

function updateSizeStocks(sizeStocks) {
    const sizes = ['S', 'M', 'L', 'XL', 'XXL'];
    
    sizes.forEach(size => {
        const button = document.querySelector(`[data-size="${size}"]`);
        if (button) {
            const stock = sizeStocks[size] || 0;
            const isAvailable = stock > 0;
            
            // Update data attribute
            button.setAttribute('data-stock', stock);
            
            // Update visual appearance
            if (isAvailable) {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.removeAttribute('disabled');
                button.title = `Stock: ${stock}`;
                
                // Update stock display
                const stockSpan = button.querySelector('.text-xs');
                if (stockSpan) {
                    stockSpan.textContent = `(${stock})`;
                    stockSpan.classList.remove('text-red-500');
                }
            } else {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.setAttribute('disabled', 'disabled');
                button.title = 'Out of stock';
                
                // Update stock display
                const stockSpan = button.querySelector('.text-xs');
                if (stockSpan) {
                    stockSpan.textContent = '(0)';
                    stockSpan.classList.add('text-red-500');
                }
            }
            
            // If currently selected size becomes unavailable, deselect it
            if (selectedSize === size && !isAvailable) {
                button.classList.remove('bg-black', 'text-white');
                button.classList.add('border', 'border-gray-300', 'text-gray-700');
                selectedSize = null;
                showError(`Size ${size} is now out of stock`);
            }
        }
    });
    
    // Show update notification
    showUpdateNotification('Product stock updated!');
}

function showUpdateNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white bg-blue-500';
    notification.textContent = message;
    
    // Position notification below header
    if (typeof positionNotificationBelowHeader === 'function') {
        positionNotificationBelowHeader(notification, 16);
    } else {
        notification.style.top = '80px';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Start real-time updates
function startRealTimeUpdates() {
    // Check for updates every 10 seconds
    updateInterval = setInterval(checkForUpdates, 10000);
    console.log('Real-time updates started');
}

// Stop real-time updates
function stopRealTimeUpdates() {
    if (updateInterval) {
        clearInterval(updateInterval);
        updateInterval = null;
        console.log('Real-time updates stopped');
    }
}

// Initialize real-time updates when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Start real-time updates
    startRealTimeUpdates();
    
    // Stop updates when page is hidden (to save resources)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopRealTimeUpdates();
        } else {
            startRealTimeUpdates();
        }
    });
});

function addToCart() {
    const productId = @json($product->id);
    const quantity = currentQuantity;
    const size = selectedSize;
    const color = document.getElementById('colorSelect').value;
    
    const payload = {
        product_id: Number(productId),
        quantity: Number(quantity),
        size: size || null,
        color: color || null,
    };
    
    console.log('Final values:', { productId, quantity, size, color });
    
    console.log(' Add to Cart Debug:', {
        productId: productId,
        quantity: quantity,
        size: size,
        color: color
    });
    
    // Check stock availability
    const selectedButton = document.querySelector(`[data-size="${size}"]`);
    const availableStock = parseInt(selectedButton.getAttribute('data-stock'));
    
    if (availableStock <= 0) {
        showError(`Size ${size} is out of stock`);
        return;
    }
    
    if (quantity > availableStock) {
        showError(`Only ${availableStock} items available in size ${size}`);
        return;
    }
    
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Position notification below header dynamically
            const notification = document.createElement('div');
            notification.className = 'fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white bg-green-500';
            notification.textContent = `Product added to cart successfully! (Size: ${size}, Qty: ${quantity})`;
            
            // Position notification below header
            if (typeof positionNotificationBelowHeader === 'function') {
                positionNotificationBelowHeader(notification, 16);
            } else {
                notification.style.top = '80px';
            }
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
            
            // Update cart count
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        } else {
            showError('Error adding product to cart: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while adding to cart');
    });
}

function buyNow() {
    const productId = @json($product->id);
    const quantity = currentQuantity;
    const size = selectedSize;
    const color = document.getElementById('colorSelect').value;
    
    const payload = {
        product_id: Number(productId),
        quantity: Number(quantity),
        size: size || null,
        color: color || null,
    };
    
    // Check stock availability
    const selectedButton = document.querySelector(`[data-size="${size}"]`);
    const availableStock = parseInt(selectedButton.getAttribute('data-stock'));
    
    if (availableStock <= 0) {
        showError(`Size ${size} is out of stock`);
        return;
    }
    
    if (quantity > availableStock) {
        showError(`Only ${availableStock} items available in size ${size}`);
        return;
    }
    
    // Add to cart first, then redirect to checkout
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to checkout page
            window.location.href = '/checkout';
        } else {
            showError('Error adding product to cart: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while processing purchase');
    });
}

function showError(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white bg-red-500';
    notification.textContent = message;
    
    // Position notification below header
    if (typeof positionNotificationBelowHeader === 'function') {
        positionNotificationBelowHeader(notification, 16);
    } else {
        notification.style.top = '80px';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

async function addToWishlist() {
    const productId = @json($product->id);
    try {
        const response = await fetch('/api/v1/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_id: Number(productId) })
        });
        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json') ? await response.json() : { success: false };
        if (response.ok && data.success) {
            const notification = document.createElement('div');
            notification.className = 'fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white bg-green-500';
            notification.textContent = 'Added to wishlist';
            positionNotificationBelowHeader(notification, 16);
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
            const btn = document.querySelector('button[onclick="addToWishlist()"]');
            if (btn) {
                btn.disabled = true;
                btn.classList.remove('bg-red-600', 'hover:bg-red-700');
                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                btn.innerHTML = '<i class="fas fa-heart mr-2"></i> In Wishlist';
            }
            return;
        }
        if (response.status === 400 && data && typeof data.message === 'string' && data.message.toLowerCase().includes('already')) {
            const info = document.createElement('div');
            info.className = 'fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white bg-gray-600';
            info.textContent = 'Already in wishlist';
            positionNotificationBelowHeader(info, 16);
            document.body.appendChild(info);
            setTimeout(() => info.remove(), 2500);
            const btn = document.querySelector('button[onclick="addToWishlist()"]');
            if (btn) {
                btn.disabled = true;
                btn.classList.remove('bg-red-600', 'hover:bg-red-700');
                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                btn.innerHTML = '<i class="fas fa-heart mr-2"></i> In Wishlist';
            }
            return;
        }
        if (response.status === 401) {
            showError('Please log in to add to wishlist');
            return;
        }
        showError((data && data.message) || 'Failed to add to wishlist');
    } catch (e) {
        showError('Failed to add to wishlist');
    }
}

// Add thumbnail class to existing thumbnails
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[onclick*="changeMainImage"]').forEach(thumb => {
        thumb.classList.add('thumbnail');
    });
});
</script>
@endsection


