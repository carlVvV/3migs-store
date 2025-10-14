{{-- Variations Section --}}
{{-- File: resources/views/admin/partials/product-form/variations.blade.php --}}

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
