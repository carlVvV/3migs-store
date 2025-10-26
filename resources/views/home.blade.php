<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3Migs Gowns & Barong - Premium Filipino Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-card img {
            height: 160px;
            object-fit: cover;
        }
        .product-card .product-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.25;
            min-height: 2.5rem;
        }
        .product-card .product-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Sidebar Navigation -->
            <aside class="w-full lg:w-1/4">
                @include('layouts.categories-sidebar', ['categories' => $categories])
            </aside>

            <div class="flex-1">
                <!-- Hero Section -->
                <section class="hero-gradient rounded-lg p-8 mb-8 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Premium Filipino Fashion</h1>
                        <p class="text-xl mb-6 opacity-90">Discover our exquisite collection of gowns and barongs</p>
                        <a href="#products" class="bg-white text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors" onclick="scrollToProducts()">
                            Shop Now
                        </a>
                    </div>
                    <!-- Decorative pattern overlay -->
                    <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"2\" fill=\"black\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');"></div>
                </section>
        
                <!-- Featured Products Section -->
                <section class="mt-8" id="featured">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                            <div>
                                <span class="text-sm text-red-600 font-medium">Featured</span>
                                <h2 class="text-2xl font-bold text-gray-800">Featured Products</h2>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="#products" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200" onclick="scrollToProducts()">
                                View All
                            </a>
                        </div>
                    </div>
                    
                    <!-- Featured Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @forelse($featuredProducts as $product)
                        <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow block">
                            @if($product->is_on_sale)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn" 
                                    data-product-id="{{ $product->id }}" 
                                    title="Add to Wishlist">
                                <i class="far fa-heart text-gray-600 text-sm"></i>
                            </button>
                            
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full object-cover">
                            <div class="p-3 product-content">
                                <h3 class="font-semibold text-gray-800 product-title">{{ $product->name }}</h3>
                                <div class="flex items-center mt-2">
                                    @if($product->is_on_sale)
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->base_price, 0) }}</span>
                                    @else
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                    @for($i = 0; $i < floor($product->average_rating); $i++)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @endfor
                                    @if($product->average_rating - floor($product->average_rating) > 0)
                                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                                    @endif
                                    @for($i = 0; $i < (5 - ceil($product->average_rating)); $i++)
                                        <i class="far fa-star text-yellow-400"></i>
                                    @endfor
                                    <span class="ml-1">({{ $product->review_count }})</span>
                                </div>
                                @php
                                    $totalStock = 0;
                                    if (!empty($product->variations)) {
                                        $totalStock = array_sum(array_map(fn($v) => (int)($v['stock'] ?? 0), $product->variations));
                                    } elseif (!empty($product->size_stocks)) {
                                        $totalStock = array_sum(array_map('intval', $product->size_stocks));
                                    } else {
                                        $totalStock = (int) ($product->stock ?? 0);
                                    }
                                @endphp
                                @if($totalStock > 0)
                                    <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                            data-product-id="{{ $product->id }}"
                                            onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }});">Add To Cart</button>
                                @else
                                    <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 block text-center" 
                                            onclick="event.preventDefault(); event.stopPropagation(); addCardToWishlist({{ $product->id }});">
                                        <i class="fas fa-heart mr-2"></i> Add to Wishlist
                                    </button>
                                @endif
                            </div>
                        </a>
                        @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500">No featured products available at the moment.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
        
                <!-- New Arrivals Section -->
                <section class="mt-8" id="new-arrivals">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                            <div>
                                <span class="text-sm text-red-600 font-medium">Latest</span>
                                <h2 class="text-2xl font-bold text-gray-800">New Arrivals</h2>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="#products" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200" onclick="scrollToProducts()">
                                View All
                            </a>
                        </div>
                    </div>
                    
                    <!-- New Arrivals Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @forelse($newArrivals as $product)
                        <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow block">
                            @if($product->is_on_sale)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            
                            <!-- New Badge -->
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded font-bold">
                                <i class="fas fa-star mr-1"></i>New
                            </div>
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-2 right-12 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn" 
                                    data-product-id="{{ $product->id }}" 
                                    title="Add to Wishlist">
                                <i class="far fa-heart text-gray-600 text-sm"></i>
                            </button>
                            
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full object-cover">
                            <div class="p-3 product-content">
                                <h3 class="font-semibold text-gray-800 product-title">{{ $product->name }}</h3>
                                <div class="flex items-center mt-2">
                                    @if($product->is_on_sale)
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->base_price, 0) }}</span>
                                    @else
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                    @for($i = 0; $i < floor($product->average_rating); $i++)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @endfor
                                    @if($product->average_rating - floor($product->average_rating) > 0)
                                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                                    @endif
                                    @for($i = 0; $i < (5 - ceil($product->average_rating)); $i++)
                                        <i class="far fa-star text-yellow-400"></i>
                                    @endfor
                                    <span class="ml-1">({{ $product->review_count }})</span>
                                </div>
                                @php
                                    $totalStock = 0;
                                    if (!empty($product->variations)) {
                                        $totalStock = array_sum(array_map(fn($v) => (int)($v['stock'] ?? 0), $product->variations));
                                    } elseif (!empty($product->size_stocks)) {
                                        $totalStock = array_sum(array_map('intval', $product->size_stocks));
                                    } else {
                                        $totalStock = (int) ($product->stock ?? 0);
                                    }
                                @endphp
                                @if($totalStock > 0)
                                    <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                            data-product-id="{{ $product->id }}"
                                            onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }});">Add To Cart</button>
                                @else
                                    <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 block text-center" 
                                            onclick="event.preventDefault(); event.stopPropagation(); addCardToWishlist({{ $product->id }});">
                                        <i class="fas fa-heart mr-2"></i> Add to Wishlist
                                    </button>
                                @endif
                            </div>
                        </a>
                        @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500">No new arrivals available at the moment.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
        
                <!-- Best Selling Products Section -->
                <section class="mt-8" id="best-selling">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                            <div>
                                <span class="text-sm text-red-600 font-medium">This Month</span>
                                <h2 class="text-2xl font-bold text-gray-800">Best Selling Products</h2>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="#products" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200" onclick="scrollToProducts()">
                                View All
                            </a>
                        </div>
                    </div>
                    
                    <!-- Best Selling Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @forelse($bestSellingProducts as $product)
                        <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow block">
                            @if($product->is_on_sale)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            
                            <!-- Best Seller Badge -->
                            <div class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded font-bold">
                                <i class="fas fa-crown mr-1"></i>Best Seller
                            </div>
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-2 right-12 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn" 
                                    data-product-id="{{ $product->id }}" 
                                    title="Add to Wishlist">
                                <i class="far fa-heart text-gray-600 text-sm"></i>
                            </button>
                            
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full object-cover">
                            <div class="p-3 product-content">
                                <h3 class="font-semibold text-gray-800 product-title">{{ $product->name }}</h3>
                                <div class="flex items-center mt-2">
                                    @if($product->is_on_sale)
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->base_price, 0) }}</span>
                                    @else
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-sm text-gray-600 mt-1">
                                    <div class="flex items-center">
                                        @for($i = 0; $i < floor($product->average_rating); $i++)
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @endfor
                                        @if($product->average_rating - floor($product->average_rating) > 0)
                                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                                        @endif
                                        @for($i = 0; $i < (5 - ceil($product->average_rating)); $i++)
                                            <i class="far fa-star text-yellow-400"></i>
                                        @endfor
                                        <span class="ml-1">({{ $product->review_count }})</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $product->monthly_sales }} sold</span>
                                </div>
                                @php
                                    $totalStock = 0;
                                    if (!empty($product->variations)) {
                                        $totalStock = array_sum(array_map(fn($v) => (int)($v['stock'] ?? 0), $product->variations));
                                    } elseif (!empty($product->size_stocks)) {
                                        $totalStock = array_sum(array_map('intval', $product->size_stocks));
                                    } else {
                                        $totalStock = (int) ($product->stock ?? 0);
                                    }
                                @endphp
                                @if($totalStock > 0)
                                    <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                            data-product-id="{{ $product->id }}"
                                            onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }});">Add To Cart</button>
                                @else
                                    <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 block text-center" 
                                            onclick="event.preventDefault(); event.stopPropagation(); addCardToWishlist({{ $product->id }});">
                                        <i class="fas fa-heart mr-2"></i> Add to Wishlist
                                    </button>
                                @endif
                            </div>
                        </a>
                        @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500">No best-selling products available yet.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
        
                <!-- All Products Section -->
                <section class="mt-6" id="products">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-th-large mr-2 text-red-500"></i>
                            All Products
                        </h2>
                    </div>
                    
                    <!-- All Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @forelse($allProducts as $product)
                        <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow block">
                            @if($product->is_on_sale)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn" 
                                    data-product-id="{{ $product->id }}" 
                                    title="Add to Wishlist">
                                <i class="far fa-heart text-gray-600 text-sm"></i>
                            </button>
                            
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full object-cover">
                            <div class="p-3 product-content">
                                <h3 class="font-semibold text-gray-800 product-title">{{ $product->name }}</h3>
                                <div class="flex items-center mt-2">
                                    @if($product->is_on_sale)
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->base_price, 0) }}</span>
                                    @else
                                    <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                    @for($i = 0; $i < floor($product->average_rating); $i++)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @endfor
                                    @if($product->average_rating - floor($product->average_rating) > 0)
                                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                                    @endif
                                    @for($i = 0; $i < (5 - ceil($product->average_rating)); $i++)
                                        <i class="far fa-star text-yellow-400"></i>
                                    @endfor
                                    <span class="ml-1">({{ $product->review_count }})</span>
                                </div>
                                @php
                                    $totalStock = 0;
                                    if (!empty($product->variations)) {
                                        $totalStock = array_sum(array_map(fn($v) => (int)($v['stock'] ?? 0), $product->variations));
                                    } elseif (!empty($product->size_stocks)) {
                                        $totalStock = array_sum(array_map('intval', $product->size_stocks));
                                    } else {
                                        $totalStock = (int) ($product->stock ?? 0);
                                    }
                                @endphp
                                @if($totalStock > 0)
                                    <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                            data-product-id="{{ $product->id }}"
                                            onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }});">Add To Cart</button>
                                @else
                                    <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 block text-center" 
                                            onclick="event.preventDefault(); event.stopPropagation(); addCardToWishlist({{ $product->id }});">
                                        <i class="fas fa-heart mr-2"></i> Add to Wishlist
                                    </button>
                                @endif
                            </div>
                        </a>
                        @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500">No products available at the moment.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Notification System -->
    @include('components.notification-system')
    
    <!-- MigsBot -->
    @include('layouts.migsbot')

    <!-- Global JavaScript Functions -->
    <script>
        // Global functions for cart and wishlist management
        function addToCart(productId) {
            fetch('/api/v1/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Product added to cart!');
                    updateCartCount();
                } else {
                    showError('Failed to add to cart', data.message || 'Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Network Error', 'An error occurred while adding to cart. Please try again.');
            });
        }

        function addCardToWishlist(productId) {
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            if (!isLoggedIn) {
                showError('Please login to add items to wishlist', 'You need to be logged in to add items to your wishlist.', 3000);
                return false;
            }

            fetch('/api/v1/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => {
                if (!response.ok) return Promise.resolve({ success: false });
                const ct = response.headers.get('content-type') || '';
                if (!ct.includes('application/json')) return Promise.resolve({ success: false });
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    showSuccess('Added to wishlist!', 'Product has been added to your wishlist.', 2000);
                    updateWishlistCount();
                    return false;
                }

                showError('Failed to add to wishlist', (data && data.message) || 'Please try again.');
                return false;
            })
            .catch(e => {
                showError('Network Error', 'An error occurred while adding to wishlist. Please try again.', 5000);
                return false;
            });
        }

        function updateCartCount() {
            fetch('/api/v1/cart/count')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.data.count;
                        }
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }

        function updateWishlistCount() {
            fetch('/api/v1/wishlist/count')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const wishlistCount = document.getElementById('wishlist-count');
                        if (wishlistCount) {
                            wishlistCount.textContent = data.data.count;
                        }
                    }
                })
                .catch(error => console.error('Error updating wishlist count:', error));
        }

        function scrollToProducts() {
            document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
        }

        function checkWishlistStatus() {
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            if (!isLoggedIn) return;
            
            const productIds = Array.from(document.querySelectorAll('.wishlist-btn')).map(btn => btn.getAttribute('data-product-id'));
            
            if (productIds.length === 0) return;
            
            // Check each product's wishlist status
            productIds.forEach(productId => {
                fetch('/api/v1/wishlist/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => {
                    if (!response.ok) return Promise.resolve({ success: false });
                    const ct = response.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) return Promise.resolve({ success: false });
                    return response.json();
                })
                .then(data => {
                    if (data && data.success && data.data && data.data.is_in_wishlist) {
                        const button = document.querySelector(`[data-product-id="${productId}"]`);
                        if (!button) return;
                        const heartIcon = button.querySelector('i');
                        if (!heartIcon) return;
                        heartIcon.classList.remove('far', 'text-gray-600');
                        heartIcon.classList.add('fas', 'text-red-500');
                        button.title = 'In Wishlist';
                    }
                })
                .catch(() => {/* ignore for unauthenticated or non-JSON */});
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners for add to cart buttons
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addToCart(productId);
                });
            });

            // Add event listeners for wishlist buttons
            document.querySelectorAll('.wishlist-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addCardToWishlist(productId);
                });
            });

            // Initialize wishlist status and counts
            checkWishlistStatus();
            updateCartCount();
            updateWishlistCount();
        });
    </script>
</body>
</html>