@extends('layouts.admin')

@section('title', 'Inventory - Admin Dashboard')
@section('page-title', 'Inventory')

@section('content')
<div class="space-y-6">
    <!-- Inventory Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $products->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Low Stock</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $lowStockProducts->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Out of Stock</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $outOfStockProducts->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($lowStockProducts->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Low Stock Alert</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>{{ $lowStockProducts->count() }} product(s) are running low on stock.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Out of Stock Alert -->
    @if($outOfStockProducts->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Out of Stock Alert</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>{{ $outOfStockProducts->count() }} product(s) are out of stock.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Low Stock Notifications -->
    @if($recentNotifications->count() > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-medium text-blue-800">Recent Low Stock Notifications</h3>
            <span class="text-xs text-blue-600">{{ $recentNotifications->count() }} active alerts</span>
        </div>
        <div class="space-y-2">
            @foreach($recentNotifications as $notification)
            <div class="flex justify-between items-center bg-white rounded-md p-3 border border-blue-100 hover:shadow-md transition-shadow cursor-pointer notification-item" 
                 onclick="window.location.href='{{ route('admin.products.edit', $notification->product_id) }}'">
                <div class="flex items-center flex-1">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-900">{{ $notification->product_name }}</span>
                        <span class="text-xs text-gray-500 ml-2">({{ $notification->product_sku }})</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4" onclick="event.stopPropagation();">
                    <span class="text-sm text-red-600 font-medium">{{ $notification->current_stock }} left</span>
                    <span class="text-xs text-gray-500">{{ $notification->notified_at->diffForHumans() }}</span>
                    <button onclick="markAsResolved({{ $notification->id }}, '{{ $notification->product_name }}', {{ $notification->current_stock }}); event.stopPropagation();" 
                            class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200">
                        Mark Resolved
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Products Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">All Products</h3>
                <a href="{{ route('admin.products.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Product
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        <tr id="product-{{ $product->id }}" class="@if(request('highlight') == $product->id) bg-yellow-50 @endif">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                             src="{{ $product->cover_image_url }}" 
                                             alt="{{ $product->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->category->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->getTotalStock() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($product->is_available) bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <a href="/admin/products" 
                                   class="text-red-600 hover:text-red-900">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No products found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<script>
let currentNotificationId = null;
let currentProductName = null;
let currentStock = null;

function markAsResolved(notificationId, productName, currentStock) {
    currentNotificationId = notificationId;
    currentProductName = productName;
    currentStock = currentStock;
    showResolveModal(notificationId, productName, currentStock);
}

function showResolveModal(notificationId, productName, stock) {
    // Create modal overlay
    const overlay = document.createElement('div');
    overlay.id = 'resolve-modal-overlay';
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all';
    
    modal.innerHTML = `
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Mark as Resolved?</h3>
                    <p class="text-sm text-gray-500 mt-1">Are you sure you want to mark this low stock notification as resolved?</p>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    <div class="text-sm text-gray-700">
                        <p class="font-medium">Product: ${productName}</p>
                        <p class="text-yellow-700">Current Stock: ${stock} units</p>
                        <p class="text-xs text-gray-600 mt-1">Please make sure the stock has been updated before marking as resolved.</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeResolveModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </button>
                <button onclick="confirmResolve()" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Mark as Resolved
                </button>
            </div>
        </div>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeResolveModal();
        }
    });
}

function closeResolveModal() {
    const overlay = document.getElementById('resolve-modal-overlay');
    if (overlay) {
        overlay.remove();
    }
    currentNotificationId = null;
    currentProductName = null;
    currentStock = null;
}

function confirmResolve() {
    if (!currentNotificationId) return;
    
    const notificationId = currentNotificationId;
    
    // Always proceed with API call - backend will validate stock
    proceedResolve(notificationId);
}

function showWarningModal(notificationId) {
    // Create warning modal
    const warningModal = document.createElement('div');
    warningModal.id = 'warning-modal-overlay';
    warningModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    
    const warningContent = document.createElement('div');
    warningContent.className = 'bg-white rounded-lg shadow-xl max-w-md w-full mx-4';
    
    warningContent.innerHTML = `
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Stock Still Low</h3>
                    <p class="text-sm text-gray-500 mt-1">This product is still low on stock (${currentStock} units).</p>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    <div class="text-sm text-gray-700">
                        <p class="font-medium">Product: ${currentProductName}</p>
                        <p class="text-yellow-700">Current Stock: ${currentStock} units</p>
                        <p class="text-xs text-gray-600 mt-1">Please restock or update the inventory before marking as resolved.</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeWarningModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </button>
                <button onclick="proceedResolve(${notificationId})" 
                        class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Mark Anyway
                </button>
            </div>
        </div>
    `;
    
    warningModal.appendChild(warningContent);
    document.body.appendChild(warningModal);
    
    // Close on overlay click
    warningModal.addEventListener('click', function(e) {
        if (e.target === warningModal) {
            closeWarningModal();
        }
    });
}

function closeWarningModal() {
    const warningModal = document.getElementById('warning-modal-overlay');
    if (warningModal) {
        warningModal.remove();
    }
}

function showWarningModalWithStock(notificationId, stockCount) {
    // Create warning modal with backend stock data
    const warningModal = document.createElement('div');
    warningModal.id = 'warning-modal-overlay';
    warningModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    
    const warningContent = document.createElement('div');
    warningContent.className = 'bg-white rounded-lg shadow-xl max-w-md w-full mx-4';
    
    warningContent.innerHTML = `
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Stock Still Low</h3>
                    <p class="text-sm text-gray-500 mt-1">This product is still low on stock (${stockCount} units).</p>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    <div class="text-sm text-gray-700">
                        <p class="font-medium">Product: ${currentProductName}</p>
                        <p class="text-yellow-700">Current Stock: ${stockCount} units</p>
                        <p class="text-xs text-gray-600 mt-1">Please restock or update the inventory before marking as resolved.</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeWarningModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </button>
                <button onclick="forceResolve(${notificationId})" 
                        class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Mark Anyway
                </button>
            </div>
        </div>
    `;
    
    warningModal.appendChild(warningContent);
    document.body.appendChild(warningModal);
    
    // Close on overlay click
    warningModal.addEventListener('click', function(e) {
        if (e.target === warningModal) {
            closeWarningModal();
        }
    });
}

function forceResolve(notificationId) {
    // Close warning modal
    closeWarningModal();
    
    // Force resolve by passing force flag
    fetch(`/admin/notifications/${notificationId}/resolve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ force: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the notification from the UI
            const notificationElement = document.querySelector(`[onclick*="markAsResolved(${notificationId})"]`).closest('.bg-white');
            if (notificationElement) {
                notificationElement.style.transition = 'opacity 0.3s ease';
                notificationElement.style.opacity = '0';
                setTimeout(() => notificationElement.remove(), 300);
            }
            
            // Show success message
            showNotification('Notification marked as resolved', 'success');
            
            // Reload the page
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Failed to mark notification as resolved', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function proceedResolve(notificationId) {
    // Close any open modals
    closeWarningModal();
    closeResolveModal();
    
    fetch(`/admin/notifications/${notificationId}/resolve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the notification from the UI
            const notificationElement = document.querySelector(`[onclick*="markAsResolved(${notificationId})"]`).closest('.bg-white');
            if (notificationElement) {
                notificationElement.style.transition = 'opacity 0.3s ease';
                notificationElement.style.opacity = '0';
                setTimeout(() => notificationElement.remove(), 300);
            }
            
            // Show success message
            showNotification('Notification marked as resolved', 'success');
            
            // Reload the page after a short delay to refresh notifications
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // If stock is still low, show warning with updated stock
            if (data.needs_confirmation) {
                // Update currentStock with the value from backend
                currentStock = data.current_stock;
                currentProductName = currentProductName || data.product_name || '';
                
                // Show warning modal with updated stock
                showWarningModalWithStock(notificationId, data.current_stock);
            } else {
                showNotification(data.message || 'Failed to mark notification as resolved', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
