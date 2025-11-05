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
                <button onclick="event.stopPropagation(); ${isAvailable ? `openQuickAddModal('${item.slug}', ${item.product_id})` : ''}" 
                        class="flex-1 bg-black text-white ${isAvailable ? 'hover:bg-gray-900' : 'bg-gray-300 text-gray-500 cursor-not-allowed'} py-2 px-4 rounded-md transition-colors text-sm"
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
<!-- Quick Add Modal -->
<div id="quick-add-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="quick-add-modal" class="bg-white rounded-lg shadow-2xl w-full max-w-lg transform transition-all"></div>
  </div>
<script>
// Lightweight quick-add copied from home page
const quickAddModal = { open: false, productId: null, productSlug: null, selectedSize: null, quantity: 1, product: null };

function openQuickAddModal(slug, productId) {
    if (!slug || !productId) return;
    quickAddModal.open = true; quickAddModal.productSlug = slug; quickAddModal.productId = productId; quickAddModal.selectedSize = null; quickAddModal.quantity = 1; quickAddModal.product = null;
    const overlay = document.getElementById('quick-add-overlay');
    const modal = document.getElementById('quick-add-modal');
    overlay.classList.remove('hidden'); overlay.classList.add('flex');
    modal.innerHTML = `<div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Add to Cart</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeQuickAddModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="flex items-center gap-3 text-gray-600"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
    </div>`;
    fetch(`/api/v1/product-data/${encodeURIComponent(slug)}`)
      .then(r => r.json()).then(data => {
        if (!data || !data.success || !data.product) { modal.innerHTML = `<div class='p-6'>Failed to load product.</div>`; return; }
        quickAddModal.product = data.product; renderQuickAddModalContent();
      }).catch(() => { modal.innerHTML = `<div class='p-6'>Failed to load product.</div>`; });
}

function renderQuickAddModalContent() {
    const p = quickAddModal.product; if (!p) return;
    const sizeStocks = p.size_stocks || {}; const sizes = Object.keys(sizeStocks);
    const sizeButtonsHtml = sizes.map(s => {
        const stock = parseInt(sizeStocks[s] || 0);
        const disabled = stock <= 0 ? 'opacity-50 cursor-not-allowed' : '';
        const active = quickAddModal.selectedSize === s ? 'bg-black text-white border-black' : 'border-gray-300 text-gray-700';
        return `<button class="px-3 py-2 border rounded-lg size-btn-modal ${active} ${disabled}" data-size="${s}" data-stock="${stock}" onclick="selectSizeInModal('${s}', this)" ${stock<=0?'disabled':''}>${s} <span class="text-xs ml-1">(${stock})</span></button>`;
    }).join('');
    const html = `
      <div class="p-6">
        <div class="flex gap-4">
          <img src="${p.cover_image_url || (p.images && p.images[0]) || '/images/placeholder.jpg'}" class="w-28 h-28 object-cover rounded" alt="${p.name}">
          <div class="flex-1">
            <div class="font-semibold text-gray-900">${p.name}</div>
            <div class="text-red-600 font-bold">₱${p.current_price}</div>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">Select Size <span class="text-red-500">*</span></label>
            <button type="button" onclick="openSizeChartModal()" class="text-xs text-blue-600 hover:text-blue-800 flex items-center underline">
              <i class="fas fa-ruler mr-1"></i>
              Size Guide
            </button>
          </div>
          <div class="flex flex-wrap gap-2">${sizeButtonsHtml}</div>
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
          <div class="flex items-center border border-gray-300 rounded-lg w-32">
            <button class="px-3 py-2 text-gray-600 hover:text-gray-800" onclick="decreaseModalQuantity()">-</button>
            <span id="modal-quantity" class="px-4 py-2 font-medium flex-1 text-center">${quickAddModal.quantity}</span>
            <button class="px-3 py-2 text-gray-600 hover:text-gray-800" onclick="increaseModalQuantity()">+</button>
          </div>
        </div>
        <div class="flex gap-3 pt-4 border-t border-gray-200 mt-4">
          <button class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg" onclick="closeQuickAddModal()">Cancel</button>
          <button id="wishlist-modal-add-btn" class="flex-1 px-4 py-2.5 bg-black text-white rounded-lg disabled:bg-gray-400" onclick="addToCartFromModal()" ${quickAddModal.selectedSize ? '' : 'disabled'}>
            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
          </button>
        </div>
      </div>`;
    document.getElementById('quick-add-modal').innerHTML = html;
}

function selectSizeInModal(size, el) {
    const stock = parseInt(el.getAttribute('data-stock')||'0'); if (stock<=0) return;
    quickAddModal.selectedSize = size;
    document.querySelectorAll('.size-btn-modal').forEach(b=>{ b.classList.remove('bg-black','text-white','border-black'); b.classList.add('border-gray-300','text-gray-700'); });
    el.classList.remove('border-gray-300','text-gray-700'); el.classList.add('bg-black','text-white','border-black');
    const addBtn = document.getElementById('wishlist-modal-add-btn'); if (addBtn) addBtn.disabled = false;
}
function increaseModalQuantity() {
    const max = quickAddModal.selectedSize ? parseInt(document.querySelector(`.size-btn-modal[data-size="${quickAddModal.selectedSize}"]`)?.getAttribute('data-stock')||'0') : 0;
    if (max>0 && quickAddModal.quantity<max) { quickAddModal.quantity++; document.getElementById('modal-quantity').textContent = quickAddModal.quantity; }
}
function decreaseModalQuantity() { if (quickAddModal.quantity>1) { quickAddModal.quantity--; document.getElementById('modal-quantity').textContent = quickAddModal.quantity; } }

async function addToCartFromModal() {
    if (!quickAddModal.selectedSize) { showNotification('Please select a size', 'error'); return; }
    const addBtn = document.getElementById('wishlist-modal-add-btn'); const original = addBtn.innerHTML; addBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...'; addBtn.disabled = true;
    const payload = { product_id: parseInt(quickAddModal.productId), quantity: quickAddModal.quantity, size: quickAddModal.selectedSize };
    try {
        const res = await fetch('/api/v1/cart/add', { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept':'application/json' }, body: JSON.stringify(payload) });
        if (res.status===419){ showNotification('Session expired. Refresh and try again.','error'); return; }
        if (res.status===422){ const d=await res.json().catch(()=>null); showNotification(d?.message||'Validation failed','error'); addBtn.innerHTML=original; addBtn.disabled=false; return; }
        const data = await res.json();
        if (data && data.success) { showNotification('Added to cart!','success'); updateCartCount(); closeQuickAddModal(); }
        else { showNotification(data?.message||'Failed to add to cart','error'); addBtn.innerHTML=original; addBtn.disabled=false; }
    } catch (e) { showNotification('Network error. Please try again.','error'); addBtn.innerHTML=original; addBtn.disabled=false; }
}

function closeQuickAddModal(){ const overlay = document.getElementById('quick-add-overlay'); overlay.classList.add('hidden'); overlay.classList.remove('flex'); quickAddModal.open=false; }
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

// Size Chart Modal Functions
function openSizeChartModal() {
    const modal = createOrGetSizeChartModal();
    modal.classList.remove('hidden');
}

function closeSizeChartModal() {
    const modal = document.getElementById('size-chart-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function createOrGetSizeChartModal() {
    let modal = document.getElementById('size-chart-modal');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'size-chart-modal';
    modal.className = 'fixed inset-0 z-[100] hidden';
    modal.innerHTML = `
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeSizeChartModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-xl w-full max-h-[70vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-3 py-2 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">Size Guide</h2>
                    <button onclick="closeSizeChartModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                
                <div class="p-3 space-y-3">
                    <!-- Barong Size Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1.5">Barong Sizing Guide</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-2 py-1.5 text-left text-xs font-semibold text-gray-900">Size</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Chest</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Shoulder</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Sleeve</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Length</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">S</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">36-38</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">17-18</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">24-25</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">28-29</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">M</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">38-40</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">18-19</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">25-26</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">29-30</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">L</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">40-42</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">19-20</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">26-27</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">30-31</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">XL</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">42-44</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">20-21</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">27-28</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">31-32</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">XXL</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">44-46</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">21-22</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">28-29</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">32-33</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                            <div class="mt-1.5 p-1.5 bg-blue-50 rounded text-xs">
                                <h4 class="font-semibold text-gray-900 mb-0.5"><i class="fas fa-info-circle mr-1 text-blue-600"></i>Measuring Tips:</h4>
                                <ul class="list-disc list-inside text-xs text-gray-700 space-y-0">
                                <li><strong>Chest:</strong> Measure the fullest part of your chest over a well-fitted shirt</li>
                                <li><strong>Shoulder Width:</strong> Measure from shoulder to shoulder across the back</li>
                                <li><strong>Sleeve Length:</strong> Measure from shoulder to wrist</li>
                                <li><strong>Length:</strong> Measure from shoulder to desired length</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Filipiniana Size Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1.5">Filipiniana Sizing Guide</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-2 py-1.5 text-left text-xs font-semibold text-gray-900">Size</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Bust</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Waist</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Hips</th>
                                        <th class="border border-gray-300 px-2 py-1.5 text-center text-xs font-semibold text-gray-900">Length</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">S</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">32-34</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">26-28</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">34-36</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">38-40</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">M</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">34-36</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">28-30</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">36-38</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">40-42</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">L</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">36-38</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">30-32</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">38-40</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">42-44</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">XL</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">38-40</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">32-34</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">40-42</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">44-46</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1.5 font-medium text-xs text-gray-900">XXL</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">40-42</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">34-36</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">42-44</td>
                                        <td class="border border-gray-300 px-2 py-1.5 text-center text-xs text-gray-700">46-48</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                            <div class="mt-1.5 p-1.5 bg-pink-50 rounded text-xs">
                                <h4 class="font-semibold text-gray-900 mb-0.5"><i class="fas fa-info-circle mr-1 text-pink-600"></i>Measuring Tips:</h4>
                                <ul class="list-disc list-inside text-xs text-gray-700 space-y-0">
                                <li><strong>Bust:</strong> Measure the fullest part of your bust over undergarments</li>
                                <li><strong>Waist:</strong> Measure your natural waistline (usually the narrowest part)</li>
                                <li><strong>Hips:</strong> Measure the fullest part of your hips</li>
                                <li><strong>Length:</strong> Measure from shoulder to desired length</li>
                            </ul>
                        </div>
                    </div>

                    <!-- General Tips -->
                    <div class="border-t border-gray-200 pt-1.5">
                        <h3 class="text-xs font-semibold text-gray-900 mb-1">General Sizing Tips</h3>
                        <div class="grid grid-cols-1 gap-1">
                            <div class="p-1.5 bg-yellow-50 rounded text-xs">
                                <h4 class="font-semibold text-gray-900 mb-0.5"><i class="fas fa-ruler-combined mr-1 text-yellow-600"></i>How to Measure</h4>
                                <p class="text-gray-700">Measure over clothing you'll wear underneath. Use a flexible measuring tape.</p>
                            </div>
                            <div class="p-1.5 bg-green-50 rounded text-xs">
                                <h4 class="font-semibold text-gray-900 mb-0.5"><i class="fas fa-tshirt mr-1 text-green-600"></i>Fit Preference</h4>
                                <p class="text-gray-700">If between sizes, we recommend sizing up for a more comfortable fit.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-3 py-1.5 flex justify-end">
                    <button onclick="closeSizeChartModal()" class="px-3 py-1 text-xs bg-gray-800 text-white rounded hover:bg-gray-900 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('bg-black')) {
            closeSizeChartModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeSizeChartModal();
        }
    });

    return modal;
}
</script>

@endsection