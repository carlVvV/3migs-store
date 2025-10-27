@extends('layouts.app')

@section('title', $product->name . ' - 3Migs Barong')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="#" class="hover:text-blue-600 transition-colors">{{ $product->category->name ?? 'Products' }}</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900 font-medium">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="aspect-w-1 aspect-h-1">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="w-full h-96 object-cover" id="main-image">
                        @else
                            <div class="w-full h-96 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-tshirt text-8xl text-gray-400"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Thumbnail Images -->
                @if($product->images && count($product->images) > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product->image_urls as $index => $imageUrl)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden cursor-pointer hover:shadow-lg transition-shadow {{ $product->cover_image == $product->images[$index] ? 'ring-2 ring-blue-500' : '' }}" onclick="changeMainImage('{{ $imageUrl }}', this)">
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-20 object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            <!-- Product Details -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <!-- Product Title -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                    
                    <!-- Rating -->
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex items-center">
                            @php
                                $averageRating = $product->average_rating ?? 0;
                                $fullStars = floor($averageRating);
                                $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $fullStars)
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i == $fullStars + 1 && $hasHalfStar)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-yellow-400"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-gray-600">{{ number_format($averageRating, 1) }} ({{ $product->review_count }} reviews)</span>
                        <span class="text-sm text-gray-500">|</span>
                        <span class="text-sm text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="flex items-center space-x-4 mb-6">
                        @if($product->is_on_sale)
                            <span class="text-4xl font-bold text-red-500">₱{{ number_format($product->current_price, 2) }}</span>
                            <span class="text-2xl text-gray-500 line-through">₱{{ number_format($product->price, 2) }}</span>
                            <span class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm font-bold">
                                -{{ $product->discount_percentage }}% OFF
                            </span>
                        @else
                            <span class="text-4xl font-bold text-gray-900">₱{{ number_format($product->current_price, 2) }}</span>
                        @endif
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                    </div>
                    
                    <!-- Product Attributes -->
                    @if($product->attributes)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Product Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(isset($product->attributes['material']))
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-tag text-blue-500"></i>
                                <span class="font-medium text-gray-700">Material:</span>
                                <span class="text-gray-600">{{ $product->attributes['material'] }}</span>
                            </div>
                            @endif
                            @if(isset($product->attributes['colors']))
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-palette text-blue-500"></i>
                                <span class="font-medium text-gray-700">Colors:</span>
                                <span class="text-gray-600">{{ implode(', ', $product->attributes['colors']) }}</span>
                            </div>
                            @endif
                            @if(isset($product->attributes['sizes']))
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-ruler text-blue-500"></i>
                                <span class="font-medium text-gray-700">Sizes:</span>
                                <span class="text-gray-600">{{ implode(', ', $product->attributes['sizes']) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Stock Status -->
                    <div class="mb-6">
                        @if($product->in_stock)
                            <div class="flex items-center space-x-2 text-green-600">
                                <i class="fas fa-check-circle"></i>
                                <span class="font-medium">In Stock ({{ $product->stock_quantity }} available)</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 text-red-600">
                                <i class="fas fa-times-circle"></i>
                                <span class="font-medium">Out of Stock</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Size and Color Selection -->
                    @php
                        $availableData = $product->getAvailableColorsAndSizes();
                        $availableSizes = $availableData['sizes'] ?? [];
                        $availableColors = $availableData['colors'] ?? [];
                        $colorStocks = $availableData['color_stocks'] ?? [];
                    @endphp
                    
                    @if(!empty($availableSizes))
                    <div class="mb-6 space-y-4">
                        <!-- Size Selection -->
                        @if(!empty($availableSizes))
                        <div>
                            <label class="text-lg font-semibold text-gray-900 mb-2 block">Size</label>
                            <div class="flex flex-wrap gap-2" id="size-options">
                                @foreach($availableSizes as $size)
                                    <button type="button" 
                                            class="size-option px-4 py-2 border-2 border-gray-300 rounded-lg transition-all hover:border-blue-500 {{ $loop->first ? 'selected border-blue-600 bg-blue-50' : '' }}"
                                            data-size="{{ $size }}"
                                            onclick="selectSize('{{ $size }}')">
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Color Selection -->
                        @if(!empty($availableColors))
                        <div id="color-section">
                            <label class="text-lg font-semibold text-gray-900 mb-2 block">Color</label>
                            <div class="flex flex-wrap gap-2" id="color-options">
                                <!-- Colors will be dynamically updated based on selected size -->
                            </div>
                        </div>
                        @endif
                        
                        <!-- Stock Status -->
                        <div id="size-color-stock-info" class="text-sm text-gray-600">
                            <p id="stock-display">Select size and color to see availability</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Quantity and Actions -->
                    @if($product->in_stock)
                    <div class="space-y-4">
                        <!-- Quantity Selector -->
                        <div class="flex items-center space-x-4">
                            <label class="text-lg font-semibold text-gray-900">Quantity:</label>
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button onclick="decreaseQuantity()" class="px-3 py-2 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-16 text-center border-0 focus:ring-0">
                                <button onclick="increaseQuantity()" class="px-3 py-2 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex space-x-4">
                            <button onclick="addToCartWithOptions({{ $product->id }})" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Add to Cart
                            </button>
                            <button onclick="toggleWishlist({{ $product->id }})" class="bg-gray-200 text-gray-700 px-6 py-4 rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Care Instructions -->
                    @if($product->care_instructions)
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Care Instructions
                        </h3>
                        <p class="text-blue-800">{{ $product->care_instructions }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="mt-16">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>
                
                <!-- Rating Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Overall Rating -->
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($product->average_rating ?? 0, 1) }}</div>
                        <div class="flex items-center justify-center mb-2">
                            @php
                                $averageRating = $product->average_rating ?? 0;
                                $fullStars = floor($averageRating);
                                $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $fullStars)
                                    <i class="fas fa-star text-yellow-400 text-xl"></i>
                                @elseif($i == $fullStars + 1 && $hasHalfStar)
                                    <i class="fas fa-star-half-alt text-yellow-400 text-xl"></i>
                                @else
                                    <i class="far fa-star text-yellow-400 text-xl"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-gray-600">Based on {{ $product->review_count }} reviews</p>
                    </div>
                    
                    <!-- Rating Distribution -->
                    <div class="space-y-2">
                        @php
                            $ratingDistribution = $product->getRatingDistribution();
                            $totalReviews = $product->review_count;
                        @endphp
                        @for($rating = 5; $rating >= 1; $rating--)
                            @php
                                $count = $ratingDistribution->where('rating', $rating)->first()->count ?? 0;
                                $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-700 w-8">{{ $rating }}★</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600 w-8">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
                
                <!-- Reviews List -->
                <div id="reviews-container">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Loading reviews...</p>
                    </div>
                </div>
                
                <!-- Load More Reviews Button -->
                <div class="text-center mt-6">
                    <button id="load-more-reviews" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors hidden">
                        Load More Reviews
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">You Might Also Like</h2>
                <p class="text-gray-600">Discover more beautiful barong designs</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover group">
                    <div class="relative">
                        <div class="h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            @if($relatedProduct->images && count($relatedProduct->images) > 0)
                                <img src="{{ $relatedProduct->images[0] }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-tshirt text-6xl text-gray-400"></i>
                            @endif
                        </div>
                        @if($relatedProduct->is_on_sale)
                            <div class="absolute top-4 left-4 bg-red-500 text-white px-2 py-1 rounded-lg text-sm font-medium">
                                -{{ $relatedProduct->discount_percentage }}%
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                            {{ $relatedProduct->name }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-3">{{ $relatedProduct->category->name ?? 'Uncategorized' }}</p>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <span class="text-xl font-bold text-gray-800">₱{{ number_format($relatedProduct->current_price, 2) }}</span>
                                @if($relatedProduct->is_on_sale)
                                    <span class="text-sm text-gray-500 line-through">₱{{ number_format($relatedProduct->price, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                                @endfor
                            </div>
                        </div>
                        <a href="{{ route('product.details', $relatedProduct->slug) }}" class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-3 rounded-lg font-medium hover:from-green-700 hover:to-blue-700 transition-all duration-300 text-center block">
                            <i class="fas fa-eye mr-2"></i>
                            View Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
// Size and Color Selection
const colorStocksData = @json($colorStocks);
let selectedSize = '';
let selectedColor = '';

function selectSize(size) {
    selectedSize = size;
    document.querySelectorAll('.size-option').forEach(btn => {
        btn.classList.remove('selected', 'border-blue-600', 'bg-blue-50');
        btn.classList.add('border-gray-300');
    });
    event.target.classList.add('selected', 'border-blue-600', 'bg-blue-50');
    event.target.classList.remove('border-gray-300');
    updateColorsForSize(size);
}

function updateColorsForSize(size) {
    const colorContainer = document.getElementById('color-options');
    if (!colorStocksData[size]) {
        colorContainer.innerHTML = '<p class="text-gray-500 text-sm">Size only - no color options</p>';
        selectedColor = null; // No color selection needed
        updateStockDisplay();
        return;
    }
    
    // Check if this is a numeric value (size-based stock without colors)
    const stockValue = colorStocksData[size];
    if (typeof stockValue === 'number') {
        // Size-based stock only, no color selection
        colorContainer.innerHTML = '<p class="text-green-600 text-sm"><i class="fas fa-check-circle"></i> Available</p>';
        selectedColor = null;
        updateStockDisplay();
        return;
    }
    
    // Color-based stock
    colorContainer.innerHTML = '';
    Object.keys(colorStocksData[size]).forEach(color => {
        const qty = colorStocksData[size][color];
        if (qty > 0) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'color-option px-4 py-2 border-2 border-gray-300 rounded-lg transition-all hover:border-blue-500';
            btn.textContent = color;
            btn.dataset.color = color;
            btn.onclick = function() { selectColor(color); };
            if (selectedColor === color) {
                btn.classList.add('selected', 'border-blue-600', 'bg-blue-50');
            }
            colorContainer.appendChild(btn);
        }
    });
    if (colorContainer.children.length > 0 && !selectedColor) {
        selectColor(Object.keys(colorStocksData[size])[0]);
    } else if (colorContainer.children.length === 0) {
        colorContainer.innerHTML = '<p class="text-gray-500 text-sm">No colors available for this size</p>';
    }
}

function selectColor(color) {
    selectedColor = color;
    document.querySelectorAll('.color-option').forEach(btn => {
        btn.classList.remove('selected', 'border-blue-600', 'bg-blue-50');
        btn.classList.add('border-gray-300');
    });
    event.target.classList.add('selected', 'border-blue-600', 'bg-blue-50');
    event.target.classList.remove('border-gray-300');
    updateStockDisplay();
}

function updateStockDisplay() {
    const stockDisplay = document.getElementById('stock-display');
    const quantityInput = document.getElementById('quantity');
    
    if (!selectedSize) {
        stockDisplay.innerHTML = '<i class="fas fa-info-circle text-blue-600"></i> Select size to see availability';
        if (quantityInput) quantityInput.setAttribute('max', '1');
        return;
    }
    
    if (!colorStocksData[selectedSize]) {
        stockDisplay.innerHTML = '<i class="fas fa-times-circle text-red-600"></i> Out of Stock';
        if (quantityInput) quantityInput.setAttribute('max', '0');
        return;
    }
    
    const stockValue = colorStocksData[selectedSize];
    
    // Handle size-based stock (numeric value)
    if (typeof stockValue === 'number') {
        const qty = stockValue;
        stockDisplay.innerHTML = `<i class="fas fa-check-circle text-green-600"></i> In Stock (${qty} available)`;
        if (quantityInput) quantityInput.setAttribute('max', qty);
        return;
    }
    
    // Handle color-based stock
    if (selectedColor && stockValue[selectedColor]) {
        const qty = stockValue[selectedColor];
        stockDisplay.innerHTML = `<i class="fas fa-check-circle text-green-600"></i> In Stock (${qty} available)`;
        if (quantityInput) quantityInput.setAttribute('max', qty);
    } else {
        stockDisplay.innerHTML = '<i class="fas fa-times-circle text-red-600"></i> Out of Stock';
        if (quantityInput) quantityInput.setAttribute('max', '0');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const firstSizeBtn = document.querySelector('.size-option.selected');
    if (firstSizeBtn) {
        const firstSize = firstSizeBtn.dataset.size;
        if (firstSize) updateColorsForSize(firstSize);
    }
});

function changeMainImage(imageSrc, element) {
    // Update main image
    document.getElementById('main-image').src = imageSrc;
    
    // Update active thumbnail
    document.querySelectorAll('.grid .bg-white').forEach(thumb => {
        thumb.classList.remove('ring-2', 'ring-blue-500');
    });
    element.classList.add('ring-2', 'ring-blue-500');
}

function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const maxQuantity = parseInt(quantityInput.getAttribute('max'));
    const currentQuantity = parseInt(quantityInput.value);
    
    if (currentQuantity < maxQuantity) {
        quantityInput.value = currentQuantity + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentQuantity = parseInt(quantityInput.value);
    
    if (currentQuantity > 1) {
        quantityInput.value = currentQuantity - 1;
    }
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    const data = { product_id: productId, quantity: quantity };
    
    // Add size and color if selected
    if (selectedSize) data.size = selectedSize;
    if (selectedColor) data.color = selectedColor;
    
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
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
        showNotification('An error occurred', 'error');
    });
}

function toggleWishlist(productId) {
    fetch('/api/v1/wishlist/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Added to wishlist!', 'success');
        } else {
            showNotification('Failed to add to wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Reviews functionality
let currentPage = 1;
const reviewsPerPage = 5;

document.addEventListener('DOMContentLoaded', function() {
    loadReviews();
    
    // Load more reviews button
    document.getElementById('load-more-reviews').addEventListener('click', function() {
        currentPage++;
        loadReviews();
    });
});

async function loadReviews() {
    try {
        const response = await fetch(`/api/v1/products/{{ $product->id }}/reviews?page=${currentPage}&limit=${reviewsPerPage}`);
        const data = await response.json();
        
        if (data.success) {
            displayReviews(data.data);
            
            // Show/hide load more button
            const loadMoreBtn = document.getElementById('load-more-reviews');
            if (data.data.next_page_url) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.classList.add('hidden');
            }
        } else {
            throw new Error(data.message || 'Failed to load reviews');
        }
    } catch (error) {
        console.error('Error loading reviews:', error);
        document.getElementById('reviews-container').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-2xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Failed to load reviews. Please try again later.</p>
            </div>
        `;
    }
}

function displayReviews(reviewsData) {
    const container = document.getElementById('reviews-container');
    
    if (currentPage === 1) {
        container.innerHTML = '';
    }
    
    if (reviewsData.data && reviewsData.data.length > 0) {
        reviewsData.data.forEach(review => {
            const reviewElement = createReviewElement(review);
            container.appendChild(reviewElement);
        });
    } else if (currentPage === 1) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-comment-slash text-2xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No reviews yet. Be the first to review this product!</p>
            </div>
        `;
    }
}

function createReviewElement(review) {
    const reviewDiv = document.createElement('div');
    reviewDiv.className = 'border-b border-gray-200 py-6 last:border-b-0';
    
    const stars = generateStars(review.rating);
    const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    reviewDiv.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    <h4 class="font-medium text-gray-900">${review.user.name}</h4>
                    <div class="flex items-center">
                        ${stars}
                    </div>
                    <span class="text-sm text-gray-500">${reviewDate}</span>
                    ${review.is_verified_purchase ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Verified Purchase</span>' : ''}
                </div>
                ${review.review_text ? `<p class="text-gray-700">${review.review_text}</p>` : ''}
            </div>
        </div>
    `;
    
    return reviewDiv;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star text-yellow-400"></i>';
        } else {
            stars += '<i class="far fa-star text-yellow-400"></i>';
        }
    }
    return stars;
}
</script>
@endsection
        thumb.classList.remove('ring-2', 'ring-blue-500');
    });
    element.classList.add('ring-2', 'ring-blue-500');
}

function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const maxQuantity = parseInt(quantityInput.getAttribute('max'));
    const currentQuantity = parseInt(quantityInput.value);
    
    if (currentQuantity < maxQuantity) {
        quantityInput.value = currentQuantity + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentQuantity = parseInt(quantityInput.value);
    
    if (currentQuantity > 1) {
        quantityInput.value = currentQuantity - 1;
    }
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    const data = { product_id: productId, quantity: quantity };
    
    // Add size and color if selected
    if (selectedSize) data.size = selectedSize;
    if (selectedColor) data.color = selectedColor;
    
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
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
        showNotification('An error occurred', 'error');
    });
}

function toggleWishlist(productId) {
    fetch('/api/v1/wishlist/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Added to wishlist!', 'success');
        } else {
            showNotification('Failed to add to wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Reviews functionality
let currentPage = 1;
const reviewsPerPage = 5;

document.addEventListener('DOMContentLoaded', function() {
    loadReviews();
    
    // Load more reviews button
    document.getElementById('load-more-reviews').addEventListener('click', function() {
        currentPage++;
        loadReviews();
    });
});

async function loadReviews() {
    try {
        const response = await fetch(`/api/v1/products/{{ $product->id }}/reviews?page=${currentPage}&limit=${reviewsPerPage}`);
        const data = await response.json();
        
        if (data.success) {
            displayReviews(data.data);
            
            // Show/hide load more button
            const loadMoreBtn = document.getElementById('load-more-reviews');
            if (data.data.next_page_url) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.classList.add('hidden');
            }
        } else {
            throw new Error(data.message || 'Failed to load reviews');
        }
    } catch (error) {
        console.error('Error loading reviews:', error);
        document.getElementById('reviews-container').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-2xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Failed to load reviews. Please try again later.</p>
            </div>
        `;
    }
}

function displayReviews(reviewsData) {
    const container = document.getElementById('reviews-container');
    
    if (currentPage === 1) {
        container.innerHTML = '';
    }
    
    if (reviewsData.data && reviewsData.data.length > 0) {
        reviewsData.data.forEach(review => {
            const reviewElement = createReviewElement(review);
            container.appendChild(reviewElement);
        });
    } else if (currentPage === 1) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-comment-slash text-2xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No reviews yet. Be the first to review this product!</p>
            </div>
        `;
    }
}

function createReviewElement(review) {
    const reviewDiv = document.createElement('div');
    reviewDiv.className = 'border-b border-gray-200 py-6 last:border-b-0';
    
    const stars = generateStars(review.rating);
    const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    reviewDiv.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    <h4 class="font-medium text-gray-900">${review.user.name}</h4>
                    <div class="flex items-center">
                        ${stars}
                    </div>
                    <span class="text-sm text-gray-500">${reviewDate}</span>
                    ${review.is_verified_purchase ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Verified Purchase</span>' : ''}
                </div>
                ${review.review_text ? `<p class="text-gray-700">${review.review_text}</p>` : ''}
            </div>
        </div>
    `;
    
    return reviewDiv;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star text-yellow-400"></i>';
        } else {
            stars += '<i class="far fa-star text-yellow-400"></i>';
        }
    }
    return stars;
}
</script>
@endsection