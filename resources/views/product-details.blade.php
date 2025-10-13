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
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-yellow-400"></i>
                            @endfor
                        </div>
                        <span class="text-gray-600">({{ $product->review_count }} reviews)</span>
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
                            <button onclick="addToCart({{ $product->id }})" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
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
    
    fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart successfully!', 'success');
            updateCartCount();
        } else {
            showNotification('Failed to add product to cart', 'error');
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
</script>
@endsection