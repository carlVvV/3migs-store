// Product Form JavaScript - Main Initialization
// File: resources/js/admin/product-form-main.js

// Video functionality
function initializeVideoFunctionality() {
    const enableVideoCheckbox = document.getElementById('enable_video');
    const videoSection = document.getElementById('videoSection');
    const videoUrlInput = document.getElementById('video_url');
    const videoPreview = document.getElementById('videoPreview');
    const videoFrame = document.getElementById('videoFrame');

    if (enableVideoCheckbox) {
        enableVideoCheckbox.addEventListener('change', function() {
            if (this.checked) {
                videoSection.classList.remove('hidden');
            } else {
                videoSection.classList.add('hidden');
                videoPreview.classList.add('hidden');
            }
        });
    }

    if (videoUrlInput) {
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
    }
}

// Variations functionality
function initializeVariationsFunctionality() {
    const hasVariationsCheckbox = document.getElementById('has_variations');
    const variationsSection = document.getElementById('variationsSection');
    const addVariationBtn = document.getElementById('addVariationBtn');
    const variationsContainer = document.getElementById('variationsContainer');
    let variationIndex = {{ isset($barongProduct) && $barongProduct->variations ? count($barongProduct->variations) : 0 }};

    if (hasVariationsCheckbox) {
        hasVariationsCheckbox.addEventListener('change', function() {
            if (this.checked) {
                variationsSection.classList.remove('hidden');
            } else {
                variationsSection.classList.add('hidden');
            }
        });
    }

    if (addVariationBtn) {
        addVariationBtn.addEventListener('click', function() {
            addVariationRow();
        });
    }

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

    // Global function for removing variations
    window.removeVariation = function(button) {
        button.closest('.variation-item').remove();
    };
}

// Utility functions
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

// Main initialization function
function initializeProductForm() {
    console.log('🚀 Initializing Product Form...');
    
    // Initialize all modules
    initializeVideoFunctionality();
    initializeVariationsFunctionality();
    
    // Initialize stock calculation
    setTimeout(() => {
        calculateTotalStock();
    }, 100);
    
    // Initialize image upload
    initializeImageUpload();
    
    // Initialize attributes modal
    initializeAttributesModal();
    
    // Initialize form submission
    initializeFormSubmission();
    
    console.log('✅ Product Form initialized successfully');
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeProductForm);
