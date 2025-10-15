{{-- Pricing and Stock Section --}}
{{-- File: resources/views/admin/partials/product-form/pricing-stock.blade.php --}}

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
        
        <!-- Special price removed per request -->
        
        <div>
            <input type="hidden" name="has_variations" value="0">
            
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
                                   onchange="calculateTotalStock()" 
                                   oninput="calculateTotalStock()" 
                                   onkeyup="calculateTotalStock()"
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
    
    {{-- Variations removed: stocks come from Size Stock Management and colors from attributes --}}
</div>
