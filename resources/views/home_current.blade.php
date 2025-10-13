@extends('layouts.app')

@section('title', '3Migs Gowns & Barong - Premium Filipino Fashion')

@section('content')
<div class="min-h-screen">
    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Sidebar Navigation -->
            <aside class="w-full lg:w-1/4">
                @include('layouts.categories-sidebar', ['categories' => $categories])
            </aside>

            <!-- Hero Section (Right Main Content) -->
            <section class="w-full lg:w-3/4 bg-black rounded-lg shadow-md flex items-center justify-center p-8 relative overflow-hidden h-96">
                <div class="text-white text-center z-10">
                    <p class="text-sm">3Migs Gowns & Barong</p>
                    <h2 class="text-4xl font-bold mt-2">Up to 10% off Voucher</h2>
                    <p class="text-sm mt-2">Get 10% off your first order</p>
                    <button class="mt-4 bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 font-medium">Shop Now</button>
                </a>
                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent"></div>
            </section>
        </div>
        
        <!-- Flash Sale Section -->
        <section class="mt-12">
            <div class="flex items-center mb-4">
                <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                <h2 class="text-2xl font-bold text-gray-800">Flash Sale</h2>
                <button class="ml-auto bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 font-medium">View All</button>
            </div>
            <div class="flex items-center mb-4 text-sm text-gray-600">
                <span class="mr-2">Ends in:</span>
                <div class="flex items-center space-x-2">
                    <span class="text-lg font-bold">23</span> H
                    <span class="text-lg font-bold">19</span> M
                    <span class="text-lg font-bold">56</span> S
                    <button class="ml-4 p-2 rounded-full bg-white shadow-md text-gray-600 hover:bg-gray-200"><i class="fas fa-arrow-left"></i></button>
                    <button class="p-2 rounded-full bg-white shadow-md text-gray-600 hover:bg-gray-200"><i class="fas fa-arrow-right"></i></button>
                </a>
            </div>
            <div id="flash-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($featuredProducts->take(5) as $product)
                <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow cursor-pointer">
                    @if($product->is_on_sale)
                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                    @endif
                    <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                        <div class="flex items-center mt-2">
                            @if($product->is_on_sale)
                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                            <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->price, 0) }}</span>
                            @else
                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                            @endif
                        </a>
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
                        </a>
                        <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" data-product-id="{{ $product->id }}">Add To Cart</button>
                    </a>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <button class="bg-red-500 text-white px-8 py-3 rounded-md hover:bg-red-600 font-medium">View All Products</button>
            </div>
        </section>
        
        <!-- Best Selling Products Section -->
        <section class="mt-12">
            <div class="flex items-center mb-4">
                <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                <h2 class="text-2xl font-bold text-gray-800">Best Selling Products</h2>
                <button class="ml-auto bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 font-medium">View All</button>
            </div>
            <div id="best-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($featuredProducts->skip(5)->take(5) as $product)
                <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow cursor-pointer">
                    @if($product->is_on_sale)
                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                    @endif
                    <img src="{{ asset($product->images[0] ?? 'images/placeholder.jpg') }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                        <div class="flex items-center mt-2">
                            @if($product->is_on_sale)
                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                            <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->price, 0) }}</span>
                            @else
                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                            @endif
                        </a>
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
                        </a>
                        <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" data-product-id="{{ $product->id }}">Add To Cart</button>
                    </a>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <button class="bg-red-500 text-white px-8 py-3 rounded-md hover:bg-red-600 font-medium">View All Products</button>
            </div>
        </section>
        
        <!-- New Arrivals Section -->
        <section class="mt-12">
            <div class="flex items-center mb-4">
                <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                <h2 class="text-2xl font-bold text-gray-800">New Arrivals</h2>
                <button class="ml-auto bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 font-medium">View All</button>
            </div>
            <div id="new-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($newArrivals->take(5) as $product)
                <a href="{{ route('product.details', $product->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow cursor-pointer">
                    @if($product->is_on_sale)
                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                    @endif
                    <img src="{{ asset($product->images[0] ?? 'images/placeholder.jpg') }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                        <div class="flex items-center mt-2">
                            @if($product->is_on_sale)
                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                            <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->price, 0) }}</span>
                            @else
                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                            @endif
                        </a>
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
                        </a>
                        <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" data-product-id="{{ $product->id }}">Add To Cart</button>
                    </a>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <button class="bg-red-500 text-white px-8 py-3 rounded-md hover:bg-red-600 font-medium">View All Products</button>
            </div>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            addToCart(productId);
        });
    });
    
    function addToCart(productId) {
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
                showNotification('Product added to cart successfully!', 'success');
                updateCartCount();
            } else {
                showNotification(data.message || 'Failed to add product to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while adding to cart', 'error');
        });
    }
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        // Position notification below header dynamically
        positionNotificationBelowHeader(notification, 16);
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
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

    // Initialize cart count on page load
    updateCartCount();
});
</script>
@endsection
