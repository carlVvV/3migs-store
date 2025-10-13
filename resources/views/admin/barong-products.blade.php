@extends('layouts.admin')

@section('title', 'Barong Products - Admin Dashboard')
@section('page-title', 'Barong Products')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Barong Products</h1>
                    <p class="mt-2 text-gray-600">Manage your barong product inventory</p>
                </div>
                <a href="{{ route('admin.products.create') }}" 
                   class="bg-black hover:bg-gray-800 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Barong
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name or SKU..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <select name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
                
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200">
                        Filter
                    </button>
                    <a href="{{ route('admin.products') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($barongProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                    <th class="w-1/4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="w-1/8 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                    <th class="w-1/8 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="w-1/8 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="w-1/12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="w-1/8 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="w-1/8 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($barongProducts as $product)
                                <tr class="hover:bg-gray-50" data-product-id="{{ $product->id }}">
                                    <td class="px-6 py-4 text-sm text-gray-900 truncate">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded object-cover border border-gray-200" 
                                                     src="{{ $product->cover_image_url }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAxMkMxNi42ODYzIDEyIDE0IDE0LjY4NjMgMTQgMThDMTQgMjEuMzEzNyAxNi42ODYzIDI0IDIwIDI0QzIzLjMxMzcgMjQgMjYgMjEuMzEzNyAyNiAxOEMyNiAxNC42ODYzIDIzLjMxMzcgMTIgMjAgMTJaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xMiAyOEMxMiAyNi44OTU0IDEyLjg5NTQgMjYgMTQgMjZIMjZDMjcuMTA0NiAyNiAyOCAyNi44OTU0IDI4IDI4VjMwSDI4VjI4SDEyVjI4WiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K'">
                                            </div>
                                            <div class="ml-3 min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 truncate" title="{{ $product->name }}">{{ $product->name }}</div>
                                                <div class="text-xs text-gray-500 truncate" title="{{ $product->sku }}">{{ $product->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 truncate" title="{{ $product->brand->name }}">
                                        {{ $product->brand->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 truncate" title="{{ $product->category->name }}">
                                        {{ $product->category->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span class="font-medium">₱{{ number_format($product->current_price, 2) }}</span>
                                            @if($product->is_on_sale)
                                                <span class="text-xs text-red-600 line-through">₱{{ number_format($product->base_price, 2) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                        {{ $product->total_stock }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                            </span>
                                            @if($product->is_featured)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-6">
                                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                                               class="text-gray-900 hover:text-black transition-colors duration-200">
                                                Edit
                                            </a>
                                            <button onclick="deleteProduct({{ $product->id }})" 
                                                    class="text-red-600 hover:text-red-800 transition-colors duration-200">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $barongProducts->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No barong products</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new barong product.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.products.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Barong
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

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

// Updated deleteProduct function
function deleteProduct(id) {
    showDeleteModal('Are you sure you want to delete this barong product?', () => {
        fetch(`/admin/barong-products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row from the table instead of reloading
                const row = document.querySelector(`tr[data-product-id="${id}"]`);
                if (row) {
                    row.remove();
                }
                
                // Show beautiful success notification
                showProductDeletedNotification(data.product_name || 'Product');
            } else {
                alert('Error deleting product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting product');
        });
    });
}

// Beautiful Product Deletion Notification
function showProductDeletedNotification(productName) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    const notificationId = 'deletion-' + Date.now();
    
    notification.setAttribute('data-notification-id', notificationId);
    notification.className = 'notification-item bg-white border-l-4 shadow-lg rounded-lg p-4 max-w-sm transform transition-all duration-500 ease-out translate-x-full opacity-0 border-green-500';
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="notification-icon w-10 h-10 rounded-full flex items-center justify-center bg-green-100 animate-pulse">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="notification-title text-sm font-semibold text-gray-900">Product Deleted Successfully!</p>
                <p class="notification-message text-sm text-gray-600">"${productName}" has been permanently removed from the system.</p>
                <div class="mt-2 flex items-center text-xs text-gray-500">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Deleted at ${new Date().toLocaleTimeString()}</span>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button class="notification-close inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150" onclick="removeNotification('${notificationId}')">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Animate in with bounce effect
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
        
        // Add subtle bounce animation
        notification.style.transform = 'translateX(0) scale(1.02)';
        setTimeout(() => {
            notification.style.transform = 'translateX(0) scale(1)';
        }, 150);
    }, 10);
    
    // Auto remove after 6 seconds with fade out
    setTimeout(() => {
        removeNotification(notificationId);
    }, 6000);
}

// Enhanced notification removal with animation
function removeNotification(notificationId) {
    const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (!notification) return;
    
    // Animate out
    notification.style.transform = 'translateX(100%) scale(0.95)';
    notification.style.opacity = '0';
    
    // Remove from DOM after animation
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

// Position notification container below header
function positionNotificationBelowHeader(container, offset = 16) {
    const header = document.querySelector('header, .header, nav');
    if (header) {
        const headerRect = header.getBoundingClientRect();
        const headerHeight = headerRect.height;
        container.style.top = (headerHeight + offset) + 'px';
    } else {
        container.style.top = '80px';
    }
}

// Initialize positioning
document.addEventListener('DOMContentLoaded', function() {
    const notificationContainer = document.getElementById('notification-container');
    if (notificationContainer) {
        positionNotificationBelowHeader(notificationContainer, 16);
        
        // Recalculate position on window resize
        window.addEventListener('resize', () => {
            positionNotificationBelowHeader(notificationContainer, 16);
        });
    }
});
</script>
@endsection
