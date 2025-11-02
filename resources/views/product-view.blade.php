@extends('layouts.app')

@section('title', $product->name . ' - 3Migs Barong')

@section('content')
<div class="min-h-screen bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hidden input for product ID -->
        <input type="hidden" id="productId" value="{{ $product->id }}">
        
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
                
                <!-- Stock / Size Selection -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-900">Size:</h3>
                        <button type="button" onclick="checkForUpdates()" 
                                class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Refresh Stock
                        </button>
                    </div>
                    @php
                        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                        $sizeStocks = [];
                        
                        // Get available data
                        $availableData = $product->getAvailableColorsAndSizes();
                        $availableSizes = $availableData['sizes'] ?? [];
                        
                        // Calculate stock per size from size_stocks
                        $rawStocks = $product->size_stocks ?? [];
                        if (is_array($rawStocks) && count($rawStocks) > 0) {
                            foreach ($sizes as $s) { 
                                $sizeStocks[$s] = intval($rawStocks[$s] ?? 0); 
                            }
                        } else {
                            $simple = (int) ($product->total_stock ?? $product->stock ?? 0);
                            foreach ($sizes as $s) { 
                                $sizeStocks[$s] = $simple; 
                            }
                        }

                        // Determine availability and default selection
                        $hasAnyStock = false; foreach ($sizes as $s) { if (($sizeStocks[$s] ?? 0) > 0) { $hasAnyStock = true; break; } }
                        $defaultSize = 'M';
                        $selectedSize = null;
                        
                        // First try to select the default size if it has stock
                        if (($sizeStocks[$defaultSize] ?? 0) > 0) {
                            $selectedSize = $defaultSize;
                        } else {
                            // Otherwise, select the first available size
                            foreach ($sizes as $s) {
                                if (($sizeStocks[$s] ?? 0) > 0) {
                                    $selectedSize = $s;
                                    break;
                                }
                            }
                        }
                        
                        // If no sizes have stock, don't set a default (let user select manually)
                        if ($selectedSize === null) { 
                            $selectedSize = null; // Don't auto-select if no stock
                        }
                        
                        // Debug: Log the final selected size
                        \Log::info('Product selected size debug', [
                            'product_id' => $product->id,
                            'selected_size' => $selectedSize,
                            'size_stocks' => $sizeStocks,
                            'has_any_stock' => $hasAnyStock,
                            'calculated_size_stocks' => $sizeStocks
                        ]);
                    @endphp
                    

                    <div class="flex space-x-3">
                        <div class="flex space-x-3">
                            @foreach($sizes as $size)
                                @php
                                    $stock = $sizeStocks[$size] ?? 0;
                                    $isAvailable = $stock > 0;
                                    $isSelected = $size === $selectedSize;
                                @endphp
                                <button class="px-6 py-3 border rounded-lg transition-colors size-btn {{ $isSelected ? 'bg-black text-white' : 'border-gray-300 text-gray-700 hover:border-gray-400' }} {{ !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        data-size="{{ $size }}" data-stock="{{ $stock }}"
                                        onclick="selectSize('{{ $size }}', this)" {{ !$isAvailable ? 'disabled' : '' }}
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
                    </div>
                    @if(!$hasAnyStock)
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
let selectedSize = @json($selectedSize); // Use JSON to properly handle null values
let lastUpdateTime = '{{ $product->updated_at->toISOString() }}';
let updateInterval;

// Auto-select first available size if none is selected
if (!selectedSize || selectedSize === null || selectedSize === 'null') {
    const availableSizes = document.querySelectorAll('.size-btn:not(.opacity-50)');
    
    if (availableSizes.length > 0) {
        const firstAvailable = availableSizes[0];
        const size = firstAvailable.getAttribute('data-size');
        selectSize(size, firstAvailable);
    }
} else {
    // Ensure the selected size button is visually selected
    const selectedButton = document.querySelector(`[data-size="${selectedSize}"]`);
    if (selectedButton) {
        selectedButton.classList.add('bg-black', 'text-white');
        selectedButton.classList.remove('border-gray-300', 'text-gray-700');
    }
}

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
}

// Real-time update functions
function checkForUpdates() {
    const productSlug = '{{ $product->slug }}';
    
    fetch(`/api/v1/product-data/${productSlug}/size-stocks`)
        .then(response => {
            if (!response.ok) {
                if (response.status === 404) {
                    console.warn('Product not found for updates:', productSlug);
                    stopRealTimeUpdates();
                    return;
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.updated_at !== lastUpdateTime) {
                updateSizeStocks(data.size_stocks);
                lastUpdateTime = data.updated_at;
            }
        })
        .catch(error => {
            // Don't stop updates for network errors, only for 404s
            if (error.message.includes('404')) {
                stopRealTimeUpdates();
            }
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
}

// Stop real-time updates
function stopRealTimeUpdates() {
    if (updateInterval) {
        clearInterval(updateInterval);
        updateInterval = null;
    }
}

// Initialize real-time updates when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check if user was redirected from homepage to select size
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('select_size') === '1') {
        // Show notification about selecting size
        if (typeof showWarning === 'function') {
            showWarning('Select Size Required', 'Please select a size before adding to cart.');
        } else if (typeof showInfo === 'function') {
            showInfo('Select Size Required', 'Please select a size before adding to cart.');
        }
        // Remove query parameter from URL without page reload
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
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
    // Check if user is logged in
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    
    if (!isLoggedIn) {
        // Show visually pleasing login prompt
        showLoginPrompt();
        return;
    }
    
    // Get product ID from hidden input first, then fallback to PHP
    let productId = document.getElementById('productId')?.value;
    if (!productId) {
        productId = @json($product->id);
    }
    
    const quantity = currentQuantity;
    const size = selectedSize;
    
    // Check if size is properly selected
    if (!size || size === 'null' || size === '' || size === null) {
        const availableSizes = document.querySelectorAll('.size-btn:not(.opacity-50)');
        if (availableSizes.length > 0) {
            const firstAvailable = availableSizes[0];
            const emergencySize = firstAvailable.getAttribute('data-size');
            selectSize(emergencySize, firstAvailable);
            
            const updatedPayload = {
                product_id: Number(productId),
                quantity: Number(quantity),
                size: emergencySize
            };
            
            proceedWithCartAdd(updatedPayload);
            return;
        } else {
            showError('Please select a size before adding to cart.');
            return;
        }
    }
    
    // Validate product ID
    if (!productId || productId === null) {
        showError('Product information is missing. Please refresh the page and try again.');
        return;
    }
    
    const payload = {
        product_id: Number(productId),
        quantity: Number(quantity)
    };
    
    // Only add size if it has a valid value
    if (size && size !== 'null' && size !== null && size !== '') {
        payload.size = size;
    }
    
    // Proceed with cart addition
    proceedWithCartAdd(payload);
}

function showLoginPrompt() {
    // Prevent multiple modals from being created
    if (document.getElementById('login-modal-overlay')) {
        return;
    }
    
    // Create a modal overlay
    const overlay = document.createElement('div');
    overlay.id = 'login-modal-overlay';
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    overlay.style.animation = 'fadeIn 0.3s ease-in-out';
    
    // Create modal content
    const modal = document.createElement('div');
    modal.className = 'bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all';
    modal.style.animation = 'slideUp 0.3s ease-out';
    
    modal.innerHTML = `
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-50 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Login Required</h3>
            <p class="text-gray-600 text-center mb-6">Please log in to add items to your cart and continue shopping.</p>
            <div class="flex gap-3">
                <button id="login-cancel-btn" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button id="login-redirect-btn" class="flex-1 px-4 py-2.5 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition-colors">
                    Go to Login
                </button>
            </div>
        </div>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Add CSS animations if not already present
    if (!document.getElementById('login-modal-styles')) {
        const style = document.createElement('style');
        style.id = 'login-modal-styles';
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideUp {
                from { 
                    opacity: 0;
                    transform: translateY(20px);
                }
                to { 
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Handle close
    let isClosing = false;
    const closeModal = (e) => {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        if (isClosing) return;
        isClosing = true;
        
        overlay.style.animation = 'fadeOut 0.3s ease-in-out';
        modal.style.animation = 'slideDown 0.3s ease-in-out';
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
            isClosing = false;
        }, 300);
    };
    
    // Use setTimeout to ensure modal is in DOM before adding listeners
    setTimeout(() => {
        const redirectBtn = overlay.querySelector('#login-redirect-btn');
        const cancelBtn = overlay.querySelector('#login-cancel-btn');
        
        if (redirectBtn) {
            redirectBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                window.location.href = '/login';
            });
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }
        
        // Prevent event bubbling from modal content
        modal.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay && !isClosing) {
                closeModal(e);
            }
        });
    }, 10);
}

function proceedWithCartAdd(payload) {
    const size = payload.size;
    const quantity = payload.quantity;
    
    // Check stock availability only if size is provided
    if (size) {
        const selectedButton = document.querySelector(`[data-size="${size}"]`);
        if (!selectedButton) {
            showError('Size selection error. Please select a size again.');
            return;
        }
        
        const availableStock = parseInt(selectedButton.getAttribute('data-stock'));
        
        if (availableStock <= 0) {
            showError(`Size ${size} is out of stock`);
            return;
        }
        
        if (quantity > availableStock) {
            showError(`Only ${availableStock} items available in size ${size}`);
            return;
        }
    }
    
    // Show loading state
    const addToCartBtn = document.querySelector('button[onclick="addToCart()"]');
    const originalText = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
    addToCartBtn.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (response.status === 419) {
            showError('Session expired. Please refresh the page and try again.');
            // Optionally refresh the page after a delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
            return;
        }
        
        return response.json();
    })
    .then(data => {
        if (!data) return; // Handle 419 case
        
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
            console.error('Add to cart failed:', data);
            showError('Error adding product to cart: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        showError('An error occurred while adding to cart');
    })
    .finally(() => {
        // Restore button state
        addToCartBtn.innerHTML = originalText;
        addToCartBtn.disabled = false;
    });
}

function buyNow() {
    const productId = @json($product->id);
    const quantity = currentQuantity;
    const size = selectedSize;
    
    // Safely get color value if element exists
    const colorSelect = document.getElementById('colorSelect');
    const color = colorSelect ? colorSelect.value : null;
    
    const payload = {
        product_id: Number(productId),
        quantity: Number(quantity),
        size: size || null,
        color: color || null,
    };
    
    // Check stock availability
    const selectedButton = document.querySelector(`[data-size="${size}"]`);
    
    // Check if selectedButton exists before getting attributes
    if (!selectedButton) {
        showError('Please select a size');
        return;
    }
    
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
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

<!-- Reviews Section -->
<section class="mt-16 bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Customer Reviews</h2>
        
        <!-- Rating Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Overall Rating -->
                <div class="text-center">
                    <div class="text-5xl font-bold text-gray-900 mb-2">{{ number_format($product->average_rating ?? 0, 1) }}</div>
                    <div class="flex items-center justify-center mb-2">
                        @php
                            $averageRating = $product->average_rating ?? 0;
                            $fullStars = floor($averageRating);
                            $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $fullStars)
                                <i class="fas fa-star text-yellow-400 text-2xl"></i>
                            @elseif($i == $fullStars + 1 && $hasHalfStar)
                                <i class="fas fa-star-half-alt text-yellow-400 text-2xl"></i>
                            @else
                                <i class="far fa-star text-yellow-400 text-2xl"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-gray-600">Based on {{ $product->review_count ?? 0 }} reviews</p>
                </div>
                
                <!-- Rating Distribution -->
                <div class="space-y-3">
                    @php
                        $ratingDistribution = $product->approvedReviews()->select('rating')->get()->groupBy('rating')->map->count();
                        $totalReviews = $product->review_count ?? 0;
                    @endphp
                    @for($rating = 5; $rating >= 1; $rating--)
                        @php
                            $count = $ratingDistribution[$rating] ?? 0;
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-gray-700 w-12">{{ $rating }}★</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-3">
                                <div class="bg-yellow-400 h-3 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-8">{{ $count }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        
        <!-- Reviews Container -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div id="reviews-container">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Loading reviews...</p>
                </div>
            </div>
            
            <!-- Load More Reviews Button -->
            <div class="text-center mt-6">
                <button id="load-more-reviews" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2 rounded-lg transition-colors hidden">
                    Load More Reviews
                </button>
            </div>
        </div>
    </div>
</section>

<script>
// Reviews functionality
let currentPage = 1;
const reviewsPerPage = 5;

document.addEventListener('DOMContentLoaded', function() {
    loadReviews();
    
    // Load more reviews button
    const loadMoreBtn = document.getElementById('load-more-reviews');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            loadReviews();
        });
    }
});

async function loadReviews() {
    try {
        const productId = @json($product->id);
        const response = await fetch(`/api/v1/products/${productId}/reviews?page=${currentPage}&limit=${reviewsPerPage}`);
        const data = await response.json();
        
        if (data.success) {
            displayReviews(data.data);
            
            // Show/hide load more button
            const loadMoreBtn = document.getElementById('load-more-reviews');
            if (loadMoreBtn) {
                if (data.data.next_page_url) {
                    loadMoreBtn.classList.remove('hidden');
                } else {
                    loadMoreBtn.classList.add('hidden');
                }
            }
        } else {
            throw new Error(data.message || 'Failed to load reviews');
        }
    } catch (error) {
        document.getElementById('reviews-container').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-2xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Failed to load reviews. Please try again later.</p>
            </div>
        `;
    }
}

function displayReviews(reviewsData) {
    const container = document.getElementById('reviews-container');
    
    if (currentPage === 1) {
        container.innerHTML = '';
    }
    
    if (reviewsData.data && reviewsData.data.length > 0) {
        reviewsData.data.forEach(review => {
            const reviewElement = createReviewElement(review);
            container.appendChild(reviewElement);
        });
    } else if (currentPage === 1) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-comment-slash text-2xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No reviews yet. Be the first to review this product!</p>
            </div>
        `;
    }
}

function createReviewElement(review) {
    const reviewDiv = document.createElement('div');
    reviewDiv.className = 'border-b border-gray-200 py-6 last:border-b-0';
    
    const stars = generateStars(review.rating);
    const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    reviewDiv.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <h4 class="font-semibold text-gray-900">${review.user.name}</h4>
                    <div class="flex items-center">
                        ${stars}
                    </div>
                    ${review.is_verified_purchase ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Verified Purchase</span>' : ''}
                </div>
                <p class="text-sm text-gray-500 mb-2">${reviewDate}</p>
                ${review.review_text ? `<p class="text-gray-700 mt-2">${review.review_text}</p>` : ''}
            </div>
        </div>
    `;
    
    return reviewDiv;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star text-yellow-400"></i>';
        } else {
            stars += '<i class="far fa-star text-yellow-400"></i>';
        }
    }
    return stars;
}
</script>
@endsection


