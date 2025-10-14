// Product Form JavaScript - Attributes Modal
// File: resources/js/admin/product-form-attributes.js

function initializeAttributesModal() {
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

    const selectedAttributes = document.getElementById('selectedAttributes');
    if (selectedAttributes) {
        selectedAttributes.innerHTML = html;
    }
}

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
