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
                <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"2\" fill=\"black\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');"></div>
            </section>
        </div>
        
        <!-- All Products Section -->
        <section class="mt-6" id="products">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span> All Products
                </h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ $featuredProducts->count() + $newArrivals->count() }} Products</span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($featuredProducts->concat($newArrivals)->unique('id') as $product)
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
                @endforeach
            </div>
        </section>
    </main>

    @include('layouts.footer')

    <script>
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
        // Expose for inline onclick on product cards
        window.addCardToWishlist = addCardToWishlist;
        
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

        // Initialize cart count on page load
        updateCartCount();
        
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
        
        async function addToWishlistByProductId(productId) {
            try {
                const res = await fetch('/api/v1/wishlist/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                const ct = res.headers.get('content-type') || '';
                const data = ct.includes('application/json') ? await res.json() : { success: false };

                if (res.ok && data.success) {
                    showSuccess('Added to Wishlist', 'The item has been added to your wishlist.', 3000);
                    updateWishlistCount();
                    return true;
                }

                if (res.status === 400) {
                    // Already in wishlist → show neutral/info toast and update UI
                    showSuccess('Already in Wishlist', 'This item is already in your wishlist.', 2500);
                    updateWishlistCount();
                    // Update heart icon if present on the card
                    const btn = document.querySelector(`.wishlist-btn[data-product-id="${productId}"]`);
                    if (btn) {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('far', 'text-gray-600');
                            icon.classList.add('fas', 'text-red-500');
                        }
                        btn.title = 'In Wishlist';
                    }
                    return true;
                }

                if (res.status === 401) {
                    showError('Please log in to add items to your wishlist', 'Your session has expired. Please log in again to continue.');
                    setTimeout(() => {
                        if (confirm('Your session has expired. Would you like to log in now?')) {
                            window.location.href = '/login';
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
    
    <!-- Notification System -->
    @include('components.notification-system')
</body>
@include('layouts.migsbot')
</html>