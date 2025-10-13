@extends('layouts.app')

@section('title', 'Checkout - 3Migs Gowns & Barong')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-100 py-2">
    <div class="container mx-auto px-4">
        <nav class="text-sm">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
            <span class="mx-2 text-gray-400">/</span>
            <a href="{{ route('cart') }}" class="text-gray-600 hover:text-gray-900">Cart</a>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-gray-900 font-medium">Checkout</span>
        </nav>
    </div>
</div>

<!-- Main Checkout Content -->
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
        
        <!-- Loading State -->
        <div id="checkout-loading" class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600 mb-4"></div>
            <p class="text-gray-600">Loading checkout...</p>
        </div>
        
        <!-- Not Logged In Message -->
        <div id="not-logged-in-message" class="flex items-center justify-center min-h-[60vh]" @if(auth()->check()) style="display: none;" @endif>
            <div class="bg-white rounded-lg shadow-md p-8 text-center max-w-md w-full mx-4 hover:bg-gray-50 transition-colors duration-300 relative z-10">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-user-lock text-4xl text-gray-400"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-3">Please Log In</h2>
                <p class="text-gray-600 mb-8">You need to be logged in to proceed to checkout.</p>
                <div class="space-x-4 relative z-10">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3 text-white font-bold rounded-full hover:bg-gray-700 transition-colors shadow-lg relative z-20" style="position: relative; z-index: 20; background-color: #4b5563 !important; min-height: 50px; min-width: 140px;">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 bg-red-600 text-white font-bold rounded-full hover:bg-red-700 transition-colors shadow-lg relative z-20" style="position: relative; z-index: 20;">
                        <i class="fas fa-user-plus mr-2"></i>
                        Sign Up
                    </a>
                </div>
            </div>
        </div>

        <!-- Empty Cart Message -->
        <div id="empty-cart-message" class="hidden flex items-center justify-center min-h-[60vh]">
            <div class="bg-white rounded-lg shadow-md p-8 text-center max-w-md w-full mx-4 hover:bg-gray-50 transition-colors duration-300">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-3">Your cart is empty</h2>
                <p class="text-gray-600 mb-8">You need to add items to your cart before proceeding to checkout.</p>
                <div class="space-x-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-8 py-3 bg-red-600 text-white font-bold rounded-full hover:bg-red-700 transition-colors shadow-md">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Continue Shopping
                    </a>
                    <a href="{{ route('cart') }}" class="inline-flex items-center px-8 py-3 bg-gray-600 text-white font-bold rounded-full hover:bg-gray-700 transition-colors shadow-md">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        View Cart
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Checkout Content -->
        <div id="checkout-content" class="grid grid-cols-1 lg:grid-cols-2 gap-8" @if(!auth()->check()) style="display: none;" @endif>
            <!-- Billing Details Form -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Billing Details</h2>
                    
                    <form id="checkout-form" class="space-y-4">
                        @csrf
                        
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                First Name Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="full_name" id="full_name" required 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Enter your full name">
                            <div id="full_name_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name or Landmark
                            </label>
                            <input type="text" name="company_name" id="company_name"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Enter company name or landmark">
                        </div>
                        
                        <!-- Street Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="street_address" id="street_address" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Enter street address">
                            <div id="street_address_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Apartment/Floor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Apartment, floor, etc. (optional)
                            </label>
                            <input type="text" name="apartment" id="apartment"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Apartment, floor, etc.">
                        </div>
                        
                        <!-- City -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Town/City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" id="city" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Enter city">
                            <div id="city_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" id="phone" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Enter phone number">
                            <div id="phone_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Email Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Enter email address">
                            <div id="email_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Save Information Checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" name="save_info" id="save_info" checked
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="save_info" class="ml-2 text-sm text-gray-700">
                                Save this information for faster check-out next time
                            </label>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary and Payment -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                    
                    <!-- Order Items -->
                    <div id="order-items" class="space-y-3 mb-4">
                        <!-- Items will be loaded dynamically -->
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="order-subtotal" class="font-medium">₱0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="text-green-600 font-medium">Free</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                            <span>Total:</span>
                            <span id="order-total" class="text-red-600">₱0.00</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods</h3>
                    
                    <div class="space-y-3">
                        <!-- E-Wallet Option -->
                        <div class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50">
                            <input type="radio" name="payment_method" id="ewallet" value="ewallet" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <label for="ewallet" class="ml-3 flex items-center">
                                <span class="text-sm font-medium text-gray-700">GCash</span>
                                <div class="ml-4 flex space-x-2">
                                    <div class="w-8 h-5 bg-blue-600 rounded text-white text-xs flex items-center justify-center font-bold">GC</div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Cash on Delivery Option -->
                        <div class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50">
                            <input type="radio" name="payment_method" id="cod" value="cod" checked
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <label for="cod" class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Cash on delivery</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Coupon Code -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Coupon Code</h3>
                    <div class="flex space-x-2">
                        <input type="text" id="coupon-code" placeholder="Enter coupon code"
                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <button id="apply-coupon-btn" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                            Apply Coupon
                        </button>
                    </div>
                    <div id="coupon-message" class="mt-2 text-sm hidden"></div>
                </div>
                
                <!-- Place Order Button -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <button id="place-order-btn" class="w-full bg-red-600 text-white font-medium py-3 px-4 rounded-md hover:bg-red-700 transition-colors">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mb-4"></div>
        <p class="text-gray-600">Processing your order...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Immediately hide loading state
    document.getElementById('checkout-loading').classList.add('hidden');
    
    // Check authentication status immediately
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    
    if (isLoggedIn) {
        // User is logged in - show checkout content immediately
        document.getElementById('checkout-content').style.display = 'grid';
        document.getElementById('not-logged-in-message').style.display = 'none';
        document.getElementById('empty-cart-message').style.display = 'none';
        
        // Load cart data
        loadOrderSummary();
    } else {
        // User is not logged in - show login message immediately
        document.getElementById('not-logged-in-message').style.display = 'flex';
        document.getElementById('checkout-content').style.display = 'none';
        document.getElementById('empty-cart-message').style.display = 'none';
    }
    
    // Form validation
    setupFormValidation();
    
    // Event listeners
    document.getElementById('apply-coupon-btn').addEventListener('click', applyCoupon);
    document.getElementById('place-order-btn').addEventListener('click', placeOrder);

    function loadOrderSummary() {
        // This function only runs for logged-in users
        fetch('/api/v1/cart')
            .then(response => response.json())
            .then(data => {
                document.getElementById('checkout-loading').classList.add('hidden');
                
                if (data.success && data.items && data.items.length > 0) {
                    displayOrderItems(data.items);
                    updateOrderTotals(data);
                    document.getElementById('checkout-content').classList.remove('hidden');
                    document.getElementById('empty-cart-message').classList.add('hidden');
                    document.getElementById('not-logged-in-message').classList.add('hidden');
                } else {
                    // Show empty cart message
                    document.getElementById('checkout-content').classList.add('hidden');
                    document.getElementById('empty-cart-message').classList.remove('hidden');
                    document.getElementById('not-logged-in-message').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading cart:', error);
                document.getElementById('checkout-loading').classList.add('hidden');
                document.getElementById('checkout-content').classList.add('hidden');
                document.getElementById('empty-cart-message').classList.remove('hidden');
                document.getElementById('not-logged-in-message').classList.add('hidden');
            });
    }

    function displayOrderItems(items) {
        const container = document.getElementById('order-items');
        container.innerHTML = '';

        items.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'flex items-center space-x-3';
            itemElement.innerHTML = `
                <img src="${item.image || '/images/placeholder.jpg'}" 
                     alt="${item.name}" 
                     class="w-12 h-12 object-cover rounded">
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">${item.name}</h4>
                    <p class="text-sm text-gray-500">Qty: ${item.quantity}</p>
                </div>
                <div class="text-sm font-medium text-gray-900">
                    ₱${(item.price * item.quantity).toFixed(2)}
                </div>
            `;
            container.appendChild(itemElement);
        });
    }

    function updateOrderTotals(data) {
        document.getElementById('order-subtotal').textContent = `₱${data.subtotal || 0}`;
        document.getElementById('order-total').textContent = `₱${data.total || 0}`;
    }

    function setupFormValidation() {
        const form = document.getElementById('checkout-form');
        const requiredFields = ['full_name', 'street_address', 'city', 'phone', 'email'];
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            const errorDiv = document.getElementById(fieldName + '_error');
            
            field.addEventListener('blur', function() {
                validateField(field, errorDiv);
            });
        });
    }

    function validateField(field, errorDiv) {
        const value = field.value.trim();
        const fieldName = field.name;
        
        // Clear previous error
        errorDiv.classList.add('hidden');
        field.classList.remove('border-red-500');
        
        if (!value) {
            showFieldError(field, errorDiv, `${fieldName.replace('_', ' ')} is required`);
            return false;
        }
        
        // Email validation
        if (fieldName === 'email' && !isValidEmail(value)) {
            showFieldError(field, errorDiv, 'Please enter a valid email address');
            return false;
        }
        
        // Phone validation
        if (fieldName === 'phone' && !isValidPhone(value)) {
            showFieldError(field, errorDiv, 'Please enter a valid phone number');
            return false;
        }
        
        return true;
    }

    function showFieldError(field, errorDiv, message) {
        field.classList.add('border-red-500');
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }

    function validateForm() {
        const requiredFields = ['full_name', 'street_address', 'city', 'phone', 'email'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            const errorDiv = document.getElementById(fieldName + '_error');
            
            if (!validateField(field, errorDiv)) {
                isValid = false;
            }
        });
        
        // Check payment method
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            showNotification('Please select a payment method', 'error');
            isValid = false;
        }
        
        return isValid;
    }

    function applyCoupon() {
        const couponCode = document.getElementById('coupon-code').value.trim();
        if (!couponCode) {
            showCouponMessage('Please enter a coupon code', 'error');
            return;
        }

        fetch('/api/v1/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                coupon_code: couponCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCouponMessage(data.message, 'success');
                loadOrderSummary(); // Reload to show updated totals
            } else {
                showCouponMessage(data.message || 'Invalid coupon code', 'error');
            }
        })
        .catch(error => {
            console.error('Error applying coupon:', error);
            showCouponMessage('Error applying coupon', 'error');
        });
    }

    function showCouponMessage(message, type) {
        const messageDiv = document.getElementById('coupon-message');
        messageDiv.textContent = message;
        messageDiv.className = `mt-2 text-sm ${type === 'success' ? 'text-green-600' : 'text-red-600'}`;
        messageDiv.classList.remove('hidden');
        
        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 5000);
    }

    function placeOrder() {
        if (!validateForm()) {
            return;
        }

        // Show loading modal
        document.getElementById('loading-modal').classList.remove('hidden');
        
        // Get form data
        const formData = new FormData(document.getElementById('checkout-form'));
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        const orderData = {
            full_name: formData.get('full_name'),
            company_name: formData.get('company_name'),
            street_address: formData.get('street_address'),
            apartment: formData.get('apartment'),
            city: formData.get('city'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            save_info: formData.get('save_info') === 'on',
            payment_method: paymentMethod
        };

        fetch('/api/v1/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading-modal').classList.add('hidden');
            
            if (data.success) {
                showNotification('Order placed successfully!', 'success');
                // Redirect to order confirmation or orders page
                setTimeout(() => {
                    window.location.href = '/orders';
                }, 2000);
            } else {
                showNotification(data.message || 'Failed to place order', 'error');
            }
        })
        .catch(error => {
            document.getElementById('loading-modal').classList.add('hidden');
            console.error('Error placing order:', error);
            showNotification('An error occurred while placing your order', 'error');
        });
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-20 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-gray-600'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
</script>
@endsection