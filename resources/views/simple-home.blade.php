<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3Migs Gowns & Barong - Premium Filipino Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            <div class="flex-1">
                <!-- Hero Banner -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-8 text-white mb-8">
                    <h1 class="text-4xl font-bold mb-4">Premium Filipino Fashion</h1>
                    <p class="text-xl mb-6">Discover our exquisite collection of barong and gowns</p>
                    <a href="#products" class="bg-white text-red-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Shop Now
                    </a>
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
                            <a href="#products" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200">
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

                <!-- All Products Section -->
                <section class="mt-12" id="products">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                            <div>
                                <span class="text-sm text-red-600 font-medium">All Products</span>
                                <h2 class="text-2xl font-bold text-gray-800">Our Collection</h2>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse($allProducts as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow">
                            @if($product->is_on_sale)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            
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
                            <p class="text-gray-500">No products available at the moment.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Simple JavaScript for basic functionality -->
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
                alert('Product Added to Cart!');
            } else {
                alert('Failed to add product to cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the product to cart.');
        });
    }

    function addCardToWishlist(productId) {
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        if (!isLoggedIn) {
            alert('Please log in to add items to your wishlist');
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
                alert('Added to Wishlist');
            } else {
                alert('Failed to add to wishlist: ' + data.message);
            }
        })
        .catch(() => alert('Failed to add to wishlist.'));
    }

    // Add event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getAttribute('data-product-id');
                addToCart(productId);
            });
        });
    });
    </script>
    
    <!-- Notification System -->
    @include('components.notification-system')
    
    <!-- MigsBot -->
    @include('layouts.migsbot')
</body>
</html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3Migs Gowns & Barong - Premium Filipino Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            <div class="flex-1">
                <!-- Hero Banner -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-8 text-white mb-8">
                    <h1 class="text-4xl font-bold mb-4">Premium Filipino Fashion</h1>
                    <p class="text-xl mb-6">Discover our exquisite collection of barong and gowns</p>
                    <a href="#products" class="bg-white text-red-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Shop Now
                    </a>
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
                            <a href="#products" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200">
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

                <!-- All Products Section -->
                <section class="mt-12" id="products">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                            <div>
                                <span class="text-sm text-red-600 font-medium">All Products</span>
                                <h2 class="text-2xl font-bold text-gray-800">Our Collection</h2>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse($allProducts as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow">
                            @if($product->is_on_sale)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            
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
                            <p class="text-gray-500">No products available at the moment.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Simple JavaScript for basic functionality -->
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
                alert('Product Added to Cart!');
            } else {
                alert('Failed to add product to cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the product to cart.');
        });
    }

    function addCardToWishlist(productId) {
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        if (!isLoggedIn) {
            alert('Please log in to add items to your wishlist');
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
                alert('Added to Wishlist');
            } else {
                alert('Failed to add to wishlist: ' + data.message);
            }
        })
        .catch(() => alert('Failed to add to wishlist.'));
    }

    // Add event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getAttribute('data-product-id');
                addToCart(productId);
            });
        });
    });
    </script>
    
    <!-- Notification System -->
    @include('components.notification-system')
    
    <!-- MigsBot -->
    @include('layouts.migsbot')
</body>
</html>


