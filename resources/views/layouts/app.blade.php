<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', '3Migs Barong - Premium Filipino Barong Store')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Header Navigation -->
    @include('layouts.header')
    
    <!-- Page Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Global function to get header height and position notifications
        function getHeaderHeight() {
            const header = document.querySelector('header');
            if (header) {
                const headerRect = header.getBoundingClientRect();
                return headerRect.height;
            }
            return 64; // Fallback height
        }

        function positionNotificationBelowHeader(element, gap = 16) {
            if (!element) return;
            
            const headerHeight = getHeaderHeight();
            const topPosition = headerHeight + gap;
            element.style.top = `${topPosition}px`;
        }

        // Recalculate notification positions on window resize
        window.addEventListener('resize', () => {
            // Trigger repositioning for all notification systems
            if (window.notificationSystem && window.notificationSystem.positionNotifications) {
                window.notificationSystem.positionNotifications();
            }
        });
        // Cart functionality
        function addToCart(productId, quantity = 1) {
            fetch('/api/v1/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Product added to cart!');
                    updateCartCount();
                } else {
                    alert('Failed to add product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function toggleWishlist(productId) {
            fetch('/api/v1/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Added to wishlist!');
                } else {
                    alert('Failed to add to wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function showNotification(message, type = 'info') {
            // Create notification element
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
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function updateCartCount() {
            // Prevent multiple simultaneous calls
            if (window.cartCountUpdating) {
                return;
            }
            window.cartCountUpdating = true;
            
            // Update cart count in navigation
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

        // Initialize cart count on page load (only on customer-facing pages)
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.location.pathname.startsWith('/admin')) {
                updateCartCount();
            }
            
            // Language dropdown functionality
            const langBtn = document.getElementById('lang-dropdown-btn');
            const langMenu = document.getElementById('lang-dropdown-menu');
            const selectedLang = document.getElementById('selected-lang');
            
            if (langBtn && langMenu && selectedLang) {
                langBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    langMenu.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!langBtn.contains(e.target) && !langMenu.contains(e.target)) {
                        langMenu.classList.add('hidden');
                    }
                });
                
                // Handle language selection
                langMenu.addEventListener('click', function(e) {
                    if (e.target.tagName === 'BUTTON') {
                        const lang = e.target.getAttribute('data-lang');
                        selectedLang.textContent = e.target.textContent;
                        langMenu.classList.add('hidden');
                        // Here you can add language switching logic
                    }
                });
            }
        });
    </script>
    
    @include('layouts.migsbot')
    
    <!-- Notification System -->
    @include('components.notification-system')
    
    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>
