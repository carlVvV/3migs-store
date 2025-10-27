{{-- Pricing and Stock Section --}}
{{-- File: resources/views/admin/partials/product-form/pricing-stock.blade.php --}}

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Pricing and Stock</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Pricing and Settings -->
        <div class="space-y-6">
            <!-- Base Price -->
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
            
            <!-- Special Price (Optional) -->
            <div>
                <label for="special_price" class="block text-sm font-medium text-gray-700 mb-2">Special Price (PHP) <span class="text-gray-500">(Optional)</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">₱</span>
                    <input type="number" id="special_price" name="special_price" 
                           value="{{ old('special_price', $barongProduct->special_price ?? '') }}" 
                           step="0.01" min="0" autocomplete="off"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="mt-1 text-xs text-gray-500">Sale price shown to customers</p>
            </div>
            
            <!-- Wholesale Price -->
            <div>
                <label for="wholesale_price" class="block text-sm font-medium text-gray-700 mb-2">Wholesale Price (PHP) <span class="text-gray-500">(Optional)</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">₱</span>
                    <input type="number" id="wholesale_price" name="wholesale_price" 
                           value="{{ old('wholesale_price', $barongProduct->wholesale_price ?? '') }}" 
                           step="0.01" min="0" autocomplete="off"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="mt-1 text-xs text-gray-500">Applies when customer orders 20+ items of any product</p>
            </div>
            
            <!-- Status and Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Status and Settings</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" id="is_available" name="is_available" value="1"
                               {{ old('is_available', $barongProduct->is_available ?? true ? 'checked' : '') }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_available" class="ml-2 text-sm font-medium text-gray-700">Available for Sale</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                               {{ old('is_featured', $barongProduct->is_featured ?? false ? 'checked' : '') }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700">Featured Product</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="hidden" name="is_new_arrival" value="0">
                        <input type="checkbox" id="is_new_arrival" name="is_new_arrival" value="1"
                               {{ old('is_new_arrival', $barongProduct->is_new_arrival ?? false ? 'checked' : '') }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_new_arrival" class="ml-2 text-sm font-medium text-gray-700">New Arrival</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Stock Management -->
        <div>
            <!-- Size-Based Stock -->
            <div id="size-stock-section" class="stock-section">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Stock Management</h3>
                <p class="text-sm text-gray-600 mb-4">Set individual stock quantities for each size.</p>
                
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    @php
                        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                        $sizeStocks = old('size_stocks', $barongProduct->size_stocks ?? []);
                    @endphp
                    
                    @foreach($sizes as $size)
                        <div>
                            <label for="size_stock_{{ $size }}" class="block text-sm font-medium text-gray-700 mb-2">Size {{ $size }}</label>
                            <input type="number" id="size_stock_{{ $size }}" name="size_stocks[{{ $size }}]" 
                                   value="{{ $sizeStocks[$size] ?? 0 }}" 
                                   min="0" autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 size-stock-input"
                                   onchange="calculateTotalStock()" 
                                   placeholder="0">
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Total Stock:</span>
                        <span id="total-stock-display" class="text-2xl font-bold text-blue-600">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Variations removed: stocks come from Size Stock Management and colors from attributes --}}
</div>
