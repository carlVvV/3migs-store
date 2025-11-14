@extends('layouts.app')

@section('title', 'Shopping Cart - 3Migs Gowns & Barong')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-100 py-2">
    <div class="container mx-auto px-4">
        <nav class="text-sm">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-gray-900 font-medium">Cart</span>
        </nav>
    </div>
</div>

<!-- Main Cart Content -->
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Cart</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Cart Table Header -->
                    <div class="bg-gray-50 border-b border-gray-200">
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-4 gap-4 text-sm font-medium text-gray-700">
                                <div>Product</div>
                                <div>Price</div>
                                <div>Quantity</div>
                                <div>Subtotal</div>
                            </div>
                        </div>
                        <div class="px-6 pb-4 border-t border-gray-200">
                            <div class="flex items-center space-x-2 justify-end">
                                <button id="sort-by-size-btn" onclick="toggleSortBySize()" class="text-xs px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-sort-amount-down mr-1"></i>Sort by Size
                                </button>
                                <button id="group-by-size-btn" onclick="toggleGroupBySize()" class="text-xs px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-layer-group mr-1"></i>Group by Size
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cart Items -->
                    <div id="cart-items" class="divide-y divide-gray-200">
                        <!-- Loading State -->
                        <div id="cart-loading" class="p-8 text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600"></div>
                            <p class="text-gray-600 mt-4">Loading your cart...</p>
                        </div>
                        
                        <!-- Cart Items will be loaded here dynamically -->
                    </div>
                    
                    <!-- Empty State - Centered in the entire cart area -->
                    <div id="cart-empty" class="hidden">
                        <div class="flex flex-col items-center justify-center py-16 px-8 min-h-[400px]">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-800 mb-3">Your cart is empty</h3>
                            <p class="text-gray-600 mb-8 text-center max-w-md">Start adding items to your cart</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center px-8 py-3 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                    
                    <!-- Cart Actions -->
                    <div class="bg-gray-50 px-6 py-4">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Return To Shop
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Cart Total -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cart Total</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="cart-subtotal" class="font-medium">₱0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="text-green-600 font-medium">Free</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                            <span>Total:</span>
                            <span id="cart-total" class="text-red-600">₱0.00</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('checkout') }}" class="w-full bg-red-600 text-white font-medium py-3 px-4 rounded-md hover:bg-red-700 transition-colors text-center block mb-4">
                        Proceed to checkout
                    </a>
                    
                    <!-- Coupon Code -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Coupon Code</h4>
                        <div class="flex space-x-2">
                            <input type="text" id="coupon-code" placeholder="Enter coupon code" 
                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <button id="apply-coupon-btn" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors text-sm">
                                Apply Coupon
                            </button>
                        </div>
                        <div id="coupon-message" class="mt-2 text-sm hidden"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal (Updated Design) -->
<div id="delete-confirmation-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full mx-4 p-4 max-w-xs">
        <!-- Header -->
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-2">
                <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900">Warning</h3>
        </div>
        
        <!-- Content -->
        <p class="text-gray-600 mb-4 text-sm">Do you want to delete this product?</p>
        
        <!-- Actions -->
        <div class="flex space-x-2">
            <button id="confirm-delete" class="flex-1 bg-red-600 text-white py-2 px-3 rounded-md hover:bg-red-700 transition-colors font-medium text-sm">
                Yes
            </button>
            <button id="cancel-delete" class="flex-1 bg-gray-200 text-gray-800 py-2 px-3 rounded-md hover:bg-gray-300 transition-colors font-medium text-sm">
                No
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteItemId = null;
    
    // Load cart items
    loadCartItems();
    
    // Setup event listeners for quantity inputs
    setupQuantityInputListeners();
    
    // Apply coupon button
    document.getElementById('apply-coupon-btn').addEventListener('click', applyCoupon);
    
    // Delete modal handlers
    document.getElementById('confirm-delete').addEventListener('click', confirmDelete);
    document.getElementById('cancel-delete').addEventListener('click', cancelDelete);
    
    // Close modal when clicking outside
    document.getElementById('delete-confirmation-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            cancelDelete();
        }
    });

    function loadCartItems() {
        fetch('/api/v1/cart')
            .then(response => response.json())
            .then(data => {
                document.getElementById('cart-loading').classList.add('hidden');
                
                if (data.success && data.items && data.items.length > 0) {
                    displayCartItems(data.items);
                    // Recalculate totals from items to ensure correctness
                    updateCartTotals({ items: data.items });
                    document.getElementById('cart-empty').classList.add('hidden');
                    document.getElementById('cart-items').classList.remove('hidden');
                } else {
                    document.getElementById('cart-empty').classList.remove('hidden');
                    document.getElementById('cart-items').classList.add('hidden');
                    updateCartTotals({ subtotal: 0, total: 0 });
                }
            })
            .catch(error => {
                console.error('Error loading cart:', error);
                document.getElementById('cart-loading').classList.add('hidden');
                document.getElementById('cart-empty').classList.remove('hidden');
                document.getElementById('cart-items').classList.add('hidden');
                showNotification('Error loading cart', 'error');
            });
    }

    // Cart sorting and grouping state
    let cartSortMode = 'default'; // 'default', 'size', 'grouped'
    let originalCartItems = [];
    
    function displayCartItems(items) {
        originalCartItems = [...items]; // Keep a copy of original order
        const container = document.getElementById('cart-items');
        container.innerHTML = '';
        container.classList.remove('hidden');

        // Apply sorting/grouping
        const sortedItems = applySortingAndGrouping(items);
        
        sortedItems.forEach(item => {
            const itemElement = createCartItemElement(item);
            container.appendChild(itemElement);
        });
        // Update totals immediately after rendering
        updateCartTotals({ items });
    }
    
    function applySortingAndGrouping(items) {
        if (cartSortMode === 'size') {
            // Sort by size (S, M, L, XL, XXL)
            const sizeOrder = { 'S': 1, 'M': 2, 'L': 3, 'XL': 4, 'XXL': 5 };
            return [...items].sort((a, b) => {
                const aSize = a.size ? (sizeOrder[a.size] || 999) : 999;
                const bSize = b.size ? (sizeOrder[b.size] || 999) : 999;
                return aSize - bSize;
            });
        } else if (cartSortMode === 'grouped') {
            // Group by size, then by product name
            const grouped = {};
            items.forEach(item => {
                const key = item.size || 'No Size';
                if (!grouped[key]) grouped[key] = [];
                grouped[key].push(item);
            });
            
            // Sort groups by size order
            const sizeOrder = { 'S': 1, 'M': 2, 'L': 3, 'XL': 4, 'XXL': 5, 'No Size': 999 };
            const sortedGroups = Object.keys(grouped).sort((a, b) => {
                return (sizeOrder[a] || 999) - (sizeOrder[b] || 999);
            });
            
            // Add group headers and flatten
            const result = [];
            sortedGroups.forEach(groupKey => {
                if (grouped[groupKey].length > 0) {
                    // Add group header if more than one item
                    if (grouped[groupKey].length > 1 || sortedGroups.length > 1) {
                        result.push({ 
                            isGroupHeader: true, 
                            size: groupKey,
                            count: grouped[groupKey].length 
                        });
                    }
                    result.push(...grouped[groupKey]);
                }
            });
            return result;
        }
        return items; // Default: original order
    }
    
    function toggleSortBySize() {
        if (cartSortMode === 'size') {
            cartSortMode = 'default';
            document.getElementById('sort-by-size-btn').innerHTML = '<i class="fas fa-sort-amount-down mr-1"></i>Sort by Size';
            document.getElementById('sort-by-size-btn').classList.remove('bg-gray-200');
        } else {
            cartSortMode = 'size';
            document.getElementById('sort-by-size-btn').innerHTML = '<i class="fas fa-check mr-1"></i>Sorted by Size';
            document.getElementById('sort-by-size-btn').classList.add('bg-gray-200');
            document.getElementById('group-by-size-btn').innerHTML = '<i class="fas fa-layer-group mr-1"></i>Group by Size';
            document.getElementById('group-by-size-btn').classList.remove('bg-gray-200');
        }
        // Reload and reapply sorting
        loadCartItems();
    }
    
    function toggleGroupBySize() {
        if (cartSortMode === 'grouped') {
            cartSortMode = 'default';
            document.getElementById('group-by-size-btn').innerHTML = '<i class="fas fa-layer-group mr-1"></i>Group by Size';
            document.getElementById('group-by-size-btn').classList.remove('bg-gray-200');
        } else {
            cartSortMode = 'grouped';
            document.getElementById('group-by-size-btn').innerHTML = '<i class="fas fa-check mr-1"></i>Grouped';
            document.getElementById('group-by-size-btn').classList.add('bg-gray-200');
            document.getElementById('sort-by-size-btn').innerHTML = '<i class="fas fa-sort-amount-down mr-1"></i>Sort by Size';
            document.getElementById('sort-by-size-btn').classList.remove('bg-gray-200');
        }
        // Reload and reapply grouping
        loadCartItems();
    }

    function createCartItemElement(item) {
        // Check if this is a group header
        if (item.isGroupHeader) {
            const div = document.createElement('div');
            div.className = 'px-6 py-3 bg-gray-100 border-t border-b border-gray-200';
            div.innerHTML = `
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-gray-900">
                        <i class="fas fa-ruler-vertical mr-2"></i>Size: ${item.size}
                    </h4>
                    <span class="text-sm text-gray-600">${item.count} item${item.count > 1 ? 's' : ''}</span>
                </div>
            `;
            return div;
        }
        const div = document.createElement('div');
        div.className = '';
        div.setAttribute('data-id', item.id);
        // Keep product_id available for removal (works for guest and auth flows)
        if (item.product_id) {
            div.setAttribute('data-product-id', item.product_id);
        }
        div.setAttribute('data-price', parseFloat(item.price));
        
        // Use the correct ID (both guest and authenticated users now use the same ID structure)
        const itemId = item.id;
        const itemImage = item.images && item.images[0] ? item.images[0] : '/images/placeholder.jpg';
        const categoryName = item.category ? (item.category.name || item.category) : 'Barong';
        
        // Parse price as float for calculations
        const price = parseFloat(item.price);
        const quantity = parseInt(item.quantity);
        const subtotal = price * quantity;
        
        div.innerHTML = `
            <div class="grid grid-cols-4 gap-4 items-start px-6 py-4">
                <div class="flex items-start space-x-3">
                    <button onclick="showDeleteModal(${itemId})" class="text-red-500 hover:text-red-700 transition-colors mt-1" title="Remove item">
                        <i class="fas fa-times"></i>
                    </button>
                    <a href="/product/${item.slug}" class="flex items-start space-x-3 hover:opacity-80 transition-opacity flex-1">
                        <img src="${itemImage}" 
                             alt="${item.name}" 
                             class="w-16 h-16 object-cover rounded-md flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-medium text-gray-900 hover:text-blue-600 transition-colors">${item.name}</h4>
                                ${item.custom_measurements && Object.keys(item.custom_measurements).some(k => item.custom_measurements[k] && item.custom_measurements[k].trim() !== '') ? `
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-ruler-combined mr-1 text-xs"></i>Custom Size
                                    </span>
                                ` : ''}
                            </div>
                            <p class="text-sm text-gray-500 mt-1">${categoryName}</p>
                            ${item.custom_measurements && Object.keys(item.custom_measurements).some(k => item.custom_measurements[k] && item.custom_measurements[k].trim() !== '') ? `
                                <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                                    ${formatCustomMeasurements(item.custom_measurements)}
                                </div>
                            ` : ''}
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                ${item.size ? `
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-black text-white" id="size-badge-${itemId}">
                                        <i class="fas fa-ruler-vertical mr-1 text-xs"></i>Size: ${item.size}
                                    </span>
                                ` : ''}
                                ${item.color ? `
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-palette mr-1 text-xs"></i>Color: ${item.color}
                                    </span>
                                ` : ''}
                                ${item.size ? `
                                    <button onclick="openSizeEditModal(${itemId}, '${item.slug || item.product_id}', '${item.size || ''}')" class="text-xs text-blue-600 hover:text-blue-800 transition-colors" title="Change size">
                                        <i class="fas fa-edit"></i> Change
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </a>
                </div>
                <div class="text-gray-900 font-medium pt-1">
                    ${formatCurrency(price)}
                </div>
                <div class="pt-1">
                    <input type="number" 
                           value="${quantity}" 
                           min="1" 
                           max="10"
                           data-item-id="${itemId}"
                           class="w-20 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent cart-quantity-input">
                </div>
                <div class="text-gray-900 font-medium pt-1" id="subtotal-${itemId}">
                    ${formatCurrency(subtotal)}
                </div>
            </div>
        `;
        return div;
    }

    function formatCurrency(value) {
        const num = Number(value || 0);
        return '₱' + num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatCustomMeasurements(measurements) {
        if (!measurements || typeof measurements !== 'object') return '';
        
        const parts = [];
        const labels = {
            shoulder: 'Shoulder',
            chest: 'Chest',
            sleeve: 'Sleeve',
            waist: 'Waist',
            notes: 'Notes'
        };
        
        Object.keys(labels).forEach(key => {
            if (measurements[key] && measurements[key].trim() !== '') {
                if (key === 'notes') {
                    parts.push(`<span class="text-gray-500 italic">${escapeHtml(measurements[key])}</span>`);
                } else {
                    parts.push(`<span>${labels[key]}: ${escapeHtml(measurements[key])}"</span>`);
                }
            }
        });
        
        return parts.length > 0 ? parts.join(' • ') : '';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Optimistic UI subtotal + total update while typing
    window.instantUpdateSubtotal = function(itemId, newQty) {
        const row = Array.from(document.querySelectorAll('#cart-items > div'))
            .find(el => el.getAttribute('data-id') == String(itemId));
        const qty = Math.max(0, parseInt(newQty || 0));
        if (!row || isNaN(qty)) return;
        const price = parseFloat(row.getAttribute('data-price')) || 0;
        const subtotal = price * qty;
        const subtotalEl = document.getElementById(`subtotal-${itemId}`);
        if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);

        // Build items array from DOM to recompute totals live
        const items = Array.from(document.querySelectorAll('#cart-items > div')).map(el => {
            return {
                id: el.getAttribute('data-id'),
                price: parseFloat(el.getAttribute('data-price')) || 0,
                quantity: parseInt(el.querySelector('input[type="number"]').value || '0') || 0
            };
        });
        updateCartTotals({ items });
    }

    function updateCartTotals(data) {
        // Prefer computing from items if provided to avoid any client/server drift
        let subtotal = 0;
        if (data && Array.isArray(data.items)) {
            subtotal = data.items.reduce((sum, item) => {
                const price = parseFloat(item.price);
                const qty = parseInt(item.quantity);
                if (!isNaN(price) && !isNaN(qty)) {
                    return sum + (price * qty);
                }
                return sum;
            }, 0);
        } else {
            subtotal = parseFloat(data && data.subtotal ? data.subtotal : 0);
        }

        const total = subtotal; // shipping free; adjust here if shipping/coupons apply
        document.getElementById('cart-subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('cart-total').textContent = formatCurrency(total);
    }

    function getDomItems() {
        return Array.from(document.querySelectorAll('#cart-items > div')).map(el => {
            return {
                id: el.getAttribute('data-id'),
                price: parseFloat(el.getAttribute('data-price')) || 0,
                quantity: parseInt(el.querySelector('input[type="number"]').value || '0') || 0
            };
        });
    }

    function updateCartCountFromDom() {
        const items = getDomItems();
        const count = items.reduce((sum, i) => sum + (i.quantity || 0), 0);
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) cartCountElement.textContent = count;
        if (items.length === 0) {
            document.getElementById('cart-items').classList.add('hidden');
            document.getElementById('cart-empty').classList.remove('hidden');
        }
    }

    function updateItemQuantity(itemId, quantity) {
        if (quantity < 1) {
            showDeleteModal(itemId);
            return;
        }
        
        // Immediately update the subtotal for this item for instant feedback
        const row = Array.from(document.querySelectorAll('#cart-items > div'))
            .find(el => el.getAttribute('data-id') == String(itemId));
        const qty = parseInt(quantity);
        if (row && !isNaN(qty)) {
            const price = parseFloat(row.getAttribute('data-price')) || 0;
            const subtotal = price * qty;
            const subtotalEl = document.getElementById(`subtotal-${itemId}`);
            if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
            
            // Update cart totals immediately
            const items = Array.from(document.querySelectorAll('#cart-items > div')).map(el => {
                return {
                    id: el.getAttribute('data-id'),
                    price: parseFloat(el.getAttribute('data-price')) || 0,
                    quantity: parseInt(el.querySelector('input[type="number"]').value || '0') || 0
                };
            });
            updateCartTotals({ items });
        }
        
        fetch('/api/v1/cart/update', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: parseInt(quantity)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload cart to get server-confirmed prices (wholesale pricing)
                loadCartItems(); 
                updateCartCount();
            } else {
                // Revert to previous state if update failed
                loadCartItems();
                showNotification(data.message || 'Error updating quantity', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
            // Revert to previous state on error
            loadCartItems();
            showNotification('Error updating quantity', 'error');
        });
    }

    function setupQuantityInputListeners() {
        // Use event delegation to handle dynamically created inputs
        document.getElementById('cart-items').addEventListener('input', function(e) {
            if (e.target.classList.contains('cart-quantity-input')) {
                const itemId = e.target.getAttribute('data-item-id');
                const quantity = e.target.value;
                if (itemId && quantity) {
                    instantUpdateSubtotal(itemId, quantity);
                }
            }
        });
        
        document.getElementById('cart-items').addEventListener('change', function(e) {
            if (e.target.classList.contains('cart-quantity-input')) {
                const itemId = e.target.getAttribute('data-item-id');
                const quantity = e.target.value;
                if (itemId && quantity) {
                    updateItemQuantity(itemId, quantity);
                }
            }
        });
    }

    function showDeleteModal(itemId) {
        deleteItemId = itemId;
        document.getElementById('delete-confirmation-modal').classList.remove('hidden');
    }

    function confirmDelete() {
        if (deleteItemId) {
            // Find the row and capture product_id before removing
            const row = Array.from(document.querySelectorAll('#cart-items > div'))
                .find(el => el.getAttribute('data-id') == String(deleteItemId));
            const productIdAttr = row ? row.getAttribute('data-product-id') : null;
            if (row) {
                row.parentNode.removeChild(row);
                updateCartTotals({ items: getDomItems() });
                updateCartCountFromDom();
            }

            // Hide modal right away
            cancelDelete();

            // Ensure numeric IDs in payload (send both to support guest/auth cases)
            const payload = {
                item_id: Number(deleteItemId),
                ...(productIdAttr ? { product_id: Number(productIdAttr) } : {})
            };
            fetch(`/api/v1/cart/remove`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Item removed from cart', 'success');
                } else {
                    showNotification(data.message || 'Error removing item', 'error');
                    // Reload authoritative state if server rejected
                    loadCartItems();
                    updateCartCount();
                }
            })
            .catch(error => {
                console.error('Error removing item:', error);
                showNotification('Error removing item', 'error');
                // Reload authoritative state on error
                loadCartItems();
                updateCartCount();
            });
        }
    }

    function cancelDelete() {
        deleteItemId = null;
        document.getElementById('delete-confirmation-modal').classList.add('hidden');
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
                loadCartItems(); // Reload to show updated totals
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

    function updateCartCount() {
        fetch('/api/v1/cart/count')
            .then(response => response.json())
            .then(data => {
                const count = (data.success && data.data) ? data.data.count : (data.count || 0);
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = count;
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }

    // Make updateCartCount globally accessible
    window.updateCartCount = updateCartCount;
    
    // Also make loadCartItems globally accessible for other pages
    window.loadCartItems = loadCartItems;

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-gray-600'
        }`;
        notification.textContent = message;
        
        // Position notification below header dynamically
        positionNotificationBelowHeader(notification, 16);
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Helper functions for size editing modal
    function showError(title, message, duration = 3000) {
        const notification = document.createElement('div');
        notification.className = 'fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg bg-red-500 text-white max-w-md';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                <div>
                    <div class="font-semibold">${title}</div>
                    ${message ? `<div class="text-sm opacity-90">${message}</div>` : ''}
                </div>
            </div>
        `;
        positionNotificationBelowHeader(notification, 16);
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), duration);
    }
    
    function showSuccess(title, message, duration = 3000) {
        const notification = document.createElement('div');
        notification.className = 'fixed right-4 z-50 px-6 py-3 rounded-md shadow-lg bg-green-500 text-white max-w-md';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-xl"></i>
                <div>
                    <div class="font-semibold">${title}</div>
                    ${message ? `<div class="text-sm opacity-90">${message}</div>` : ''}
                </div>
            </div>
        `;
        positionNotificationBelowHeader(notification, 16);
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), duration);
    }
    
    // Size editing modal
    let sizeEditModal = {
        itemId: null,
        productSlugOrId: null,
        currentSize: null,
        productData: null
    };
    let selectedNewSize = null;
    
    async function openSizeEditModal(itemId, productSlugOrId, currentSize) {
        sizeEditModal.itemId = itemId;
        sizeEditModal.productSlugOrId = productSlugOrId;
        sizeEditModal.currentSize = currentSize;
        selectedNewSize = currentSize; // Default to current size
        
        // Prevent multiple modals
        if (document.getElementById('size-edit-modal-overlay')) {
            return;
        }
        
        try {
            // Fetch product data to get available sizes and stock (use slug from cart item)
            const response = await fetch(`/api/v1/product-data/${productSlugOrId}`);
            const data = await response.json();
            
            if (!data.success || !data.product) {
                showError('Error', 'Failed to load product sizes');
                return;
            }
            
            sizeEditModal.productData = data.product;
            showSizeEditModal();
        } catch (error) {
            console.error('Error fetching product:', error);
            showError('Error', 'Unable to load product sizes');
        }
    }
    
    function showSizeEditModal() {
        const overlay = document.createElement('div');
        overlay.id = 'size-edit-modal-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        overlay.style.animation = 'fadeIn 0.3s ease-in-out';
        
        const product = sizeEditModal.productData;
        const sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        const sizeStocks = product.size_stocks || {};
        
        let sizeButtonsHtml = '';
        sizes.forEach(size => {
            const stock = parseInt(sizeStocks[size] || 0);
            const isAvailable = stock > 0;
            const isSelected = sizeEditModal.currentSize === size;
            
            sizeButtonsHtml += `
                <button 
                    class="px-4 py-2 border rounded-lg transition-colors ${isSelected ? 'bg-black text-white border-black' : 'border-gray-300 text-gray-700 hover:border-gray-400'} ${!isAvailable ? 'opacity-50 cursor-not-allowed' : ''}"
                    data-size="${size}" 
                    data-stock="${stock}"
                    ${!isAvailable ? 'disabled' : ''}
                    onclick="selectSizeInEditModal('${size}', this)"
                    title="${isAvailable ? `Stock: ${stock}` : 'Out of stock'}">
                    ${size}
                    ${isAvailable ? `<span class="text-xs ml-1">(${stock})</span>` : '<span class="text-xs ml-1 text-red-500">(0)</span>'}
                </button>
            `;
        });
        
        const modal = document.createElement('div');
        modal.id = 'size-edit-modal';
        modal.className = 'bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all';
        modal.style.animation = 'slideUp 0.3s ease-out';
        
        modal.innerHTML = `
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Change Size</h3>
                    <button id="size-edit-close-btn" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">Select a new size for this item:</p>
                    <div class="flex flex-wrap gap-2">
                        ${sizeButtonsHtml}
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button onclick="closeSizeEditModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button id="size-edit-save-btn" onclick="saveSizeChange()" class="flex-1 px-4 py-2.5 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Add CSS animations if not already present
        if (!document.getElementById('size-edit-modal-styles')) {
            const style = document.createElement('style');
            style.id = 'size-edit-modal-styles';
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideUp {
                    from { 
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to { 
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                @keyframes fadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
                @keyframes slideDown {
                    from { 
                        opacity: 1;
                        transform: translateY(0);
                    }
                    to { 
                        opacity: 0;
                        transform: translateY(20px);
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Event listeners
        setTimeout(() => {
            document.getElementById('size-edit-close-btn').addEventListener('click', closeSizeEditModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeSizeEditModal();
                }
            });
        }, 10);
        
        // Highlight current size
        setTimeout(() => {
            const currentBtn = modal.querySelector(`[data-size="${sizeEditModal.currentSize}"]`);
            if (currentBtn) {
                currentBtn.classList.add('bg-black', 'text-white', 'border-black');
                currentBtn.classList.remove('border-gray-300', 'text-gray-700');
            }
        }, 50);
    }
    
    function selectSizeInEditModal(size, element) {
        const stock = parseInt(element.getAttribute('data-stock'));
        if (stock <= 0) {
            showError('Size not available', `Size ${size} is out of stock`);
            return;
        }
        
        selectedNewSize = size;
        
        // Update button styles
        document.querySelectorAll('#size-edit-modal button[data-size]').forEach(btn => {
            btn.classList.remove('bg-black', 'text-white', 'border-black');
            btn.classList.add('border-gray-300', 'text-gray-700');
        });
        
        element.classList.remove('border-gray-300', 'text-gray-700');
        element.classList.add('bg-black', 'text-white', 'border-black');
    }
    
    async function saveSizeChange() {
        if (!selectedNewSize || selectedNewSize === sizeEditModal.currentSize) {
            closeSizeEditModal();
            return;
        }
        
        const saveBtn = document.getElementById('size-edit-save-btn');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        saveBtn.disabled = true;
        
        try {
            // Get current quantity
            const quantityInput = document.querySelector(`input[data-item-id="${sizeEditModal.itemId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            const response = await fetch('/api/v1/cart/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_id: parseInt(sizeEditModal.itemId),
                    quantity: quantity,
                    size: selectedNewSize
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccess('Size Updated', `Item size changed to ${selectedNewSize}`, 2000);
                closeSizeEditModal();
                loadCartItems(); // Reload to show updated size
                updateCartCount();
            } else {
                showError('Update Failed', data.message || 'Unable to update size. Please try again.');
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error updating size:', error);
            showError('Network Error', 'An error occurred. Please try again.');
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    }
    
    function closeSizeEditModal() {
        const overlay = document.getElementById('size-edit-modal-overlay');
        if (overlay) {
            overlay.style.animation = 'fadeOut 0.3s ease-in-out';
            const modal = document.getElementById('size-edit-modal');
            if (modal) {
                modal.style.animation = 'slideDown 0.3s ease-in-out';
            }
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 300);
        }
        
        // Reset modal state
        sizeEditModal = {
            itemId: null,
            productSlugOrId: null,
            currentSize: null,
            productData: null
        };
        selectedNewSize = null;
    }
    
    // expose functions for inline handlers
    window.showDeleteModal = showDeleteModal;
    window.updateItemQuantity = updateItemQuantity;
    window.openSizeEditModal = openSizeEditModal;
    window.selectSizeInEditModal = selectSizeInEditModal;
    window.saveSizeChange = saveSizeChange;
    window.closeSizeEditModal = closeSizeEditModal;
    window.toggleSortBySize = toggleSortBySize;
    window.toggleGroupBySize = toggleGroupBySize;
});
</script>
@endsection