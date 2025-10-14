// Product Form JavaScript - Form Submission
// File: resources/js/admin/product-form-submission.js

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
    if (typeof window.getUploadedImages === 'function') {
        const uploadedImages = window.getUploadedImages();
        uploadedImages.forEach((imageData, index) => {
            formData.append(`new_images[${index}]`, imageData.file);
        });
    }
    
    // Debug: Log form data
    console.log('📋 Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    const url = form.action || window.location.href;
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
                    window.location.href = '/admin/products';
                } catch (error) {
                    console.error('❌ Route generation failed:', error);
                    window.location.href = '/admin/products';
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

function initializeFormSubmission() {
    const form = document.getElementById('barongProductForm');

    if (form) {
        console.log('✅ Form element found:', form);
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('🚀 Form submission intercepted by JavaScript');
            submitProductForm();
        });
    } else {
        console.error('❌ Form element not found!');
    }

    // Additional safety: Prevent any form submission that might bypass our handler
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'barongProductForm') {
            console.log('🛡️ Additional form submission intercepted');
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);
}
