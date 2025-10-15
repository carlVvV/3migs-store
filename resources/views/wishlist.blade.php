@extends('layouts.app')

@section('title', 'My Wishlist - 3Migs Gowns & Barong')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">My Wishlist</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">My Wishlist</h1>
                <p class="text-gray-600 mt-2">Save items you love for later</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600" id="wishlist-page-count">0</div>
                    <div class="text-sm text-gray-600">Items Saved</div>
                </div>
                <button class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors" onclick="clearWishlist()" id="clear-wishlist-btn" style="display: none;">
                    <i class="fas fa-trash mr-2"></i>Clear All
                </button>
            </div>
        </div>
    </div>

    <!-- Wishlist Content -->
    <div id="wishlist-content">
        <!-- Loading State -->
        <div id="wishlist-loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600"></div>
            <p class="text-gray-600 mt-4">Loading your wishlist...</p>
        </div>

        <!-- Empty State -->
        <div id="wishlist-empty" class="text-center py-12 hidden">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-heart text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-600 mb-6">Start adding items you love to your wishlist</p>
            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Start Shopping
            </a>
        </div>

        <!-- Wishlist Items -->
        <div id="wishlist-items" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="wishlist-grid">
                <!-- Items will be loaded here dynamically -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadWishlist();
    updateWishlistCount();
});

function loadWishlist() {
    fetch('/api/v1/wishlist', { cache: 'no-store' })
        .then(response => {
            const ct = response.headers.get('content-type') || '';
            if (!response.ok) {
                // Unauthenticated or server error
                return Promise.resolve({ success: false, items: [], status: response.status });
            }
            if (!ct.includes('application/json')) {
                return Promise.resolve({ success: false, items: [] });
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('wishlist-loading').classList.add('hidden');
            
            if (data && data.items && data.items.length > 0) {
                displayWishlistItems(data.items);
                document.getElementById('wishlist-items').classList.remove('hidden');
                document.getElementById('clear-wishlist-btn').style.display = 'block';
                const pageCount = document.getElementById('wishlist-page-count');
                if (pageCount) pageCount.textContent = data.items.length;
            } else {
                document.getElementById('wishlist-empty').classList.remove('hidden');
                const pageCount = document.getElementById('wishlist-page-count');
                if (pageCount) pageCount.textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error loading wishlist:', error);
            document.getElementById('wishlist-loading').classList.add('hidden');
            document.getElementById('wishlist-empty').classList.remove('hidden');
        });
}

function displayWishlistItems(items) {
    const grid = document.getElementById('wishlist-grid');
    grid.innerHTML = '';

    items.forEach(item => {
        const itemElement = createWishlistItemElement(item);
        grid.appendChild(itemElement);
    });
}

function createWishlistItemElement(item) {
    const div = document.createElement('div');
    const isAvailable = item.is_available && item.total_stock > 0;
    const clickableClass = isAvailable ? 'cursor-pointer hover:shadow-lg' : 'cursor-default';
    
    div.className = `bg-white rounded-lg shadow-md overflow-hidden transition-shadow ${clickableClass}`;
    div.setAttribute('data-wishlist-id', item.id);
    div.setAttribute('data-product-id', item.product_id);
    div.setAttribute('data-product-slug', item.slug);
    
    // Make the entire card clickable if available
    if (isAvailable) {
        div.onclick = () => viewProduct(item.slug);
    }
    
    div.innerHTML = `
        <div class="relative">
            <img src="${item.image || '/images/placeholder.jpg'}" 
                 alt="${item.name}" 
                 class="w-full h-48 object-cover">
            <button onclick="event.stopPropagation(); removeFromWishlist(${item.id})" 
                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
            ${!isAvailable ? `
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                    <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-times mr-1"></i>Out of Stock
                    </span>
                </div>
            ` : ''}
        </div>
        <div class="p-4">
            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 ${isAvailable ? 'hover:text-blue-600' : 'text-gray-500'}">${item.name}</h3>
            <p class="text-sm text-gray-600 mb-2">${item.category || 'Barong'}</p>
            <div class="flex items-center justify-between mb-3">
                <span class="text-lg font-bold ${isAvailable ? 'text-gray-900' : 'text-gray-500'}">₱${item.current_price}</span>
                ${item.original_price && item.original_price > item.current_price ? 
                    `<span class="text-sm text-gray-500 line-through">₱${item.original_price}</span>` : ''
                }
            </div>
            <div class="flex space-x-2">
                <button onclick="event.stopPropagation(); addToCart(${item.product_id})" 
                        class="flex-1 ${isAvailable ? 'bg-gray-600 hover:bg-gray-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'} py-2 px-4 rounded-md transition-colors text-sm"
                        ${!isAvailable ? 'disabled' : ''}>
                    <i class="fas fa-shopping-cart mr-1"></i> ${isAvailable ? 'Add to Cart' : 'Out of Stock'}
                </button>
                <button onclick="event.stopPropagation(); viewProduct('${item.slug}')" 
                        class="flex-1 ${isAvailable ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed'} py-2 px-4 rounded-md transition-colors text-sm"
                        ${!isAvailable ? 'disabled' : ''}>
                    <i class="fas fa-eye mr-1"></i> View
                </button>
            </div>
            ${isAvailable ? `
                <div class="mt-2 text-xs text-green-600 font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Available (${item.total_stock} in stock)
                </div>
            ` : `
                <div class="mt-2 text-xs text-red-600 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>Currently unavailable
                </div>
            `}
        </div>
    `;
    return div;
}



function addToCart(itemId) {
    // Find the item to check availability
    const itemElement = document.querySelector(`[data-product-id="${itemId}"]`);
    if (!itemElement) {
        showNotification('Item not found', 'error');
        return;
    }
    
    // Check if item is available
    const isAvailable = !itemElement.querySelector('.bg-red-600');
    if (!isAvailable) {
        showNotification('This item is currently out of stock', 'error');
        return;
    }
    
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: itemId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Item added to cart!', 'success');
            updateCartCount();
        } else {
            showNotification(data.message || 'Error adding item to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Error adding item to cart', 'error');
    });
}

function viewProduct(slug) {
    // Check if the item is available before navigating
    const itemElement = document.querySelector(`[data-product-slug="${slug}"]`);
    if (itemElement) {
        const isAvailable = !itemElement.querySelector('.bg-red-600');
        if (!isAvailable) {
            showNotification('This item is currently out of stock', 'warning');
            return;
        }
    }
    
    window.location.href = `/product/${slug}`;
}

function updateWishlistCount() {
    fetch('/api/v1/wishlist/count')
        .then(response => response.json())
        .then(data => {
            const count = data?.data?.count ?? 0;
            const pageCount = document.getElementById('wishlist-page-count');
            if (pageCount) pageCount.textContent = count;
            
            // Update wishlist count in header if it exists
            const headerCount = document.getElementById('wishlist-count');
            if (headerCount) {
                headerCount.textContent = count;
                headerCount.classList.toggle('hidden', count === 0);
            }
        })
        .catch(error => console.error('Error updating wishlist count:', error));
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
    // Create notification element
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
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

</script>
<!-- Delete Confirmation Modal -->
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
        <p id="delete-modal-message" class="text-gray-600 mb-4 text-sm">
            Do you want to delete this product?
        </p>
        
        <!-- Actions -->
        <div class="flex space-x-2">
            <button id="delete-confirm-btn" class="flex-1 bg-red-600 text-white py-2 px-3 rounded-md hover:bg-red-700 transition-colors font-medium text-sm">
                Yes
            </button>
            <button id="delete-cancel-btn" class="flex-1 bg-gray-200 text-gray-800 py-2 px-3 rounded-md hover:bg-gray-300 transition-colors font-medium text-sm">
                No
            </button>
        </div>
    </div>
</div>

<script>
// Delete Confirmation Modal Handler
let deleteModal;
let currentDeleteCallback = null;

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('delete-confirmation-modal');
    const messageElement = document.getElementById('delete-modal-message');
    const confirmBtn = document.getElementById('delete-confirm-btn');
    const cancelBtn = document.getElementById('delete-cancel-btn');
    
    // Event listeners
    confirmBtn.addEventListener('click', () => {
        if (currentDeleteCallback) {
            currentDeleteCallback();
        }
        hideDeleteModal();
    });
    
    cancelBtn.addEventListener('click', hideDeleteModal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideDeleteModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideDeleteModal();
        }
    });
});

function showDeleteModal(message, onConfirm) {
    const modal = document.getElementById('delete-confirmation-modal');
    const messageElement = document.getElementById('delete-modal-message');
    
    messageElement.textContent = message;
    currentDeleteCallback = onConfirm;
    modal.classList.remove('hidden');
    
    // Focus the cancel button for accessibility
    document.getElementById('delete-cancel-btn').focus();
}

function hideDeleteModal() {
    const modal = document.getElementById('delete-confirmation-modal');
    modal.classList.add('hidden');
    currentDeleteCallback = null;
}

// Updated removeFromWishlist function
function removeFromWishlist(itemId) {
    showDeleteModal('Are you sure you want to remove this item from your wishlist?', () => {
        const card = document.querySelector(`[data-wishlist-id="${itemId}"]`);
        if (card) card.remove();

        fetch(`/api/v1/wishlist/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json().catch(() => ({ success: false })))
        .then(data => {
            if (!data.success) {
                // Fallback to resync state if removal failed
                loadWishlist();
            }
            updateWishlistCount();
        })
        .catch(error => {
            console.error('Error removing from wishlist:', error);
            alert('Error removing item from wishlist');
            loadWishlist();
        });
    });
}

// Updated clearWishlist function
function clearWishlist() {
    showDeleteModal('Are you sure you want to clear your entire wishlist?', () => {
        fetch('/api/v1/wishlist/clear', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadWishlist(); // Reload the wishlist
                updateWishlistCount();
            } else {
                alert('Error clearing wishlist');
            }
        })
        .catch(error => {
            console.error('Error clearing wishlist:', error);
            alert('Error clearing wishlist');
        });
    });
}
</script>

@endsection