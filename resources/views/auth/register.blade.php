<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - 3Migs Gowns & Barong</title>
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
            
            <!-- Right Side - Signup Form -->
            <div class="w-1/2 flex items-center justify-center p-12">
                <div class="w-full max-w-md">
                    <h1 class="text-4xl font-bold text-black mb-2">Create an account</h1>
                    <p class="text-gray-600 mb-8">Enter your details below</p>
                    
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

                        <!-- Name Field -->
        <div>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="name"
                                   placeholder="Name"
                                   class="w-full underline-input text-lg @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
        </div>

                        <!-- Email Field -->
                        <div>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
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
                                   autocomplete="new-password"
                                   placeholder="Password"
                                   class="w-full underline-input text-lg @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Confirm Password Field -->
                        <div>
                            <input type="password" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Confirm Password"
                                   class="w-full underline-input text-lg @error('password_confirmation') border-red-500 @enderror">
                            @error('password_confirmation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Upload ID Button -->
                        <div class="pt-4">
                            <button type="button" class="w-full bg-black text-white py-4 px-6 rounded-lg font-medium hover:bg-gray-800 transition-colors">
                                Upload Identification Card
                            </button>
                            <p class="text-gray-500 text-sm text-center mt-2">(for verification purposes)</p>
        </div>

                        <!-- Create Account Button -->
                        <div>
                            <button type="submit" class="w-full bg-red-600 text-white py-4 px-6 rounded-lg font-medium hover:bg-red-700 transition-colors">
                                Create Account
                            </button>
                        </div>
                        
                        <!-- Google Signup Button -->
                        <div>
                            <button type="button" class="w-full bg-white border border-gray-300 text-black py-4 px-6 rounded-lg font-medium hover:bg-gray-50 transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Sign up with Google
                            </button>
                        </div>
                        
                        <!-- Login Link -->
                        <div class="text-center pt-4">
                            <span class="text-gray-600">Already have account? </span>
                            <a href="{{ route('login') }}" class="text-black font-medium hover:underline">Log in</a>
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
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                @include('layouts.footer')
        </div>

    <script>
        // Handle file upload for ID card
        document.querySelector('button[type="button"]').addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    this.textContent = 'ID Card Uploaded ✓';
                    this.classList.add('bg-green-600');
                    this.classList.remove('bg-black');
                }
            };
            input.click();
        });
    </script>
</body>
</html>