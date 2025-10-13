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
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="grid grid-cols-4 gap-4 text-sm font-medium text-gray-700">
                            <div>Product</div>
                            <div>Price</div>
                            <div>Quantity</div>
                            <div>Subtotal</div>
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
                    <div class="bg-gray-50 px-6 py-4 flex justify-between">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Return To Shop
                        </a>
                        <button id="update-cart-btn" class="inline-flex items-center px-6 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Update Cart
                        </button>
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
    
    // Update cart button
    document.getElementById('update-cart-btn').addEventListener('click', updateCart);
    
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

    function displayCartItems(items) {
        const container = document.getElementById('cart-items');
        container.innerHTML = '';
        container.classList.remove('hidden');

        items.forEach(item => {
            const itemElement = createCartItemElement(item);
            container.appendChild(itemElement);
        });
        // Update totals immediately after rendering
        updateCartTotals({ items });
    }

    function createCartItemElement(item) {
        const div = document.createElement('div');
        div.className = 'p-6';
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
            <div class="grid grid-cols-4 gap-4 items-center">
                <div class="flex items-center space-x-3">
                    <button onclick="showDeleteModal(${itemId})" class="text-red-500 hover:text-red-700 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                    <img src="${itemImage}" 
                         alt="${item.name}" 
                         class="w-16 h-16 object-cover rounded-md">
                    <div>
                        <h4 class="font-medium text-gray-900">${item.name}</h4>
                        <p class="text-sm text-gray-500">${categoryName}</p>
                    </div>
                </div>
                <div class="text-gray-900 font-medium">
                    ₱${price.toFixed(2)}
                </div>
                <div>
                    <input type="number" 
                           value="${quantity}" 
                           min="1" 
                           max="10"
                           oninput="instantUpdateSubtotal(${itemId}, this.value)"
                           onchange="updateItemQuantity(${itemId}, this.value)"
                           class="w-20 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div class="text-gray-900 font-medium" id="subtotal-${itemId}">
                    ₱${subtotal.toFixed(2)}
                </div>
            </div>
        `;
        return div;
    }

    function formatCurrency(value) {
        const num = Number(value || 0);
        return '₱' + num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
                showNotification('Quantity updated successfully!', 'success');
                loadCartItems(); // Reload cart to update totals
                updateCartCount();
            } else {
                showNotification(data.message || 'Error updating quantity', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
            showNotification('Error updating quantity', 'error');
        });
    }

    function updateCart() {
        loadCartItems();
        showNotification('Cart updated successfully!', 'success');
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
                const count = data.count || 0;
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = count;
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }

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
    // expose functions for inline handlers
    window.showDeleteModal = showDeleteModal;
    window.updateItemQuantity = updateItemQuantity;
});
</script>
@endsection