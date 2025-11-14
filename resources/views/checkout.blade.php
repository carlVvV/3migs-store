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

                    <!-- Display Selected Address -->
                    <div id="selected-address-display" class="mb-4 hidden bg-gray-50 border border-gray-300 rounded-md p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-900">Selected Address</h3>
                            <button id="edit-address-btn" type="button" class="text-sm text-blue-600 hover:text-blue-800">Edit</button>
                        </div>
                        <div id="address-display-content" class="text-sm text-gray-700">
                            <!-- Address will be displayed here -->
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

                    <!-- ID Verification -->
                    <div id="id-verification-section" class="mt-6 border-t border-gray-200 pt-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">ID Verification</h3>
                        <div class="space-y-3">
                            <div id="id-status-wrapper" class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
                                <span id="id-status-badge" class="inline-flex items-center gap-2 px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700">
                                    <i class="fas fa-id-card"></i>
                                    Checking status...
                                </span>
                                <span id="id-status-message" class="text-sm text-gray-600 mt-2 sm:mt-0">
                                    Checking your ID verification status...
                                </span>
                            </div>
                            <p id="id-instructions" class="text-sm text-gray-500">
                                We partner with Veriff to securely verify your identity.
                            </p>
                            <div id="id-action-container" class="flex items-center gap-3 hidden">
                                <button id="id-start-veriff" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900 transition-colors">
                                    <i class="fas fa-id-badge mr-2"></i>
                                    Start ID Verification
                                </button>
                                <span class="text-xs text-gray-500">A secure window will open to complete the process.</span>
                            </div>
                            <div id="id-refresh-container" class="flex items-center gap-3 hidden">
                                <button id="id-refresh-status" type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-sync-alt mr-2"></i>
                                    Refresh Status
                                </button>
                            </div>
                            <div id="id-veriff-loading" class="flex items-center gap-2 text-sm text-gray-600 hidden">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-500"></div>
                                Starting verification...
                            </div>
                        </div>
                    </div>
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
                        <div id="wholesale-savings" class="flex justify-between text-sm text-green-600 hidden">
                            <span>Wholesale Savings:</span>
                            <span id="wholesale-savings-amount" class="font-medium">₱0.00</span>
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

    const ID_DOCUMENT_STORAGE_KEY = 'checkoutIdDocumentId';
    let idVerificationState = {
        status: isLoggedIn ? 'loading' : 'hidden',
        documentId: null,
        document: null,
    };

    const idVerificationSection = document.getElementById('id-verification-section');
    const idStatusBadge = document.getElementById('id-status-badge');
    const idStatusMessage = document.getElementById('id-status-message');
    const idInstructions = document.getElementById('id-instructions');
    const idActionContainer = document.getElementById('id-action-container');
    const idStartButton = document.getElementById('id-start-veriff');
    const idRefreshContainer = document.getElementById('id-refresh-container');
    const idRefreshButton = document.getElementById('id-refresh-status');
    const idVeriffLoading = document.getElementById('id-veriff-loading');

    let statusPollInterval = null;

    if (idStartButton) {
        idStartButton.addEventListener('click', startVeriff);
    }

    if (idRefreshButton) {
        idRefreshButton.addEventListener('click', refreshIdVerificationStatus);
    }
    
    if (isLoggedIn) {
        // User is logged in - show checkout content immediately
        document.getElementById('checkout-content').style.display = 'grid';
        document.getElementById('not-logged-in-message').style.display = 'none';
        document.getElementById('empty-cart-message').style.display = 'none';
        
        // Load cart data
        loadOrderSummary();
        initializeIdVerification();
    } else {
        // User is not logged in - show login message immediately
        document.getElementById('not-logged-in-message').style.display = 'flex';
        document.getElementById('checkout-content').style.display = 'none';
        document.getElementById('empty-cart-message').style.display = 'none';
        if (idVerificationSection) {
            idVerificationSection.classList.add('hidden');
        }
    }
    
    // Load saved addresses and setup form
    loadSavedAddresses();
    // Restore draft after attempting to load saved addresses
    restoreCheckoutDraft();
    
    // Setup Edit button handler
    const editBtn = document.getElementById('edit-address-btn');
    if (editBtn) {
        editBtn.addEventListener('click', () => {
            // Show form and dropdown, hide address display
            document.getElementById('checkout-form').classList.remove('hidden');
            document.getElementById('selected-address-display').classList.add('hidden');
            const savedAddressesDiv = document.getElementById('saved-addresses');
            // Show the saved addresses dropdown when editing
            if (savedAddressesDiv && savedAddressesDiv.classList.contains('hidden')) {
                savedAddressesDiv.classList.remove('hidden');
            }
        });
    }
    
    // Persist form as draft on change
    attachDraftPersistence();

    // Form validation
    setupFormValidation();

    async function initializeIdVerification() {
        if (!idVerificationSection) {
            return;
        }

        idVerificationState.status = 'loading';
        updateIdVerificationUI();

        try {
            const documents = await fetchUserIdDocuments();
            updateIdVerificationStateFromDocuments(documents);
        } catch (error) {
            console.error('Failed to load ID documents', error);
            idVerificationState = {
                status: 'error',
                documentId: null,
                document: null,
            };
            persistIdDocumentState();
            updateIdVerificationUI();
        }
    }

    async function fetchUserIdDocuments() {
        const response = await fetch('/api/v1/id-documents', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Failed to load ID documents');
        }

        const data = await response.json();
        return Array.isArray(data.data) ? data.data : [];
    }

    function updateIdVerificationStateFromDocuments(documents) {
        const docs = Array.isArray(documents) ? documents : [];
        const normalized = docs.map((doc) => ({
            ...doc,
            normalizedStatus: normalizeDocumentStatus(doc.status),
        }));

        const approved = normalized.find(doc => doc.normalizedStatus === 'approved');
        const pending = normalized.find(doc => doc.normalizedStatus === 'pending');
        const rejected = normalized.find(doc => doc.normalizedStatus === 'rejected');

        if (approved) {
            idVerificationState = {
                status: 'approved',
                documentId: approved.id,
                document: approved,
            };
        } else if (pending) {
            idVerificationState = {
                status: 'pending',
                documentId: pending.id,
                document: pending,
            };
        } else if (rejected) {
            idVerificationState = {
                status: 'rejected',
                documentId: rejected.id ?? null,
                document: rejected,
            };
        } else {
            idVerificationState = {
                status: 'none',
                documentId: null,
                document: null,
            };
        }

        persistIdDocumentState();
        updateIdVerificationUI();
    }

    function persistIdDocumentState() {
        if (idVerificationState.documentId && (idVerificationState.status === 'approved' || idVerificationState.status === 'pending')) {
            sessionStorage.setItem(ID_DOCUMENT_STORAGE_KEY, String(idVerificationState.documentId));
        } else {
            sessionStorage.removeItem(ID_DOCUMENT_STORAGE_KEY);
        }
    }

    function updateIdVerificationUI() {
        if (!idVerificationSection) {
            return;
        }

        if (idVerificationState.status === 'hidden') {
            idVerificationSection.classList.add('hidden');
            return;
        }

        idVerificationSection.classList.remove('hidden');

        const statusConfig = {
            loading: {
                label: 'Checking ID status',
                icon: 'fas fa-spinner fa-spin',
                badgeClasses: 'bg-gray-100 text-gray-700',
                message: 'Checking your ID verification status...',
                instructions: '',
                showAction: false,
            },
            approved: {
                label: 'ID Verified',
                icon: 'fas fa-check-circle',
                badgeClasses: 'bg-green-100 text-green-800',
                message: 'Your ID has been verified. You are all set for checkout.',
                instructions: 'Thank you for verifying your identity.',
                showAction: false,
            },
            pending: {
                label: 'Verification In Progress',
                icon: 'fas fa-hourglass-half',
                badgeClasses: 'bg-yellow-100 text-yellow-800',
                message: 'Your verification is being processed. We will notify you once it is completed.',
                instructions: 'You can continue shopping while we finish verifying your ID.',
                showAction: false,
            },
            rejected: {
                label: 'Verification Failed',
                icon: 'fas fa-times-circle',
                badgeClasses: 'bg-red-100 text-red-800',
                message: 'Your last verification attempt was unsuccessful. Please try again.',
                instructions: 'Click the button below to relaunch the secure verification process.',
                showAction: true,
            },
            none: {
                label: 'Verification Required',
                icon: 'fas fa-id-card',
                badgeClasses: 'bg-gray-100 text-gray-700',
                message: 'Verify your identity to unlock all checkout options.',
                instructions: 'This quick, secure check is powered by Veriff.',
                showAction: true,
            },
            error: {
                label: 'Status Unavailable',
                icon: 'fas fa-exclamation-circle',
                badgeClasses: 'bg-red-100 text-red-800',
                message: 'We could not determine your ID status. Please try again.',
                instructions: 'Click the button below to start the verification process.',
                showAction: true,
            },
        };

        const stateKey = statusConfig[idVerificationState.status] ? idVerificationState.status : 'none';
        const config = statusConfig[stateKey];

        if (idStatusBadge) {
            idStatusBadge.className = `inline-flex items-center gap-2 px-3 py-1 text-sm font-semibold rounded-full ${config.badgeClasses}`;
            idStatusBadge.innerHTML = `<i class="${config.icon}"></i>${config.label}`;
        }

        if (idStatusMessage) {
            idStatusMessage.textContent = config.message;
        }

        if (idInstructions) {
            if (config.instructions) {
                idInstructions.textContent = config.instructions;
                idInstructions.classList.remove('hidden');
            } else {
                idInstructions.classList.add('hidden');
            }
        }

        if (idActionContainer) {
            if (config.showAction) {
                idActionContainer.classList.remove('hidden');
            } else {
                idActionContainer.classList.add('hidden');
            }
        }

        // Show refresh button for pending status
        if (idRefreshContainer) {
            if (idVerificationState.status === 'pending') {
                idRefreshContainer.classList.remove('hidden');
            } else {
                idRefreshContainer.classList.add('hidden');
            }
        }

        if (idStartButton) {
            idStartButton.disabled = !config.showAction;
            idStartButton.classList.toggle('opacity-50', !config.showAction);
            idStartButton.classList.toggle('cursor-not-allowed', !config.showAction);
        }

        if (idVeriffLoading) {
            idVeriffLoading.classList.add('hidden');
        }

        // Start/stop automatic polling for pending status
        if (idVerificationState.status === 'pending') {
            startStatusPolling();
        } else {
            stopStatusPolling();
        }
    }

    function normalizeDocumentStatus(status) {
        const normalized = (status || '').toLowerCase();
        switch (normalized) {
            case 'approved':
                return 'approved';
            case 'declined':
            case 'rejected':
            case 'fail':
                return 'rejected';
            case 'resubmission_requested':
            case 'pending':
            case 'created':
            case 'submitted':
            default:
                return 'pending';
        }
    }

    async function refreshIdVerificationStatus() {
        if (idVerificationState.status === 'loading') {
            return;
        }

        if (idRefreshButton) {
            idRefreshButton.disabled = true;
            const icon = idRefreshButton.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spin');
            }
        }

        try {
            const documents = await fetchUserIdDocuments();
            updateIdVerificationStateFromDocuments(documents);
            showNotification('Status refreshed.', 'success');
        } catch (error) {
            console.error('Failed to refresh ID documents', error);
            showNotification('Failed to refresh status. Please try again.', 'error');
        } finally {
            if (idRefreshButton) {
                idRefreshButton.disabled = false;
                const icon = idRefreshButton.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-spin');
                }
            }
        }
    }

    function startStatusPolling() {
        // Clear any existing interval
        stopStatusPolling();

        // Poll every 30 seconds when status is pending
        statusPollInterval = setInterval(async () => {
            if (idVerificationState.status !== 'pending') {
                stopStatusPolling();
                return;
            }

            try {
                const documents = await fetchUserIdDocuments();
                updateIdVerificationStateFromDocuments(documents);
            } catch (error) {
                console.error('Status polling error:', error);
                // Don't show error notification for polling failures
            }
        }, 30000); // 30 seconds
    }

    function stopStatusPolling() {
        if (statusPollInterval) {
            clearInterval(statusPollInterval);
            statusPollInterval = null;
        }
    }

    async function startVeriff() {
        if (idVerificationState.status === 'loading') {
            return;
        }

        if (typeof window.createVeriffFrame !== 'function') {
            if (typeof window.notify === 'function') {
                window.notify('Verification service unavailable. Please refresh and try again.', 'error');
            } else {
                showNotification('Verification service unavailable. Please refresh and try again.', 'error');
            }
            return;
        }

        // Store previous status to restore if user cancels
        const previousStatus = idVerificationState.status;
        idVerificationState.previousStatus = previousStatus;

        if (idVeriffLoading) {
            idVeriffLoading.classList.remove('hidden');
        }
        if (idActionContainer) {
            idActionContainer.classList.add('pointer-events-none', 'opacity-50');
        }
        if (idStartButton) {
            idStartButton.disabled = true;
            idStartButton.classList.add('opacity-50', 'cursor-not-allowed');
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('/api/v1/veriff-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: JSON.stringify({}),
                credentials: 'same-origin',
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Failed to start verification session.');
            }

            const sessionUrl = payload.data?.session_url;
            const sessionId = payload.data?.session_id || null;
            const documentId = payload.data?.document_id || null;

            if (!sessionUrl) {
                throw new Error('Missing verification session URL.');
            }

            // Store session info but DON'T set status to 'pending' yet
            // Only set to 'pending' when user actually submits
            idVerificationState.documentId = documentId;
            idVerificationState.document = {
                veriff_session_id: sessionId,
                status: 'pending',
            };

            const controller = window.createVeriffFrame({
                url: sessionUrl,
                onEvent: (event) => {
                    const messages = window.VeriffMessages || {};

                    // --- Start of New Logic ---
                    
                    // 1. Handle errors (if any error event exists)
                    if (event === messages.ERROR || event === 'error') {
                        console.error("Veriff SDK Error:", event);
                        if (typeof window.notify === 'function') {
                            window.notify('An error occurred. Please try again.', 'error');
                        } else {
                            showNotification('An error occurred. Please try again.', 'error');
                        }
                        return;
                    }

                    // 2. Handle the "success" (user completed the flow)
                    if (event === messages.SUBMITTED || event === messages.FINISHED) {
                        // This is the *only* time we should show "processing/pending".
                        // The user has successfully submitted their documents.
                        idVerificationState.status = 'pending';
                        persistIdDocumentState();
                        updateIdVerificationUI();
                        if (typeof window.notify === 'function') {
                            window.notify('ID verification submitted. We will notify you when it is complete.', 'info');
                        } else {
                            showNotification('ID verification submitted. We will notify you when it is complete.', 'info');
                        }
                    }
                    // 3. Handle the "cancelled" (user closed the modal)
                    else if (event === messages.CANCELED) {
                        // The user clicked "X" or "Cancel".
                        // *Do not* change the UI state to 'processing'.
                        // Reset to previous status (before starting verification)
                        idVerificationState.status = idVerificationState.previousStatus || 'none';
                        // Clear the document info since verification was cancelled
                        idVerificationState.documentId = null;
                        idVerificationState.document = null;
                        persistIdDocumentState();
                        updateIdVerificationUI();
                        if (typeof window.notify === 'function') {
                            window.notify('ID verification was cancelled.', 'warning');
                        } else {
                            showNotification('ID verification was cancelled.', 'warning');
                        }
                    }
                    // 4. Handle other statuses
                    else if (event === messages.STARTED) {
                        if (typeof window.notify === 'function') {
                            window.notify('Verification started. Follow the instructions in the Veriff window.', 'info');
                        } else {
                            showNotification('Verification started. Follow the instructions in the Veriff window.', 'info');
                        }
                    }
                    else if (event === messages.RELOAD_REQUEST) {
                        if (typeof window.notify === 'function') {
                            window.notify('Verification requires a reload. Please try again.', 'info');
                        } else {
                            showNotification('Verification requires a reload. Please try again.', 'info');
                        }
                    }
                    // 5. Handle any other status (e.g., 'expired')
                    else {
                        console.warn("Veriff session ended with event:", event);
                        if (typeof window.notify === 'function') {
                            window.notify('Verification session ended. Please try again.', 'error');
                        } else {
                            showNotification('Verification session ended. Please try again.', 'error');
                        }
                    }

                    // --- End of New Logic ---
                },
            });
        } catch (error) {
            console.error('Failed to start Veriff session', error);
            if (typeof window.notify === 'function') {
                window.notify(error.message || 'Unable to start verification. Please try again.', 'error');
            } else {
                showNotification(error.message || 'Unable to start verification. Please try again.', 'error');
            }
        } finally {
            if (idVeriffLoading) {
                idVeriffLoading.classList.add('hidden');
            }
            if (idActionContainer) {
                idActionContainer.classList.remove('pointer-events-none', 'opacity-50');
            }
            if (idStartButton) {
                idStartButton.disabled = false;
                idStartButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    function getCurrentIdDocumentId() {
        if (idVerificationState.documentId && (idVerificationState.status === 'approved' || idVerificationState.status === 'pending')) {
            return idVerificationState.documentId;
        }

        const storedId = sessionStorage.getItem(ID_DOCUMENT_STORAGE_KEY);
        if (!storedId) {
            return null;
        }

        const parsed = parseInt(storedId, 10);
        return Number.isNaN(parsed) ? null : parsed;
    }
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
            // Apply default to form and show selected address
            const defaultAddr = data.data.find(a => a.is_default) || data.data[0];
            if (defaultAddr) {
                applyAddressToForm(defaultAddr, function(addr) {
                    displaySelectedAddress(addr);
                    // Hide form and show address display for default address
                    document.getElementById('checkout-form').classList.add('hidden');
                    document.getElementById('selected-address-display').classList.remove('hidden');
                });
            }
            
            sel.addEventListener('change', () => {
                const addr = data.data.find(a => String(a.id) === sel.value);
                if (addr) {
                    applyAddressToForm(addr, function(addr) {
                        displaySelectedAddress(addr);
                        // Hide dropdown and form, show address display
                        document.getElementById('saved-addresses').classList.add('hidden');
                        document.getElementById('checkout-form').classList.add('hidden');
                        document.getElementById('selected-address-display').classList.remove('hidden');
                    });
                }
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
                
                // Show form and hide address display when adding new address
                document.getElementById('checkout-form').classList.remove('hidden');
                document.getElementById('selected-address-display').classList.add('hidden');
            });
        } catch (_) { /* ignore */ }
    }

    async function getCityNameFromCode(code) {
        if (!code || !code.match(/^\d+$/)) return code;
        
        try {
            const res = await fetch(`/api/v1/psgc/cities/${code}`);
            const data = await res.json();
            if (data.success && data.data && data.data.name) {
                return data.data.name;
            }
        } catch (e) { /* ignore */ }
        return code;
    }
    
    async function getBarangayNameFromCode(code) {
        if (!code || !code.match(/^\d+$/)) return code;
        
        try {
            const res = await fetch(`/api/v1/psgc/barangays/${code}`);
            const data = await res.json();
            if (data.success && data.data && data.data.name) {
                return data.data.name;
            }
        } catch (e) { /* ignore */ }
        return code;
    }
    
    async function getProvinceNameFromCode(code) {
        if (!code || !code.match(/^\d+$/)) return code;
        
        try {
            // Province code is like 031400000 (9 digits)
            // To get provinces, we need to iterate through regions and find the matching province
            const res = await fetch(`/api/v1/psgc/regions`);
            const data = await res.json();
            if (data.success && data.data && Array.isArray(data.data)) {
                for (const region of data.data) {
                    const provinceRes = await fetch(`/api/v1/psgc/regions/${region.code}/provinces`);
                    const provinceData = await provinceRes.json();
                    if (provinceData.success && provinceData.data && Array.isArray(provinceData.data)) {
                        const province = provinceData.data.find(p => p.code === code);
                        if (province && province.name) {
                            return province.name;
                        }
                    }
                }
            }
        } catch (e) { /* ignore */ }
        return code;
    }
    
    async function getRegionNameFromCode(code) {
        if (!code || !code.match(/^\d+$/)) return code;
        
        try {
            const res = await fetch(`/api/v1/psgc/regions`);
            const data = await res.json();
            if (data.success && data.data && Array.isArray(data.data)) {
                const region = data.data.find(r => r.code === code);
                if (region && region.name) {
                    return region.name;
                }
            }
        } catch (e) { /* ignore */ }
        return code;
    }
    
    async function displaySelectedAddress(addr) {
        if (!addr) return;
        
        const addressDisplay = document.getElementById('selected-address-display');
        const addressContent = document.getElementById('address-display-content');
        
        // Use stored names if available, otherwise try to get from dropdown options or fetch from API
        let provinceName = addr.province;
        let cityName = addr.city;
        let regionName = addr.region || '';
        let barangayName = addr.barangay;
        
        // Check if values are codes (numeric)
        const isNumeric = (str) => str && str.match(/^\d+$/);
        
        // If city is a code, fetch the name from API
        if (isNumeric(addr.city)) {
            cityName = await getCityNameFromCode(addr.city);
        }
        
        // If barangay is a code, fetch the name from API
        if (isNumeric(addr.barangay)) {
            barangayName = await getBarangayNameFromCode(addr.barangay);
        }
        
        // If province is a code, fetch the name from API
        if (isNumeric(addr.province)) {
            provinceName = await getProvinceNameFromCode(addr.province);
        }
        
        // If region is a code, fetch the name from API
        if (isNumeric(addr.region)) {
            regionName = await getRegionNameFromCode(addr.region);
        }
        
        // Try to get names from dropdown if available
        const provinceSelect = document.getElementById('province');
        if (provinceSelect && addr.province && isNumeric(addr.province)) {
            const provinceOption = provinceSelect.querySelector(`option[value="${addr.province}"]`);
            if (provinceOption && provinceOption.textContent.trim() !== '') {
                provinceName = provinceOption.textContent;
            }
        }
        
        const regionSelect = document.getElementById('region');
        if (regionSelect && addr.region && isNumeric(addr.region)) {
            const regionOption = regionSelect.querySelector(`option[value="${addr.region}"]`);
            if (regionOption && regionOption.textContent.trim() !== '') {
                regionName = regionOption.textContent;
            }
        }
        
        // Build address string
        let addressText = `<div class="mb-2"><strong>${addr.full_name}</strong></div>`;
        addressText += `<div>${addr.street_address}</div>`;
        if (addr.apartment) {
            addressText += `<div>${addr.apartment}</div>`;
        }
        
        // Build location line - show barangay, city, province
        let locationParts = [];
        if (barangayName && !isNumeric(barangayName)) {
            locationParts.push(barangayName);
        }
        if (cityName && !isNumeric(cityName)) {
            locationParts.push(cityName);
        }
        if (provinceName && !isNumeric(provinceName)) {
            locationParts.push(provinceName);
        }
        
        const locationLine = locationParts.length > 0 ? locationParts.join(', ') : '';
        if (locationLine) {
            addressText += `<div>${locationLine}</div>`;
        }
        
        // Add region if it's not already in the location line and it's not a code
        if (regionName && !isNumeric(regionName) && !locationParts.includes(regionName)) {
            addressText += `<div>Region: ${regionName}</div>`;
        }
        
        addressText += `<div>${addr.postal_code}</div>`;
        if (addr.phone) {
            addressText += `<div class="mt-2">Phone: ${addr.phone}</div>`;
        }
        if (addr.email) {
            addressText += `<div>Email: ${addr.email}</div>`;
        }
        
        addressContent.innerHTML = addressText;
    }

    function applyAddressToForm(addr, callback) {
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
                        // Call callback after all dropdowns are populated
                        if (callback && typeof callback === 'function') {
                            callback(addr);
                        }
                    }, 100);
                }, 100);
            }, 100);
        } else if (callback && typeof callback === 'function') {
            callback(addr);
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
        
        
        // Check if we have a custom design order ID from session storage
        const customDesignOrderId = sessionStorage.getItem('customDesignOrderId');
        
        if (customDesignOrderId) {
            
            loadCustomDesignOrder(customDesignOrderId);
        } else {
            
            loadRegularCart();
        }
    }

    function loadCustomDesignOrder(orderId) {
        
        
        // Validate order ID
        if (!orderId || orderId === 'null' || orderId === 'undefined') {
            console.error('Invalid custom design order ID:', orderId);
            loadRegularCart();
            return;
        }
        
        fetch(`/api/v1/custom-design-orders/${orderId}`)
            .then(response => {
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                
                document.getElementById('checkout-loading').classList.add('hidden');
                
                if (data.success && data.data) {
                    const order = data.data;
                    
                    
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
        
        
        fetch('/api/v1/cart')
            .then(response => response.json())
            .then(data => {
                
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
            
            // Determine if wholesale pricing applies
            const isWholesale = item.is_wholesale_price || (item.price < item.current_price);
            let priceDisplay = '';
            
            if (isWholesale && item.current_price) {
                priceDisplay = `
                    <div class="text-right">
                        <div class="text-sm line-through text-gray-400">₱${(item.current_price * item.quantity).toFixed(2)}</div>
                        <div class="text-sm font-medium text-green-600">₱${(item.price * item.quantity).toFixed(2)}</div>
                        <div class="text-xs text-green-600">Wholesale</div>
                    </div>
                `;
            } else {
                priceDisplay = `
                    <div class="text-sm font-medium text-gray-900">
                        ₱${(item.price * item.quantity).toFixed(2)}
                    </div>
                `;
            }
            
            itemElement.innerHTML = `
                <img src="${item.image || '/images/placeholder.jpg'}" 
                     alt="${item.name}" 
                     class="w-12 h-12 object-cover rounded">
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">${item.name}</h4>
                    <p class="text-sm text-gray-500">Qty: ${item.quantity}</p>
                </div>
                ${priceDisplay}
            `;
            container.appendChild(itemElement);
        });
    }

    function updateOrderTotals(data) {
        document.getElementById('order-subtotal').textContent = `₱${(data.subtotal || 0).toFixed(2)}`;
        document.getElementById('order-total').textContent = `₱${(data.total || 0).toFixed(2)}`;
        
        // Display wholesale savings if applicable
        if (data.wholesale_savings && data.wholesale_savings > 0) {
            document.getElementById('wholesale-savings').classList.remove('hidden');
            document.getElementById('wholesale-savings-amount').textContent = `-₱${(data.wholesale_savings || 0).toFixed(2)}`;
        } else {
            document.getElementById('wholesale-savings').classList.add('hidden');
        }
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
        
        
        // Check if we have a custom design order ID
        const customDesignOrderId = sessionStorage.getItem('customDesignOrderId');
        
        if (customDesignOrderId) {
            
            await processCustomDesignOrder(customDesignOrderId);
        } else {
            
            await processRegularOrder();
        }
    }

    async function processCustomDesignOrder(orderId) {
        
        
        // Check if saved address is selected
        const addressSelect = document.getElementById('address-select');
        const savedAddressesDiv = document.getElementById('saved-addresses');
        const selectedAddressDisplay = document.getElementById('selected-address-display');
        let billingData = null;
        
        // Check if a saved address is being used (either dropdown is visible and has selection, or address display is visible)
        const isSavedAddressSelected = (addressSelect && addressSelect.value && 
            (!savedAddressesDiv.classList.contains('hidden') || 
             (selectedAddressDisplay && !selectedAddressDisplay.classList.contains('hidden'))));
        
        if (isSavedAddressSelected) {
            // Get saved address data from sessionStorage
            const savedAddresses = JSON.parse(sessionStorage.getItem('savedAddresses') || '[]');
            // Use address select value if available, otherwise use the first/default address
            const addressId = addressSelect ? addressSelect.value : null;
            const selectedAddress = addressId ? 
                savedAddresses.find(addr => String(addr.id) === addressId) :
                (savedAddresses.find(a => a.is_default) || savedAddresses[0]);
            
            if (selectedAddress) {
                // Use saved address data
                billingData = {
                    full_name: selectedAddress.full_name || '',
                    street_address: selectedAddress.street_address || '',
                    city: selectedAddress.city || '',
                    province: selectedAddress.province || '',
                    postal_code: selectedAddress.postal_code || '',
                    phone: selectedAddress.phone || '',
                    email: selectedAddress.email || ''
                };
                
                // Add optional fields if they exist
                if (selectedAddress.company_name) billingData.company_name = selectedAddress.company_name;
                if (selectedAddress.apartment) billingData.apartment = selectedAddress.apartment;
                
                // Validate required fields from saved address
                if (!billingData.full_name || !billingData.street_address || !billingData.city || 
                    !billingData.province || !billingData.postal_code || !billingData.phone || !billingData.email) {
                    showNotification('Selected address is missing required information. Please edit or select another address.', 'error');
                    return;
                }
            }
        }
        
        // If no saved address or saved address not found, validate form fields
        if (!billingData) {
            billingData = validateBillingForm();
            if (!billingData) {
                return;
            }
        }

        // Get payment method
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        

        if (paymentMethod === 'ewallet') {
            // Process online payment for custom design order
            await processCustomDesignOnlinePayment(orderId, billingData);
        } else {
            showNotification('Please select online payment for custom barong orders', 'error');
        }
    }

    async function processCustomDesignOnlinePayment(orderId, billingData) {
        
        
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
            
            // Check if validation failed
            if (updateResponse.status === 422 || !updateData.success) {
                let errorMessage = 'Validation failed';
                if (updateData.errors) {
                    // Format validation errors
                    const errorMessages = Object.values(updateData.errors).flat();
                    errorMessage = errorMessages.join(', ');
                } else if (updateData.message) {
                    errorMessage = updateData.message;
                }
                showNotification(errorMessage, 'error');
                return;
            }

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
                
                // Get text content from select elements instead of values
                const getSelectText = (id) => {
                    const select = document.getElementById(id);
                    if (!select) return '';
                    const option = select.options[select.selectedIndex];
                    return option ? option.text : '';
                };
                
                orderData = {
                    full_name: formData.get('full_name'),
                    company_name: formData.get('company_name'),
                    street_address: formData.get('street_address'),
                    apartment: formData.get('apartment'),
                    city: getSelectText('city') || formData.get('city'),
                    province: getSelectText('province') || formData.get('province'),
                    region: getSelectText('region') || formData.get('region'),
                    barangay: getSelectText('barangay') || formData.get('barangay'),
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
            
            // Get text content from select elements instead of values
            const getSelectText = (id) => {
                const select = document.getElementById(id);
                if (!select) return '';
                const option = select.options[select.selectedIndex];
                return option ? option.text : '';
            };
            
            orderData = {
                full_name: formData.get('full_name'),
                company_name: formData.get('company_name'),
                street_address: formData.get('street_address'),
                apartment: formData.get('apartment'),
                city: getSelectText('city') || formData.get('city'),
                province: getSelectText('province') || formData.get('province'),
                region: getSelectText('region') || formData.get('region'),
                barangay: getSelectText('barangay') || formData.get('barangay'),
                postal_code: formData.get('postal_code'),
                phone: formData.get('phone'),
                email: formData.get('email'),
                save_info: formData.get('save_info') === 'on',
                payment_method: paymentMethod
            };
        }

        const currentIdDocumentId = getCurrentIdDocumentId();
        if (currentIdDocumentId) {
            orderData.id_document_id = currentIdDocumentId;
        }

        if (paymentMethod === 'cod') {
            if (idVerificationState.status === 'loading') {
                showNotification('We are still checking your ID verification status. Please wait a moment and try again.', 'info');
                return;
            }

            if (idVerificationState.status !== 'approved') {
                showNotification('Cash on delivery requires a verified ID. Please upload a valid ID and wait for approval, or choose Online Payment.', 'error');
                return;
            }
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
        
        // Extract and clean form data
        const full_name = formData.get('full_name')?.trim();
        const street_address = formData.get('street_address')?.trim();
        const city = formData.get('city')?.trim();
        const province = formData.get('province')?.trim();
        const postal_code = formData.get('postal_code')?.trim();
        const phone = formData.get('phone')?.trim();
        const email = formData.get('email')?.trim();

        // Basic validation for required fields
        if (!full_name || !street_address || !city || !province || !postal_code || !phone || !email) {
            showNotification('Please fill in all required fields (Full Name, Street Address, City, Province, Postal Code, Phone, Email)', 'error');
            return null;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showNotification('Please enter a valid email address', 'error');
            return null;
        }

        // Build billing data object matching backend expectations
        const billingData = {
            full_name: full_name,
            street_address: street_address,
            city: city,
            province: province,
            postal_code: postal_code,
            phone: phone,
            email: email
        };

        // Add optional fields only if they have values
        const company_name = formData.get('company_name')?.trim();
        const apartment = formData.get('apartment')?.trim();
        if (company_name) billingData.company_name = company_name;
        if (apartment) billingData.apartment = apartment;

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

    // Cleanup polling on page unload
    window.addEventListener('beforeunload', () => {
        stopStatusPolling();
    });
});
</script>
@endsection