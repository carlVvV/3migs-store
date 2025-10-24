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
    <header class="bg-black text-white py-2">
        <div class="container mx-auto flex items-center justify-center relative px-4">
            <span class="absolute left-0"></span>
            <span class="text-xs mx-auto">Summer Sale For All Gown & Barong And Free Express Delivery <a href="#" class="underline">ShopNow</a></span>
            <div class="absolute right-0">
                <div class="relative inline-block">
                    <button id="lang-dropdown-btn" class="bg-black text-white text-xs border-none px-2 py-1 rounded focus:outline-none flex items-center">
                        <span id="selected-lang">English</span>
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="lang-dropdown-menu" class="hidden absolute right-0 mt-1 w-28 bg-white text-black rounded shadow-lg z-50">
                        <button class="block w-full text-left px-4 py-2 hover:bg-gray-200" data-lang="en">English</button>
                        <button class="block w-full text-left px-4 py-2 hover:bg-gray-200" data-lang="fil">Filipino</button>
                    </div>
                    
                </div>
            </div>
        </div>
    </header>
    
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 text-2xl font-bold text-gray-800">
                    <img src="{{ asset('3migs-logo.png') }}" alt="3Migs Logo" class="h-10 w-auto">
                    <span>3Migs Gowns & Barong</span>
                </a>
                <nav class="hidden md:flex space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 font-medium">Home</a>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" placeholder="What are you looking for?" class="border border-gray-300 rounded-md py-2 px-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex items-center space-x-4 text-gray-600 text-lg">
                    <a href="{{ route('wishlist') }}" class="relative">
                        <i class="far fa-heart"></i>
                        <span id="wishlist-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </a>
                    <a href="{{ route('cart') }}" class="relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count" class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </a>
                    <div class="relative flex items-center space-x-3">
                        <button id="profile-btn" class="focus:outline-none flex items-center space-x-2">
                            <i class="far fa-user"></i>
                            @auth
                                <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            @else
                                <span class="text-sm text-gray-700">Guest</span>
                            @endauth
                        </button>
                        <button id="bot-btn" class="focus:outline-none hover:bg-grey-700 text-grey rounded-full w-8 h-8 flex items-center justify-center transition-colors duration-200" title="Chat with MigsBot">
                            <i class="fas fa-robot text-sm"></i>
                        </button>
                        <div id="profileMenu" class="absolute right-0 mt-3 w-56 bg-gray-800 text-white rounded-md shadow-lg py-2 hidden z-50">
                            @auth
                                <!-- Logged-in user menu items -->
                                <div>
                                    <div class="px-4 py-2 border-b border-gray-600">
                                        <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                                        <i class="fas fa-user mr-3"></i>
                                        <span>My Account</span>
                                    </a>
                                    <a href="{{ route('orders') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                                        <i class="fas fa-box mr-3"></i>
                                        <span>My Orders</span>
                                    </a>
                                    <a href="{{ route('wishlist') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                                        <i class="fas fa-heart mr-3"></i>
                                        <span>Wishlist</span>
                                    </a>
                                    @if(auth()->user()->hasRole('admin'))
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                                            <i class="fas fa-cog mr-3"></i>
                                            <span>Admin Panel</span>
                                        </a>
                                    @endif
                                    <hr class="my-2 border-gray-600">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 hover:bg-gray-700 text-left">
                                            <i class="fas fa-sign-out-alt mr-3"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <!-- Guest menu items -->
                                <div>
                                    <a href="{{ route('login') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                                        <i class="fas fa-sign-in-alt mr-3"></i>
                                        <span>Log in</span>
                                    </a>
                                    <a href="{{ route('register') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                                        <i class="fas fa-user-plus mr-3"></i>
                                        <span>Sign up</span>
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 py-2">
            <!-- Language and currency dropdowns removed as requested -->
        </div>
    </header>

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
                    <a href="#products" class="inline-block bg-green-500 text-white px-6 py-3 rounded-md mt-4 hover:bg-green-600">Shop Now</a>
                </div>
                <!-- Professional gradient background with pattern -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-red-900/20 to-blue-900/20"></div>
                <!-- Decorative pattern overlay -->
                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"1\" fill=\"white\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');"></div>
            </section>
        </div>
        
        <!-- Flash Sales Section -->
        <section class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span> Today's Flash Sales
                </h2>
                <div class="flex items-center space-x-2 text-gray-700">
                    <span class="text-lg font-bold">03</span> D
                    <span class="text-lg font-bold">23</span> H
                    <span class="text-lg font-bold">19</span> M
                    <span class="text-lg font-bold">56</span> S
                    <button class="ml-4 p-2 rounded-full bg-white shadow-md text-gray-600 hover:bg-gray-200"><i class="fas fa-arrow-left"></i></button>
                    <button class="p-2 rounded-full bg-white shadow-md text-gray-600 hover:bg-gray-200"><i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
            <div id="flash-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($featuredProducts->take(5) as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card">
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
                        <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" data-product-id="{{ $product->id }}">Add To Cart</button>
                    </div>
                </div>
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
                <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card">
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
                        <button class="mt-4 w-full bg-black text-white py-2 rounded-md hover:bg-gray-800 block text-center add-to-cart-btn" data-product-id="{{ $product->id }}">Add To Cart</button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </main>

    @include('layouts.footer')

    <script>
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
                    showNotification('Product added to cart!', 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message || 'Failed to add product to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
        
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
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
</body>
@include('layouts.migsbot')
</html>