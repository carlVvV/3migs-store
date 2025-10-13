<!-- Summer Sale Banner -->
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
@include('layouts.migsbot')

<!-- Main Header -->
<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-8">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">3Migs Gowns & Barong</a>
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
                    <button id="bot-btn" class="focus:outline-none bg-blue-600 hover:bg-blue-700 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors duration-200" title="Chat with MigsBot">
                        <i class="fas fa-robot text-sm"></i>
                    </button>
                    <div id="profileMenu" class="absolute right-0 mt-2 w-56 bg-gray-800 text-white rounded-md shadow-lg py-2 hidden z-50">
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
                                @if(auth()->user()->role === 'admin')
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

</header>