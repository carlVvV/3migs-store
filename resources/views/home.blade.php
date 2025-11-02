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
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        }
        /* Product Container Styling */
        .product-container {
            width: 100%;
            max-width: none;
        }
        @media (min-width: 1024px) {
            .product-container {
                max-width: calc(100% - 16rem); /* 16rem = 256px (categories width) */
            }
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
            height: 100px;
            object-fit: cover;
        }
        .product-card .product-title {
            font-size: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.2;
            min-height: 1.8rem;
            margin-bottom: 0.125rem;
        }
        .product-card .product-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0.375rem;
        }
        .product-card .product-content .price-container {
            font-size: 0.8125rem;
        }
        .product-card .product-content .rating-container {
            font-size: 0.6875rem;
        }
        .product-card .product-content button {
            font-size: 0.6875rem;
            padding: 0.25rem;
            margin-top: 0.375rem;
        }
        
        /* Carousel Styles */
        .carousel-container {
            position: relative;
        }
        
        .carousel-wrapper {
            position: relative;
            overflow: hidden;
            padding-bottom: 10px; /* Space for dots */
        }
        
        #featured-carousel,
        #new-arrivals-carousel,
        #best-selling-carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
            gap: 1rem;
        }
        
        .carousel-item {
            flex: 0 0 auto;
            width: 100%;
        }
        
        @media (min-width: 640px) {
            .carousel-item {
                width: calc(50% - 0.5rem);
            }
        }
        
        @media (min-width: 768px) {
            .carousel-item {
                width: calc(33.333% - 0.667rem);
            }
        }
        
        @media (min-width: 1024px) {
            .carousel-item {
                width: calc(25% - 0.75rem);
            }
        }
        
        @media (min-width: 1280px) {
            .carousel-item {
                width: calc(19% - 0.76rem);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Sidebar Navigation -->
            <aside class="w-full lg:w-64">
                @include('layouts.categories-sidebar', ['categories' => $categories])
            </aside>

            <div class="flex-1 product-container">
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
                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"2\" fill=\"white\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg'></div>
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
                    
                    <!-- Featured Products Carousel -->
                    @if($featuredProducts->count() > 0)
                    <div class="carousel-container relative">
                        <div class="carousel-wrapper overflow-hidden">
                            <div id="featured-carousel" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                                @foreach($featuredProducts as $product)
                                <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow block carousel-item">
                                    <!-- Tags Container (Right Side) -->
                                    <div class="absolute top-2 right-2 flex flex-col gap-0.5 z-10 items-end">
                                        @if($product->is_on_sale)
                                        <div class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded">-{{ $product->discount_percentage }}%</div>
                                        @endif
                                        @if($product->wholesale_price)
                                        <div class="bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded">Wholesale</div>
                                        @endif
                                    </div>
                                    
                                    <!-- Wishlist Button -->
                                    <button class="absolute top-2 left-2 w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn z-30" 
                                            data-product-id="{{ $product->id }}" 
                                            title="Add to Wishlist">
                                        <i class="far fa-heart text-gray-600 text-sm"></i>
                                    </button>
                                    
                                    <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-[300px] object-cover">
                                    <div class="p-6 product-content">
                                        <h3 class="font-semibold text-2xl text-gray-800 product-title">{{ $product->name }}</h3>
                                        <div class="flex items-center mt-2 price-container">
                                            <span class="text-red-500 font-bold text-lg">₱{{ number_format($product->current_price, 0) }}</span>
                    @if($product->is_on_sale)
                                            <span class="text-gray-500 text-sm ml-2">(₱{{ number_format($product->base_price, 0) }})</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center text-base text-gray-600 mt-2 rating-container">
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
                                            <button class="mt-4 w-full bg-black text-white py-3 text-lg font-semibold rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-slug="{{ $product->slug }}"
                                                    onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }}, '{{ $product->slug }}');">Add To Cart</button>
                                        @else
                                            <button class="mt-4 w-full bg-red-600 text-white py-3 text-lg font-semibold rounded-md hover:bg-red-700 block text-center add-to-wishlist-out-of-stock" 
                                                    data-product-id="{{ $product->id }}">
                                                <i class="fas fa-heart mr-2"></i> Add to Wishlist
                                            </button>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @if($featuredProducts->count() > 5)
                        <div class="carousel-dots flex justify-center mt-4">
                            @for($i = 0; $i < ceil($featuredProducts->count() / 5); $i++)
                                <button class="dot mx-1 w-2 h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-red-600 w-6' : 'bg-gray-300' }}" data-index="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
                            @endfor
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No featured products available at the moment.</p>
                    </div>
                    @endif
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
                    
                    <!-- New Arrivals Carousel -->
                    @if($newArrivals->count() > 0)
                    <div class="carousel-container relative">
                        <div class="carousel-wrapper overflow-hidden">
                            <div id="new-arrivals-carousel" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                                @foreach($newArrivals as $product)
                                <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow block carousel-item">
                                    <!-- Tags Container (Right Side) -->
                                    <div class="absolute top-2 right-2 flex flex-col gap-0.5 z-10 items-end">
                                        @if($product->is_on_sale)
                                        <div class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded">-{{ $product->discount_percentage }}%</div>
                                        @endif
                                        @if($product->wholesale_price)
                                        <div class="bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded">Wholesale</div>
                                        @endif
                                    </div>
                                    
                                    <!-- New Badge (removed, shown in tags container) -->
                    
                    <!-- Wishlist Button -->
                                    <button class="absolute top-2 left-2 w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn z-20" 
                            data-product-id="{{ $product->id }}" 
                                            title="Add to Wishlist"
                                            onclick="event.preventDefault(); event.stopPropagation(); addCardToWishlist({{ $product->id }});">
                        <i class="far fa-heart text-gray-600 text-sm"></i>
                    </button>
                    
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-[300px] object-cover">
                            <div class="p-6 product-content">
                                <h3 class="font-semibold text-2xl text-gray-800 product-title">{{ $product->name }}</h3>
                        <div class="flex items-center mt-2 price-container">
                            <span class="text-red-500 font-bold text-lg">₱{{ number_format($product->current_price, 0) }}</span>
                            @if($product->is_on_sale)
                            <span class="text-gray-500 text-sm ml-2">(₱{{ number_format($product->base_price, 0) }})</span>
                            @endif
                        </div>
                        <div class="flex items-center text-base text-gray-600 mt-2 rating-container">
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
                            <button class="mt-4 w-full bg-black text-white py-3 text-lg font-semibold rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                    data-product-id="{{ $product->id }}"
                                    data-product-slug="{{ $product->slug }}"
                                    onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }}, '{{ $product->slug }}');">Add To Cart</button>
                        @else
                            <button class="mt-4 w-full bg-red-600 text-white py-3 text-lg font-semibold rounded-md hover:bg-red-700 block text-center add-to-wishlist-out-of-stock" 
                                    data-product-id="{{ $product->id }}">
                                <i class="fas fa-heart mr-2"></i> Add to Wishlist
                            </button>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
                        </div>
                        @if($newArrivals->count() > 5)
                        <div class="carousel-dots flex justify-center mt-4">
                            @for($i = 0; $i < ceil($newArrivals->count() / 5); $i++)
                                <button class="dot mx-1 w-2 h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-red-600 w-6' : 'bg-gray-300' }}" data-index="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
                            @endfor
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No new arrivals available at the moment.</p>
                    </div>
                    @endif
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
                    
                    <!-- Best Selling Products Carousel -->
                    @if($bestSellingProducts->count() > 0)
                    <div class="carousel-container relative">
                        <div class="carousel-wrapper overflow-hidden">
                            <div id="best-selling-carousel" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                                @foreach($bestSellingProducts as $product)
                                <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow block carousel-item">
                                    <!-- Tags Container (Right Side) -->
                                    <div class="absolute top-2 right-2 flex flex-col gap-0.5 z-10 items-end">
                                        @if($product->is_on_sale)
                                        <div class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded">-{{ $product->discount_percentage }}%</div>
                                        @endif
                                        @if($product->wholesale_price)
                                        <div class="bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded">Wholesale</div>
                                        @endif
                                    </div>
                                    
                                    <!-- Best Seller Badge (removed, shown in tags container) -->
                                    
                                    <!-- Wishlist Button -->
                                    <button class="absolute top-2 left-2 w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn z-30" 
                                            data-product-id="{{ $product->id }}" 
                                            title="Add to Wishlist">
                                        <i class="far fa-heart text-gray-600 text-sm"></i>
                                    </button>
                            
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-[300px] object-cover">
                            <div class="p-6 product-content">
                                <h3 class="font-semibold text-2xl text-gray-800 product-title">{{ $product->name }}</h3>
                                <div class="flex items-center mt-2 price-container">
                                    <span class="text-red-500 font-bold text-lg">₱{{ number_format($product->current_price, 0) }}</span>
                                    @if($product->is_on_sale)
                                    <span class="text-gray-500 text-sm ml-2">(₱{{ number_format($product->base_price, 0) }})</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-600 mt-1 rating-container">
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
                                    <button class="mt-4 w-full bg-black text-white py-3 text-lg font-semibold rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                            data-product-id="{{ $product->id }}"
                                            data-product-slug="{{ $product->slug }}"
                                            onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }}, '{{ $product->slug }}');">Add To Cart</button>
                                @else
                                    <button class="mt-4 w-full bg-red-600 text-white py-3 text-lg font-semibold rounded-md hover:bg-red-700 block text-center" 
                                            onclick="event.preventDefault(); event.stopPropagation(); addCardToWishlist({{ $product->id }});">
                                        <i class="fas fa-heart mr-2"></i> Add to Wishlist
                                    </button>
                                @endif
                            </div>
                        </a>
                        @endforeach
                        </div>
                        </div>
                        @if($bestSellingProducts->count() > 5)
                        <div class="carousel-dots flex justify-center mt-4">
                            @for($i = 0; $i < ceil($bestSellingProducts->count() / 5); $i++)
                                <button class="dot mx-1 w-2 h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-red-600 w-6' : 'bg-gray-300' }}" data-index="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
                            @endfor
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No best-selling products available yet.</p>
                    </div>
                    @endif
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
                            <!-- Tags Container (Right Side) -->
                            <div class="absolute top-2 right-2 flex flex-col gap-0.5 z-10 items-end">
                                        @if($product->is_on_sale)
                                        <div class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded">-{{ $product->discount_percentage }}%</div>
                                        @endif
                                        @if($product->wholesale_price)
                                        <div class="bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded">Wholesale</div>
                                        @endif
                            </div>
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-2 left-2 w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors wishlist-btn z-30" 
                                    data-product-id="{{ $product->id }}" 
                                    title="Add to Wishlist">
                                <i class="far fa-heart text-gray-600 text-sm"></i>
                            </button>
                            
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-[300px] object-cover">
                            <div class="p-6 product-content">
                                <h3 class="font-semibold text-2xl text-gray-800 product-title">{{ $product->name }}</h3>
                                <div class="flex items-center mt-2">
                                    <span class="text-red-500 font-bold text-lg">₱{{ number_format($product->current_price, 0) }}</span>
                                    @if($product->is_on_sale)
                                    <span class="text-gray-500 text-sm ml-2">(₱{{ number_format($product->base_price, 0) }})</span>
                                    @endif
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mt-2">
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
                                    <button class="mt-4 w-full bg-black text-white py-3 text-lg font-semibold rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" 
                                            data-product-id="{{ $product->id }}"
                                            data-product-slug="{{ $product->slug }}"
                                            onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }}, '{{ $product->slug }}');">Add To Cart</button>
                                @else
                                    <button class="mt-4 w-full bg-red-600 text-white py-3 text-lg font-semibold rounded-md hover:bg-red-700 block text-center" 
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
        function addToCart(productId, productSlug) {
            // Check if user is logged in
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            
            if (!isLoggedIn) {
                // Show visually pleasing login prompt
                showLoginPrompt();
                return;
            }
            
            // Open quick-add modal instead of redirecting
            if (productSlug) {
                openQuickAddModal(productSlug, productId);
            } else {
                showError('Error', 'Unable to open product selection. Please try again.');
            }
        }
        
        // Quick-add modal state
        let quickAddModal = {
            productId: null,
            productSlug: null,
            productData: null,
            selectedSize: null,
            quantity: 1
        };
        
        async function openQuickAddModal(productSlug, productId) {
            // Prevent multiple modals
            if (document.getElementById('quick-add-modal-overlay')) {
                return;
            }
            
            quickAddModal.productSlug = productSlug;
            quickAddModal.productId = productId;
            quickAddModal.selectedSize = null;
            quickAddModal.quantity = 1;
            
            // Show loading state
            showQuickAddModal(true);
            updateQuickAddModalContent('<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i><p class="text-gray-600">Loading product...</p></div>');
            
            try {
                // Fetch product data
                const response = await fetch(`/api/v1/product-data/${productSlug}`);
                const data = await response.json();
                
                if (data.success && data.product) {
                    quickAddModal.productData = data.product;
                    renderQuickAddModalContent();
                } else {
                    showError('Error', 'Failed to load product details. Please try again.');
                    closeQuickAddModal();
                }
            } catch (error) {
                console.error('Error fetching product:', error);
                showError('Error', 'Unable to load product. Please try again.');
                closeQuickAddModal();
            }
        }
        
        function showQuickAddModal(isLoading = false) {
            const overlay = document.createElement('div');
            overlay.id = 'quick-add-modal-overlay';
            overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
            overlay.style.animation = 'fadeIn 0.3s ease-in-out';
            
            const modal = document.createElement('div');
            modal.id = 'quick-add-modal';
            modal.className = 'bg-white rounded-lg shadow-2xl max-w-lg w-full transform transition-all max-h-[90vh] overflow-y-auto';
            modal.style.animation = 'slideUp 0.3s ease-out';
            
            modal.innerHTML = `
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Quick Add to Cart</h3>
                        <button id="quick-add-close-btn" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="quick-add-content">
                        ${isLoading ? '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i><p class="text-gray-600">Loading product...</p></div>' : ''}
                    </div>
                </div>
            `;
            
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            
            // Add CSS animations if not already present
            if (!document.getElementById('quick-add-modal-styles')) {
                const style = document.createElement('style');
                style.id = 'quick-add-modal-styles';
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
            
            // Event listeners
            document.getElementById('quick-add-close-btn').addEventListener('click', closeQuickAddModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeQuickAddModal();
                }
            });
        }
        
        function updateQuickAddModalContent(html) {
            const content = document.getElementById('quick-add-content');
            if (content) {
                content.innerHTML = html;
            }
        }
        
        function renderQuickAddModalContent() {
            const product = quickAddModal.productData;
            const sizes = ['S', 'M', 'L', 'XL', 'XXL'];
            const sizeStocks = product.size_stocks || {};
            
            // Calculate available sizes
            const availableSizes = sizes.map(size => {
                const stock = parseInt(sizeStocks[size] || 0);
                return { size, stock, available: stock > 0 };
            }).filter(s => s.available);
            
            let sizeButtonsHtml = '';
            sizes.forEach(size => {
                const stock = parseInt(sizeStocks[size] || 0);
                const isAvailable = stock > 0;
                const isSelected = quickAddModal.selectedSize === size;
                
                sizeButtonsHtml += `
                    <button 
                        class="px-4 py-2 border rounded-lg transition-colors size-btn-modal ${isSelected ? 'bg-black text-white border-black' : 'border-gray-300 text-gray-700 hover:border-gray-400'} ${!isAvailable ? 'opacity-50 cursor-not-allowed' : ''}"
                        data-size="${size}" 
                        data-stock="${stock}"
                        ${!isAvailable ? 'disabled' : ''}
                        onclick="selectSizeInModal('${size}', this)"
                        title="${isAvailable ? `Stock: ${stock}` : 'Out of stock'}">
                        ${size}
                        ${isAvailable ? `<span class="text-xs ml-1">(${stock})</span>` : '<span class="text-xs ml-1 text-red-500">(0)</span>'}
                    </button>
                `;
            });
            
            const html = `
                <div class="space-y-4">
                    <!-- Product Image and Name -->
                    <div class="flex items-start space-x-4">
                        <img src="${product.cover_image_url || '/images/placeholder.jpg'}" alt="${product.name}" class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">${product.name}</h4>
                            <p class="text-lg font-bold text-red-500">₱${parseFloat(product.current_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                        </div>
                    </div>
                    
                    <!-- Size Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Size <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-2">
                            ${sizeButtonsHtml}
                        </div>
                        ${availableSizes.length === 0 ? '<p class="text-sm text-red-500 mt-2">All sizes are out of stock</p>' : ''}
                    </div>
                    
                    <!-- Quantity Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <div class="flex items-center border border-gray-300 rounded-lg w-32">
                            <button class="px-3 py-2 text-gray-600 hover:text-gray-800 transition-colors" onclick="decreaseModalQuantity()">-</button>
                            <span class="px-4 py-2 text-gray-900 font-medium flex-1 text-center" id="modal-quantity">${quickAddModal.quantity}</span>
                            <button class="px-3 py-2 text-gray-600 hover:text-gray-800 transition-colors" onclick="increaseModalQuantity()">+</button>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button onclick="closeQuickAddModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button id="modal-add-to-cart-btn" onclick="addToCartFromModal()" class="flex-1 px-4 py-2.5 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed" ${quickAddModal.selectedSize ? '' : 'disabled'}>
                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            `;
            
            updateQuickAddModalContent(html);
        }
        
        function selectSizeInModal(size, element) {
            const stock = parseInt(element.getAttribute('data-stock'));
            if (stock <= 0) {
                showError('Size not available', `Size ${size} is out of stock`);
                return;
            }
            
            // Update selected size
            quickAddModal.selectedSize = size;
            
            // Update button styles
            document.querySelectorAll('.size-btn-modal').forEach(btn => {
                btn.classList.remove('bg-black', 'text-white', 'border-black');
                btn.classList.add('border-gray-300', 'text-gray-700');
            });
            
            element.classList.remove('border-gray-300', 'text-gray-700');
            element.classList.add('bg-black', 'text-white', 'border-black');
            
            // Enable add to cart button
            const addBtn = document.getElementById('modal-add-to-cart-btn');
            if (addBtn) {
                addBtn.disabled = false;
            }
        }
        
        function increaseModalQuantity() {
            const maxStock = quickAddModal.selectedSize ? 
                parseInt(document.querySelector(`.size-btn-modal[data-size="${quickAddModal.selectedSize}"]`)?.getAttribute('data-stock') || 0) : 0;
            
            if (maxStock > 0 && quickAddModal.quantity < maxStock) {
                quickAddModal.quantity++;
                document.getElementById('modal-quantity').textContent = quickAddModal.quantity;
            }
        }
        
        function decreaseModalQuantity() {
            if (quickAddModal.quantity > 1) {
                quickAddModal.quantity--;
                document.getElementById('modal-quantity').textContent = quickAddModal.quantity;
            }
        }
        
        async function addToCartFromModal() {
            if (!quickAddModal.selectedSize) {
                showError('Size Required', 'Please select a size before adding to cart.');
                return;
            }
            
            const addBtn = document.getElementById('modal-add-to-cart-btn');
            const originalText = addBtn.innerHTML;
            addBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
            addBtn.disabled = true;
            
            const payload = {
                product_id: parseInt(quickAddModal.productId),
                quantity: quickAddModal.quantity,
                size: quickAddModal.selectedSize
            };
            
            try {
                const response = await fetch('/api/v1/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Added to Cart!', `Product added successfully (Size: ${quickAddModal.selectedSize}, Qty: ${quickAddModal.quantity})`, 3000);
                    closeQuickAddModal();
                    updateCartCount();
                } else {
                    showError('Failed to Add', data.message || 'Unable to add product to cart. Please try again.');
                    addBtn.innerHTML = originalText;
                    addBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                showError('Network Error', 'An error occurred. Please try again.');
                addBtn.innerHTML = originalText;
                addBtn.disabled = false;
            }
        }
        
        function closeQuickAddModal() {
            const overlay = document.getElementById('quick-add-modal-overlay');
            if (overlay) {
                overlay.style.animation = 'fadeOut 0.3s ease-in-out';
                const modal = document.getElementById('quick-add-modal');
                if (modal) {
                    modal.style.animation = 'slideDown 0.3s ease-in-out';
                }
                setTimeout(() => {
                    if (overlay.parentNode) {
                        overlay.parentNode.removeChild(overlay);
                    }
                }, 300);
            }
            
            // Reset modal state
            quickAddModal = {
                productId: null,
                productSlug: null,
                productData: null,
                selectedSize: null,
                quantity: 1
            };
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

        // Prevent multiple simultaneous calls
        let isWishlistProcessing = false;
        
        function addCardToWishlist(productId) {
            try {
                // Prevent multiple simultaneous calls
                if (isWishlistProcessing) {
                    console.log('Wishlist operation already in progress, ignoring duplicate call');
                    return false;
                }
                
                isWishlistProcessing = true;
                console.log('addCardToWishlist called with productId:', productId);
                
                const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
                if (!isLoggedIn) {
                    isWishlistProcessing = false;
                    console.log('User not logged in');
                    if (typeof showError === 'function') {
                        showError('Please login to add items to wishlist', 'You need to be logged in to add items to your wishlist.', 3000);
                    } else {
                        alert('Please login to add items to wishlist');
                    }
                    return false;
                }

            // Check current state
            const button = document.querySelector(`.wishlist-btn[data-product-id="${productId}"]`);
            console.log('Button found:', button);
            const heartIcon = button ? button.querySelector('i') : null;
            const isInWishlist = heartIcon && heartIcon.classList.contains('fas') && heartIcon.classList.contains('text-red-500');
            console.log('Is in wishlist:', isInWishlist);
            
            // If already in wishlist, remove it
            if (isInWishlist) {
                console.log('Removing from wishlist, productId:', productId);
                fetch('/api/v1/wishlist/remove-by-product', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: parseInt(productId) })
                })
                .then(response => {
                    console.log('Remove response status:', response.status);
                    const ct = response.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('Non-JSON response:', text);
                            return { success: false, message: 'Server returned an invalid response' };
                        });
                    }
                    return response.json().then(data => {
                        if (!response.ok) {
                            console.error('Error response:', data);
                            return { success: false, ...data };
                        }
                        return data;
                    });
                })
                .then(data => {
                    console.log('Remove response data:', data);
                    if (data && data.success) {
                        showSuccess('Removed from wishlist!', 'Product has been removed from your wishlist.', 2000);
                        updateWishlistCount();
                        
                        // Update button visual state
                        if (button && heartIcon) {
                            heartIcon.classList.remove('fas', 'text-red-500');
                            heartIcon.classList.add('far', 'text-gray-600');
                            button.title = 'Add to Wishlist';
                        }
                    } else {
                        showError('Failed to remove from wishlist', (data && data.message) || 'Please try again.');
                    }
                })
                .catch(e => {
                    console.error('Error removing from wishlist:', e);
                    showError('Network Error', 'An error occurred while removing from wishlist. Please try again.', 5000);
                })
                .finally(() => {
                    isWishlistProcessing = false;
                });
                return false;
            }

            // Add to wishlist
            console.log('Adding to wishlist, productId:', productId, 'type:', typeof productId);
            fetch('/api/v1/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: parseInt(productId) })
            })
            .then(response => {
                console.log('Add response status:', response.status);
                const ct = response.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        return { success: false, message: 'Server returned an invalid response' };
                    });
                }
                return response.json().then(data => {
                    if (!response.ok) {
                        console.error('Error response:', data);
                        return { success: false, ...data };
                    }
                    return data;
                });
            })
            .then(data => {
                console.log('Add response data:', data);
                if (data && data.success) {
                    showSuccess('Added to wishlist!', 'Product has been added to your wishlist.', 2000);
                    updateWishlistCount();
                    
                    // Update button visual state
                    if (button && heartIcon) {
                        heartIcon.classList.remove('far', 'text-gray-600');
                        heartIcon.classList.add('fas', 'text-red-500');
                        button.title = 'In Wishlist';
                    }
                    return false;
                }

                // Handle specific error cases
                if (data && data.message) {
                    if (data.message === 'Product already in wishlist') {
                        // Update button to show it's already in wishlist
                        if (button && heartIcon) {
                            heartIcon.classList.remove('far', 'text-gray-600');
                            heartIcon.classList.add('fas', 'text-red-500');
                            button.title = 'In Wishlist';
                        }
                        showInfo('Already in wishlist', 'This product is already in your wishlist.', 2000);
                    } else {
                        showError('Failed to add to wishlist', data.message || 'Please try again.');
                    }
                } else {
                    showError('Failed to add to wishlist', 'Please try again.');
                }
                return false;
            })
            .catch(e => {
                console.error('Error adding to wishlist:', e);
                showError('Network Error', 'An error occurred while adding to wishlist. Please try again.', 5000);
                return false;
            })
            .finally(() => {
                isWishlistProcessing = false;
            });
            } catch (error) {
                console.error('Error in addCardToWishlist:', error);
                isWishlistProcessing = false;
                alert('An error occurred while processing your request. Please try again.');
                return false;
            }
        }
        
        // Make function globally accessible immediately
        if (typeof window !== 'undefined') {
            window.addCardToWishlist = addCardToWishlist;
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
                        const button = document.querySelector(`.wishlist-btn[data-product-id="${productId}"]`);
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
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const productId = this.getAttribute('data-product-id');
                    const productSlug = this.getAttribute('data-product-slug');
                    addToCart(productId, productSlug);
                });
            });

            // Remove onclick handlers and use only event listeners to prevent duplicates
            document.querySelectorAll('.wishlist-btn, .add-to-wishlist-out-of-stock').forEach(button => {
                // Remove existing onclick handler
                button.onclick = null;
                // Remove onclick attribute
                button.removeAttribute('onclick');
                
                // Add single event listener
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const productId = this.getAttribute('data-product-id');
                    if (productId && typeof window.addCardToWishlist === 'function') {
                        window.addCardToWishlist(productId);
                    } else if (productId && typeof addCardToWishlist === 'function') {
                        addCardToWishlist(productId);
                    }
                }, { once: false });
            });

            // Initialize wishlist status and counts
            checkWishlistStatus();
            updateCartCount();
            updateWishlistCount();
            
            // Initialize carousels
            initCarousels();
        });
        
        // Carousel functionality
        function initCarousels() {
            const carousels = [
                { id: 'featured-carousel', name: 'featured' },
                { id: 'new-arrivals-carousel', name: 'new-arrivals' },
                { id: 'best-selling-carousel', name: 'best-selling' }
            ];
            
            carousels.forEach(carousel => {
                const carouselElement = document.getElementById(carousel.id);
                if (!carouselElement) return;
                
                const items = Array.from(carouselElement.children);
                if (items.length <= 5) {
                    return; // No carousel needed if 5 or fewer items
                }
                
                const itemsPerSlide = 5;
                const totalSlides = Math.ceil(items.length / itemsPerSlide);
                
                let currentSlide = 0;
                const dotsContainer = carouselElement.parentElement.parentElement.querySelector('.carousel-dots');
                const dots = dotsContainer ? Array.from(dotsContainer.querySelectorAll('.dot')) : [];
                
                // Function to update carousel transform
                function updateCarousel() {
                    const container = carouselElement.parentElement;
                    const itemWidth = items[0].offsetWidth;
                    const gap = 24; // 1.5rem = 24px
                    const translateX = -(currentSlide * (itemsPerSlide * (itemWidth + gap)));
                    carouselElement.style.transform = `translateX(${translateX}px)`;
                    
                    // Update dot indicators
                    dots.forEach((dot, index) => {
                        if (index === currentSlide) {
                            dot.classList.remove('bg-gray-300', 'w-2');
                            dot.classList.add('bg-red-600', 'w-6');
                        } else {
                            dot.classList.remove('bg-red-600', 'w-6');
                            dot.classList.add('bg-gray-300', 'w-2');
                        }
                    });
                }
                
                // Add click event to dots
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        currentSlide = index;
                        updateCarousel();
                    });
                });
                
                // Auto-rotate every 10 seconds
                setInterval(() => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    updateCarousel();
                }, 10000); // 10 seconds
                
                // Initial update
                updateCarousel();
            });
        }
    </script>
</body>
</html>



