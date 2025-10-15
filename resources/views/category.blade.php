@extends('layouts.app')

@section('title', $category->name . ' - 3Migs Gowns & Barong')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-3">
            <nav class="text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900 font-medium">{{ $category->name }}</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col gap-6">
            <!-- Filters Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 order-first">
                <div class="space-y-6">
                    <!-- Filter Header -->
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Filter Products</h2>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-filter text-gray-400"></i>
                            <span class="text-sm text-gray-500">Refine your search</span>
                        </div>
                    </div>
                    
                    <!-- Filter Controls -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Category Filter -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-tags mr-2 text-gray-400"></i>
                                Category
                            </label>
                            <select id="category-select" class="w-full h-11 border border-gray-300 rounded-lg px-4 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors bg-white">
                                @foreach($allCategories as $cat)
                                    <option value="{{ route('category.show', $cat->slug) }}" {{ $cat->id === $category->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>
                                Price Range
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative">
                                    <input type="number" id="min-price" placeholder="Min" value="{{ request('min_price') }}"
                                           class="w-full h-11 border border-gray-300 rounded-lg px-4 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-400">₱</span>
                                </div>
                                <div class="relative">
                                    <input type="number" id="max-price" placeholder="Max" value="{{ request('max_price') }}"
                                           class="w-full h-11 border border-gray-300 rounded-lg px-4 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-400">₱</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sort Filter -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-sort mr-2 text-gray-400"></i>
                                Sort By
                            </label>
                            <select id="sort-select" class="w-full h-11 border border-gray-300 rounded-lg px-4 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors bg-white">
                                <option value="name" {{ request('sort')==='name' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="price_low" {{ request('sort')==='price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort')==='price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="newest" {{ request('sort')==='newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="popular" {{ request('sort')==='popular' ? 'selected' : '' }}>Most Popular</option>
                            </select>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-cog mr-2 text-gray-400"></i>
                                Actions
                            </label>
                            <div class="flex space-x-3">
                                <button id="apply-filters" class="flex-1 h-11 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 font-medium flex items-center justify-center">
                                    <i class="fas fa-check mr-2"></i>
                                    Apply Filters
                                </button>
                                <button id="clear-filters" class="flex-1 h-11 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 font-medium flex items-center justify-center">
                                    <i class="fas fa-times mr-2"></i>
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Filters Display -->
                    <div id="active-filters" class="hidden">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">Active filters:</span>
                            <div class="flex flex-wrap gap-2">
                                <!-- Active filters will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remove left sidebar entirely for this view to maximize space -->
            
            <!-- Main Content -->
            <div class="flex-1 max-w-screen-xl mx-auto w-full">
                <!-- Category Header -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <!-- Left Section: Title and Info -->
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <div class="w-1 h-6 bg-gradient-to-b from-red-500 to-red-600 mr-3 rounded-full"></div>
                                <div>
                                    <h1 class="text-xl font-bold text-gray-900">{{ $category->name }}</h1>
                                    <p class="text-sm text-gray-600">{{ $category->description }}</p>
                                </div>
                            </div>
                            
                            <!-- Product Count -->
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600">
                                    Showing <span id="product-count">{{ $barongProducts->count() }}</span> of {{ $barongProducts->total() }} products
                                </span>
                                @if(request()->hasAny(['min_price', 'max_price', 'sort']))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-filter mr-1"></i>
                                        Filtered
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right Section: View Toggle and Back Button -->
                        <div class="flex items-center space-x-4">
                            <!-- View Toggle -->
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-700">View:</span>
                                <div class="flex bg-gray-100 rounded-lg p-1">
                                    <button id="grid-view" class="p-2 rounded-md bg-red-100 text-red-600">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button id="list-view" class="p-2 rounded-md text-gray-400 hover:bg-gray-200">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Back to Home Button -->
                            <a href="{{ route('home') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse($barongProducts as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden relative product-card hover:shadow-lg transition-shadow flex flex-col">
                            @if($product->is_on_sale)
                                <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $product->discount_percentage }}%</div>
                            @endif
                            <a href="{{ route('product.details', $product->slug) }}" class="block flex-grow">
                                <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                                    <div class="flex items-center mt-2">
                                        @if($product->is_on_sale)
                                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                            <span class="text-gray-500 text-sm line-through ml-2">₱{{ number_format($product->price, 0) }}</span>
                                        @else
                                            <span class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 mt-1">
                                        @for($i = 0; $i < floor($product->average_rating); $i++)
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @endfor
                                        @if($product->average_rating - floor($product->average_rating) > 0)
                                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                                        @endif
                                        @for($i = 0; $i < (5 - ceil($product->average_rating)); $i++)
                                            <i class="far fa-star text-yellow-400"></i>
                                        @endfor
                                        <span class="ml-1">({{ $product->review_count }})</span>
                                    </div>
                                </div>
                            </a>
                            <button 
                                class="m-4 mt-auto w-[calc(100%-2rem)] bg-black text-white py-2 rounded-md hover:bg-gray-800 text-center add-to-cart-btn"
                                data-product-id="{{ $product->id }}"
                                onclick="addToCart({{ $product->id }})">
                                Add To Cart
                            </button>
                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="col-span-full flex items-center justify-center min-h-[80vh] px-4">
                            <div class="text-center max-w-lg mx-auto">
                                <!-- Icon -->
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <i class="fas fa-search text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Message -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Products Found</h3>
                                    <p class="text-gray-600 text-base leading-relaxed">
                                        We couldn't find any products matching your criteria. Try adjusting your filters or search terms to discover more items.
                                    </p>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-4">
                                    <button onclick="clearFilters()" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                        <i class="fas fa-refresh mr-2"></i>
                                        Clear Filters
                                    </button>
                                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                                        <i class="fas fa-home mr-2"></i>
                                        Back to Home
                                    </a>
                                </div>
                                
                                <!-- Additional Help -->
                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-500">
                                        Need help? <a href="#" class="text-red-500 hover:text-red-600 font-medium">Contact our support team</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($barongProducts->hasPages())
                    <div class="mt-8">
                        {{ $barongProducts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applyFiltersBtn = document.getElementById('apply-filters');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const sortSelect = document.getElementById('sort-select');
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');
    const categorySelect = document.getElementById('category-select');
    const productsContainer = document.getElementById('products-container');
    
    // Apply filters with visual feedback
    applyFiltersBtn.addEventListener('click', function() {
        // Add loading state
        applyFiltersBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Applying...';
        applyFiltersBtn.disabled = true;
        
        setTimeout(() => {
            applyFilters();
        }, 300);
    });
    
    // Category change
    if (categorySelect) {
        categorySelect.addEventListener('change', () => {
            window.location.href = categorySelect.value;
        });
    }

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        minPriceInput.value = '';
        maxPriceInput.value = '';
        sortSelect.value = 'name';
        applyFilters();
    });
    
    // Global clear filters function for empty state
    window.clearFilters = function() {
        minPriceInput.value = '';
        maxPriceInput.value = '';
        sortSelect.value = 'name';
        applyFilters();
    };
    
    // Sort change
    sortSelect.addEventListener('change', function() {
        applyFilters();
    });
    
    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        const min = minPriceInput.value;
        const max = maxPriceInput.value;

        // Allow filtering with any combination: min only, max only, both, or neither (for sorting only)
        // No validation needed - let backend handle it

        // Only filter if at least one bound is provided; if both empty, keep existing filters
        if (min !== '') {
            params.set('min_price', min);
        } else {
            params.delete('min_price');
        }

        if (max !== '') {
            params.set('max_price', max);
        } else {
            params.delete('max_price');
        }

        // Always apply sorting - backend will handle when to use it
        if (sortSelect.value && sortSelect.value !== 'name') {
            params.set('sort', sortSelect.value);
        } else {
            params.delete('sort');
        }

        const qs = params.toString();
        const url = window.location.pathname + (qs ? ('?' + qs) : '');
        
        // Use assign to create a new history entry; inputs remain filled server-side via request()
        window.location.assign(url);
    }
    
    // View toggle
    const gridViewBtn = document.getElementById('grid-view');
    const listViewBtn = document.getElementById('list-view');
    
    gridViewBtn.addEventListener('click', function() {
        productsContainer.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6';
        gridViewBtn.className = 'p-2 rounded-md bg-red-100 text-red-600';
        listViewBtn.className = 'p-2 rounded-md text-gray-400 hover:bg-gray-100';
    });
    
    listViewBtn.addEventListener('click', function() {
        productsContainer.className = 'grid grid-cols-1 gap-6';
        listViewBtn.className = 'p-2 rounded-md bg-red-100 text-red-600';
        gridViewBtn.className = 'p-2 rounded-md text-gray-400 hover:bg-gray-100';
    });
    
    // Show active filters
    function showActiveFilters(min, max, sort) {
        const activeFiltersDiv = document.getElementById('active-filters');
        const filtersContainer = activeFiltersDiv.querySelector('.flex.flex-wrap.gap-2');
        
        // Clear existing filters
        filtersContainer.innerHTML = '';
        
        let hasActiveFilters = false;
        
        if (min || max) {
            hasActiveFilters = true;
            const priceFilter = document.createElement('span');
            priceFilter.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
            priceFilter.innerHTML = `<i class="fas fa-dollar-sign mr-1"></i>Price: ${min || '0'} - ${max || '∞'}`;
            filtersContainer.appendChild(priceFilter);
        }
        
        if (sort && sort !== 'name') {
            hasActiveFilters = true;
            const sortFilter = document.createElement('span');
            sortFilter.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
            sortFilter.innerHTML = `<i class="fas fa-sort mr-1"></i>${sortSelect.options[sortSelect.selectedIndex].text}`;
            filtersContainer.appendChild(sortFilter);
        }
        
        if (hasActiveFilters) {
            activeFiltersDiv.classList.remove('hidden');
        } else {
            activeFiltersDiv.classList.add('hidden');
        }
    }
    
    // Initialize active filters on page load
    showActiveFilters(minPriceInput.value, maxPriceInput.value, sortSelect.value);
});

// Add to Cart functionality
function addToCart(productId) {
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart successfully!', 'success');
            updateCartCount();
        } else {
            showNotification(data.message || 'Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while adding to cart', 'error');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    // Position notification below header dynamically
    positionNotificationBelowHeader(notification, 16);
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function updateCartCount() {
    fetch('/api/v1/cart/count')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.data.count;
                }
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}
</script>
<script>
// Lightweight toast if page doesn't already include one
if (typeof window.showNotification !== 'function') {
    window.showNotification = function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed right-5 z-50 px-4 py-3 rounded-md shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-gray-700'
        }`;
        toast.textContent = message;
        
        // Position notification below header dynamically
        positionNotificationBelowHeader(toast, 16);
        
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }
}
</script>
@endsection
