<!-- Top Banner -->
<header class="bg-black text-white py-2">
    <div class="container mx-auto flex items-center justify-center relative px-4">
        <span class="absolute left-0"></span>
        <span class="text-xs mx-auto">Premium Barong Collection - Free Express Delivery <a href="#" class="underline">Shop Now</a></span>
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

<!-- Main Header -->
<header class="bg-white shadow-md sticky top-0 z-40">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-8">
            <a href="{{ route('home') }}" class="flex items-center space-x-3 text-2xl font-bold text-gray-800">
                <img src="{{ asset('3migs-logo.png') }}" alt="3Migs Logo" class="h-10 w-auto">
                <span>3Migs Barong</span>
            </a>
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 font-medium">Home</a>
                <a href="#" class="text-gray-600 hover:text-gray-900 font-medium">Collections</a>
                <a href="{{ route('cart') }}" class="text-gray-600 hover:text-gray-900 font-medium">Cart</a>
                <a href="#" class="text-gray-600 hover:text-gray-900 font-medium">Account</a>
            </nav>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" placeholder="Search for Barong..." class="border border-gray-300 rounded-md py-2 px-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <div class="flex items-center space-x-4 text-gray-600 text-lg">
                <a href="#" class="relative">
                    <i class="far fa-heart"></i>
                    <span id="wishlist-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                </a>
                <a href="{{ route('cart') }}" class="relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count" class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>
                <div class="relative">
                  <button id="profile-btn" class="focus:outline-none"><i class="far fa-user"></i></button>
                  <div id="profileMenu" class="absolute right-0 mt-3 w-56 bg-gray-800 text-white rounded-md shadow-lg py-2 hidden z-50">
                      <!-- Guest menu items -->
                      <div id="guest-menu" class="">
                          <a href="{{ route('login') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                              <i class="fas fa-sign-in-alt mr-3"></i>
                              <span>Log in</span>
                          </a>
                          <a href="{{ route('signup') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                              <i class="fas fa-user-plus mr-3"></i>
                              <span>Sign up</span>
                          </a>
                      </div>
                      <!-- Logged-in user menu items -->
                      <div id="user-menu" class="hidden">
                          <div class="px-4 py-2 border-b border-gray-700">
                              <div class="text-sm text-gray-300">Welcome back!</div>
                              <div id="user-name" class="font-semibold"></div>
                          </div>
                          <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                              <i class="fas fa-user mr-3"></i>
                              <span>Profile</span>
                          </a>
                          <a href="{{ route('orders') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                              <i class="fas fa-shopping-bag mr-3"></i>
                              <span>My Orders</span>
                          </a>
                          <a href="{{ route('wishlist') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                              <i class="far fa-heart mr-3"></i>
                              <span>Wishlist</span>
                          </a>
                          <div class="border-t border-gray-700 my-2"></div>
                          <button id="logout-btn" class="w-full text-left flex items-center px-4 py-2 hover:bg-gray-700 text-red-400">
                              <i class="fas fa-sign-out-alt mr-3"></i>
                              <span>Logout</span>
                          </button>
                      </div>
                  </div>
                </div>
                <button id="chatbotButton" class="focus:outline-none ml-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full w-10 h-10 flex items-center justify-center transition-all duration-200 hover:scale-105" title="Chat with MigsBot"><i class="fas fa-robot"></i></button>

                <!-- Enhanced MigsBot Chatbox Component -->
                <div id="chatboxContainer" class="fixed top-20 right-4 w-80 h-[calc(100vh-8rem)] bg-white rounded-lg shadow-xl flex flex-col z-[1000] hidden md:w-96 h-[calc(100vh-8rem)] lg:w-[400px] transition-transform transform translate-x-full duration-300 ease-in-out">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">MigsBot</h3>
                                <p class="text-xs text-blue-100">AI Assistant</p>
                            </div>
                        </div>
                        <button id="closeChatbox" class="text-white hover:text-blue-200 transition-colors duration-200 p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-4 text-sm bg-gray-50">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-white text-sm"></i>
                            </div>
                            <div class="bg-white p-4 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%]">
                                <p class="text-gray-800 text-sm leading-relaxed">
                                    👋 Hi! I'm <strong>MigsBot</strong>, your AI assistant for 3Migs Barong. 
                                </p>
                                <p class="text-gray-600 text-xs mt-2">
                                    I can help you find barong and gowns, check order info, or answer questions about shipping, returns, and more.
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Find barong</span>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Shipping info</span>
                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">Returns</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-200 bg-white">
                        <div class="flex items-center space-x-3">
                            <div class="flex-1 relative">
                                <input type="text" id="chatInput" placeholder="Type your message..." class="w-full border border-gray-300 rounded-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" autocomplete="off">
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                    <i class="fas fa-paper-plane text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <button id="sendBtn" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full w-12 h-12 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105">
                                <i class="fas fa-paper-plane text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
