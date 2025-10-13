<!-- Footer -->
<footer id="main-footer" class="bg-black text-white py-12 px-4">
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
                <li class="mb-2"><a href="{{ route('profile') }}" class="text-sm hover:underline">My Account</a></li>
                <li class="mb-2"><a href="{{ route('login') }}" class="text-sm hover:underline">Login / Register</a></li>
                <li class="mb-2"><a href="{{ route('cart') }}" class="text-sm hover:underline">Cart</a></li>
                <li class="mb-2"><a href="{{ route('wishlist') }}" class="text-sm hover:underline">Wishlist</a></li>
                <li class="mb-2"><a href="{{ route('home') }}" class="text-sm hover:underline">Shop</a></li>
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