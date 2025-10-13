@extends('layouts.app')

@section('title', 'Customized Design - 3Migs Gowns & Barong')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-3">
            <nav class="text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900 font-medium">Customized Design</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                <h1 class="text-4xl font-bold text-gray-900">Customized Design</h1>
            </div>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Create your perfect barong with our custom design service. Choose from premium fabrics, colors, and embroidery details.
            </p>
        </div>

        <!-- Customization Form -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <form id="custom-design-form" class="space-y-6">
                @csrf
                
                <!-- Customization Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Fabric Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fabric</label>
                        <select name="fabric" id="fabric" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select Fabric</option>
                            <option value="jusilyn">Jusilyn</option>
                            <option value="hugo_boss">Hugo Boss</option>
                            <option value="pina_cocoon">Piña Cocoon</option>
                            <option value="gusot_mayaman">Gusot Mayaman</option>
                        </select>
                    </div>

                    <!-- Color Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <select name="color" id="color" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Embroidery Details</label>
                        <select name="embroidery" id="embroidery" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select Embroidery</option>
                            <option value="none">No Embroidery</option>
                            <option value="simple">Simple Embroidery</option>
                            <option value="detailed">Detailed Embroidery</option>
                            <option value="custom">Custom Design</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Measurements Section -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-ruler mr-2 text-red-600"></i>
                        Custom Measurements (in inches)
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Please provide your measurements for a perfect fit. Follow the measurement guides below:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Chest Measurement -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Chest (Garment) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="chest" id="chest" step="0.5" min="20" max="60" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="e.g., 42">
                                <span class="absolute right-3 top-2 text-gray-500 text-sm">in</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lay the shirt flat. Measure from the bottom of the sleeve seam across to the other end.</p>
                        </div>

                        <!-- Waist Measurement -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Waist (Garment) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="waist" id="waist" step="0.5" min="20" max="60" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="e.g., 40">
                                <span class="absolute right-3 top-2 text-gray-500 text-sm">in</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lay the shirt flat. Measure across the shirt at the waistline, midway between armpit and bottom hem.</p>
                        </div>

                        <!-- Length Measurement -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Length (Garment) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="length" id="length" step="0.5" min="20" max="40" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="e.g., 28">
                                <span class="absolute right-3 top-2 text-gray-500 text-sm">in</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Measure from the center of the back of the neck, down to the bottom hem of the shirt.</p>
                        </div>

                        <!-- Shoulder Width Measurement -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Shoulder Width (Garment) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="shoulder_width" id="shoulder_width" step="0.5" min="12" max="25" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="e.g., 18">
                                <span class="absolute right-3 top-2 text-gray-500 text-sm">in</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lay the shirt flat and measure across the top from one shoulder seam to the other.</p>
                        </div>

                        <!-- Sleeve Length Measurement -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sleeve Length (Garment) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="sleeve_length" id="sleeve_length" step="0.5" min="15" max="35" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="e.g., 24">
                                <span class="absolute right-3 top-2 text-gray-500 text-sm">in</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lay the sleeve flat and measure from the top of the shoulder seam to the end of the cuff.</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes Section -->
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-blue-600"></i>
                        Additional Notes for Seller
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Any special requests, preferences, or additional information for your custom barong:</p>
                    <textarea name="additional_notes" id="additional_notes" rows="4" 
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="e.g., Please make the sleeves slightly longer, prefer a looser fit around the chest, special occasion for wedding..."></textarea>
                </div>

                <!-- Update Cart Button -->
                <div class="flex justify-end">
                    <button type="button" id="update-cart-btn" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition-colors">
                        Update Cart
                    </button>
                </div>
            </form>
        </div>

        <!-- Selected Item Summary -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Item Details -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <!-- Item Image -->
                        <div class="relative">
                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tshirt text-2xl text-gray-400"></i>
                            </div>
                            <button type="button" id="remove-item" class="absolute -top-2 -left-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        
                        <!-- Item Info -->
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900" id="item-fabric">Piña</h3>
                            <p class="text-sm text-gray-600">Custom Barong</p>
                        </div>
                    </div>

                    <!-- Item Specifications -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fabric</label>
                            <span class="text-sm text-gray-900" id="summary-fabric">Piña</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-white border border-gray-300 rounded-full" id="color-swatch"></div>
                                <span class="text-sm text-gray-900" id="summary-color">White</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chest</label>
                            <span class="text-sm text-gray-900" id="summary-chest">42 in</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Length</label>
                            <span class="text-sm text-gray-900" id="summary-length">28 in</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shoulder</label>
                            <span class="text-sm text-gray-900" id="summary-shoulder">18 in</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                            <span class="text-sm font-semibold text-gray-900" id="subtotal">₱2,500.00</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col justify-end space-y-4">
                    <button type="button" id="return-to-shop" class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-50 transition-colors">
                        Return To Shop
                    </button>
                    <button type="button" id="pay-now" class="w-full bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 transition-colors font-semibold">
                        Pay Now
                    </button>
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
    const fabricLabelMap = {
        'jusilyn': 'jusilyn',
        'hugo_boss': 'hugo boss',
        'pina_cocoon': 'piña cocoon',
        'gusot_mayaman': 'gusot mayaman'
    };
    
    // Initialize with default values
    updateSummary();
    
    // Event listeners
    fabricSelect.addEventListener('change', updateSummary);
    colorSelect.addEventListener('change', updateSummary);
    embroiderySelect.addEventListener('change', updateSummary);
    
    // Custom measurement event listeners
    chestInput.addEventListener('input', updateSummary);
    waistInput.addEventListener('input', updateSummary);
    lengthInput.addEventListener('input', updateSummary);
    shoulderWidthInput.addEventListener('input', updateSummary);
    sleeveLengthInput.addEventListener('input', updateSummary);
    
    updateCartBtn.addEventListener('click', updateCart);
    removeItemBtn.addEventListener('click', removeItem);
    returnToShopBtn.addEventListener('click', () => window.location.href = '/home');
    payNowBtn.addEventListener('click', proceedToPayment);
    
    function updateSummary() {
        const fabric = fabricSelect.value || 'jusilyn';
        const color = colorSelect.value || 'white';
        const chest = chestInput.value || '42';
        const waist = waistInput.value || '40';
        const length = lengthInput.value || '28';
        const shoulderWidth = shoulderWidthInput.value || '18';
        const sleeveLength = sleeveLengthInput.value || '24';
        
        // Update fabric (use friendly labels)
        const fabricLabel = fabricLabelMap[fabric] || fabric;
        itemFabric.textContent = fabricLabel;
        summaryFabric.textContent = fabricLabel;
        
        // Update color
        summaryColor.textContent = color.charAt(0).toUpperCase() + color.slice(1);
        colorSwatch.style.backgroundColor = colorMap[color] || '#ffffff';
        
        // Update measurements
        summaryChest.textContent = chest + ' in';
        summaryLength.textContent = length + ' in';
        summaryShoulder.textContent = shoulderWidth + ' in';
        
        // Update price
        const basePrice = priceMap[fabric] || 2500;
        const embroideryPrice = embroiderySelect.value === 'detailed' ? 500 : 
                               embroiderySelect.value === 'custom' ? 1000 : 0;
        const totalPrice = basePrice + embroideryPrice;
        
        subtotal.textContent = `₱${totalPrice.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
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
