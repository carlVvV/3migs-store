<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - 3Migs Gowns & Barong</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
        }
        .underline-input {
            border: none;
            border-bottom: 1px solid #D1D5DB;
            border-radius: 0;
            background: transparent;
            padding: 8px 0;
        }
        .underline-input:focus {
            outline: none;
            border-bottom: 2px solid #000;
        }
    </style>
</head>
<body class="bg-white">
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <!-- Main Content -->
    <main class="min-h-screen">
        <div class="flex min-h-screen">
            <!-- Left Side - Illustration -->
            <div class="w-1/2 gradient-bg flex items-center justify-center relative overflow-hidden">
                <!-- Shopping Cart -->
                <div class="absolute left-16 bottom-20 transform rotate-12">
                    <div class="w-32 h-20 bg-gray-300 rounded-lg relative">
                        <div class="absolute -top-2 left-2 w-8 h-8 bg-gray-300 rounded-full"></div>
                        <div class="absolute -top-2 right-2 w-8 h-8 bg-gray-300 rounded-full"></div>
                        <div class="absolute top-2 left-4 w-24 h-12 bg-gray-200 rounded"></div>
                    </div>
                </div>
                
                <!-- Smartphone -->
                <div class="absolute right-20 top-32 transform rotate-12">
                    <div class="w-16 h-32 bg-gray-400 rounded-2xl relative">
                        <div class="absolute top-2 left-1 w-14 h-24 bg-gray-800 rounded-xl"></div>
                        <div class="absolute bottom-2 left-2 w-12 h-2 bg-gray-300 rounded-full"></div>
                    </div>
                </div>
                
                <!-- Shopping Bags -->
                <div class="absolute left-32 top-40">
                    <div class="w-12 h-16 bg-pink-300 rounded-lg relative transform rotate-12">
                        <div class="absolute top-1 left-1 w-10 h-12 bg-pink-200 rounded"></div>
                        <div class="absolute top-0 left-2 w-8 h-2 bg-pink-400 rounded-full"></div>
                    </div>
                </div>
                
                <div class="absolute left-40 top-32">
                    <div class="w-8 h-12 bg-pink-400 rounded-lg relative transform -rotate-12">
                        <div class="absolute top-1 left-1 w-6 h-8 bg-pink-300 rounded"></div>
                        <div class="absolute top-0 left-1 w-6 h-1 bg-pink-500 rounded-full"></div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="w-1/2 flex items-center justify-center p-12">
                <div class="w-full max-w-md">
                    <h1 class="text-4xl font-bold text-black mb-2">Log in to 3Migs Gowns & Barong</h1>
                    <p class="text-gray-600 mb-8">Enter your details below</p>
                    
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="Email or Phone Number"
                                   class="w-full underline-input text-lg @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password Field -->
                        <div>
                            <input type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Password"
                                   class="w-full underline-input text-lg @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Log In Button -->
                        <div>
                            <button type="submit" class="w-full bg-red-600 text-white py-4 px-6 rounded-lg font-medium hover:bg-red-700 transition-colors">
                                Log In
                            </button>
                        </div>
                        
                        <!-- Forgot Password Link -->
                        <div class="text-right">
                            <a href="{{ route('password.request') }}" class="text-red-600 hover:underline">
                                Forgot Password?
                            </a>
                        </div>
                        
                        <!-- Signup Link -->
                        <div class="text-center pt-4">
                            <span class="text-gray-600">Don't have account? </span>
                            <a href="{{ route('register') }}" class="text-black font-medium hover:underline">Sign up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Column 1: Brand & Subscribe -->
                <div>
                    <h3 class="text-xl font-bold mb-4">3Migs Gowns & Barong</h3>
                    <div class="mb-4">
                        <p class="text-sm mb-2">Get 10% off your first order</p>
                        <div class="flex">
                            <input type="email" placeholder="Enter your email" class="flex-1 bg-black text-white text-sm px-4 py-2 rounded-l-md focus:outline-none border border-white">
                            <button class="bg-white text-black px-3 py-2 rounded-r-md hover:bg-gray-200 transition-colors">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Column 2: Support -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Support</h3>
                    <div class="space-y-2 text-sm">
                        <p>Pandi, Bulacan</p>
                        <p>3migs@gmail.com</p>
                        <p>+839**********</p>
                    </div>
                </div>
                
                <!-- Column 3: Account -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Account</h3>
                    <div class="space-y-2 text-sm">
                        <a href="#" class="block hover:text-gray-300">My Account</a>
                        <a href="{{ route('login') }}" class="block hover:text-gray-300">Login / Register</a>
                        <a href="{{ route('cart') }}" class="block hover:text-gray-300">Cart</a>
                        <a href="{{ route('wishlist') }}" class="block hover:text-gray-300">Wishlist</a>
                        <a href="#" class="block hover:text-gray-300">Shop</a>
                    </div>
                </div>
                
                <!-- Column 4: Quick Link -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Link</h3>
                    <div class="space-y-2 text-sm">
                        <a href="#" class="block hover:text-gray-300">Privacy Policy</a>
                        <a href="#" class="block hover:text-gray-300">Terms Of Use</a>
                        <a href="#" class="block hover:text-gray-300">FAQ</a>
                        <a href="#contact" class="block hover:text-gray-300">Contact</a>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            @include('layouts.footer')
        </div>
</body>
</html>