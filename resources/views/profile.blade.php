<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Account - 3Migs Gowns & Barong</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-button.active {
            background-color: #3B82F6;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-8">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ Auth::user()->name }}</h1>
                        <p class="text-blue-100">{{ Auth::user()->email }}</p>
                        <p class="text-blue-100">{{ Auth::user()->phone ?? 'No phone number' }}</p>
                        <div class="flex items-center mt-2">
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-check-circle mr-1"></i>Verified Account
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6 bg-gray-50">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ Auth::user()->orders()->count() }}</div>
                    <div class="text-gray-600">Total Orders</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ Auth::user()->orders()->where('status', 'completed')->count() }}</div>
                    <div class="text-gray-600">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ Auth::user()->wishlist()->count() }}</div>
                    <div class="text-gray-600">Wishlist Items</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ Auth::user()->reviews()->count() }}</div>
                    <div class="text-gray-600">Reviews Written</div>
                </div>
            </div>

            <!-- Account Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6">
                    <button class="tab-button py-4 px-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 active" data-tab="overview">
                        <i class="fas fa-tachometer-alt mr-2"></i>Overview
                    </button>
                    <button class="tab-button py-4 px-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="orders">
                        <i class="fas fa-box mr-2"></i>My Orders
                    </button>
                    <button class="tab-button py-4 px-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="wishlist">
                        <i class="fas fa-heart mr-2"></i>Wishlist
                    </button>
                    <button class="tab-button py-4 px-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="addresses">
                        <i class="fas fa-map-marker-alt mr-2"></i>Addresses
                    </button>
                    <button class="tab-button py-4 px-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="settings">
                        <i class="fas fa-cog mr-2"></i>Settings
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Overview Tab -->
                <div id="overview" class="tab-content active">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Account Overview</h2>
                    
                    <!-- Recent Orders -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Orders</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if(Auth::user()->orders()->latest()->take(3)->count() > 0)
                                @foreach(Auth::user()->orders()->latest()->take(3)->get() as $order)
                                <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-gray-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">Order #{{ $order->order_number }}</div>
                                            <div class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-800">₱{{ number_format($order->total_amount, 2) }}</div>
                                        <span class="inline-block px-2 py-1 text-xs rounded-full 
                                            @if($order->status === 'completed') bg-green-100 text-green-800
                                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                                <div class="text-center mt-4">
                                    <a href="#orders" class="text-blue-600 hover:text-blue-800 font-medium">View All Orders</a>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-box text-4xl mb-4"></i>
                                    <p>No orders yet</p>
                                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Start Shopping</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Wishlist Preview -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Wishlist Items</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if(Auth::user()->wishlist()->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach(Auth::user()->wishlist()->with('product')->take(3)->get() as $wishlistItem)
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <img src="{{ asset($wishlistItem->product->images[0] ?? 'images/placeholder.jpg') }}" alt="{{ $wishlistItem->product->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                                        <h4 class="font-semibold text-gray-800">{{ $wishlistItem->product->name }}</h4>
                                        <p class="text-blue-600 font-bold">₱{{ number_format($wishlistItem->product->current_price, 2) }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-4">
                                    <a href="#wishlist" class="text-blue-600 hover:text-blue-800 font-medium">View All Wishlist Items</a>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-heart text-4xl mb-4"></i>
                                    <p>No wishlist items yet</p>
                                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Start Shopping</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div id="orders" class="tab-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Orders</h2>
                    
                    @if(Auth::user()->orders()->count() > 0)
                        @foreach(Auth::user()->orders()->latest()->get() as $order)
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Order #{{ $order->order_number }}</h3>
                                    <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-800">₱{{ number_format($order->total_amount, 2) }}</p>
                                    <span class="inline-block px-3 py-1 text-sm rounded-full 
                                        @if($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($order->orderItems as $item)
                                <div class="flex items-center space-x-4 bg-white p-4 rounded-lg">
                                    <img src="{{ asset($item->product->images[0] ?? 'images/placeholder.jpg') }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-800">₱{{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="flex justify-end space-x-4 mt-4">
                                <button class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </button>
                                @if($order->status === 'completed')
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i class="fas fa-star mr-2"></i>Write Review
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-box text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold mb-2">No orders yet</h3>
                            <p class="mb-4">Start shopping to see your orders here</p>
                            <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Wishlist Tab -->
                <div id="wishlist" class="tab-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Wishlist</h2>
                    
                    @if(Auth::user()->wishlist()->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach(Auth::user()->wishlist()->with('product')->get() as $wishlistItem)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <img src="{{ asset($wishlistItem->product->images[0] ?? 'images/placeholder.jpg') }}" alt="{{ $wishlistItem->product->name }}" class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-800 mb-2">{{ $wishlistItem->product->name }}</h3>
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-lg font-bold text-blue-600">₱{{ number_format($wishlistItem->product->current_price, 2) }}</span>
                                        @if($wishlistItem->product->is_on_sale)
                                        <span class="text-sm text-gray-500 line-through">₱{{ number_format($wishlistItem->product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="flex-1 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 add-to-cart-btn" data-product-id="{{ $wishlistItem->product->id }}">
                                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                                        </button>
                                        <button class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 remove-wishlist-btn" data-wishlist-id="{{ $wishlistItem->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-heart text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold mb-2">Your wishlist is empty</h3>
                            <p class="mb-4">Save items you love for later</p>
                            <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Addresses Tab -->
                <div id="addresses" class="tab-content">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">My Addresses</h2>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700" onclick="openAddressModal()">
                            <i class="fas fa-plus mr-2"></i>Add New Address
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Default Address -->
                        <div class="bg-white border-2 border-blue-500 rounded-lg p-6 relative">
                            <div class="absolute top-4 right-4">
                                <span class="bg-blue-500 text-white px-2 py-1 text-xs rounded-full">Default</span>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2">{{ Auth::user()->name }}</h3>
                            <p class="text-gray-600 mb-2">{{ Auth::user()->address ?? 'No address set' }}</p>
                            <p class="text-gray-600 mb-2">{{ Auth::user()->phone ?? 'No phone number' }}</p>
                            <div class="flex space-x-2 mt-4">
                                <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button class="px-3 py-1 bg-red-200 text-red-700 rounded-md hover:bg-red-300 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                        
                        <!-- Add New Address Card -->
                        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 flex items-center justify-center hover:border-blue-500 hover:bg-blue-50 cursor-pointer" onclick="openAddressModal()">
                            <div class="text-center">
                                <i class="fas fa-plus text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Add New Address</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div id="settings" class="tab-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Account Settings</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Personal Information -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                            <form id="personal-info-form">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                        <input type="tel" name="phone" value="{{ Auth::user()->phone ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                                        <i class="fas fa-save mr-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
                            <form id="change-password-form">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                        <input type="password" name="current_password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                        <input type="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700">
                                        <i class="fas fa-key mr-2"></i>Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Notification Preferences</h3>
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500" checked>
                                <span class="ml-3 text-gray-700">Email notifications for order updates</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500" checked>
                                <span class="ml-3 text-gray-700">SMS notifications for delivery updates</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                                <span class="ml-3 text-gray-700">Marketing emails and promotions</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black text-white py-12 px-4 mt-12">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-bold mb-4">3Migs Gowns & Barong</h3>
                <p class="text-sm mb-2">Subscribe</p>
                <p class="text-xs mb-4">Get 10% off your first order</p>
                <div class="flex">
                    <input type="email" placeholder="Enter your email" class="bg-black text-white text-sm px-4 py-2 rounded-l-md focus:outline-none border border-white flex-grow">
                    <button class="bg-red-500 px-4 py-2 rounded-r-md hover:bg-red-600 border border-red-500">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Support</h3>
                <p class="text-sm">Pandi, Bulacan</p>
                <p class="text-sm">3migs@gmail.com</p>
                <p class="text-sm">+639*********</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Account</h3>
                <ul>
                    <li class="mb-2"><a href="#" class="text-sm hover:underline">My Account</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="text-sm hover:underline">Login / Register</a></li>
                    <li class="mb-2"><a href="{{ route('cart') }}" class="text-sm hover:underline">Cart</a></li>
                    <li class="mb-2"><a href="{{ route('wishlist') }}" class="text-sm hover:underline">Wishlist</a></li>
                    <li class="mb-2"><a href="#products" class="text-sm hover:underline">Shop</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Quick Link</h3>
                <ul>
                    <li class="mb-2"><a href="#" class="text-sm hover:underline">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-sm hover:underline">Terms Of Use</a></li>
                    <li class="mb-2"><a href="#" class="text-sm hover:underline">FAQ</a></li>
                    <li class="mb-2"><a href="#contact" class="text-sm hover:underline">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center text-xs mt-8">
            <p class="text-gray-500">Copyright Group 6 2025. All right reserved</p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');
                
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });

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

        // Remove from wishlist functionality
        document.querySelectorAll('.remove-wishlist-btn').forEach(button => {
            button.addEventListener('click', function() {
                const wishlistId = this.getAttribute('data-wishlist-id');
                removeFromWishlist(wishlistId);
            });
        });

        // Personal info form
        document.getElementById('personal-info-form').addEventListener('submit', function(e) {
            e.preventDefault();
            updatePersonalInfo(this);
        });

        // Change password form
        document.getElementById('change-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            changePassword(this);
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
                    showNotification('Failed to add product to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        function removeFromWishlist(wishlistId) {
            fetch(`/api/v1/wishlist/remove/${wishlistId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Item removed from wishlist', 'success');
                    location.reload(); // Reload to update the wishlist
                } else {
                    showNotification('Failed to remove item from wishlist', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        function updatePersonalInfo(form) {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            fetch('/api/v1/profile/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Profile updated successfully!', 'success');
                } else {
                    showNotification('Failed to update profile', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        function changePassword(form) {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            fetch('/api/v1/profile/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Password changed successfully!', 'success');
                    form.reset();
                } else {
                    showNotification(data.message || 'Failed to change password', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg ${
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
            fetch('/api/v1/cart')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.data.length;
                        }
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }

        // Initialize cart count on page load
        updateCartCount();
    });

    function openAddressModal() {
        // This would open a modal for adding/editing addresses
        showNotification('Address management feature coming soon!', 'info');
    }
    </script>
    
    @include('layouts.footer')
</body>
</html>
