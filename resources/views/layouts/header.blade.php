<!-- Main Header Navigation -->
<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Left Section: Brand & Navigation -->
            <div class="flex items-center space-x-8">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900 hover:text-gray-700 transition-colors">
                    3Migs Gowns & Barong
                </a>
                
                <!-- Navigation Links -->
                <nav class="hidden md:flex space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                        Home
                    </a>
                </nav>
            </div>
            
            <!-- Right Section: Icons & User -->
            <div class="flex items-center space-x-4">
                <!-- Wishlist Icon -->
                <a href="{{ route('wishlist') }}" class="relative text-gray-600 hover:text-red-500 transition-colors">
                    <i class="far fa-heart text-xl"></i>
                    <span id="wishlist-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                </a>
                
                <!-- Cart Icon -->
                <a href="{{ route('cart') }}" class="relative text-gray-600 hover:text-blue-600 transition-colors">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>
                
                <!-- MigsBot Icon -->
                <button id="bot-btn" class="text-gray-600 hover:text-gray-800 transition-colors duration-200" title="Chat with MigsBot">
                    <i class="fas fa-robot text-xl"></i>
                </button>
                
                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button id="profile-btn" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="far fa-user text-xl"></i>
                        @auth
                            <span class="text-sm text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                        @else
                            <span class="text-sm text-gray-700 font-medium">Guest</span>
                        @endauth
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    
                    <div id="profileMenu" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 hidden z-50" style="display: none;">
                        @auth
                            <!-- Logged-in user menu -->
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('account') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-3 text-gray-400"></i>
                                <span>Account</span>
                            </a>
                            <a href="{{ route('orders') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-box mr-3 text-gray-400"></i>
                                <span>Orders</span>
                            </a>
                            <a href="{{ route('wishlist') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-heart mr-3 text-gray-400"></i>
                                <span>Wishlist</span>
                            </a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-3 text-gray-400"></i>
                                    <span>Admin Panel</span>
                                </a>
                            @endif
                            <hr class="my-2 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                                    <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        @else
                            <!-- Guest menu -->
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">Guest User</p>
                                <p class="text-xs text-gray-500">Please log in to access your account</p>
                            </div>
                            <a href="{{ route('login') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-in-alt mr-3 text-gray-400"></i>
                                <span>Login</span>
                            </a>
                            <a href="{{ route('register') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-plus mr-3 text-gray-400"></i>
                                <span>Sign up</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</header>

<!-- MigsBot Chat Panel -->
<div id="migsbot-panel" class="fixed top-16 right-4 w-80 h-[calc(100vh-8rem)] bg-white rounded-lg shadow-xl flex flex-col z-[1000] hidden md:w-96 md:h-[calc(100vh-8rem)] lg:w-[400px] transition-transform transform translate-x-full duration-300 ease-in-out">
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
        <button id="migsbot-close-btn" class="text-white hover:text-blue-200 transition-colors duration-200 p-2 rounded-full hover:bg-white hover:bg-opacity-10">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    <div id="migsbot-messages" class="flex-1 p-4 overflow-y-auto space-y-4 text-sm bg-gray-50">
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
    <form id="migsbot-form" class="p-4 border-t border-gray-200 bg-white">
        <div class="flex items-center space-x-3">
            <div class="flex-1 relative">
                <input type="text" id="migsbot-input" placeholder="Type your message..." class="w-full border border-gray-300 rounded-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" autocomplete="off">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-paper-plane text-gray-400 text-sm"></i>
                </div>
            </div>
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full w-12 h-12 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105">
                <i class="fas fa-paper-plane text-sm"></i>
            </button>
        </div>
    </form>
</div>

<style>
    .migsbot-message {
        animation: fadeInUp 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .migsbot-typing {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #6B7280;
        animation: typing 1.4s infinite ease-in-out;
    }
    
    .migsbot-typing:nth-child(1) { animation-delay: -0.32s; }
    .migsbot-typing:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes typing {
        0%, 80%, 100% {
            transform: scale(0.8);
            opacity: 0.5;
        }
        40% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Header JavaScript loaded');
    
    const migsbotPanel = document.getElementById('migsbot-panel');
    const migsbotCloseBtn = document.getElementById('migsbot-close-btn');
    const migsbotForm = document.getElementById('migsbot-form');
    const migsbotInput = document.getElementById('migsbot-input');
    const migsbotMessages = document.getElementById('migsbot-messages');
    const botTriggerBtn = document.getElementById('bot-btn');
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profileMenu');

    console.log('Profile button found:', profileBtn);
    console.log('Profile menu found:', profileMenu);

    // MigsBot functionality
    function toggleMigsBot() {
        if (migsbotPanel && migsbotPanel.classList.contains('translate-x-full')) {
            migsbotPanel.classList.remove('hidden');
            setTimeout(() => {
                migsbotPanel.classList.remove('translate-x-full');
            }, 10);
        } else if (migsbotPanel) {
            migsbotPanel.classList.add('translate-x-full');
            setTimeout(() => {
                migsbotPanel.classList.add('hidden');
            }, 300);
        }
    }

    // Event listeners
    if (botTriggerBtn) {
        botTriggerBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMigsBot();
        });
    }

    if (migsbotCloseBtn) {
        migsbotCloseBtn.addEventListener('click', toggleMigsBot);
    }

    // Profile dropdown functionality
    function initProfileDropdown() {
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profileMenu');
        
        if (profileBtn && profileMenu) {
            console.log('Adding profile dropdown event listener');
            
            // Remove any existing event listeners
            profileBtn.removeEventListener('click', handleProfileClick);
            
            // Add new event listener
            profileBtn.addEventListener('click', handleProfileClick);
            
            function handleProfileClick(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Profile button clicked');
                
                // Toggle the dropdown
                if (profileMenu.classList.contains('hidden') || profileMenu.style.display === 'none') {
                    profileMenu.classList.remove('hidden');
                    profileMenu.style.display = 'block';
                    console.log('Profile menu shown');
                } else {
                    profileMenu.classList.add('hidden');
                    profileMenu.style.display = 'none';
                    console.log('Profile menu hidden');
                }
            }
        } else {
            console.log('Profile button or menu not found');
        }
    }
    
    // Initialize profile dropdown once
    initProfileDropdown();

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profileMenu');
        
        if (migsbotPanel && !migsbotPanel.contains(e.target) && botTriggerBtn && !botTriggerBtn.contains(e.target) && !migsbotPanel.classList.contains('translate-x-full')) {
            toggleMigsBot();
        }
        if (profileMenu && profileBtn && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
            profileMenu.classList.add('hidden');
            profileMenu.style.display = 'none';
        }
    });

    // Enhanced MigsBot chat functionality
    function addMessage(sender, text, isProduct = false, products = []) {
        const messageContainer = document.createElement('div');
        messageContainer.className = `flex items-start space-x-3 migsbot-message ${sender === 'user' ? 'justify-end' : ''}`;

        if (sender === 'user') {
            const messageBubble = document.createElement('div');
            messageBubble.className = 'bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%]';
            messageBubble.innerHTML = `<p class="text-sm leading-relaxed">${text}</p>`;
            messageContainer.appendChild(messageBubble);
        } else {
            const botIcon = document.createElement('div');
            botIcon.className = 'flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center';
            botIcon.innerHTML = '<i class="fas fa-robot text-white text-sm"></i>';
            messageContainer.appendChild(botIcon);

            const messageBubble = document.createElement('div');
            messageBubble.className = 'bg-white p-4 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%]';
            messageBubble.innerHTML = `<p class="text-gray-800 text-sm leading-relaxed">${text}</p>`;

            if (isProduct && products.length > 0) {
                let productHtml = '<div class="mt-3"><p class="text-gray-800 text-sm font-medium mb-2">Here are some products you might like:</p>';
                products.forEach(product => {
                    productHtml += `
                        <div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="font-medium text-blue-900">${product.name}</div>
                            <div class="text-sm text-blue-700">₱${parseFloat(product.current_price || product.price).toFixed(2)}</div>
                            <a href="/product/${product.slug}" class="text-xs text-blue-600 hover:text-blue-800 underline">View Product</a>
                        </div>
                    `;
                });
                productHtml += '</div>';
                messageBubble.innerHTML += productHtml;
            }

            messageContainer.appendChild(messageBubble);
        }

        migsbotMessages.appendChild(messageContainer);
        migsbotMessages.scrollTop = migsbotMessages.scrollHeight;
    }

    // Enhanced MigsBot form submission
    if (migsbotForm) {
        migsbotForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = migsbotInput.value.trim();
            if (!message) return;

            // Disable input while processing
            migsbotInput.disabled = true;
            const submitBtn = migsbotForm.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';

            // Add user message
            addMessage('user', message);
            migsbotInput.value = '';

            // Show typing indicator
            const typingContainer = document.createElement('div');
            typingContainer.className = 'flex items-start space-x-3 migsbot-message';
            typingContainer.innerHTML = `
                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div class="bg-white p-4 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%]">
                    <div class="flex items-center space-x-1">
                        <span class="migsbot-typing"></span>
                        <span class="migsbot-typing"></span>
                        <span class="migsbot-typing"></span>
                    </div>
                </div>
            `;
            migsbotMessages.appendChild(typingContainer);
            migsbotMessages.scrollTop = migsbotMessages.scrollHeight;

            fetch('/api/v1/migsbot/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                // Remove typing indicator
                typingContainer.remove();

                if (data.success) {
                    addMessage('bot', data.reply || 'I apologize, but I\'m having trouble processing your request. Please try again or contact our store directly.', data.is_product_search, data.products);
                } else {
                    addMessage('bot', 'I apologize, but I\'m having trouble processing your request. Please try again or contact our store directly.');
                }
            })
            .catch(error => {
                console.error('MigsBot API Error:', error);
                // Remove typing indicator
                typingContainer.remove();
                addMessage('bot', 'Network error, please try again.');
            })
            .finally(() => {
                // Re-enable input
                migsbotInput.disabled = false;
                submitBtn.innerHTML = originalContent;
                migsbotInput.focus();
            });
        });
    }

    // Update cart count
    function updateCartCount() {
        // Prevent multiple simultaneous calls
        if (window.cartCountUpdating) {
            return;
        }
        window.cartCountUpdating = true;
        
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
            .catch(error => console.error('Error updating cart count:', error))
            .finally(() => {
                window.cartCountUpdating = false;
            });
    }

    // Initialize cart count only on customer-facing pages (not admin pages)
    if (!window.location.pathname.startsWith('/admin')) {
        updateCartCount();
    }
});
</script>
