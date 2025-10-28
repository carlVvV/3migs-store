<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3Migs Gowns & Barong - Premium Filipino Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Enhanced chatbot message styling */
        #chatMessages strong {
            font-weight: 600;
            color: #1f2937;
        }
        
        #chatMessages em {
            font-style: italic;
            color: #6b7280;
        }
        
        #chatMessages br + br {
            margin-top: 0.5rem;
        }
        
        #chatMessages .self-start,
        #chatMessages .self-end {
            max-width: 280px;
            word-wrap: break-word;
        }
        
        /* Improve bullet point spacing */
        #chatMessages br + • {
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Sidebar Navigation -->
            <aside class="w-full lg:w-1/4">
                @include('layouts.categories-sidebar', ['categories' => $categories])
            </aside>

            <!-- Hero Section (Right Main Content) -->
            <section class="w-full lg:w-3/4 bg-gradient-to-br from-red-600 to-red-800 rounded-lg shadow-md flex items-center justify-center p-6 relative overflow-hidden h-80">
                <div class="text-white text-center z-10">
                    <p class="text-sm font-medium">Best Selling Product</p>
                    <h2 class="text-3xl font-bold mt-2">Premium Barong Collection</h2>
                    <p class="text-base mt-2 opacity-90">Handcrafted Filipino Fashion</p>
                    <a href="#products" class="inline-block bg-white text-red-600 px-6 py-2 rounded-lg mt-4 hover:bg-gray-100 font-semibold transition-colors text-sm" onclick="scrollToProducts()">Shop Now</a>
                </div>
                <!-- Professional gradient background with pattern -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-800"></div>
                <!-- Decorative pattern overlay -->
                <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"2\" fill=\"black\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>></div>
            </section>
        </div>
        
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
                        <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow">
                            @if($product->is_on_sale)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn" 
                                    data-product-id="{{ $product->id }}" 
                                    title="Add to Wishlist">
                                <i class="far fa-heart text-gray-600 text-sm"></i>
                            </button>
                            
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                            <div class="p-3">
                                <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
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
                                            data-product-id="{{ $product->id }}">Add To Cart</button>
                                @else
                                    <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 block text-center" 
                                            onclick="addCardToWishlist({{ $product->id }});">
                                        <i class="fas fa-heart mr-2"></i> Add to Wishlist
                                    </button>
                                @endif
                            </div>
                        </div>
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
                <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow">
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
                    
                    <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
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
                                    data-product-id="{{ $product->id }}">Add To Cart</button>
                        @else
                            <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 block text-center" 
                                    onclick="addCardToWishlist({{ $product->id }});">
                                <i class="fas fa-heart mr-2"></i> Add to Wishlist
                            </button>
                        @endif
                    </div>
                </div>
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
                <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow">
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
                    
                    <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
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
                                    data-product-id="{{ $product->id }}">Add To Cart</button>
                        @else
                            <button class="mt-4 w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 block text-center" 
                                    onclick="addCardToWishlist({{ $product->id }});">
                                <i class="fas fa-heart mr-2"></i> Add to Wishlist
                            </button>
                        @endif
                    </div>
                </div>
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
                    <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span> All Products
                </h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ $allProducts->count() }} Products</span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($allProducts as $product)
                <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow cursor-pointer">
                    @if($product->is_on_sale)
                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                    @endif
                    
                    <!-- Wishlist Button -->
                    <button class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn" 
                            data-product-id="{{ $product->id }}" 
                            title="Add to Wishlist">
                        <i class="far fa-heart text-gray-600 text-sm"></i>
                    </button>
                    
                    <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
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
    </main>

    @include('layouts.footer')

    <script>
    // Global functions for product interactions
    function addToCart(productId) {
        console.log('addToCart called with productId:', productId);
        
        fetch('/api/v1/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Product Added to Cart!', 'The item has been successfully added to your cart.', 3000);
                updateCartCount();
            } else {
                showError('Failed to Add Product', data.message || 'Failed to add product to cart. Please try again.', 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network Error', 'An error occurred while adding the product to cart. Please try again.', 5000);
        });
    }

    function addCardToWishlist(productId) {
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        if (!isLoggedIn) {
            showError('Please log in to add items to your wishlist', 'You need to be logged in to save items to your wishlist. Click here to log in.', 5000);
            setTimeout(() => {
                const notification = document.querySelector('.notification');
                if (notification) {
                    notification.style.cursor = 'pointer';
                    notification.onclick = () => window.location.href = '/login';
                }
            }, 100);
            return;
        }
        
        fetch('/api/v1/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccess('Added to Wishlist', 'The item has been added to your wishlist.', 3000);
                updateWishlistCount();
            } else {
                showError('Failed to Add', data.message || 'Failed to add to wishlist.');
            }
        })
        .catch(() => showError('Network Error', 'Failed to add to wishlist.'));
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

    // Smooth scroll to products section
    function scrollToProducts() {
        const productsSection = document.getElementById('products');
        if (productsSection) {
            productsSection.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Language dropdown functionality
        const langBtn = document.getElementById('lang-dropdown-btn');
        const langMenu = document.getElementById('lang-dropdown-menu');
        const selectedLang = document.getElementById('selected-lang');

        if (langBtn && langMenu) {
            langBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                langMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function() {
                langMenu.classList.add('hidden');
            });

            langMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Handle language selection
            langMenu.querySelectorAll('button[data-lang]').forEach(button => {
                button.addEventListener('click', function() {
                    selectedLang.textContent = this.textContent;
                    langMenu.classList.add('hidden');
                });
            });
        }

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

        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getAttribute('data-product-id');
                console.log('Add to cart button clicked', { productId });
                addToCart(productId);
            });
        });
        
        // Initialize wishlist status on page load
        checkWishlistStatus();
        updateWishlistCount();
    });

    // Global functions for product interactions
    function addToCart(productId) {
        console.log('addToCart called with productId:', productId);
        
        fetch('/api/v1/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => {
            console.log('Cart add response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Cart add response data:', data);
            if (data.success) {
                showSuccess('Product Added to Cart!', 'The item has been successfully added to your cart.', 3000);
                updateCartCount();
            } else {
                showError('Failed to Add Product', data.message || 'Failed to add product to cart. Please try again.', 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network Error', 'An error occurred while adding the product to cart. Please try again.', 5000);
        });
    }

    // Add to wishlist from card (homepage)
    function addCardToWishlist(productId) {
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        if (!isLoggedIn) {
            showError('Please log in to add items to your wishlist', 'You need to be logged in to save items to your wishlist. Click here to log in.', 5000);
            // Add a clickable link to login
            setTimeout(() => {
                const notification = document.querySelector('.notification');
                if (notification) {
                    notification.style.cursor = 'pointer';
                    notification.onclick = () => window.location.href = '/login';
                }
            }, 100);
            return;
        }
        
        fetch('/api/v1/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccess('Added to Wishlist', 'The item has been added to your wishlist.', 3000);
                updateWishlistCount();
            } else {
                showError('Failed to Add', data.message || 'Failed to add to wishlist.');
            }
        })
        .catch(() => showError('Network Error', 'Failed to add to wishlist.'));
    }

    async function addToWishlistByProductId(productId) {
        try {
            const res = await fetch('/api/v1/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId })
            });
            const data = await res.json();
            if (data.success) {
                showSuccess('Added to Wishlist', 'The item has been added to your wishlist.', 3000);
                updateWishlistCount();
                return true;
            } else {
                showError('Failed to Add', data.message || 'Failed to add to wishlist.');
                return false;
            }
        } catch (error) {
            console.error('Error adding to wishlist:', error);
            showError('Network Error', 'Failed to add to wishlist.');
            return false;
        }
    }

    function updateCartCount() {
        console.log('updateCartCount called');
        fetch('/api/v1/cart/count')
            .then(response => {
                console.log('Cart count response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Cart count response data:', data);
                if (data.success) {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.data.count;
                        console.log('Cart count updated to:', data.data.count);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
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

    function checkWishlistStatus() {
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        if (!isLoggedIn) return;
        
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            const productId = btn.getAttribute('data-product-id');
            fetch(`/api/v1/wishlist/check/${productId}`)
            .then(response => response.json())
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

    // Initialize cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
        checkWishlistStatus();
        updateWishlistCount();
        
        // Wishlist functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.wishlist-btn')) {
                e.preventDefault();
                const button = e.target.closest('.wishlist-btn');
                const productId = button.getAttribute('data-product-id');
                const heartIcon = button.querySelector('i');
                
                const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
                if (!isLoggedIn) {
                    showNotification('Please log in to add items to your wishlist', 'error');
                    // Redirect to login after a short delay
                    setTimeout(() => {
                        if (confirm('You need to be logged in to add items to your wishlist. Would you like to log in now?')) {
                            window.location.href = '/login';
                        }
                    }, 2000);
                    return;
                }
                
                addToWishlistByProductId(productId).then((ok) => {
                    if (ok) {
                        heartIcon.classList.remove('far', 'text-gray-600');
                        heartIcon.classList.add('fas', 'text-red-500');
                        button.title = 'In Wishlist';
                    }
                });
            }
        });
    });
    </script>
    
    <!-- Product Carousel Styles -->
    <style>
        .product-carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        
        .product-carousel-item {
            flex: 0 0 100%;
            padding: 0 8px;
        }
        
        @media (min-width: 640px) {
            .product-carousel-item {
                flex: 0 0 50%;
            }
        }
        
        @media (min-width: 768px) {
            .product-carousel-item {
                flex: 0 0 33.333%;
            }
        }
        
        @media (min-width: 1024px) {
            .product-carousel-item {
                flex: 0 0 25%;
            }
        }
        
        @media (min-width: 1280px) {
            .product-carousel-item {
                flex: 0 0 20%;
            }
        }
        
        .carousel-container {
            overflow: hidden;
            position: relative;
        }
        
        .carousel-dots {
            display: flex;
            justify-content: center;
            margin-top: 16px;
            gap: 8px;
        }
                    }, 2000);
                    return false;
                }

                showError('Failed to add to wishlist', (data && data.message) || 'Please try again.');
                return false;
            } catch (e) {
                showError('Network Error', 'An error occurred while adding to wishlist. Please try again.', 5000);
                return false;
            }
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
        
        // Initialize wishlist status on page load
        checkWishlistStatus();
        updateWishlistCount();
    });
    </script>
    
    <!-- Product Carousel Styles -->
    <style>
        .product-carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        
        .product-carousel-item {
            flex: 0 0 100%;
            padding: 0 8px;
        }
        
        @media (min-width: 640px) {
            .product-carousel-item {
                flex: 0 0 50%;
            }
        }
        
        @media (min-width: 768px) {
            .product-carousel-item {
                flex: 0 0 33.333%;
            }
        }
        
        @media (min-width: 1024px) {
            .product-carousel-item {
                flex: 0 0 25%;
            }
        }
        
        @media (min-width: 1280px) {
            .product-carousel-item {
                flex: 0 0 20%;
            }
        }
        
        .carousel-container {
            overflow: hidden;
            position: relative;
        }
        
        .carousel-dots {
            display: flex;
            justify-content: center;
            margin-top: 16px;
            gap: 8px;
        }
        
        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #d1d5db;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .carousel-dot.active {
            background-color: #ef4444;
        }
        
        .carousel-dot:hover {
            background-color: #9ca3af;
        }
    </style>
    
    <!-- Product Carousel JavaScript -->
    <script>
    // Product Rotation Functionality
    class ProductCarousel {
        constructor(carouselId, dotClass, autoRotateInterval = 10000) {
            this.carousel = document.getElementById(carouselId);
            this.dots = document.querySelectorAll(`.${dotClass}`);
            this.autoRotateInterval = autoRotateInterval;
            this.currentSlide = 0;
            this.intervalId = null;
            this.slidesPerView = this.getSlidesPerView();
            this.totalSlides = Math.ceil(this.carousel.children.length / this.slidesPerView);
            
            
            
            if (this.carousel && this.totalSlides > 1) {
                this.init();
            } else {
                
            }
        }
        
        getSlidesPerView() {
            const width = window.innerWidth;
            if (width >= 1280) return 5; // xl
            if (width >= 1024) return 4; // lg
            if (width >= 768) return 3;  // md
            if (width >= 640) return 2;  // sm
            return 1; // mobile
        }
        
        init() {
            this.setupEventListeners();
            this.startAutoRotate();
            this.updateDots();
            
        }
        
        setupEventListeners() {
            // Navigation dots
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    
                    this.goToSlide(index);
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                const newSlidesPerView = this.getSlidesPerView();
                if (newSlidesPerView !== this.slidesPerView) {
                    this.slidesPerView = newSlidesPerView;
                    this.totalSlides = Math.ceil(this.carousel.children.length / this.slidesPerView);
                    this.currentSlide = Math.min(this.currentSlide, this.totalSlides - 1);
                    this.updateCarousel();
                    this.updateDots();
                }
            });
        }
        
        startAutoRotate() {
            if (this.totalSlides <= 1) return;
            
            this.intervalId = setInterval(() => {
                this.nextSlide();
            }, this.autoRotateInterval);
        }
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
            
            this.updateCarousel();
            this.updateDots();
        }
        
        goToSlide(slideIndex) {
            this.currentSlide = slideIndex;
            
            this.updateCarousel();
            this.updateDots();
        }
        
        updateCarousel() {
            const translateX = -(this.currentSlide * 100);
            this.carousel.style.transform = `translateX(${translateX}%)`;
            
        }
        
        updateDots() {
            this.dots.forEach((dot, index) => {
                if (index === this.currentSlide) {
                    dot.classList.add('active');
                    dot.classList.remove('bg-gray-300');
                    dot.classList.add('bg-red-500');
                } else {
                    dot.classList.remove('active');
                    dot.classList.remove('bg-red-500');
                    dot.classList.add('bg-gray-300');
                }
            });
        }
    }
    
    // Initialize carousels when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        
        // Wait a bit for all elements to be rendered
        setTimeout(() => {
            // Initialize Featured Products Carousel (10 second interval)
            new ProductCarousel('featured-carousel', 'featured-dot', 10000);
            
            // Initialize New Arrivals Carousel (10 second interval)
            new ProductCarousel('new-arrivals-carousel', 'new-arrivals-dot', 10000);
            
            // Initialize Best Selling Products Carousel (10 second interval)
            new ProductCarousel('best-selling-carousel', 'best-selling-dot', 10000);
        }, 100);
    });
    </script>
    
    <!-- Notification System -->
    @include('components.notification-system')
    
    <!-- MigsBot -->
    @include('layouts.migsbot')
</body>
</html>