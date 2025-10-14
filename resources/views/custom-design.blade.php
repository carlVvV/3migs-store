@extends('layouts.app')

@section('title', 'Customized Design - 3Migs Gowns & Barong')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-2">
            <nav class="text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900 font-medium">Customized Design</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-4">
        <!-- Page Header -->
        <div class="text-center mb-4">
            <div class="flex items-center justify-center mb-2">
                <span class="w-2 h-6 bg-red-500 mr-2 rounded-sm"></span>
                <h1 class="text-2xl font-bold text-gray-900">Customized Design</h1>
            </div>
            <p class="text-sm text-gray-600 max-w-xl mx-auto">
                Create your perfect barong with our custom design service.
            </p>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Customization Form -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-4">
                <form id="custom-design-form" class="space-y-4">
                    @csrf
                    
                    <!-- Customization Options -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Fabric Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fabric</label>
                            <select name="fabric" id="fabric" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="">Select Fabric</option>
                                <option value="jusilyn" data-price="160">Jusilyn - ₱160 per yard</option>
                                <option value="hugo_boss" data-price="130">Hugo Boss - ₱130 per yard</option>
                                <option value="pina_cocoon" data-price="130">Piña Cocoon - ₱130 per yard</option>
                                <option value="gusot_mayaman" data-price="220">Gusot Mayaman - ₱220 per yard</option>
                            </select>
                        </div>

                        <!-- Color Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <select name="color" id="color" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="">Select Color</option>
                                <option value="white">White</option>
                                <option value="cream">Cream</option>
                                <option value="ivory">Ivory</option>
                                <option value="beige">Beige</option>
                                <option value="light-blue">Light Blue</option>
                                <option value="light-pink">Light Pink</option>
                            </select>
                        </div>

                        <!-- Embroidery Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Embroidery</label>
                            <select name="embroidery" id="embroidery" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="">Select Embroidery</option>
                                <option value="none">No Embroidery</option>
                                <option value="simple">Simple Embroidery - ₱200</option>
                                <option value="detailed">Detailed Embroidery - ₱350</option>
                                <option value="custom">Custom Design - ₱500+ (varies)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Size Adjustment Section -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-magic mr-2 text-blue-600 text-sm"></i>
                            Automated Size Adjustment
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Standard Size Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Standard Size</label>
                                <select name="standard_size" id="standard_size" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Standard Size</option>
                                    <option value="S">Small (S)</option>
                                    <option value="M">Medium (M)</option>
                                    <option value="L">Large (L)</option>
                                    <option value="XL">Extra Large (XL)</option>
                                    <option value="XXL">Double XL (XXL)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Select a standard size to auto-fill measurements</p>
                            </div>
                            
                            <!-- Fabric Yardage Calculation -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fabric Yardage</label>
                                <div class="flex items-center space-x-2">
                                    <input type="number" name="fabric_yardage" id="fabric_yardage" step="0.1" min="1" max="10" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="2.5" readonly>
                                    <span class="text-sm text-gray-500">yards</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Automatically calculated based on measurements</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-ruler mr-2 text-red-600 text-sm"></i>
                            Custom Measurements (inches)
                        </h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Chest Measurement -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chest <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="chest" id="chest" step="0.5" min="20" max="60" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           placeholder="42">
                                    <span class="absolute right-3 top-2 text-gray-500 text-xs">in</span>
                                </div>
                            </div>

                            <!-- Waist Measurement -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Waist <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="waist" id="waist" step="0.5" min="20" max="60" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           placeholder="40">
                                    <span class="absolute right-3 top-2 text-gray-500 text-xs">in</span>
                                </div>
                            </div>

                            <!-- Length Measurement -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Length <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="length" id="length" step="0.5" min="20" max="40" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           placeholder="28">
                                    <span class="absolute right-3 top-2 text-gray-500 text-xs">in</span>
                                </div>
                            </div>

                            <!-- Shoulder Width Measurement -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Shoulder <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="shoulder_width" id="shoulder_width" step="0.5" min="12" max="25" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           placeholder="18">
                                    <span class="absolute right-3 top-2 text-gray-500 text-xs">in</span>
                                </div>
                            </div>

                            <!-- Sleeve Length Measurement -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Sleeve <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="sleeve_length" id="sleeve_length" step="0.5" min="15" max="35" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           placeholder="24">
                                    <span class="absolute right-3 top-2 text-gray-500 text-xs">in</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes Section -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-blue-600 text-sm"></i>
                            Additional Notes
                        </h3>
                        <textarea name="additional_notes" id="additional_notes" rows="2" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                  placeholder="Special requests or preferences..."></textarea>
                    </div>

                    <!-- Update Cart Button -->
                    <div class="flex justify-end">
                        <button type="button" id="update-cart-btn" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition-colors text-sm">
                            Update Cart
                        </button>
                    </div>
                </form>
            </div>

            <!-- Selected Item Summary -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="space-y-4">
                    <!-- Item Details -->
                    <div class="flex items-center space-x-3">
                        <!-- Item Image -->
                        <div class="relative">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tshirt text-xl text-gray-400"></i>
                            </div>
                            <button type="button" id="remove-item" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        
                        <!-- Item Info -->
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-sm" id="item-fabric">Jusilyn</h3>
                            <p class="text-xs text-gray-600">Custom Barong</p>
                        </div>
                    </div>

                    <!-- Item Specifications -->
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700">Color:</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-white border border-gray-300 rounded-full" id="color-swatch"></div>
                                <span class="text-gray-900" id="summary-color">White</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700">Chest:</span>
                            <span class="text-gray-900" id="summary-chest">42 in</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700">Length:</span>
                            <span class="text-gray-900" id="summary-length">28 in</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700">Shoulder:</span>
                            <span class="text-gray-900" id="summary-shoulder">18 in</span>
                        </div>
                    <!-- Pricing Breakdown -->
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Fabric Cost:</span>
                            <span class="font-medium text-gray-800" id="fabric-cost">₱0.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Labor Cost:</span>
                            <span class="font-medium text-gray-800">₱1,500.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Embroidery:</span>
                            <span class="font-medium text-gray-800" id="embroidery-cost">₱0.00</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <span class="font-medium text-gray-700">Subtotal:</span>
                            <span class="text-sm font-semibold text-gray-900" id="subtotal">₱2,000.00</span>
                        </div>
                    </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <button type="button" id="return-to-shop" class="w-full border border-gray-300 text-gray-700 py-2 px-3 rounded-md hover:bg-gray-50 transition-colors text-sm">
                            Return To Shop
                        </button>
                        <button type="button" id="pay-now" class="w-full bg-red-600 text-white py-2 px-3 rounded-md hover:bg-red-700 transition-colors font-semibold text-sm">
                            Pay Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mb-4"></div>
        <p class="text-gray-600">Processing your custom order...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const fabricSelect = document.getElementById('fabric');
    const colorSelect = document.getElementById('color');
    const embroiderySelect = document.getElementById('embroidery');
    const standardSizeSelect = document.getElementById('standard_size');
    const fabricYardageInput = document.getElementById('fabric_yardage');
    
    // Custom measurement elements
    const chestInput = document.getElementById('chest');
    const waistInput = document.getElementById('waist');
    const lengthInput = document.getElementById('length');
    const shoulderWidthInput = document.getElementById('shoulder_width');
    const sleeveLengthInput = document.getElementById('sleeve_length');
    const additionalNotesInput = document.getElementById('additional_notes');
    
    // Summary elements
    const itemFabric = document.getElementById('item-fabric');
    const summaryFabric = document.getElementById('summary-fabric');
    const summaryColor = document.getElementById('summary-color');
    const colorSwatch = document.getElementById('color-swatch');
    const summaryChest = document.getElementById('summary-chest');
    const summaryLength = document.getElementById('summary-length');
    const summaryShoulder = document.getElementById('summary-shoulder');
    const fabricCostElement = document.getElementById('fabric-cost');
    const embroideryCostElement = document.getElementById('embroidery-cost');
    const subtotal = document.getElementById('subtotal');
    
    // Buttons
    const updateCartBtn = document.getElementById('update-cart-btn');
    const removeItemBtn = document.getElementById('remove-item');
    const returnToShopBtn = document.getElementById('return-to-shop');
    const payNowBtn = document.getElementById('pay-now');
    
    // Color mapping
    const colorMap = {
        'white': '#ffffff',
        'cream': '#f5f5dc',
        'ivory': '#fffff0',
        'beige': '#f5f5dc',
        'light-blue': '#add8e6',
        'light-pink': '#ffb6c1'
    };
    
    // Price mapping (example pricing; adjust as needed)
    const priceMap = {
        'jusilyn': 2000,
        'hugo_boss': 2800,
        'pina_cocoon': 3500,
        'gusot_mayaman': 2200
    };

    // Display label mapping for fabrics
    // Fabric pricing data
    const fabricPrices = {
        'jusilyn': 160,
        'hugo_boss': 130,
        'pina_cocoon': 130,
        'gusot_mayaman': 220
    };
    
    // Embroidery pricing
    const embroideryPrices = {
        'none': 0,
        'simple': 200,
        'detailed': 350,
        'custom': 500 // Base price, will vary based on design complexity
    };
    
    // Standard size measurements (chest, waist, length, shoulder, sleeve)
    const standardSizes = {
        'S': [36, 34, 26, 16, 22],
        'M': [40, 38, 28, 18, 24],
        'L': [44, 42, 30, 20, 26],
        'XL': [48, 46, 32, 22, 28],
        'XXL': [52, 50, 34, 24, 30]
    };
    
    // Labor cost
    const laborCost = 1500;
    
    const fabricLabelMap = {
        'jusilyn': 'jusilyn',
        'hugo_boss': 'hugo boss',
        'pina_cocoon': 'piña cocoon',
        'gusot_mayaman': 'gusot mayaman'
    };
    
    // Debounced update function for better performance
    let updateTimeout;
    function debouncedUpdateSummary() {
        clearTimeout(updateTimeout);
        // Show loading indicator
        if (subtotal) {
            subtotal.textContent = 'Calculating...';
            subtotal.style.color = '#6b7280';
        }
        updateTimeout = setTimeout(updateSummary, 100); // Update after 100ms of inactivity
    }
    
    // Immediate update function for critical changes
    function immediateUpdateSummary() {
        clearTimeout(updateTimeout);
        updateSummary();
    }
    
    // Add visual feedback for real-time updates
    function addUpdateIndicator() {
        if (subtotal) {
            subtotal.style.transition = 'all 0.3s ease';
        }
        if (fabricCostElement) {
            fabricCostElement.style.transition = 'all 0.3s ease';
        }
        if (embroideryCostElement) {
            embroideryCostElement.style.transition = 'all 0.3s ease';
        }
    }
    
    // Initialize summary with proper defaults
    function initializeSummary() {
        // Add visual feedback for updates
        addUpdateIndicator();
        
        // Set default values if fields are empty
        if (!chestInput.value) chestInput.value = '';
        if (!waistInput.value) waistInput.value = '';
        if (!lengthInput.value) lengthInput.value = '';
        if (!shoulderWidthInput.value) shoulderWidthInput.value = '';
        if (!sleeveLengthInput.value) sleeveLengthInput.value = '';
        
        // Update summary with current values
        updateSummary();
    }
    
    // Initialize when page loads
    initializeSummary();
    
    // Enhanced real-time event listeners
    fabricSelect.addEventListener('change', immediateUpdateSummary);
    fabricSelect.addEventListener('input', immediateUpdateSummary);
    colorSelect.addEventListener('change', immediateUpdateSummary);
    colorSelect.addEventListener('input', immediateUpdateSummary);
    embroiderySelect.addEventListener('change', immediateUpdateSummary);
    embroiderySelect.addEventListener('input', immediateUpdateSummary);
    standardSizeSelect.addEventListener('change', applyStandardSize);
    
    // Custom measurement event listeners - debounced for typing, immediate for other events
    chestInput.addEventListener('input', debouncedUpdateSummary);
    chestInput.addEventListener('change', immediateUpdateSummary);
    chestInput.addEventListener('keyup', debouncedUpdateSummary);
    chestInput.addEventListener('blur', immediateUpdateSummary);
    
    waistInput.addEventListener('input', debouncedUpdateSummary);
    waistInput.addEventListener('change', immediateUpdateSummary);
    waistInput.addEventListener('keyup', debouncedUpdateSummary);
    waistInput.addEventListener('blur', immediateUpdateSummary);
    
    lengthInput.addEventListener('input', debouncedUpdateSummary);
    lengthInput.addEventListener('change', immediateUpdateSummary);
    lengthInput.addEventListener('keyup', debouncedUpdateSummary);
    lengthInput.addEventListener('blur', immediateUpdateSummary);
    
    shoulderWidthInput.addEventListener('input', debouncedUpdateSummary);
    shoulderWidthInput.addEventListener('change', immediateUpdateSummary);
    shoulderWidthInput.addEventListener('keyup', debouncedUpdateSummary);
    shoulderWidthInput.addEventListener('blur', immediateUpdateSummary);
    
    sleeveLengthInput.addEventListener('input', debouncedUpdateSummary);
    sleeveLengthInput.addEventListener('change', immediateUpdateSummary);
    sleeveLengthInput.addEventListener('keyup', debouncedUpdateSummary);
    sleeveLengthInput.addEventListener('blur', immediateUpdateSummary);
    
    // Additional notes for completeness
    additionalNotesInput.addEventListener('input', debouncedUpdateSummary);
    
    updateCartBtn.addEventListener('click', updateCart);
    removeItemBtn.addEventListener('click', removeItem);
    returnToShopBtn.addEventListener('click', () => window.location.href = '/home');
    payNowBtn.addEventListener('click', proceedToPayment);
    
    // Apply standard size measurements
    function applyStandardSize() {
        const selectedSize = standardSizeSelect.value;
        if (selectedSize && standardSizes[selectedSize]) {
            const measurements = standardSizes[selectedSize];
            chestInput.value = measurements[0];
            waistInput.value = measurements[1];
            lengthInput.value = measurements[2];
            shoulderWidthInput.value = measurements[3];
            sleeveLengthInput.value = measurements[4];
            updateSummary();
        }
    }
    
    // Calculate fabric yardage based on measurements
    function calculateFabricYardage() {
        const chest = parseFloat(chestInput.value) || 0;
        const waist = parseFloat(waistInput.value) || 0;
        const length = parseFloat(lengthInput.value) || 0;
        const shoulder = parseFloat(shoulderWidthInput.value) || 0;
        const sleeve = parseFloat(sleeveLengthInput.value) || 0;
        
        // If any measurement is missing, return 0
        if (!chest || !waist || !length || !shoulder || !sleeve) {
            return 0;
        }
        
        // Convert inches to yards (1 yard = 36 inches)
        // For barong: we need fabric for body + sleeves + extra for seams and finishing
        // Body fabric calculation: (chest + waist) / 2 * length / 36
        const bodyWidth = (chest + waist) / 2; // Average width
        const bodyYardage = (bodyWidth * length) / 36; // Convert to yards
        
        // Sleeve fabric calculation: sleeve length * sleeve width (estimated as chest/3) / 36
        const sleeveWidth = chest / 3; // Estimate sleeve width as 1/3 of chest
        const sleeveYardage = (sleeve * sleeveWidth) / 36;
        
        // Add extra fabric for seams, hemming, and finishing (typically 0.5-1 yard)
        const extraFabric = 0.75; // 3/4 yard for seams and finishing
        
        const totalYardage = bodyYardage + sleeveYardage + extraFabric;
        
        return Math.ceil(totalYardage * 10) / 10; // Round up to 1 decimal place
    }
    
    // Calculate pricing
    function calculatePricing() {
        const fabric = fabricSelect.value;
        const embroidery = embroiderySelect.value || 'none';
        const yardage = calculateFabricYardage();
        
        // Get fabric price per yard
        const fabricPricePerYard = fabric && fabricPrices[fabric] ? fabricPrices[fabric] : 0;
        const fabricCost = fabricPricePerYard * yardage;
        
        // Get embroidery cost
        const embroideryCost = embroideryPrices[embroidery] || 0;
        
        // Calculate total cost
        const totalCost = fabricCost + laborCost + embroideryCost;
        
        console.log('Pricing calculation:', {
            fabric: fabric,
            fabricPricePerYard: fabricPricePerYard,
            yardage: yardage,
            fabricCost: fabricCost,
            embroidery: embroidery,
            embroideryCost: embroideryCost,
            laborCost: laborCost,
            totalCost: totalCost
        });
        
        return {
            fabricCost: fabricCost,
            embroideryCost: embroideryCost,
            laborCost: laborCost,
            totalCost: totalCost,
            yardage: yardage
        };
    }
    
    function updateSummary() {
        const fabric = fabricSelect.value || '';
        const color = colorSelect.value || 'white';
        const chest = chestInput.value || '';
        const waist = waistInput.value || '';
        const length = lengthInput.value || '';
        const shoulderWidth = shoulderWidthInput.value || '';
        const sleeveLength = sleeveLengthInput.value || '';
        
        // Update fabric (use friendly labels)
        const fabricLabel = fabricLabelMap[fabric] || fabric;
        if (itemFabric) {
            itemFabric.textContent = fabricLabel;
            itemFabric.style.color = fabric ? '#1f2937' : '#9ca3af'; // Gray if no fabric selected
        }
        if (summaryFabric) {
            summaryFabric.textContent = fabricLabel;
            summaryFabric.style.color = fabric ? '#1f2937' : '#9ca3af';
        }
        
        // Update color
        if (summaryColor) {
            summaryColor.textContent = color.charAt(0).toUpperCase() + color.slice(1);
            summaryColor.style.color = color ? '#1f2937' : '#9ca3af';
        }
        if (colorSwatch) colorSwatch.style.backgroundColor = colorMap[color] || '#ffffff';
        
        // Update measurements - only show if values exist
        if (summaryChest) {
            summaryChest.textContent = chest ? chest + ' in' : '—';
            summaryChest.style.color = chest ? '#1f2937' : '#9ca3af';
        }
        if (summaryLength) {
            summaryLength.textContent = length ? length + ' in' : '—';
            summaryLength.style.color = length ? '#1f2937' : '#9ca3af';
        }
        if (summaryShoulder) {
            summaryShoulder.textContent = shoulderWidth ? shoulderWidth + ' in' : '—';
            summaryShoulder.style.color = shoulderWidth ? '#1f2937' : '#9ca3af';
        }
        
        // Calculate and update pricing
        const pricing = calculatePricing();
        if (fabricYardageInput) {
            fabricYardageInput.value = pricing.yardage;
            fabricYardageInput.style.backgroundColor = pricing.yardage > 0 ? '#f0f9ff' : '#f9fafb';
        }
        if (fabricCostElement) {
            fabricCostElement.textContent = '₱' + pricing.fabricCost.toFixed(2);
            fabricCostElement.style.color = pricing.fabricCost > 0 ? '#059669' : '#9ca3af';
        }
        if (embroideryCostElement) {
            embroideryCostElement.textContent = '₱' + pricing.embroideryCost.toFixed(2);
            embroideryCostElement.style.color = pricing.embroideryCost > 0 ? '#059669' : '#9ca3af';
        }
        if (subtotal) {
            subtotal.textContent = '₱' + pricing.totalCost.toFixed(2);
            subtotal.style.color = pricing.totalCost > 0 ? '#dc2626' : '#9ca3af';
        }
        
        // Add visual indicator for incomplete form
        const isFormComplete = fabric && color && chest && waist && length && shoulderWidth && sleeveLength;
        if (subtotal) {
            subtotal.style.fontWeight = isFormComplete ? 'bold' : 'normal';
        }
        
        console.log('Summary updated:', {
            fabric: fabric,
            color: color,
            chest: chest,
            waist: waist,
            length: length,
            shoulderWidth: shoulderWidth,
            sleeveLength: sleeveLength,
            pricing: pricing,
            isFormComplete: isFormComplete
        });
    }
    
    function updateCart() {
        // Validate required fields
        if (!fabricSelect.value || !colorSelect.value || !chestInput.value || !waistInput.value || !lengthInput.value || !shoulderWidthInput.value || !sleeveLengthInput.value) {
            showNotification('Please select fabric, color, and provide all measurements', 'error');
            return;
        }
        
        // Validate measurement ranges
        if (chestInput.value < 20 || chestInput.value > 60) {
            showNotification('Chest measurement must be between 20 and 60 inches', 'error');
            return;
        }
        if (waistInput.value < 20 || waistInput.value > 60) {
            showNotification('Waist measurement must be between 20 and 60 inches', 'error');
            return;
        }
        if (lengthInput.value < 20 || lengthInput.value > 40) {
            showNotification('Length measurement must be between 20 and 40 inches', 'error');
            return;
        }
        if (shoulderWidthInput.value < 12 || shoulderWidthInput.value > 25) {
            showNotification('Shoulder width must be between 12 and 25 inches', 'error');
            return;
        }
        if (sleeveLengthInput.value < 15 || sleeveLengthInput.value > 35) {
            showNotification('Sleeve length must be between 15 and 35 inches', 'error');
            return;
        }
        
        // Show loading
        document.getElementById('loading-modal').classList.remove('hidden');
        
        // Prepare data
        const customData = {
            fabric: fabricSelect.value,
            color: colorSelect.value,
            embroidery: embroiderySelect.value || 'none',
            quantity: 1,
            measurements: {
                chest: parseFloat(chestInput.value),
                waist: parseFloat(waistInput.value),
                length: parseFloat(lengthInput.value),
                shoulder_width: parseFloat(shoulderWidthInput.value),
                sleeve_length: parseFloat(sleeveLengthInput.value)
            },
            fabric_yardage: parseFloat(fabricYardageInput.value),
            pricing: calculatePricing(),
            additional_notes: additionalNotesInput.value || ''
        };
        
        // Call API to add to cart
        fetch('/api/v1/custom-design/add-to-cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(customData)
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading-modal').classList.add('hidden');
            
            if (data.success) {
                showNotification('Custom barong added to cart!', 'success');
                // Update cart count if available
                updateCartCount();
            } else {
                showNotification(data.message || 'Failed to add to cart', 'error');
            }
        })
        .catch(error => {
            document.getElementById('loading-modal').classList.add('hidden');
            console.error('Error adding to cart:', error);
            showNotification('Error adding to cart', 'error');
        });
    }
    
    function removeItem() {
        if (confirm('Are you sure you want to remove this custom barong from your cart?')) {
            showNotification('Item removed from cart', 'success');
            // Reset form
            fabricSelect.value = '';
            colorSelect.value = '';
            embroiderySelect.value = '';
            chestInput.value = '';
            waistInput.value = '';
            lengthInput.value = '';
            shoulderWidthInput.value = '';
            sleeveLengthInput.value = '';
            additionalNotesInput.value = '';
            updateSummary();
        }
    }
    
    function proceedToPayment() {
        // Validate required fields
        if (!fabricSelect.value || !colorSelect.value || !chestInput.value || !waistInput.value || !lengthInput.value || !shoulderWidthInput.value || !sleeveLengthInput.value) {
            showNotification('Please complete your custom barong selection and measurements', 'error');
            return;
        }
        
        // First add to cart, then redirect to checkout
        updateCart();
        
        // Redirect to checkout after successful cart update
        setTimeout(() => {
            window.location.href = '/checkout';
        }, 2000);
    }
    
    function updateCartCount() {
        // Update cart count in header if available
        fetch('/api/v1/cart/count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.count;
                    }
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
    }
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-20 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-gray-600'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
</script>
@endsection

