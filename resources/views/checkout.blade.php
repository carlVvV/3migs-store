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
                    
                    <!-- Saved Addresses -->
                    <div id="saved-addresses" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select saved address</label>
                        <div class="flex items-center space-x-2">
                            <select id="address-select" class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"></select>
                            <button id="new-address-btn" type="button" class="px-3 py-2 text-sm border rounded-md">Add new</button>
                        </div>
                    </div>

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
                        
                        <!-- Region -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Region <span class="text-red-500">*</span>
                            </label>
                            <select name="region" id="region" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                                <option value="">Select Region</option>
                            </select>
                            <div id="region_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Province -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Province <span class="text-red-500">*</span>
                            </label>
                            <select name="province" id="province" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                    disabled>
                                <option value="">Select Province</option>
                            </select>
                            <div id="province_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- City/Municipality -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                City/Municipality <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="city" id="city" required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        disabled>
                                    <option value="">Select City/Municipality</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">

                                </div>
                            </div>
                            <div id="city_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Barangay -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Barangay <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="barangay" id="barangay" required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        disabled>
                                    <option value="">Select Barangay</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">

                                </div>
                            </div>
                            <div id="barangay_error" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        
                        <!-- Postal Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="postal_code" id="postal_code" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                   placeholder="Enter postal code">
                            <div id="postal_code_error" class="text-red-500 text-xs mt-1 hidden"></div>
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
                                Save this information to my account
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
                        <!-- Custom Order Pricing Breakdown -->
                        <div id="custom-pricing-breakdown" class="hidden space-y-1 text-sm border-b border-gray-200 pb-3">
                            <div class="flex justify-between">
                                <span>Fabric Cost:</span>
                                <span id="breakdown-fabric-cost">₱0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Labor Cost:</span>
                                <span>₱1,500.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Embroidery:</span>
                                <span id="breakdown-embroidery-cost">₱0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Yardage:</span>
                                <span id="breakdown-yardage-cost">₱0.00</span>
                            </div>
                            <div class="flex justify-between border-t pt-1">
                                <span>Per Unit:</span>
                                <span id="breakdown-per-unit">₱0.00</span>
                            </div>
                        </div>
                        
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
                                <span class="text-sm font-medium text-gray-700">Online Payment</span>
                                <div class="ml-4 flex space-x-2">
                                </div>
                            </label>
                        </div>
                        
                        <!-- Cash on Delivery Option -->
                        <div class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50" id="cod-option">
                            <input type="radio" name="payment_method" id="cod" value="cod" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <label for="cod" class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Cash on delivery</span>
                            </label>
                        </div>
                        
                        <!-- Custom Barong Notice -->
                        <div id="custom-barong-notice" class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mt-3" style="display: none;">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                                <span class="text-sm text-yellow-800">
                                    Custom barong orders require online payment only. Cash on delivery is not available for custom designs.
                                </span>
                            </div>
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
    
    // Load saved addresses and setup form
    loadSavedAddresses();
    // Restore draft after attempting to load saved addresses
    restoreCheckoutDraft();
    
    // Persist form as draft on change
    attachDraftPersistence();

    // Form validation
    setupFormValidation();
    async function loadSavedAddresses() {
        try {
            const res = await fetch('/api/v1/addresses', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (!res.ok || !data.success || !Array.isArray(data.data) || data.data.length === 0) {
                return; // keep hidden
            }
            
            // Store addresses in sessionStorage for later use
            sessionStorage.setItem('savedAddresses', JSON.stringify(data.data));
            
            const wrap = document.getElementById('saved-addresses');
            const sel = document.getElementById('address-select');
            wrap.classList.remove('hidden');
            // Populate select
            sel.innerHTML = '';
            data.data.forEach(addr => {
                const opt = document.createElement('option');
                opt.value = String(addr.id);
                opt.textContent = `${addr.label ? '['+addr.label+'] ' : ''}${addr.full_name}, ${addr.street_address}, ${addr.city}`;
                if (addr.is_default) opt.selected = true;
                sel.appendChild(opt);
            });
            // Apply default to form
            applyAddressToForm(data.data.find(a => a.is_default) || data.data[0]);
            sel.addEventListener('change', () => {
                const addr = data.data.find(a => String(a.id) === sel.value);
                if (addr) applyAddressToForm(addr);
            });
            document.getElementById('new-address-btn').addEventListener('click', () => {
                // Clear form for new address input
                ['full_name','company_name','street_address','apartment','city','province','region','barangay','postal_code','phone','email'].forEach(id => {
                    const el = document.getElementById(id); if (el) el.value = '';
                });
                // Clear search fields (removed - using dropdowns now)
                // Reset dropdowns
                document.getElementById('province').disabled = true;
                document.getElementById('city').disabled = true;
                document.getElementById('barangay').disabled = true;
                document.getElementById('province').innerHTML = '<option value="">Select Province</option>';
                document.getElementById('city').innerHTML = '<option value="">Select City/Municipality</option>';
                document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
                document.getElementById('full_name').focus();
            });
        } catch (_) { /* ignore */ }
    }

    function applyAddressToForm(addr) {
        if (!addr) return;
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
        set('full_name', addr.full_name);
        set('company_name', addr.company_name);
        set('street_address', addr.street_address);
        set('apartment', addr.apartment);
        set('city', addr.city);
        set('province', addr.province);
        set('region', addr.region);
        set('barangay', addr.barangay);
        set('postal_code', addr.postal_code);
        set('phone', addr.phone);
        set('email', addr.email);
        
        // Handle dropdown dependencies
        if (addr.region) {
            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');
            
            // Trigger region change to populate provinces
            regionSelect.dispatchEvent(new Event('change'));
            
            // Wait for provinces to load, then set province
            setTimeout(() => {
                set('province', addr.province);
                provinceSelect.dispatchEvent(new Event('change'));
                
                // Wait for cities to load, then set city
                setTimeout(() => {
                    set('city', addr.city);
                    citySelect.dispatchEvent(new Event('change'));
                    
                    // Wait for barangays to load, then set barangay
                    setTimeout(() => {
                        set('barangay', addr.barangay);
                    }, 100);
                }, 100);
            }, 100);
        }
    }

    function attachDraftPersistence() {
        const ids = ['full_name','company_name','street_address','apartment','city','province','region','barangay','postal_code','phone','email'];
        const save = () => {
            const draft = {};
            ids.forEach(id => { const el = document.getElementById(id); if (el) draft[id] = el.value || ''; });
            try { localStorage.setItem('checkoutDraft', JSON.stringify(draft)); } catch(_) {}
        };
        ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', save);
                el.addEventListener('change', save);
            }
        });
    }

    function restoreCheckoutDraft() {
        try {
            const raw = localStorage.getItem('checkoutDraft');
            if (!raw) return;
            const draft = JSON.parse(raw);
            const ids = ['full_name','company_name','street_address','apartment','city','province','region','barangay','postal_code','phone','email'];
            // Only fill empty fields to avoid overriding a selected saved address
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.value && draft[id] !== undefined) {
                    el.value = draft[id];
                }
            });
            
            // Handle dropdown dependencies for draft restoration
            if (draft.region && !document.getElementById('region').value) {
                const regionSelect = document.getElementById('region');
                regionSelect.value = draft.region;
                regionSelect.dispatchEvent(new Event('change'));
                
                setTimeout(() => {
                    if (draft.province && !document.getElementById('province').value) {
                        const provinceSelect = document.getElementById('province');
                        provinceSelect.value = draft.province;
                        provinceSelect.dispatchEvent(new Event('change'));
                        
                        setTimeout(() => {
                            if (draft.city && !document.getElementById('city').value) {
                                const citySelect = document.getElementById('city');
                                citySelect.value = draft.city;
                                citySelect.dispatchEvent(new Event('change'));
                                
                                setTimeout(() => {
                                    if (draft.barangay && !document.getElementById('barangay').value) {
                                        document.getElementById('barangay').value = draft.barangay;
                                    }
                                }, 100);
                            }
                        }, 100);
                    }
                }, 100);
            }
        } catch(_) {}
    }
    
    // Event listeners
    document.getElementById('apply-coupon-btn').addEventListener('click', applyCoupon);
    document.getElementById('place-order-btn').addEventListener('click', placeOrder);

    function loadOrderSummary() {
        console.log('Loading checkout order summary...');
        
        // Check if we have a custom design order ID from session storage
        const customDesignOrderId = sessionStorage.getItem('customDesignOrderId');
        
        if (customDesignOrderId) {
            console.log('Custom design order ID found:', customDesignOrderId);
            console.log('Validating custom design order...');
            loadCustomDesignOrder(customDesignOrderId);
        } else {
            console.log('No custom design order ID, loading regular cart');
            console.log('Validating regular cart items...');
            loadRegularCart();
        }
    }

    function loadCustomDesignOrder(orderId) {
        console.log('Loading custom design order:', orderId);
        
        // Validate order ID
        if (!orderId || orderId === 'null' || orderId === 'undefined') {
            console.error('Invalid custom design order ID:', orderId);
            loadRegularCart();
            return;
        }
        
        fetch(`/api/v1/custom-design-orders/${orderId}`)
            .then(response => {
                console.log('Custom design order API response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Custom design order response:', data);
                document.getElementById('checkout-loading').classList.add('hidden');
                
                if (data.success && data.data) {
                    const order = data.data;
                    console.log('Custom design order validated successfully:', order);
                    
                    // Validate pricing data
                    if (!order.pricing || !order.pricing.total) {
                        console.warn('Custom design order missing pricing data, using fallback');
                        order.pricing = {
                            fabric_cost: 0,
                            embroidery_cost: 0,
                            yardage_cost: 0,
                            total_per_unit: order.total_amount || 0,
                            total: order.total_amount || 0
                        };
                    }
                    
                    // Display custom barong item
                    displayCustomOrderItem(order);
                    updateCustomOrderTotals(order);
                    
                    // Enable custom barong features
                    enableCustomBarongMode();
                    
                    document.getElementById('checkout-content').classList.remove('hidden');
                    document.getElementById('empty-cart-message').classList.add('hidden');
                    document.getElementById('not-logged-in-message').classList.add('hidden');
                    
                    console.log('Custom design order checkout initialized successfully');
                } else {
                    console.error('Failed to load custom design order - invalid data:', data);
                    showNotification('Failed to load custom design order. Please try again.', 'error');
                    // Fallback to regular cart
                    loadRegularCart();
                }
            })
            .catch(error => {
                console.error('Error loading custom design order:', error);
                showNotification('Error loading custom design order. Please try again.', 'error');
                // Fallback to regular cart
                loadRegularCart();
            });
    }

    function loadRegularCart() {
        console.log('Loading regular cart...');
        
        fetch('/api/v1/cart')
            .then(response => response.json())
            .then(data => {
                console.log('Cart API response:', data);
                document.getElementById('checkout-loading').classList.add('hidden');
                
                if (data.success && data.items && data.items.length > 0) {
                    displayOrderItems(data.items);
                    updateOrderTotals(data);
                    checkForCustomBarong(data.items);
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
            });
    }

    function displayCustomOrderItem(order) {
        console.log('Displaying custom order item:', order);
        const container = document.getElementById('order-items');
        container.innerHTML = '';
        
        const itemDiv = document.createElement('div');
        itemDiv.className = 'flex items-center justify-between py-4 border-b border-gray-200';
        
        const pricing = order.pricing || {};
        const measurements = order.measurements || {};
        
        itemDiv.innerHTML = `
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Custom Barong</h3>
                <div class="text-sm text-gray-600 mt-1">
                    <p><strong>Fabric:</strong> ${order.fabric}</p>
                    <p><strong>Color:</strong> ${order.color}</p>
                    <p><strong>Embroidery:</strong> ${order.embroidery || 'None'}</p>
                    <p><strong>Quantity:</strong> ${order.quantity}</p>
                    <p><strong>Fabric Yardage:</strong> ${order.fabric_yardage} yards</p>
                    <div class="mt-2">
                        <p class="font-medium">Measurements:</p>
                        <p>Chest: ${measurements.chest || 0}" | Waist: ${measurements.waist || 0}" | Length: ${measurements.length || 0}"</p>
                        <p>Shoulder: ${measurements.shoulder_width || 0}" | Sleeve: ${measurements.sleeve_length || 0}"</p>
                    </div>
                    ${order.reference_image ? `<div class="mt-2"><p class="font-medium">Reference Image:</p><img src="/storage/${order.reference_image}" alt="Reference image" class="max-w-xs max-h-32 rounded-md mt-1"></div>` : ''}
                    ${order.additional_notes ? `<p class="mt-1"><strong>Notes:</strong> ${order.additional_notes}</p>` : ''}
                </div>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-gray-900">
                    ₱${(pricing.total || 0).toFixed(2)}
                </div>
                <div class="text-sm text-gray-600">
                    <div>Qty: ${order.quantity} × ₱${(pricing.total_per_unit || pricing.total || 0).toFixed(2)}</div>
                </div>
            </div>
        `;
        
        container.appendChild(itemDiv);
    }

    function updateCustomOrderTotals(order) {
        console.log('Updating custom order totals:', order);
        const pricing = order.pricing || {};
        
        // Show and populate custom pricing breakdown
        const customPricingBreakdown = document.getElementById('custom-pricing-breakdown');
        if (customPricingBreakdown) {
            customPricingBreakdown.classList.remove('hidden');
            document.getElementById('breakdown-fabric-cost').textContent = `₱${(pricing.fabric_cost || 0).toFixed(2)}`;
            document.getElementById('breakdown-embroidery-cost').textContent = `₱${(pricing.embroidery_cost || 0).toFixed(2)}`;
            document.getElementById('breakdown-yardage-cost').textContent = `₱${(pricing.yardage_cost || 0).toFixed(2)}`;
            document.getElementById('breakdown-per-unit').textContent = `₱${(pricing.total_per_unit || pricing.total || 0).toFixed(2)}`;
        }
        
        // Update main totals
        document.getElementById('order-subtotal').textContent = `₱${(pricing.total || 0).toFixed(2)}`;
        document.getElementById('order-total').textContent = `₱${(pricing.total || 0).toFixed(2)}`;
        
        // Hide shipping and coupon sections for custom orders
        const shippingSection = document.querySelector('.shipping-section');
        const couponSection = document.querySelector('.coupon-section');
        if (shippingSection) shippingSection.style.display = 'none';
        if (couponSection) couponSection.style.display = 'none';
    }

    function enableCustomBarongMode() {
        console.log('Enabling custom barong mode');
        
        // Disable COD option
        const codRadio = document.getElementById('cod');
        if (codRadio) {
            codRadio.disabled = true;
            codRadio.checked = false;
        }
        
        // Enable online payment
        const onlineRadio = document.getElementById('ewallet');
        if (onlineRadio) {
            onlineRadio.checked = true;
        }
        
        // Show custom barong notice
        const customNotice = document.getElementById('custom-barong-notice');
        if (customNotice) {
            customNotice.style.display = 'block';
        }
        
        // Update COD label to show it's disabled
        const codLabel = document.querySelector('label[for="cod"]');
        if (codLabel) {
            codLabel.innerHTML = 'Cash on Delivery <span class="text-red-500 text-sm">(Not available for custom barong)</span>';
        }
        
        // Disable the entire COD option container
        const codOption = document.getElementById('cod-option');
        if (codOption) {
            codOption.style.opacity = '0.5';
            codOption.style.pointerEvents = 'none';
        }
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
        document.getElementById('order-subtotal').textContent = `₱${(data.subtotal || 0).toFixed(2)}`;
        document.getElementById('order-total').textContent = `₱${(data.total || 0).toFixed(2)}`;
    }

    function checkForCustomBarong(items) {
        const hasCustomBarong = items.some(item => 
            item.id && item.id.startsWith('custom_') || 
            item.name && item.name.toLowerCase().includes('custom')
        );
        
        if (hasCustomBarong) {
            // Disable COD option
            const codOption = document.getElementById('cod-option');
            const codInput = document.getElementById('cod');
            const ewalletInput = document.getElementById('ewallet');
            
            codOption.style.opacity = '0.5';
            codOption.style.pointerEvents = 'none';
            codInput.disabled = true;
            
            // Auto-select online payment
            ewalletInput.checked = true;
            
            // Show notice
            document.getElementById('custom-barong-notice').style.display = 'block';
        }
    }

    function setupFormValidation() {
        const form = document.getElementById('checkout-form');
        const requiredFields = ['full_name', 'street_address', 'city', 'province', 'postal_code', 'phone', 'email'];
        
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
        
        // Postal code validation
        if (fieldName === 'postal_code' && !isValidPostalCode(value)) {
            showFieldError(field, errorDiv, 'Please enter a valid postal code');
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

    function isValidPostalCode(postalCode) {
        // Philippine postal codes are typically 4 digits
        const postalCodeRegex = /^[0-9]{4}$/;
        return postalCodeRegex.test(postalCode);
    }

    function validateForm() {
        // Check if a saved address is selected
        const addressSelect = document.getElementById('address-select');
        const savedAddressesDiv = document.getElementById('saved-addresses');
        
        if (!savedAddressesDiv.classList.contains('hidden') && addressSelect.value) {
            // Saved address is selected, it's already validated
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!paymentMethod) {
                showNotification('Please select a payment method', 'error');
                return false;
            }
            return true;
        }
        
        // Otherwise validate all required fields
        const requiredFields = ['full_name', 'street_address', 'region', 'province', 'city', 'barangay', 'postal_code', 'phone', 'email'];
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

    async function placeOrder() {
        console.log('Placing order...');
        
        // Check if we have a custom design order ID
        const customDesignOrderId = sessionStorage.getItem('customDesignOrderId');
        
        if (customDesignOrderId) {
            console.log('Processing custom design order:', customDesignOrderId);
            await processCustomDesignOrder(customDesignOrderId);
        } else {
            console.log('Processing regular order');
            await processRegularOrder();
        }
    }

    async function processCustomDesignOrder(orderId) {
        console.log('Processing custom design order:', orderId);
        
        // Validate billing information
        const billingData = validateBillingForm();
        if (!billingData) {
            return;
        }

        // Get payment method
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        console.log('Payment method:', paymentMethod);

        if (paymentMethod === 'ewallet') {
            // Process online payment for custom design order
            await processCustomDesignOnlinePayment(orderId, billingData);
        } else {
            showNotification('Please select online payment for custom barong orders', 'error');
        }
    }

    async function processCustomDesignOnlinePayment(orderId, billingData) {
        console.log('Processing custom design online payment:', orderId, billingData);
        
        try {
            // Update the custom design order with billing information
            const updateResponse = await fetch(`/api/v1/custom-design-orders/${orderId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    billing_address: billingData,
                    payment_method: 'ewallet'
                })
            });

            const updateData = await updateResponse.json();
            console.log('Custom design order update response:', updateData);

            if (updateData.success) {
                // Generate Bux checkout URL for custom design order
                const buxResponse = await fetch(`/api/v1/custom-design-orders/${orderId}/bux-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        order_type: 'custom_design',
                        total_amount: updateData.data.order.total_amount,
                        billing_address: billingData
                    })
                });

                const buxData = await buxResponse.json();
                console.log('Bux checkout response:', buxData);

                if (buxData.success && buxData.checkout_url) {
                    // Clear session storage
                    sessionStorage.removeItem('customDesignOrderId');
                    sessionStorage.removeItem('customDesignData');
                    
                    // Redirect to Bux checkout
                    window.location.href = buxData.checkout_url;
                } else {
                    showNotification(buxData.message || 'Failed to create payment checkout', 'error');
                }
            } else {
                showNotification(updateData.message || 'Failed to update order', 'error');
            }
        } catch (error) {
            console.error('Error processing custom design online payment:', error);
            showNotification('Error processing payment', 'error');
        }
    }

    async function processRegularOrder() {
        console.log('Processing regular order');
        
        if (!validateForm()) {
            return;
        }
        
        // Check if saved address is selected
        const addressSelect = document.getElementById('address-select');
        const savedAddressesDiv = document.getElementById('saved-addresses');
        let orderData;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
        
        if (!savedAddressesDiv.classList.contains('hidden') && addressSelect.value) {
            // Get saved address data from the stored addresses array
            const savedAddresses = JSON.parse(sessionStorage.getItem('savedAddresses') || '[]');
            const selectedAddress = savedAddresses.find(addr => String(addr.id) === addressSelect.value);
            
            if (selectedAddress) {
                orderData = {
                    full_name: selectedAddress.full_name,
                    company_name: selectedAddress.company_name || '',
                    street_address: selectedAddress.street_address,
                    apartment: selectedAddress.apartment || '',
                    city: selectedAddress.city,
                    province: selectedAddress.province,
                    region: selectedAddress.region,
                    barangay: selectedAddress.barangay || '',
                    postal_code: selectedAddress.postal_code,
                    phone: selectedAddress.phone,
                    email: selectedAddress.email,
                    save_info: document.getElementById('save_info')?.checked || false,
                    payment_method: paymentMethod
                };
            } else {
                // Fallback to form data if address not found in session storage
                console.warn('Selected address not found in session, falling back to form data');
                const formData = new FormData(document.getElementById('checkout-form'));
                orderData = {
                    full_name: formData.get('full_name'),
                    company_name: formData.get('company_name'),
                    street_address: formData.get('street_address'),
                    apartment: formData.get('apartment'),
                    city: formData.get('city'),
                    province: formData.get('province'),
                    region: formData.get('region'),
                    barangay: formData.get('barangay'),
                    postal_code: formData.get('postal_code'),
                phone: formData.get('phone'),
                email: formData.get('email'),
                save_info: formData.get('save_info') === 'on',
                payment_method: paymentMethod
                };
            }
        } else {
            // Get form data for manual entry
            const formData = new FormData(document.getElementById('checkout-form'));
            orderData = {
                full_name: formData.get('full_name'),
                company_name: formData.get('company_name'),
                street_address: formData.get('street_address'),
                apartment: formData.get('apartment'),
                city: formData.get('city'),
                province: formData.get('province'),
                region: formData.get('region'),
                barangay: formData.get('barangay'),
                postal_code: formData.get('postal_code'),
                phone: formData.get('phone'),
                email: formData.get('email'),
                save_info: formData.get('save_info') === 'on',
                payment_method: paymentMethod
            };
        }

        // Fast-path: if Online Payment selected, create the order and open Bux.ph directly
        if (paymentMethod === 'ewallet') {
            try {
                const createRes = await fetch('/api/v1/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(orderData)
                });
                const created = await createRes.json().catch(() => ({}));
                if (!createRes.ok || !created.success) {
                    showNotification(created.message || 'Failed to place order for online payment.', 'error');
                    return;
                }
                const order = created.data?.order || created.data;
                const buxRes = await fetch(`/api/v1/orders/${order.id}/bux-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const bux = await buxRes.json().catch(() => ({}));
                const redirectUrl = bux?.data?.checkout_url || bux?.data?.redirect_url || bux?.data?.url;
                if (buxRes.ok && bux.success && redirectUrl) {
                    window.location.href = redirectUrl;
                    return;
                }
                showNotification(bux.message || 'Payment service unavailable. Please try again later.', 'error');
                return;
            } catch (err) {
                console.error('Online payment error:', err);
                showNotification('Unable to start online payment. Please try again.', 'error');
                return;
            }
        }

        // Default: COD goes through processing page (creates order then redirects to orders)
        try {
            sessionStorage.setItem('checkoutOrderData', JSON.stringify(orderData));
            window.location.href = '/processing-order';
        } catch (e) {
            console.error('Failed to start processing:', e);
            showNotification('Unable to start checkout. Please try again.', 'error');
        }
    }

    function validateBillingForm() {
        const formData = new FormData(document.getElementById('checkout-form'));
        
        const billingData = {
            full_name: formData.get('full_name'),
            company_name: formData.get('company_name'),
            street_address: formData.get('street_address'),
            apartment: formData.get('apartment'),
            city: formData.get('city'),
            province: formData.get('province'),
            region: formData.get('region'),
            barangay: formData.get('barangay'),
            postal_code: formData.get('postal_code'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            save_info: formData.get('save_info') === 'on'
        };

        // Basic validation
        if (!billingData.full_name || !billingData.street_address || !billingData.city || 
            !billingData.province || !billingData.region || !billingData.barangay || !billingData.postal_code || !billingData.phone || !billingData.email) {
            showNotification('Please fill in all required fields including Region and Barangay', 'error');
            return null;
        }

        return billingData;
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

    // PSGC-based Philippine Address Management
    let psgcData = {
        regions: [],
        provinces: [],
        cities: [],
        barangays: [],
        selectedRegion: null,
        selectedProvince: null,
        selectedCity: null,
        selectedBarangay: null
    };

    let searchTimeout = null;

    function initializePSGCAddressForm() {
        loadRegions();
        setupEventListeners();
    }

    async function loadRegions() {
        try {
            const response = await fetch('/api/v1/psgc/regions');
            const data = await response.json();
            
            if (data.success) {
                psgcData.regions = data.data;
                populateRegionDropdown();
            }
        } catch (error) {
            console.error('Failed to load regions:', error);
        }
    }

    function populateRegionDropdown() {
        const regionSelect = document.getElementById('region');
        regionSelect.innerHTML = '<option value="">Select Region</option>';
        
        psgcData.regions.forEach(region => {
            const option = document.createElement('option');
            option.value = region.code;
            option.textContent = region.name;
            regionSelect.appendChild(option);
        });
    }

    async function loadProvincesByRegion(regionCode) {
        try {
            const response = await fetch(`/api/v1/psgc/regions/${regionCode}/provinces`);
            const data = await response.json();
            
            if (data.success) {
                psgcData.provinces = data.data;
                populateProvinceDropdown();
            }
        } catch (error) {
            console.error('Failed to load provinces:', error);
        }
    }

    function populateProvinceDropdown() {
        const provinceSelect = document.getElementById('province');
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        
        psgcData.provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.code;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
        });
        
        provinceSelect.disabled = false;
    }

    async function loadCitiesByProvince(provinceCode) {
        try {
            const response = await fetch(`/api/v1/psgc/provinces/${provinceCode}/cities`);
            const data = await response.json();
            
            if (data.success) {
                psgcData.cities = data.data;
                populateCityDropdown();
            }
        } catch (error) {
            console.error('Failed to load cities:', error);
        }
    }

    async function loadCitiesByRegion(regionCode) {
        try {
            const response = await fetch(`/api/v1/psgc/regions/${regionCode}/cities`);
            const data = await response.json();
            
            if (data.success) {
                psgcData.cities = data.data;
                populateCityDropdown();
            }
        } catch (error) {
            console.error('Failed to load cities:', error);
        }
    }

    function populateCityDropdown() {
        const citySelect = document.getElementById('city');
        citySelect.innerHTML = '<option value="">Select City</option>';
        
        psgcData.cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.code;
            option.textContent = city.name;
            option.dataset.cityName = city.name;
            citySelect.appendChild(option);
        });
        
        citySelect.disabled = false;
    }

    async function loadBarangaysByCity(cityCode) {
        try {
            const response = await fetch(`/api/v1/psgc/cities/${cityCode}/barangays`);
            const data = await response.json();
            
            if (data.success) {
                psgcData.barangays = data.data;
                populateBarangayDropdown();
            }
        } catch (error) {
            console.error('Failed to load barangays:', error);
        }
    }

    function populateBarangayDropdown() {
        const barangaySelect = document.getElementById('barangay');
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        
        psgcData.barangays.forEach(barangay => {
            const option = document.createElement('option');
            option.value = barangay.code;
            option.textContent = barangay.name;
            option.dataset.barangayName = barangay.name;
            barangaySelect.appendChild(option);
        });
        
        barangaySelect.disabled = false;
    }

    // Reverse lookup functions
    async function searchCityAndAutoPopulate(cityName) {
        try {
            const response = await fetch(`/api/v1/psgc/search/city?name=${encodeURIComponent(cityName)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.data) {
                const { city, province, region } = data.data;
                
                // Auto-populate higher administrative divisions
                document.getElementById('region').value = region.code;
                document.getElementById('province').value = province.code;
                document.getElementById('city').value = city.code;
                
                // Update hidden fields
                document.getElementById('city').dataset.cityName = city.name;
                
                // Load provinces and cities to ensure dropdowns are populated
                await loadProvincesByRegion(region.code);
                await loadCitiesByProvince(province.code);
                
                // Load barangays for the selected city
                await loadBarangaysByCity(city.code);
                
                // Show success message
                showNotification(`Found ${city.name}, ${province.name}, ${region.name}`, 'success');
            } else {
                showNotification('City not found. Please try a different name.', 'error');
            }
        } catch (error) {
            console.error('Failed to search city:', error);
            showNotification('Failed to search city. Please try again.', 'error');
        }
    }

    async function searchBarangayAndAutoPopulate(barangayName, cityName = null) {
        try {
            const cityParam = cityName || psgcData.selectedCity?.name;
            const url = `/api/v1/psgc/search/barangay?name=${encodeURIComponent(barangayName)}${cityParam ? `&city=${encodeURIComponent(cityParam)}` : ''}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.data) {
                const { barangay, city, province, region } = data.data;
                
                // Auto-populate all higher administrative divisions
                document.getElementById('region').value = region.code;
                document.getElementById('province').value = province.code;
                document.getElementById('city').value = city.code;
                document.getElementById('barangay').value = barangay.code;
                
                // Update hidden fields
                document.getElementById('city').dataset.cityName = city.name;
                document.getElementById('barangay').dataset.barangayName = barangay.name;
                
                // Load all data to ensure dropdowns are populated
                await loadProvincesByRegion(region.code);
                await loadCitiesByProvince(province.code);
                await loadBarangaysByCity(city.code);
                
                // Show success message
                showNotification(`Found ${barangay.name}, ${city.name}, ${province.name}, ${region.name}`, 'success');
            } else {
                showNotification('Barangay not found. Please try a different name.', 'error');
            }
        } catch (error) {
            console.error('Failed to search barangay:', error);
            showNotification('Failed to search barangay. Please try again.', 'error');
        }
    }

    function setupEventListeners() {
        // Region change handler
        document.getElementById('region').addEventListener('change', async function() {
            const regionCode = this.value;
            psgcData.selectedRegion = psgcData.regions.find(r => r.code === regionCode);
            
            // Clear dependent fields
            clearProvinceAndBelow();
            
            if (regionCode) {
                // Check if it's NCR (130000000) - load cities directly
                if (regionCode === '130000000') {
                    await loadCitiesByRegion(regionCode);
                } else {
                    // Load provinces for regular regions
                    await loadProvincesByRegion(regionCode);
                }
            }
        });

        // Province change handler
        document.getElementById('province').addEventListener('change', async function() {
            const provinceCode = this.value;
            psgcData.selectedProvince = psgcData.provinces.find(p => p.code === provinceCode);
            
            // Clear dependent fields
            clearCityAndBelow();
            
            if (provinceCode) {
                await loadCitiesByProvince(provinceCode);
            }
        });

        // City change handler
        document.getElementById('city').addEventListener('change', async function() {
            const cityCode = this.value;
            psgcData.selectedCity = psgcData.cities.find(c => c.code === cityCode);
            
            // Clear dependent fields
            clearBarangay();
            
            if (cityCode) {
                await loadBarangaysByCity(cityCode);
            }
        });

        // City search handler - removed for pure dropdown approach
        // Barangay search handler - removed for pure dropdown approach

        // Hide suggestions event listener removed - not needed for pure dropdown approach
    }

    function clearProvinceAndBelow() {
        document.getElementById('province').innerHTML = '<option value="">Select Province</option>';
        document.getElementById('province').disabled = true;
        clearCityAndBelow();
    }

    function clearCityAndBelow() {
        document.getElementById('city').innerHTML = '<option value="">Select City/Municipality</option>';
        document.getElementById('city').disabled = true;
        clearBarangay();
    }

    function clearBarangay() {
        document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
        document.getElementById('barangay').disabled = true;
    }

    // Hide suggestions function removed - not needed for pure dropdown approach

    // Initialize the PSGC address form
    initializePSGCAddressForm();
});
</script>
@endsection