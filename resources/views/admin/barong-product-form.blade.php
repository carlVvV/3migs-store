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
            @include('admin.partials.product-form.basic-info')

            <!-- Images and Media -->
            @include('admin.partials.product-form.images-media')

            <!-- Product Attributes -->
            @include('admin.partials.product-form.attributes')

            <!-- Pricing and Stock -->
            @include('admin.partials.product-form.pricing-stock')

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

<!-- Attributes Modal -->
@include('admin.partials.product-form.attributes-modal')

<!-- Notification Container -->
<div id="notification-container" class="fixed right-4 z-50 space-y-2" style="top: 0px;">
    <!-- Notifications will be dynamically inserted here -->
</div>

@endsection

@push('scripts')
<script>
// Essential JavaScript functionality for product form

// Notification system
function showSuccess(title, message, duration = 5000) {
    showNotification('success', title, message, duration);
}

function showError(title, message, duration = 7000) {
    showNotification('error', title, message, duration);
}

function showNotification(type, title, message, duration) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    notification.className = `bg-white border-l-4 shadow-lg rounded-lg p-4 max-w-sm transform transition-all duration-300 ease-in-out translate-x-full opacity-0 ${
        type === 'success' ? 'border-green-500' : 'border-red-500'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center ${type === 'success' ? 'bg-green-100' : 'bg-red-100'}">
                    <svg class="w-5 h-5 ${type === 'success' ? 'text-green-600' : 'text-red-600'}" fill="currentColor" viewBox="0 0 20 20">
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

// Form submission handler
function handleFormSubmit(event) {
    console.log('🚀 Form submission intercepted');
    event.preventDefault();
    event.stopPropagation();
    
    const form = document.getElementById('barongProductForm');
    if (!form) {
        console.error('❌ Form not found!');
        return false;
    }
    
    const formData = new FormData(form);
    
    // Debug: Log form data
    console.log('📋 Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    const url = form.action;
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
            showSuccess(
                '🎉 Product Created Successfully!',
                'Your barong product has been added to the inventory and is now visible on the homepage.',
                5000
            );
            
            // Redirect after a short delay
            setTimeout(() => {
                console.log('🔄 Redirecting to products page...');
                window.location.href = '/admin/products';
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
        console.error('❌ Error:', error);
        showError(
            'Network Error',
            'An error occurred while saving the product. Please check your connection and try again.',
            7000
        );
    });
    
    return false; // Prevent default form submission
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Product Form initialized');
    
    // Test if form exists
    const form = document.getElementById('barongProductForm');
    if (form) {
        console.log('✅ Form found:', form);
    } else {
        console.error('❌ Form not found!');
    }

    // Initialize attributes modal and image upload/preview handlers
    try {
        initializeAttributesModal();
        initializeImageUpload();
        // Stock calculator may be referenced via inline handlers in the stock inputs
        exposeStockCalculator();
    } catch (e) {
        console.warn('Initialization warning:', e);
        try { showError('Initialization Warning', (e && e.message) ? e.message : 'Unknown error during setup'); } catch(_) {}
    }
});

// =============== Attributes Modal ===============
function initializeAttributesModal() {
    const openBtn = document.getElementById('setAttributesBtn');
    const modal = document.getElementById('attributesModal');
    const closeBtn = document.getElementById('closeAttributesModal');
    const cancelBtn = document.getElementById('cancelAttributes');
    const saveBtn = document.getElementById('saveAttributes');
    if (!openBtn || !modal) return;

    const open = (e) => { e && e.preventDefault(); modal.classList.remove('hidden'); };
    const close = (e) => { e && e.preventDefault(); modal.classList.add('hidden'); };

    openBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (cancelBtn) cancelBtn.addEventListener('click', close);

    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            // Collect values
            const selectedFabrics = Array.from(document.querySelectorAll('input[name="fabric[]"]:checked')).map(i => i.value);
            const selectedEmbroidery = Array.from(document.querySelectorAll('input[name="embroidery_style[]"]:checked')).map(i => i.value);
            const selectedColors = Array.from(document.querySelectorAll('input[name="colors[]"]:checked')).map(i => i.value);
            const selectedCollar = (document.querySelector('input[name="collar_type"]:checked') || {}).value || '';
            const selectedDesign = Array.from(document.querySelectorAll('input[name="design_details[]"]:checked')).map(i => i.value);

            // Ensure hidden inputs exist
            const form = document.getElementById('barongProductForm');
            const ensureHidden = (name, value) => {
                // Remove old inputs for this name
                Array.from(form.querySelectorAll(`input[type=hidden][name="${name}"]`)).forEach(el => el.remove());
                // Add new (array values get one input per entry)
                if (Array.isArray(value)) {
                    value.forEach(v => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = name + '[]';
                        inp.value = v;
                        form.appendChild(inp);
                    });
                } else if (value) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = value;
                    form.appendChild(inp);
                }
            };

            ensureHidden('fabric', selectedFabrics);
            ensureHidden('embroidery_style', selectedEmbroidery);
            ensureHidden('colors', selectedColors);
            ensureHidden('collar_type', selectedCollar);
            ensureHidden('design_details', selectedDesign);

            // Update display panel
            const container = document.getElementById('selectedAttributes');
            if (container) {
                const pill = (text, cls) => `<span class="${cls} px-3 py-1 text-xs rounded-full font-medium">${text}</span>`;
                container.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${selectedFabrics.length ? `<div class="space-y-2"><div class=\"flex items-center gap-2\"><div class=\"w-2 h-2 bg-blue-500 rounded-full\"></div><span class=\"font-medium text-gray-700 text-sm\">Fabric</span></div><div class=\"flex flex-wrap gap-2 ml-4\">${selectedFabrics.map(f=>pill(f,'bg-blue-100 text-blue-800')).join('')}</div></div>` : ''}
                        ${selectedEmbroidery.length ? `<div class=\"space-y-2\"><div class=\"flex items-center gap-2\"><div class=\"w-2 h-2 bg-green-500 rounded-full\"></div><span class=\"font-medium text-gray-700 text-sm\">Embroidery</span></div><div class=\"flex flex-wrap gap-2 ml-4\">${selectedEmbroidery.map(f=>pill(f,'bg-green-100 text-green-800')).join('')}</div></div>` : ''}
                        ${selectedColors.length ? `<div class=\"space-y-2\"><div class=\"flex items-center gap-2\"><div class=\"w-2 h-2 bg-purple-500 rounded-full\"></div><span class=\"font-medium text-gray-700 text-sm\">Colors</span></div><div class=\"flex flex-wrap gap-2 ml-4\">${selectedColors.map(f=>pill(f,'bg-purple-100 text-purple-800')).join('')}</div></div>` : ''}
                        ${selectedCollar ? `<div class=\"space-y-2\"><div class=\"flex items-center gap-2\"><div class=\"w-2 h-2 bg-orange-500 rounded-full\"></div><span class=\"font-medium text-gray-700 text-sm\">Collar Type</span></div><div class=\"flex flex-wrap gap-2 ml-4\"><span class=\"bg-orange-100 text-orange-800 px-3 py-1 text-xs rounded-full font-medium\">${selectedCollar}</span></div></div>` : ''}
                        ${selectedDesign.length ? `<div class=\"space-y-2 md:col-span-2\"><div class=\"flex items-center gap-2\"><div class=\"w-2 h-2 bg-indigo-500 rounded-full\"></div><span class=\"font-medium text-gray-700 text-sm\">Design Details</span></div><div class=\"flex flex-wrap gap-2 ml-4\">${selectedDesign.map(f=>pill(f,'bg-indigo-100 text-indigo-800')).join('')}</div></div>` : ''}
                    </div>`;
            }

            showSuccess('Attributes Saved', 'Your product attributes were updated.');
            modal.classList.add('hidden');
        });
    }
}

// =============== Images Upload / Preview ===============
function initializeImageUpload() {
    const input = document.getElementById('imageInput');
    const chooseBtn = document.getElementById('chooseImagesBtn'); // may not exist after wrapper change
    const dropZone = document.getElementById('dropZone');
    const previews = document.getElementById('imagePreviews');
    const counter = document.getElementById('imageCounter');
    const countSpan = document.getElementById('imageCount');
    const placeholder = document.getElementById('debugPlaceholder');
    if (!input || !previews) return;

    const logUiError = (where, err) => {
        const msg = (err && err.message) ? err.message : String(err || 'Unknown error');
        console.error(`[ImagesUI] ${where}:`, err);
        try { showError('Image Picker Error', where + ': ' + msg); } catch(_) {}
    };

    const updateCounter = () => {
        const total = previews.querySelectorAll('.image-preview-item').length;
        if (countSpan) countSpan.textContent = String(total);
        if (counter) counter.classList.toggle('hidden', total === 0);
        if (placeholder) placeholder.style.display = total === 0 ? 'block' : 'none';
    };

    const addPreview = (src) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'image-preview-item relative group';
        wrapper.innerHTML = `
            <div class=\"w-20 h-20 rounded-lg border-2 border-gray-200 overflow-hidden bg-gray-100\">
                <img src=\"${src}\" alt=\"Preview\" class=\"w-full h-full object-cover\">
            </div>
            <div class=\"absolute inset-0 bg-gray-800 bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex flex-col items-center justify-center space-y-1\">
                <button type=\"button\" class=\"set-cover-btn bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 text-xs rounded font-medium\">Set Cover</button>
                <button type=\"button\" class=\"remove-preview-btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded font-medium\">Remove</button>
            </div>`;

        // Attach button handlers (compute current index dynamically)
        wrapper.querySelector('.set-cover-btn').addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            const items = Array.from(previews.querySelectorAll('.image-preview-item'));
            const index = items.indexOf(wrapper);
            if (index >= 0) setCoverImage(index);
        });
        wrapper.querySelector('.remove-preview-btn').addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            wrapper.remove();
            updateCounter();
        });

        previews.insertBefore(wrapper, counter ? counter.nextSibling : previews.firstChild);
        updateCounter();
    };

    // Ensure we only attach one change listener
    if (!input.dataset.changeAttached) {
      input.addEventListener('change', (e) => {
        Array.from(e.target.files || []).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (ev) => addPreview(ev.target.result);
            reader.readAsDataURL(file);
        });
        // If we temporarily unhid the input to satisfy browser policies, re-hide it now
        if (input.dataset.tempShown === '1') {
            input.dataset.tempShown = '';
            input.classList.add('hidden');
            input.style.position = '';
            input.style.left = '';
            input.style.opacity = '';
            input.style.pointerEvents = '';
        }
      });
      input.dataset.changeAttached = '1';
    }

    if (dropZone) {
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-blue-400'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-blue-400'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400');
            Array.from(e.dataTransfer.files || []).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = (ev) => addPreview(ev.target.result);
                reader.readAsDataURL(file);
            });
        });
        dropZone.addEventListener('click', (e) => {
            e.preventDefault();
            if (!input) { logUiError('DropZone click: file input not found'); return; }
            try { input.click(); } catch (err) { logUiError('DropZone click: failed to open picker', err); }
        });
    }

    // Restore explicit JS button to open picker, with strong diagnostics
    // With wrapper approach, the input sits on top of the styled span, so no JS is needed to open the dialog
    if (!input) { console.warn('[ImagesUI] file input not found'); }

    // (Test button removed)

    // Global delegation fallback in case dynamic content breaks direct binding
    // No delegation needed with wrapper approach

    updateCounter();
}

// =============== Stock Calculation (exposed for inline oninput) ===============
function exposeStockCalculator() {
    // Define and expose calculateTotalStock to window so inline events can find it
    if (typeof window.calculateTotalStock !== 'function') {
        window.calculateTotalStock = function calculateTotalStock() {
            try {
                const inputs = document.querySelectorAll('.size-stock-input');
                let total = 0;
                inputs.forEach(inp => {
                    const val = parseInt(inp.value, 10);
                    if (!isNaN(val)) total += val;
                });
                const display = document.getElementById('total-stock-display');
                const hidden = document.getElementById('total-stock-input');
                if (display) display.textContent = String(total);
                if (hidden) hidden.value = String(total);
            } catch (err) {
                console.error('Stock calculation failed:', err);
            }
        };
    }
}

// Functions used by existing image items in edit mode
function setCoverImage(targetOrIndex) {
    // Resolve index whether called with button element or numeric index
    let index = typeof targetOrIndex === 'number' ? targetOrIndex : undefined;
    if (index === undefined && targetOrIndex) {
        const item = targetOrIndex.closest('.image-preview-item');
        const items = Array.from(document.querySelectorAll('#imagePreviews .image-preview-item'));
        index = items.indexOf(item);
    }
    if (index === undefined || index < 0) return;
    // Update hidden input
    let input = document.querySelector('input[name="cover_image_index"]');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cover_image_index';
        document.getElementById('barongProductForm').appendChild(input);
    }
    input.value = index;
    // Visually update borders & cover badge
    document.querySelectorAll('#imagePreviews .image-preview-item .w-20').forEach((el,i)=>{
        el.classList.remove('border-blue-500');
        el.classList.add('border-gray-200');
        if (i === Number(index)) {
            el.classList.remove('border-gray-200');
            el.classList.add('border-blue-500');
        }
    });

    // Toggle COVER badge
    const items = document.querySelectorAll('#imagePreviews .image-preview-item');
    items.forEach((item,i)=>{
        let badge = item.querySelector('.cover-badge');
        if (i === Number(index)) {
            if (!badge) {
                badge = document.createElement('div');
                badge.className = 'absolute top-1 left-1 bg-blue-600 text-white px-2 py-1 text-xs rounded font-medium cover-badge';
                badge.textContent = 'COVER';
                item.appendChild(badge);
            }
        } else if (badge) {
            badge.remove();
        }
    });
}

function removeImage(index) {
    // Mark for removal (optional UI only)
    const items = document.querySelectorAll('#imagePreviews .image-preview-item');
    if (items[index]) {
        items[index].remove();
    }
}
</script>
@endpush
