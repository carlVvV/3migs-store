@extends('layouts.admin')

@section('title', isset($barongProduct) ? 'Edit Product - Admin Dashboard' : 'Create Product - Admin Dashboard')
@section('page-title', isset($barongProduct) ? 'Edit Product' : 'Create Product')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ isset($barongProduct) ? 'Edit Barong Product' : 'Create New Barong Product' }}
                    </h1>
                    <p class="mt-2 text-gray-600">
                        {{ isset($barongProduct) ? 'Update barong product details' : 'Add a new barong product to your inventory' }}
                    </p>
                </div>
                <a href="{{ route('admin.products') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Back to Products
                </a>
            </div>
        </div>

        <form id="barongProductForm" 
              action="{{ isset($barongProduct) ? route('admin.products.update', $barongProduct->id) : route('admin.products.store') }}"
              method="POST"
              enctype="multipart/form-data" 
              onsubmit="return handleFormSubmit(event)"
              class="space-y-8">
            @csrf
            @if(isset($barongProduct))
                @method('PUT')
            @endif

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Barong Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $barongProduct->name ?? '') }}" 
                               maxlength="255" required autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Maximum 255 characters</p>
                    </div>
                    
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku', $barongProduct->sku ?? '') }}" 
                               readonly autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        <p class="mt-1 text-xs text-gray-500">Auto-generated if left empty</p>
                    </div>
                    
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">Brand *</label>
                        <select id="brand_id" name="brand_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $barongProduct->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select id="category_id" name="category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $barongProduct->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="sleeve_type" class="block text-sm font-medium text-gray-700 mb-2">Sleeve Type</label>
                        <select id="sleeve_type" name="sleeve_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Sleeve Type</option>
                            <option value="Long Sleeve" {{ old('sleeve_type', $barongProduct->sleeve_type ?? '') == 'Long Sleeve' ? 'selected' : '' }}>Long Sleeve</option>
                            <option value="Short Sleeve" {{ old('sleeve_type', $barongProduct->sleeve_type ?? '') == 'Short Sleeve' ? 'selected' : '' }}>Short Sleeve</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $barongProduct->description ?? '') }}</textarea>
                </div>
            </div>

            <!-- Enhanced Images and Media Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Images and Media</h2>
                
                <!-- Drag and Drop Upload Area -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Images (Up to 8 images)</label>
                    <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer">
                        <input type="file" id="imageInput" name="images[]" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <div class="space-y-3">
                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div>
                                <p class="text-lg font-medium text-gray-900">Upload Images</p>
                                <p class="text-sm text-gray-500">Select multiple images using the file input above</p>
                                <p class="text-xs text-gray-400 mt-1">Supports: JPG, PNG, GIF (Max 2MB each, up to 8 images)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Previews -->
                <div id="imagePreviews" class="flex flex-wrap gap-3 mb-6" style="min-height: 80px;">
                    <!-- Image Counter -->
                    <div id="imageCounter" class="w-full mb-2 text-sm text-gray-600 hidden">
                        <span id="imageCount">0</span> images uploaded (max 8)
                    </div>
                    
                    <!-- Placeholder -->
                    <div id="debugPlaceholder" class="w-full text-center text-gray-300 text-sm py-2">
                        No images uploaded yet
                    </div>
                    
                    <!-- Existing images for edit mode -->
                    @if(isset($barongProduct) && $barongProduct->images)
                        @foreach($barongProduct->images as $index => $image)
                            <div class="image-preview-item relative group" data-image-index="{{ $index }}">
                                <!-- Square image container -->
                                <div class="w-20 h-20 rounded-lg border-2 {{ $barongProduct->cover_image == $image ? 'border-blue-500' : 'border-gray-200' }} overflow-hidden bg-gray-100">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Product Image" 
                                         class="w-full h-full object-cover">
                                </div>
                                
                                <!-- Cover Badge -->
                                @if($barongProduct->cover_image == $image)
                                    <div class="absolute top-1 left-1 bg-blue-600 text-white px-2 py-1 text-xs rounded font-medium">
                                        COVER
                                    </div>
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="absolute inset-0 bg-gray-800 bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex flex-col items-center justify-center space-y-1">
                                    <button type="button" onclick="setCoverImage({{ $index }})" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 text-xs rounded font-medium">
                                        {{ $barongProduct->cover_image == $image ? 'Cover' : 'Set Cover' }}
                                    </button>
                                    <button type="button" onclick="removeImage({{ $index }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded font-medium">
                                        Remove
                                    </button>
                                </div>
                                
                                <!-- Drag Handle -->
                                <div class="absolute top-1 right-1 cursor-move text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Initialize cover image index for existing products -->
                        @if(isset($barongProduct) && $barongProduct->cover_image)
                            @php
                                $coverIndex = array_search($barongProduct->cover_image, $barongProduct->images);
                            @endphp
                            @if($coverIndex !== false)
                                <input type="hidden" name="cover_image_index" value="{{ $coverIndex }}">
                            @endif
                        @endif
                    @endif
                </div>

                <!-- Video Upload -->
                <div class="mt-6">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="enable_video" name="enable_video" 
                               {{ old('enable_video', isset($barongProduct) && $barongProduct->video_url ? 'checked' : '') }}
                               class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="enable_video" class="ml-2 text-sm font-medium text-gray-700">Enable Video</label>
                    </div>
                    <div id="videoSection" class="{{ old('enable_video', isset($barongProduct) && $barongProduct->video_url ? '' : 'hidden') }}">
                        <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">Video URL</label>
                        <input type="url" id="video_url" name="video_url" 
                               value="{{ old('video_url', $barongProduct->video_url ?? '') }}" 
                               placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..."
                               autocomplete="url"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">YouTube or Vimeo URL</p>
                        
                        <!-- Video Preview -->
                        <div id="videoPreview" class="mt-4 hidden">
                            <iframe id="videoFrame" class="w-full h-64 rounded-lg" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Attributes Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Product Attributes</h2>
                        <p class="text-sm text-gray-500 mt-1">Define key characteristics for filtering and display</p>
                    </div>
                    <button type="button" id="setAttributesBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                            onclick="console.log('Button clicked via onclick'); testModalOpen();">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Set Attributes
                    </button>
                </div>
                
                <!-- Enhanced Display Selected Attributes -->
                <div id="selectedAttributes" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    @if(isset($barongProduct))
                        @if($barongProduct->fabric || $barongProduct->embroidery_style || $barongProduct->colors || $barongProduct->collar_type || $barongProduct->design_details)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($barongProduct->fabric)
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700 text-sm">Fabric</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2 ml-4">
                                            @foreach($barongProduct->fabric as $fabric)
                                                <span class="bg-blue-100 text-blue-800 px-3 py-1 text-xs rounded-full font-medium">{{ $fabric }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @if($barongProduct->embroidery_style)
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700 text-sm">Embroidery</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2 ml-4">
                                            @foreach($barongProduct->embroidery_style as $style)
                                                <span class="bg-green-100 text-green-800 px-3 py-1 text-xs rounded-full font-medium">{{ $style }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @if($barongProduct->colors)
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700 text-sm">Colors</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2 ml-4">
                                            @foreach($barongProduct->colors as $color)
                                                <span class="bg-purple-100 text-purple-800 px-3 py-1 text-xs rounded-full font-medium">{{ $color }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @if($barongProduct->collar_type)
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700 text-sm">Collar Type</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2 ml-4">
                                            <span class="bg-orange-100 text-orange-800 px-3 py-1 text-xs rounded-full font-medium">{{ $barongProduct->collar_type }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($barongProduct->design_details)
                                    <div class="space-y-2 md:col-span-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700 text-sm">Design Details</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2 ml-4">
                                            @foreach($barongProduct->design_details as $detail)
                                                <span class="bg-indigo-100 text-indigo-800 px-3 py-1 text-xs rounded-full font-medium">{{ $detail }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">No attributes configured yet</p>
                                <p class="text-gray-400 text-xs mt-1">Click "Set Attributes" to add fabric, embroidery, colors, and other details</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <p class="text-gray-500 text-sm">No attributes set</p>
                            <p class="text-gray-400 text-xs mt-1">Click "Set Attributes" to configure fabric, embroidery, colors, and other details</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Pricing and Stock -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Pricing and Stock</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">Base Price (PHP) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">₱</span>
                            <input type="number" id="base_price" name="base_price" 
                                   value="{{ old('base_price', $barongProduct->base_price ?? '') }}" 
                                   step="0.01" min="0" required autocomplete="off"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center mb-2">
                            <!-- Special price disabled per request -->
                            <input type="hidden" name="enable_special_price" value="0">
                            <input type="checkbox" id="enable_special_price" name="enable_special_price" value="1" class="hidden" disabled>
                            <label for="enable_special_price" class="ml-2 text-sm font-medium text-gray-400 line-through">Enable Special Price</label>
                        </div>
                        <div id="specialPriceSection" class="hidden">
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                <input type="number" id="special_price" name="special_price" 
                                       value="" step="0.01" min="0" autocomplete="off" disabled
                                       class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-md bg-gray-100 cursor-not-allowed">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center mb-2">
                            <input type="hidden" name="has_variations" value="0">
                            <input type="checkbox" id="has_variations" name="has_variations" value="1"
                                   {{ old('has_variations', $barongProduct->has_variations ?? false ? 'checked' : '') }}
                                   class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="has_variations" class="ml-2 text-sm font-medium text-gray-700">Has Variations</label>
                        </div>
                        
                        <!-- Size Stock Management -->
                        <div class="mt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Size Stock Management</h3>
                            <p class="text-sm text-gray-600 mb-4">Set individual stock quantities for each size. Total stock will be calculated automatically.</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                @php
                                    $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                    $sizeStocks = old('size_stocks', $barongProduct->size_stocks ?? []);
                                @endphp
                                
                                @foreach($sizes as $size)
                                    <div>
                                        <label for="size_stock_{{ $size }}" class="block text-sm font-medium text-gray-700 mb-2">Size {{ $size }}</label>
                                        <input type="number" id="size_stock_{{ $size }}" name="size_stocks[{{ $size }}]" 
                                               value="{{ $sizeStocks[$size] ?? '' }}" 
                                               min="0" autocomplete="off"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 size-stock-input transition-all duration-200 hover:border-blue-300"
                                               onchange="console.log('onchange triggered'); calculateTotalStock()" 
                                               oninput="console.log('oninput triggered'); calculateTotalStock()" 
                                               onkeyup="console.log('onkeyup triggered'); calculateTotalStock()"
                                               placeholder="Enter quantity">
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Enhanced Total Stock Display -->
                            <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-700">Total Stock:</span>
                                    <span id="total-stock-display" class="text-2xl font-bold text-blue-600 transition-all duration-300">0</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-2">
                                    Automatically calculated from size-specific quantities
                                </div>
                                <input type="hidden" id="total-stock-input" name="stock" value="0">
                            </div>
                            
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Note:</strong> Total stock is automatically calculated from size-specific quantities. 
                                    This ensures accurate inventory management per size.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Enhanced Variations Section -->
                <div id="variationsSection" class="mt-6 {{ old('has_variations', $barongProduct->has_variations ?? false ? '' : 'hidden') }}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Product Variations</h3>
                        <button type="button" id="addVariationBtn" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200">
                            Add Variation
                        </button>
                    </div>
                    
                    <div id="variationsContainer" class="space-y-4">
                        @if(isset($barongProduct) && $barongProduct->variations)
                            @foreach($barongProduct->variations as $index => $variation)
                                <div class="variation-item border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="font-medium text-gray-900">Variation {{ $index + 1 }}</h4>
                                        <button type="button" onclick="removeVariation(this)" 
                                                class="text-red-600 hover:text-red-800 font-medium">
                                            Remove
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                                            <select id="variation_size_{{ $index }}" name="variations[{{ $index }}][size]" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select Size</option>
                                                <option value="XS" {{ ($variation['size'] ?? '') == 'XS' ? 'selected' : '' }}>XS</option>
                                                <option value="S" {{ ($variation['size'] ?? '') == 'S' ? 'selected' : '' }}>S</option>
                                                <option value="M" {{ ($variation['size'] ?? '') == 'M' ? 'selected' : '' }}>M</option>
                                                <option value="L" {{ ($variation['size'] ?? '') == 'L' ? 'selected' : '' }}>L</option>
                                                <option value="XL" {{ ($variation['size'] ?? '') == 'XL' ? 'selected' : '' }}>XL</option>
                                                <option value="2XL" {{ ($variation['size'] ?? '') == '2XL' ? 'selected' : '' }}>2XL</option>
                                                <option value="3XL" {{ ($variation['size'] ?? '') == '3XL' ? 'selected' : '' }}>3XL</option>
                                                <option value="Custom" {{ ($variation['size'] ?? '') == 'Custom' ? 'selected' : '' }}>Custom</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                            <select id="variation_color_{{ $index }}" name="variations[{{ $index }}][color]" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select Color</option>
                                                <option value="Ecru" {{ ($variation['color'] ?? '') == 'Ecru' ? 'selected' : '' }}>Ecru</option>
                                                <option value="White" {{ ($variation['color'] ?? '') == 'White' ? 'selected' : '' }}>White</option>
                                                <option value="Beige" {{ ($variation['color'] ?? '') == 'Beige' ? 'selected' : '' }}>Beige</option>
                                                <option value="Black" {{ ($variation['color'] ?? '') == 'Black' ? 'selected' : '' }}>Black</option>
                                                <option value="Blue" {{ ($variation['color'] ?? '') == 'Blue' ? 'selected' : '' }}>Blue</option>
                                                <option value="Brown" {{ ($variation['color'] ?? '') == 'Brown' ? 'selected' : '' }}>Brown</option>
                                                <option value="Green" {{ ($variation['color'] ?? '') == 'Green' ? 'selected' : '' }}>Green</option>
                                                <option value="Red" {{ ($variation['color'] ?? '') == 'Red' ? 'selected' : '' }}>Red</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Price (PHP)</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                                <input type="number" id="variation_price_{{ $index }}" name="variations[{{ $index }}][price]" 
                                                       value="{{ $variation['price'] ?? '' }}" 
                                                       step="0.01" min="0" required autocomplete="off"
                                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                                            <input type="number" id="variation_stock_{{ $index }}" name="variations[{{ $index }}][stock]" 
                                                   value="{{ $variation['stock'] ?? '' }}" 
                                                   min="0" required autocomplete="off"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                            <input type="text" id="variation_sku_{{ $index }}" name="variations[{{ $index }}][sku]" 
                                                   value="{{ $variation['sku'] ?? '' }}" 
                                                   placeholder="Auto-generated" autocomplete="off"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status and Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Status and Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" id="is_available" name="is_available" value="1"
                               {{ old('is_available', $barongProduct->is_available ?? true ? 'checked' : '') }}
                               class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_available" class="ml-2 text-sm font-medium text-gray-700">Available for Sale</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                               {{ old('is_featured', $barongProduct->is_featured ?? false ? 'checked' : '') }}
                               class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700">Featured Product</label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.products') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    {{ isset($barongProduct) ? 'Update Product' : 'Create Product' }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Small & User-Friendly Attributes Modal -->
<div id="attributesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto border w-full max-w-xl shadow-xl rounded-lg bg-white max-h-[60vh] overflow-y-auto">
        <div class="p-5">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Product Attributes</h3>
                    <p class="text-xs text-gray-500 mt-1">Select fabric, embroidery, colors & details</p>
                </div>
                <button type="button" id="closeAttributesModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                <!-- User-Friendly Fabric Selection -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Fabric</label>
                    </div>
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_jusi" name="fabric[]" value="Jusi" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Jusi</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_pinya" name="fabric[]" value="Pinya" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Pinya</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_organza" name="fabric[]" value="Organza" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Organza</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_ramie" name="fabric[]" value="Ramie" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Ramie</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_silk_cocoon" name="fabric[]" value="Silk Cocoon" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Silk Cocoon</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_linen" name="fabric[]" value="Linen" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Linen</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_cotton" name="fabric[]" value="Cotton" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Cotton</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_other" name="fabric[]" value="Other" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Other</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Embroidery Selection -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Embroidery</label>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_u_shape" name="embroidery_style[]" value="U-Shape" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">U-Shape</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_full_front" name="embroidery_style[]" value="Full Front" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Full Front</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_computerized" name="embroidery_style[]" value="Computerized" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Computerized</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_hand_embroidered" name="embroidery_style[]" value="Hand-Embroidered" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Hand-Embroidered</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_calado" name="embroidery_style[]" value="Calado" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Calado</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="embroidery_burda" name="embroidery_style[]" value="Burda" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Burda</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Color Selection -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Colors</label>
                    </div>
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_ecru" name="colors[]" value="Ecru" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Ecru</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_white" name="colors[]" value="White" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">White</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_beige" name="colors[]" value="Beige" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Beige</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_black" name="colors[]" value="Black" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Black</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_blue" name="colors[]" value="Blue" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Blue</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_brown" name="colors[]" value="Brown" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Brown</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="color_green" name="colors[]" value="Green" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Green</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="color_red" name="colors[]" value="Red" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Red</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Collar Type -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Collar Type</label>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_chinese" name="collar_type" value="Chinese Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Chinese Collar</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_sport" name="collar_type" value="Sport Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Sport Collar</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_mandarin" name="collar_type" value="Mandarin Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Mandarin Collar</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_standing" name="collar_type" value="Standing Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Standing Collar</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Design Details -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Design Details</label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="design_side_slits" name="design_details[]" value="Side Slits" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Side Slits</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="design_buttons" name="design_details[]" value="Buttons" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Buttons</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="design_cufflinks" name="design_details[]" value="Cufflinks Ready" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Cufflinks Ready</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- User-Friendly Modal Footer -->
            <div class="flex justify-end space-x-2 mt-3 pt-3 border-t border-gray-200">
                <button type="button" id="cancelAttributes" 
                        class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded text-xs font-medium hover:bg-gray-200 transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" id="saveAttributes" 
                        class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Inline Notification System -->
<div id="notification-container" class="fixed right-4 z-50 space-y-2" style="top: 0px;">
    <!-- Notifications will be dynamically inserted here -->
</div>


<script>
// Position notification container below header dynamically
document.addEventListener('DOMContentLoaded', function() {
    const notificationContainer = document.getElementById('notification-container');
    if (notificationContainer && typeof positionNotificationBelowHeader === 'function') {
        positionNotificationBelowHeader(notificationContainer, 16);
    }
    
    // Recalculate position on window resize
    window.addEventListener('resize', () => {
        if (notificationContainer && typeof positionNotificationBelowHeader === 'function') {
            positionNotificationBelowHeader(notificationContainer, 16);
        }
    });
});

// Simple notification system
function showSuccess(title, message, duration = 5000) {
    showNotification('success', title, message, duration);
}

function showError(title, message, duration = 7000) {
    showNotification('error', title, message, duration);
}

function positionNotificationBelowHeader() {
    const container = document.getElementById('notification-container');
    const header = document.querySelector('header');
    
    if (container && header) {
        const headerHeight = header.getBoundingClientRect().height;
        container.style.top = `${headerHeight + 16}px`;
    }
}

function showNotification(type, title, message, duration) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    // Position notification below header
    positionNotificationBelowHeader();
    
    const notification = document.createElement('div');
    notification.className = `bg-white border-l-4 shadow-lg rounded-lg p-4 max-w-sm transform transition-all duration-300 ease-in-out translate-x-full opacity-0 ${
        type === 'success' ? 'border-green-500' : 
        type === 'error' ? 'border-red-500' : 
        'border-blue-500'
    }`;
    
    const iconColor = type === 'success' ? 'text-green-600' : 
                     type === 'error' ? 'text-red-600' : 'text-blue-600';
    const iconBg = type === 'success' ? 'bg-green-100' : 
                  type === 'error' ? 'bg-red-100' : 'bg-blue-100';
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center ${iconBg}">
                    <svg class="w-5 h-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' ? 
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
                        }
                    </svg>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">${title}</p>
                <p class="text-sm text-gray-500">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150" onclick="this.parentElement.parentElement.parentElement.remove()">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Auto remove
    if (duration > 0) {
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
}
</script>

<script>
    // Enhanced real-time stock calculation with comprehensive logging
    function calculateTotalStock() {
        console.log('=== STOCK CALCULATION START ===');
        
        const sizeInputs = document.querySelectorAll('.size-stock-input');
        console.log('Found size inputs:', sizeInputs.length);
        
        if (sizeInputs.length === 0) {
            console.error('❌ NO SIZE INPUTS FOUND! Check if .size-stock-input class exists');
            return;
        }
        
        let totalStock = 0;
        let sizeBreakdown = {};
        let hasErrors = false;
        
        console.log('Processing each input:');
        
        sizeInputs.forEach((input, index) => {
            const rawValue = input.value;
            let value = parseInt(rawValue) || 0;
            
            // Extract size from input ID (more reliable than name)
            const size = input.id.replace('size_stock_', '');
            
            console.log(`  Input ${index + 1} (${size}):`);
            console.log(`    Raw value: "${rawValue}"`);
            console.log(`    Parsed value: ${value}`);
            console.log(`    Input element:`, input);
            
            // Validate and correct negative values
            if (value < 0) {
                console.log(`    ⚠️ Negative value detected, correcting to 0`);
                value = 0;
                input.value = 0;
                input.classList.add('border-red-500', 'bg-red-50');
                hasErrors = true;
                
                // Show temporary error message
                showInputError(input, 'Negative values not allowed');
            } else if (value > 0) {
                console.log(`    ✅ Valid positive value`);
                input.classList.remove('border-red-500', 'bg-red-50');
                input.classList.add('border-green-300', 'bg-green-50');
                
                // Remove error message if exists
                removeInputError(input);
            } else {
                console.log(`    ⚪ Zero or empty value`);
                // Empty or zero value - neutral state
                input.classList.remove('border-red-500', 'bg-red-50', 'border-green-300', 'bg-green-50');
                removeInputError(input);
            }
            
            totalStock += value;
            sizeBreakdown[size] = value;
            
            console.log(`    Running total: ${totalStock}`);
        });
        
        console.log('Final calculation:');
        console.log(`  Total Stock: ${totalStock}`);
        console.log(`  Size Breakdown:`, sizeBreakdown);
        console.log(`  Has Errors: ${hasErrors}`);
        
        // Update display with animation
        const displayElement = document.getElementById('total-stock-display');
        const inputElement = document.getElementById('total-stock-input');
        
        console.log('Looking for display elements:');
        console.log(`  Display element found:`, !!displayElement);
        console.log(`  Input element found:`, !!inputElement);
        
        if (displayElement && inputElement) {
            console.log('✅ Updating display elements');
            
            // Add animation class
            displayElement.classList.add('animate-pulse');
            
            // Update values
            const oldDisplayValue = displayElement.textContent;
            const oldInputValue = inputElement.value;
            
            displayElement.textContent = totalStock;
            inputElement.value = totalStock;
            
            console.log(`  Display updated: "${oldDisplayValue}" → "${totalStock}"`);
            console.log(`  Hidden input updated: "${oldInputValue}" → "${totalStock}"`);
            
            // Update size breakdown display
            updateSizeBreakdown(sizeBreakdown);
            
            // Update stock status indicator
            // Stock status display disabled per UI request
            
            // Update visual feedback
            updateVisualFeedback(totalStock, hasErrors);
            
            // Remove animation after short delay
            setTimeout(() => {
                displayElement.classList.remove('animate-pulse');
                console.log('Animation removed');
            }, 300);
        } else {
            console.error('❌ DISPLAY ELEMENTS NOT FOUND!');
            console.log('Available elements with similar IDs:');
            console.log('  total-stock-display:', document.getElementById('total-stock-display'));
            console.log('  total-stock-input:', document.getElementById('total-stock-input'));
        }
        
        console.log('=== STOCK CALCULATION END ===');
        return { totalStock, sizeBreakdown, hasErrors };
    }
    
    // Show input error message
    function showInputError(input, message) {
        let errorElement = input.parentNode.querySelector('.input-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'input-error text-xs text-red-600 mt-1';
            input.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
        
        // Auto-remove error after 2 seconds
        setTimeout(() => {
            removeInputError(input);
        }, 2000);
    }
    
    // Remove input error message
    function removeInputError(input) {
        const errorElement = input.parentNode.querySelector('.input-error');
        if (errorElement) {
            errorElement.remove();
        }
        input.classList.remove('border-red-500', 'bg-red-50');
    }
    
    // Update visual feedback based on stock levels
    function updateVisualFeedback(totalStock, hasErrors) {
        const displayElement = document.getElementById('total-stock-display');
        const container = displayElement.closest('.bg-gradient-to-r');
        
        // Remove existing classes
        container.classList.remove('from-red-50', 'to-red-100', 'border-red-200');
        container.classList.remove('from-yellow-50', 'to-yellow-100', 'border-yellow-200');
        container.classList.remove('from-green-50', 'to-green-100', 'border-green-200');
        
        if (hasErrors) {
            container.classList.add('from-red-50', 'to-red-100', 'border-red-200');
            displayElement.classList.remove('text-blue-600', 'text-yellow-600', 'text-green-600');
            displayElement.classList.add('text-red-600');
        } else if (totalStock === 0) {
            container.classList.add('from-red-50', 'to-red-100', 'border-red-200');
            displayElement.classList.remove('text-blue-600', 'text-yellow-600', 'text-green-600');
            displayElement.classList.add('text-red-600');
        } else if (totalStock < 10) {
            container.classList.add('from-yellow-50', 'to-yellow-100', 'border-yellow-200');
            displayElement.classList.remove('text-blue-600', 'text-red-600', 'text-green-600');
            displayElement.classList.add('text-yellow-600');
        } else {
            container.classList.add('from-green-50', 'to-green-100', 'border-green-200');
            displayElement.classList.remove('text-blue-600', 'text-red-600', 'text-yellow-600');
            displayElement.classList.add('text-green-600');
        }
    }
    
    // Size breakdown display disabled per UI request
    function updateSizeBreakdown() {
        const breakdownElement = document.getElementById('size-breakdown');
        if (breakdownElement) breakdownElement.style.display = 'none';
    }
    
    // Update stock status indicator
    function updateStockStatus(totalStock) {
        let statusElement = document.getElementById('stock-status');
        if (!statusElement) {
            // Create status element if it doesn't exist
            const totalStockContainer = document.querySelector('.bg-gradient-to-r');
            if (totalStockContainer) {
                statusElement = document.createElement('div');
                statusElement.id = 'stock-status';
                statusElement.className = 'mt-1 text-xs font-medium';
                totalStockContainer.appendChild(statusElement);
            }
        }
        
        if (statusElement) {
            if (totalStock === 0) {
                statusElement.textContent = '⚠️ Out of Stock';
                statusElement.className = 'mt-1 text-xs font-medium text-red-600';
            } else if (totalStock < 10) {
                statusElement.textContent = '⚠️ Low Stock';
                statusElement.className = 'mt-1 text-xs font-medium text-yellow-600';
            } else {
                statusElement.textContent = '✅ In Stock';
                statusElement.className = 'mt-1 text-xs font-medium text-green-600';
            }
        }
    }
    
    // Test function to verify calculation is working
    window.testStockCalculation = function() {
        console.log('🧪 MANUAL TEST: Testing stock calculation...');
        calculateTotalStock();
    };
    
    // Comprehensive diagnostic function
    window.diagnoseStockCalculation = function() {
        console.log('🔍 === DIAGNOSTIC REPORT ===');
        
        // Check if function exists
        console.log('1. Function exists:', typeof calculateTotalStock === 'function');
        
        // Check DOM elements
        const sizeInputs = document.querySelectorAll('.size-stock-input');
        console.log('2. Size inputs found:', sizeInputs.length);
        
        if (sizeInputs.length > 0) {
            console.log('3. Size input details:');
            sizeInputs.forEach((input, index) => {
                console.log(`   Input ${index + 1}:`, {
                    id: input.id,
                    name: input.name,
                    value: input.value,
                    className: input.className,
                    element: input
                });
            });
        }
        
        // Check display elements
        const displayElement = document.getElementById('total-stock-display');
        const inputElement = document.getElementById('total-stock-input');
        console.log('4. Display elements:');
        console.log('   total-stock-display:', !!displayElement, displayElement);
        console.log('   total-stock-input:', !!inputElement, inputElement);
        
        // Check event listeners
        console.log('5. Event listeners check:');
        if (sizeInputs.length > 0) {
            const firstInput = sizeInputs[0];
            console.log('   First input onchange:', firstInput.onchange);
            console.log('   First input oninput:', firstInput.oninput);
            console.log('   First input onkeyup:', firstInput.onkeyup);
        }
        
        // Test calculation
        console.log('6. Running test calculation...');
        const result = calculateTotalStock();
        console.log('   Result:', result);
        
        console.log('🔍 === DIAGNOSTIC COMPLETE ===');
        return {
            functionExists: typeof calculateTotalStock === 'function',
            inputsFound: sizeInputs.length,
            displayElementsFound: !!(displayElement && inputElement),
            testResult: result
        };
    };

// Global form submission handler
function handleFormSubmit(event) {
    console.log('🚀 Form submission intercepted by handleFormSubmit');
    event.preventDefault();
    event.stopPropagation();
    
    // Call the main form submission logic
    submitProductForm();
    return false; // Prevent default form submission
}

function submitProductForm() {
    console.log('📝 Starting product form submission...');
    
    const form = document.getElementById('barongProductForm');
    if (!form) {
        console.error('❌ Form not found!');
        return;
    }
    
    const formData = new FormData(form);
    
    // Add uploaded images to FormData
    if (typeof uploadedImages !== 'undefined') {
        uploadedImages.forEach((imageData, index) => {
            formData.append(`new_images[${index}]`, imageData.file);
        });
    }
    
    // Debug: Log form data
    console.log('📋 Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    const url = form.action || '{{ isset($barongProduct) ? route("admin.products.update", $barongProduct->id) : route("admin.products.store") }}';
    const method = 'POST';
    
    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', response.headers);
        
        if (response.status === 302) {
            console.error('❌ Server redirected (302)');
            throw new Error('Server redirected (302). This usually means validation failed or authentication issue.');
        }
        
        if (response.status === 422) {
            console.error('❌ Validation error (422)');
            return response.json().then(data => {
                console.error('Validation errors:', data);
                throw new Error('Validation failed: ' + JSON.stringify(data.errors || data.message));
            });
        }
        
        const contentType = response.headers.get('content-type');
        console.log('📄 Content-Type:', contentType);
        
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('❌ Non-JSON response received:', text.substring(0, 500));
                throw new Error('Server returned non-JSON response. Check console for details.');
            });
        }
    })
    .then(data => {
        console.log('✅ Success response received:', data);
        
        if (data.success) {
            // Show success notification
            if (typeof showSuccess === 'function') {
                showSuccess(
                    '🎉 Product Created Successfully!',
                    'Your barong product has been added to the inventory and is now visible on the homepage.',
                    5000
                );
            } else {
                alert('Product created successfully!');
            }
            
            // Redirect after a short delay
            setTimeout(() => {
                console.log('🔄 Redirecting to inventory page...');
                try {
                    window.location.href = '{{ route("admin.inventory") }}';
                } catch (error) {
                    console.error('❌ Route generation failed:', error);
                    window.location.href = '/admin/inventory';
                }
            }, 2000);
        } else {
            console.error('❌ Server returned success=false:', data);
            if (typeof showError === 'function') {
                showError(
                    'Error Creating Product',
                    data.message || 'Unknown error occurred. Please try again.',
                    7000
                );
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        if (typeof showError === 'function') {
            showError(
                'Network Error',
                'An error occurred while saving the product. Please check your connection and try again.',
                7000
            );
        } else {
            alert('Network Error: ' + error.message);
        }
    });
}
</script>

@push('scripts')
<script>
    // Global test function for debugging
    window.testModalOpen = function() {
        console.log('🧪 Test function called');
        const modal = document.getElementById('attributesModal');
        if (modal) {
            console.log('✅ Modal found, opening...');
            modal.classList.remove('hidden');
        } else {
            console.error('❌ Modal not found');
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 DOM Content Loaded - Initializing image upload and attributes...');
        console.log('🔧 Testing form submission handler...');
        
        // Test if form exists
        const testForm = document.getElementById('barongProductForm');
        if (testForm) {
            console.log('✅ Form found for testing:', testForm);
        } else {
            console.error('❌ Form not found for testing');
        }
        
        // Test if notification functions exist
        if (typeof showSuccess === 'function') {
            console.log('✅ showSuccess function exists');
        } else {
            console.error('❌ showSuccess function not found');
        }
        
        if (typeof showError === 'function') {
            console.log('✅ showError function exists');
        } else {
            console.error('❌ showError function not found');
        }
    
    // Image upload functionality
    const dropZone = document.getElementById('dropZone');
    const imageInput = document.getElementById('imageInput');
    // Upload button removed - using visible file input instead
    const imagePreviews = document.getElementById('imagePreviews');
    let uploadedImages = [];
    let coverImageIndex = null;
    
    console.log('📸 Image upload elements:', {
        dropZone: !!dropZone,
        imageInput: !!imageInput,
        imagePreviews: !!imagePreviews
    });

    // Drag and drop functionality
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            const files = Array.from(e.dataTransfer.files);
            handleImageFiles(files);
        });

        // Click to upload - make entire drop zone clickable
        dropZone.addEventListener('click', function(e) {
            // Don't trigger if clicking on the button or any of its children
            if (e.target.id !== 'uploadBtn' && e.target.closest('#uploadBtn') === null && e.target.id !== 'imageInput') {
                imageInput.click();
            }
        });
    } else {
        console.error('❌ Drop zone element not found!');
    }

    // Upload button removed - using visible file input instead

    if (imageInput) {
        console.log('✅ Setting up image input event listener');
        imageInput.addEventListener('change', function(e) {
            console.log('📸 Image input changed, files selected:', e.target.files.length);
            const files = Array.from(e.target.files);
            handleImageFiles(files);
        });
    } else {
        console.error('❌ Image input element not found!');
    }

    function handleImageFiles(files) {
        console.log('📁 Handling image files:', files.length);
        const imageFiles = files.filter(file => file.type.startsWith('image/'));
        console.log('🖼️ Valid image files:', imageFiles.length);
        
        if (imageFiles.length + uploadedImages.length > 8) {
            alert('Maximum 8 images allowed');
            return;
        }

        imageFiles.forEach((file, fileIndex) => {
            if (file.size > 2 * 1024 * 1024) {
                alert(`File ${file.name} is too large. Maximum size is 2MB.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const imageData = {
                    file: file,
                    url: e.target.result,
                    index: uploadedImages.length
                };
                uploadedImages.push(imageData);
                displayImagePreview(imageData);
            };
            reader.onerror = function(e) {
                console.error('Error reading file:', file.name, e);
            };
            reader.readAsDataURL(file);
        });
    }

    function displayImagePreview(imageData) {
        console.log('🖼️ Displaying image preview for:', imageData.index);
        const imagePreviewsContainer = document.getElementById('imagePreviews');
        
        if (!imagePreviewsContainer) {
            console.error('❌ imagePreviews container not found');
            return;
        }
        
        console.log('✅ Image previews container found');
        
        const previewDiv = document.createElement('div');
        previewDiv.className = 'image-preview-item relative group';
        previewDiv.setAttribute('data-image-index', imageData.index);
        
        // Create square container for image
        const imgContainer = document.createElement('div');
        imgContainer.className = 'w-20 h-20 rounded-lg border-2 border-gray-200 overflow-hidden bg-gray-100';
        
        const img = document.createElement('img');
        img.src = imageData.url;
        img.alt = 'Product Image';
        img.className = 'w-full h-full object-cover';
        
        imgContainer.appendChild(img);
        
        // Create action buttons container
        const actionsContainer = document.createElement('div');
        actionsContainer.className = 'absolute inset-0 bg-gray-800 bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex flex-col items-center justify-center space-y-1';
        
        const coverBtn = document.createElement('button');
        coverBtn.textContent = 'Set Cover';
        coverBtn.className = 'bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 text-xs rounded font-medium';
        coverBtn.onclick = () => setCoverImage(imageData.index);
        
        const removeBtn = document.createElement('button');
        removeBtn.textContent = 'Remove';
        removeBtn.className = 'bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded font-medium';
        removeBtn.onclick = () => removeImage(imageData.index);
        
        actionsContainer.appendChild(coverBtn);
        actionsContainer.appendChild(removeBtn);
        
        previewDiv.appendChild(imgContainer);
        previewDiv.appendChild(actionsContainer);
        
        imagePreviewsContainer.appendChild(previewDiv);
        
        // Hide debug placeholder
        const debugPlaceholder = document.getElementById('debugPlaceholder');
        if (debugPlaceholder) {
            debugPlaceholder.style.display = 'none';
        }
        
        updateImageCounter();
    }

    function updateImageCounter() {
        const imageCounter = document.getElementById('imageCounter');
        const imageCount = document.getElementById('imageCount');
        const totalImages = document.querySelectorAll('.image-preview-item').length;
        
        if (totalImages > 0) {
            imageCounter.classList.remove('hidden');
            imageCount.textContent = totalImages;
        } else {
            imageCounter.classList.add('hidden');
        }
    }

    function removeImage(index) {
        // Remove from uploadedImages array
        uploadedImages = uploadedImages.filter((img, i) => i !== index);
        
        // Remove from DOM
        const imageElement = document.querySelector(`[data-image-index="${index}"]`);
        if (imageElement) {
            imageElement.remove();
        }
        
        // Update counter
        updateImageCounter();
        
        // Reset cover image if it was the removed one
        if (coverImageIndex === index) {
            coverImageIndex = null;
        }
    }

    function setCoverImage(index) {
        // Remove cover styling from all images
        document.querySelectorAll('.image-preview-item img').forEach(img => {
            img.classList.remove('border-blue-500');
            img.classList.add('border-gray-200');
        });
        
        // Add cover styling to selected image
        const selectedImg = document.querySelector(`[data-image-index="${index}"] img`);
        if (selectedImg) {
            selectedImg.classList.remove('border-gray-200');
            selectedImg.classList.add('border-blue-500');
        }
        
        coverImageIndex = index;
    }

    // Initialize counter on page load
    updateImageCounter();

    // Video functionality
    const enableVideoCheckbox = document.getElementById('enable_video');
    const videoSection = document.getElementById('videoSection');
    const videoUrlInput = document.getElementById('video_url');
    const videoPreview = document.getElementById('videoPreview');
    const videoFrame = document.getElementById('videoFrame');

    enableVideoCheckbox.addEventListener('change', function() {
        if (this.checked) {
            videoSection.classList.remove('hidden');
        } else {
            videoSection.classList.add('hidden');
            videoPreview.classList.add('hidden');
        }
    });

    videoUrlInput.addEventListener('input', function() {
        const url = this.value;
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            const videoId = extractYouTubeId(url);
            if (videoId) {
                videoFrame.src = `https://www.youtube.com/embed/${videoId}`;
                videoPreview.classList.remove('hidden');
            }
        } else if (url.includes('vimeo.com')) {
            const videoId = extractVimeoId(url);
            if (videoId) {
                videoFrame.src = `https://player.vimeo.com/video/${videoId}`;
                videoPreview.classList.remove('hidden');
            }
        } else {
            videoPreview.classList.add('hidden');
        }
    });

    // Attributes modal functionality - MOVED INSIDE DOMContentLoaded
    console.log('🎨 Initializing attributes modal functionality...');
    
    const setAttributesBtn = document.getElementById('setAttributesBtn');
    const attributesModal = document.getElementById('attributesModal');
    const closeAttributesModal = document.getElementById('closeAttributesModal');
    const cancelAttributes = document.getElementById('cancelAttributes');
    const saveAttributes = document.getElementById('saveAttributes');
    const selectedAttributes = document.getElementById('selectedAttributes');
    
    console.log('🎨 Attributes elements:', {
        setAttributesBtn: !!setAttributesBtn,
        attributesModal: !!attributesModal,
        closeAttributesModal: !!closeAttributesModal,
        cancelAttributes: !!cancelAttributes,
        saveAttributes: !!saveAttributes,
        selectedAttributes: !!selectedAttributes
    });

    // Additional debugging
    console.log('🔍 Button element details:', setAttributesBtn);
    console.log('🔍 Modal element details:', attributesModal);
    
    // Test if we can manually trigger the modal
    if (setAttributesBtn) {
        console.log('✅ Button found, adding additional test listener');
        setAttributesBtn.addEventListener('click', function(e) {
            console.log('🎯 Additional click listener triggered');
        });
    }

    function openAttributesModal() {
        console.log('🎨 Opening attributes modal...');
        if (!attributesModal) {
            console.error('❌ Attributes modal not found');
            return;
        }
        attributesModal.classList.remove('hidden');
        console.log('✅ Attributes modal opened');
    }
    
    if (setAttributesBtn && attributesModal) {
        console.log('✅ Setting up attributes modal event listeners...');
        
        // Ensure click always opens even if other listeners interfere
        setAttributesBtn.addEventListener('click', function(e){
            console.log('🎨 Set Attributes button clicked');
            e.preventDefault();
            e.stopPropagation();
            openAttributesModal();
        }, { capture: true });

        if (closeAttributesModal) {
            closeAttributesModal.addEventListener('click', function() {
                console.log('🎨 Closing attributes modal');
                attributesModal.classList.add('hidden');
            });
        }

        if (cancelAttributes) {
            cancelAttributes.addEventListener('click', function() {
                console.log('🎨 Canceling attributes modal');
                attributesModal.classList.add('hidden');
            });
        }

        if (saveAttributes) {
            saveAttributes.addEventListener('click', function() {
                console.log('🎨 Saving attributes...');
                const fabric = Array.from(document.querySelectorAll('input[name="fabric[]"]:checked')).map(cb => cb.value);
                const embroidery = Array.from(document.querySelectorAll('input[name="embroidery_style[]"]:checked')).map(cb => cb.value);
                const colors = Array.from(document.querySelectorAll('input[name="colors[]"]:checked')).map(cb => cb.value);
                const collarType = document.querySelector('input[name="collar_type"]:checked')?.value || '';
                const designDetails = Array.from(document.querySelectorAll('input[name="design_details[]"]:checked')).map(cb => cb.value);

                console.log('🎨 Selected attributes:', { fabric, embroidery, colors, collarType, designDetails });

                updateHiddenInputs('fabric', fabric);
                updateHiddenInputs('embroidery_style', embroidery);
                updateHiddenInputs('colors', colors);
                updateHiddenInputs('collar_type', [collarType]);
                updateHiddenInputs('design_details', designDetails);

                // Update display
                updateAttributesDisplay(fabric, embroidery, colors, collarType, designDetails);

                attributesModal.classList.add('hidden');
                console.log('✅ Attributes saved and modal closed');
            });
        }
    } else {
        console.error('❌ Set Attributes button or modal not found:', {
            setAttributesBtn: !!setAttributesBtn,
            attributesModal: !!attributesModal
        });
    }

    function extractYouTubeId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    function extractVimeoId(url) {
        const regExp = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/)|(album\/\d+\/video\/)|(video\/)|)(\w+)(\/.*)?$/;
        const match = url.match(regExp);
        return match ? match[7] : null;
    }

    function updateHiddenInputs(name, values) {
        // Remove existing hidden inputs
        document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
            if (input.type === 'hidden') input.remove();
        });

        // Add new hidden inputs
        values.forEach(value => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `${name}[]`;
            input.value = value;
            document.getElementById('barongProductForm').appendChild(input);
        });
    }

    function updateAttributesDisplay(fabric, embroidery, colors, collarType, designDetails) {
        let html = '';
        
        if (fabric.length > 0 || embroidery.length > 0 || colors.length > 0 || collarType || designDetails.length > 0) {
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            
            if (fabric.length > 0) {
                html += `<div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="font-medium text-gray-700 text-sm">Fabric</span>
                    </div>
                    <div class="flex flex-wrap gap-2 ml-4">
                        ${fabric.map(f => `<span class="bg-blue-100 text-blue-800 px-3 py-1 text-xs rounded-full font-medium">${f}</span>`).join('')}
                    </div>
                </div>`;
            }
            
            if (embroidery.length > 0) {
                html += `<div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="font-medium text-gray-700 text-sm">Embroidery</span>
                    </div>
                    <div class="flex flex-wrap gap-2 ml-4">
                        ${embroidery.map(e => `<span class="bg-green-100 text-green-800 px-3 py-1 text-xs rounded-full font-medium">${e}</span>`).join('')}
                    </div>
                </div>`;
            }
            
            if (colors.length > 0) {
                html += `<div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <span class="font-medium text-gray-700 text-sm">Colors</span>
                    </div>
                    <div class="flex flex-wrap gap-2 ml-4">
                        ${colors.map(c => `<span class="bg-purple-100 text-purple-800 px-3 py-1 text-xs rounded-full font-medium">${c}</span>`).join('')}
                    </div>
                </div>`;
            }

            if (collarType) {
                html += `<div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                        <span class="font-medium text-gray-700 text-sm">Collar Type</span>
                    </div>
                    <div class="flex flex-wrap gap-2 ml-4">
                        <span class="bg-orange-100 text-orange-800 px-3 py-1 text-xs rounded-full font-medium">${collarType}</span>
                    </div>
                </div>`;
            }

            if (designDetails.length > 0) {
                html += `<div class="space-y-2 md:col-span-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                        <span class="font-medium text-gray-700 text-sm">Design Details</span>
                    </div>
                    <div class="flex flex-wrap gap-2 ml-4">
                        ${designDetails.map(d => `<span class="bg-indigo-100 text-indigo-800 px-3 py-1 text-xs rounded-full font-medium">${d}</span>`).join('')}
                    </div>
                </div>`;
            }
            
            html += '</div>';
        } else {
            html = `<div class="text-center py-8">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No attributes configured yet</p>
                <p class="text-gray-400 text-xs mt-1">Click "Set Attributes" to add fabric, embroidery, colors, and other details</p>
            </div>`;
        }

        selectedAttributes.innerHTML = html;
    }

    // Variations functionality
    const hasVariationsCheckbox = document.getElementById('has_variations');
    const variationsSection = document.getElementById('variationsSection');
    const stockSection = document.getElementById('stockSection');
    const addVariationBtn = document.getElementById('addVariationBtn');
    const variationsContainer = document.getElementById('variationsContainer');
    let variationIndex = {{ isset($barongProduct) && $barongProduct->variations ? count($barongProduct->variations) : 0 }};

    hasVariationsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            variationsSection.classList.remove('hidden');
            stockSection.classList.add('hidden');
            document.getElementById('stock').required = false;
        } else {
            variationsSection.classList.add('hidden');
            stockSection.classList.remove('hidden');
            document.getElementById('stock').required = true;
        }
    });

    addVariationBtn.addEventListener('click', function() {
        addVariationRow();
    });

    function addVariationRow() {
        const variationDiv = document.createElement('div');
        variationDiv.className = 'variation-item border border-gray-200 rounded-lg p-4 bg-gray-50';
        variationDiv.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-medium text-gray-900">Variation ${variationIndex + 1}</h4>
                <button type="button" onclick="removeVariation(this)" 
                        class="text-red-600 hover:text-red-800 font-medium">
                    Remove
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                    <select id="variation_size_${variationIndex}" name="variations[${variationIndex}][size]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Size</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="2XL">2XL</option>
                        <option value="3XL">3XL</option>
                        <option value="Custom">Custom</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <select id="variation_color_${variationIndex}" name="variations[${variationIndex}][color]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Color</option>
                        <option value="Ecru">Ecru</option>
                        <option value="White">White</option>
                        <option value="Beige">Beige</option>
                        <option value="Black">Black</option>
                        <option value="Blue">Blue</option>
                        <option value="Brown">Brown</option>
                        <option value="Green">Green</option>
                        <option value="Red">Red</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (PHP)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₱</span>
                        <input type="number" id="variation_price_${variationIndex}" name="variations[${variationIndex}][price]" 
                               step="0.01" min="0" required autocomplete="off"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <input type="number" id="variation_stock_${variationIndex}" name="variations[${variationIndex}][stock]" 
                           min="0" required autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                    <input type="text" id="variation_sku_${variationIndex}" name="variations[${variationIndex}][sku]" 
                           placeholder="Auto-generated" autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        `;
        
        variationsContainer.appendChild(variationDiv);
        variationIndex++;
    }

    // Special price toggle
    const enableSpecialPriceCheckbox = document.getElementById('enable_special_price');
    const specialPriceSection = document.getElementById('specialPriceSection');

    enableSpecialPriceCheckbox.addEventListener('change', function() {
        if (this.checked) {
            specialPriceSection.classList.remove('hidden');
        } else {
            specialPriceSection.classList.add('hidden');
        }
    });

    // Initialize total stock calculation on page load
        console.log('=== STOCK CALCULATION START ===');
        
        const sizeInputs = document.querySelectorAll('.size-stock-input');
        console.log('Found size inputs:', sizeInputs.length);
        
        if (sizeInputs.length === 0) {
            console.error('❌ NO SIZE INPUTS FOUND! Check if .size-stock-input class exists');
            return;
        }
        
        let totalStock = 0;
        let sizeBreakdown = {};
        let hasErrors = false;
        
        console.log('Processing each input:');
        
        sizeInputs.forEach((input, index) => {
            const rawValue = input.value;
            let value = parseInt(rawValue) || 0;
            
            // Extract size from input ID (more reliable than name)
            const size = input.id.replace('size_stock_', '');
            
            console.log(`  Input ${index + 1} (${size}):`);
            console.log(`    Raw value: "${rawValue}"`);
            console.log(`    Parsed value: ${value}`);
            console.log(`    Input element:`, input);
            
            // Validate and correct negative values
            if (value < 0) {
                console.log(`    ⚠️ Negative value detected, correcting to 0`);
                value = 0;
                input.value = 0;
                input.classList.add('border-red-500', 'bg-red-50');
                hasErrors = true;
                
                // Show temporary error message
                showInputError(input, 'Negative values not allowed');
            } else if (value > 0) {
                console.log(`    ✅ Valid positive value`);
                input.classList.remove('border-red-500', 'bg-red-50');
                input.classList.add('border-green-300', 'bg-green-50');
                
                // Remove error message if exists
                removeInputError(input);
            } else {
                console.log(`    ⚪ Zero or empty value`);
                // Empty or zero value - neutral state
                input.classList.remove('border-red-500', 'bg-red-50', 'border-green-300', 'bg-green-50');
                removeInputError(input);
            }
            
            totalStock += value;
            sizeBreakdown[size] = value;
            
            console.log(`    Running total: ${totalStock}`);
        });
        
        console.log('Final calculation:');
        console.log(`  Total Stock: ${totalStock}`);
        console.log(`  Size Breakdown:`, sizeBreakdown);
        console.log(`  Has Errors: ${hasErrors}`);
        
        // Update display with animation
        const displayElement = document.getElementById('total-stock-display');
        const inputElement = document.getElementById('total-stock-input');
        
        console.log('Looking for display elements:');
        console.log(`  Display element found:`, !!displayElement);
        console.log(`  Input element found:`, !!inputElement);
        
        if (displayElement && inputElement) {
            console.log('✅ Updating display elements');
            
            // Add animation class
            displayElement.classList.add('animate-pulse');
            
            // Update values
            const oldDisplayValue = displayElement.textContent;
            const oldInputValue = inputElement.value;
            
            displayElement.textContent = totalStock;
            inputElement.value = totalStock;
            
            console.log(`  Display updated: "${oldDisplayValue}" → "${totalStock}"`);
            console.log(`  Hidden input updated: "${oldInputValue}" → "${totalStock}"`);
            
            // Update size breakdown display
            updateSizeBreakdown(sizeBreakdown);
            
            // Update stock status indicator
            // Stock status display disabled per UI request
            
            // Update visual feedback
            updateVisualFeedback(totalStock, hasErrors);
            
            // Remove animation after short delay
            setTimeout(() => {
                displayElement.classList.remove('animate-pulse');
                console.log('Animation removed');
            }, 300);
        } else {
            console.error('❌ DISPLAY ELEMENTS NOT FOUND!');
            console.log('Available elements with similar IDs:');
            console.log('  total-stock-display:', document.getElementById('total-stock-display'));
            console.log('  total-stock-input:', document.getElementById('total-stock-input'));
        }
        
        console.log('=== STOCK CALCULATION END ===');
        return { totalStock, sizeBreakdown, hasErrors };
    }
    
    // Show input error message
    function showInputError(input, message) {
        let errorElement = input.parentNode.querySelector('.input-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'input-error text-xs text-red-600 mt-1';
            input.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
        
        // Auto-remove error after 2 seconds
        setTimeout(() => {
            removeInputError(input);
        }, 2000);
    }
    
    // Remove input error message
    function removeInputError(input) {
        const errorElement = input.parentNode.querySelector('.input-error');
        if (errorElement) {
            errorElement.remove();
        }
        input.classList.remove('border-red-500', 'bg-red-50');
    }
    
    // Update visual feedback based on stock levels
    function updateVisualFeedback(totalStock, hasErrors) {
        const displayElement = document.getElementById('total-stock-display');
        const container = displayElement.closest('.bg-gradient-to-r');
        
        // Remove existing classes
        container.classList.remove('from-red-50', 'to-red-100', 'border-red-200');
        container.classList.remove('from-yellow-50', 'to-yellow-100', 'border-yellow-200');
        container.classList.remove('from-green-50', 'to-green-100', 'border-green-200');
        
        if (hasErrors) {
            container.classList.add('from-red-50', 'to-red-100', 'border-red-200');
            displayElement.classList.remove('text-blue-600', 'text-yellow-600', 'text-green-600');
            displayElement.classList.add('text-red-600');
        } else if (totalStock === 0) {
            container.classList.add('from-red-50', 'to-red-100', 'border-red-200');
            displayElement.classList.remove('text-blue-600', 'text-yellow-600', 'text-green-600');
            displayElement.classList.add('text-red-600');
        } else if (totalStock < 10) {
            container.classList.add('from-yellow-50', 'to-yellow-100', 'border-yellow-200');
            displayElement.classList.remove('text-blue-600', 'text-red-600', 'text-green-600');
            displayElement.classList.add('text-yellow-600');
        } else {
            container.classList.add('from-green-50', 'to-green-100', 'border-green-200');
            displayElement.classList.remove('text-blue-600', 'text-red-600', 'text-yellow-600');
            displayElement.classList.add('text-green-600');
        }
    }
    
    // Test function to verify calculation is working
    // Initialize total stock calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 DOM Content Loaded - Initializing stock calculation...');
        
        // Wait a bit for all elements to be fully rendered
        setTimeout(() => {
            console.log('⏰ Initialization timeout reached, checking elements...');
            
            const sizeInputs = document.querySelectorAll('.size-stock-input');
            console.log('📊 Found size inputs:', sizeInputs.length);
            
            if (sizeInputs.length > 0) {
                console.log('✅ Size inputs found, running initial calculation');
                calculateTotalStock();
                console.log('✅ Initial stock calculation completed');
            } else {
                console.error('❌ NO SIZE INPUTS FOUND!');
                console.log('Available input elements:', document.querySelectorAll('input[type="number"]'));
                console.log('Available elements with "size" in ID:', document.querySelectorAll('[id*="size"]'));
            }
            
            // Also run diagnostic
            console.log('🔍 Running diagnostic...');
            diagnoseStockCalculation();
        }, 100);
    });

        // Form submission
        const form = document.getElementById('barongProductForm');

        if (form) {
            console.log('✅ Form element found:', form);
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('🚀 Form submission intercepted by JavaScript');
            
            const formData = new FormData(form);
        
        // Add uploaded images to FormData
        uploadedImages.forEach((imageData, index) => {
            formData.append(`new_images[${index}]`, imageData.file);
        });
        
        // Debug: Log form data
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        const url = form.action || '{{ isset($barongProduct) ? route("admin.products.update", $barongProduct->id) : route("admin.products.store") }}';
        const method = 'POST'; // Always use POST for fetch with FormData

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json' // Ensure we expect JSON response
            }
        })
        .then(response => {
            console.log('📡 Response status:', response.status);
            console.log('📡 Response headers:', response.headers);
            
            // Handle different response types
            if (response.status === 302) {
                console.error('❌ Server redirected (302)');
                throw new Error('Server redirected (302). This usually means validation failed or authentication issue.');
            }
            
            if (response.status === 422) {
                console.error('❌ Validation error (422)');
                return response.json().then(data => {
                    console.error('Validation errors:', data);
                    throw new Error('Validation failed: ' + JSON.stringify(data.errors || data.message));
                });
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            console.log('📄 Content-Type:', contentType);
            
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, get text to see what we received
                return response.text().then(text => {
                    console.error('❌ Non-JSON response received:', text.substring(0, 500));
                    throw new Error('Server returned non-JSON response. Check console for details.');
                });
            }
        })
        .then(data => {
            console.log('✅ Success response received:', data);
            
            if (data.success) {
                // Show success notification (simplified to avoid any parsing issues)
                if (typeof showSuccess === 'function') {
                    showSuccess('Product Created Successfully', 'Your barong product has been added to the inventory.', 5000);
                } else {
                    alert('Product created successfully');
                }
                
                // Redirect after a short delay to let user see the notification
                setTimeout(() => {
                    console.log('🔄 Redirecting to inventory page...');
                    try {
                        window.location.href = '{{ route("admin.inventory") }}';
                    } catch (error) {
                        console.error('❌ Route generation failed:', error);
                        // Fallback URL
                        window.location.href = '/admin/inventory';
                    }
                }, 2000);
            } else {
                console.error('❌ Server returned success=false:', data);
                showError(
                    'Error Creating Product',
                    data.message || 'Unknown error occurred. Please try again.',
                    7000
                );
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(
                'Network Error',
                'An error occurred while saving the product. Please check your connection and try again.',
                7000
            );
        });
    } else {
        console.error('❌ Form element not found!');
        // Fallback: if form is not found, the form will submit normally
        // This should not happen, but provides a safety net
    }

        // Additional safety: Prevent any form submission that might bypass our handler
        document.addEventListener('submit', function(e) {
            if (e.target.id === 'barongProductForm') {
                console.log('🛡️ Additional form submission intercepted');
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
});
</script>
@endpush
