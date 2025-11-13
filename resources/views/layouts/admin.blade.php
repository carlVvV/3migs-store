<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard - 3Migs Barong')</title>

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
        .sidebar-transition {
            transition: all 0.3s ease;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        .sidebar-visible {
            transform: translateX(0);
        }
        .main-content-expanded {
            margin-left: 0;
        }
        .main-content-collapsed {
            margin-left: 0;
        }
        
        /* Ensure sidebar fills full height */
        #sidebar {
            height: 100vh !important;
            min-height: 100vh;
        }
        
        /* Custom scrollbar for sidebar navigation */
        #sidebar nav::-webkit-scrollbar {
            width: 6px;
        }
        
        #sidebar nav::-webkit-scrollbar-track {
            background: #374151;
        }
        
        #sidebar nav::-webkit-scrollbar-thumb {
            background: #6b7280;
            border-radius: 3px;
        }
        
        #sidebar nav::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        @media (min-width: 768px) {
            .main-content-expanded {
                margin-left: 256px;
            }
            .main-content-collapsed {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 sidebar-transition sidebar-visible md:translate-x-0 shadow-xl flex flex-col h-screen">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 bg-gray-800 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('3migs-logo.png') }}" alt="3Migs Logo" class="h-8 w-auto">
                    <span class="text-white text-lg font-bold">Admin Panel</span>
                </div>
                <button id="sidebar-toggle" class="text-gray-400 hover:text-white md:hidden">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Admin Account Section -->
            <div class="px-6 py-4 bg-gray-800 border-b border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-300 truncate">{{ auth()->user()->email }}</p>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Admin
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 mt-6 px-3 overflow-y-auto">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-300 group-hover:text-white"></i>
                        Dashboard
                    </a>

                    

                    <!-- Orders -->
                    <a href="{{ route('admin.orders') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.orders') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-shopping-cart mr-3 text-gray-300 group-hover:text-white"></i>
                        Orders
                    </a>

                    <!-- Inventory -->
                    <a href="{{ route('admin.inventory') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ (request()->routeIs('admin.inventory') || request()->routeIs('admin.products') || request()->routeIs('admin.products.*')) ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-boxes mr-3 text-gray-300 group-hover:text-white"></i>
                        Inventory
                    </a>

                    <!-- Deleted Items -->
                    <a href="{{ route('admin.deleted-items') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.deleted-items') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-trash-alt mr-3 text-gray-300 group-hover:text-white"></i>
                        Deleted Items
                    </a>

                    <!-- Coupons -->
                    <a href="{{ route('admin.coupons') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.coupons') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-ticket-alt mr-3 text-gray-300 group-hover:text-white"></i>
                        Coupons
                    </a>

                    <!-- Reports -->
                    <a href="{{ route('admin.reports') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.reports') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-chart-bar mr-3 text-gray-300 group-hover:text-white"></i>
                        Reports
                    </a>
                </div>

                <!-- Divider -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <div class="space-y-1">
                        <!-- Users -->
                        <a href="{{ route('admin.users') }}" 
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.users') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-users mr-3 text-gray-300 group-hover:text-white"></i>
                            Users
                        </a>

                        <!-- Reviews -->
                        <a href="{{ route('admin.reviews') }}" 
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.reviews') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-star mr-3 text-gray-300 group-hover:text-white"></i>
                            Reviews
                        </a>
                    </div>
                </div>

                <!-- Additional Tools Section -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <div class="px-3 mb-3">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tools</h3>
                    </div>
                    <div class="space-y-1">
                        <!-- Settings -->
                        <a href="{{ route('admin.settings') }}" 
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.settings') ? 'bg-gray-800 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-cog mr-3 text-gray-300 group-hover:text-white"></i>
                            Settings
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 hidden md:hidden"></div>

        <!-- Main Content -->
        <div id="main-content" class="flex-1 flex flex-col main-content-expanded">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button id="mobile-sidebar-toggle" class="text-gray-500 hover:text-gray-700 md:hidden">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex items-center space-x-3 ml-4">
                            <img src="{{ asset('3migs-logo.png') }}" alt="3Migs Logo" class="h-8 w-auto">
                            <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Admin Dashboard')</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- View Store Link -->
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View Store
                        </a>
                        
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-20 left-0 right-0 z-50 flex justify-center pointer-events-none">
        <!-- Notifications will be dynamically inserted here -->
    </div>

    <!-- Toast Notification Component -->
    <x-toast-notification />

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const mobileToggle = document.getElementById('mobile-sidebar-toggle');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mainContent = document.getElementById('main-content');

            function toggleSidebar() {
                sidebar.classList.toggle('sidebar-hidden');
                sidebar.classList.toggle('sidebar-visible');
                sidebarOverlay.classList.toggle('hidden');
                
                if (window.innerWidth >= 768) {
                    mainContent.classList.toggle('main-content-expanded');
                    mainContent.classList.toggle('main-content-collapsed');
                }
            }

            function closeSidebar() {
                if (window.innerWidth < 768) {
                    sidebar.classList.add('sidebar-hidden');
                    sidebar.classList.remove('sidebar-visible');
                    sidebarOverlay.classList.add('hidden');
                }
            }

            // Mobile toggle
            mobileToggle.addEventListener('click', toggleSidebar);
            
            // Sidebar close button
            sidebarToggle.addEventListener('click', toggleSidebar);
            
            // Overlay click
            sidebarOverlay.addEventListener('click', closeSidebar);
            
            // Close on route change (for mobile)
            document.addEventListener('click', function(e) {
                if (e.target.matches('a[href]') && window.innerWidth < 768) {
                    setTimeout(closeSidebar, 100);
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('sidebar-hidden');
                    sidebar.classList.add('sidebar-visible');
                    sidebarOverlay.classList.add('hidden');
                    mainContent.classList.add('main-content-expanded');
                    mainContent.classList.remove('main-content-collapsed');
                } else {
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
