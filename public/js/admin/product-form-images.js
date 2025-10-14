// Product Form JavaScript - Image Upload
// File: resources/js/admin/product-form-images.js

// Image upload functionality
let uploadedImages = [];
let coverImageIndex = null;

function initializeImageUpload() {
    const dropZone = document.getElementById('dropZone');
    const imageInput = document.getElementById('imageInput');
    const imagePreviews = document.getElementById('imagePreviews');
    
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

    // Initialize counter on page load
    updateImageCounter();
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

// Export for use in form submission
window.getUploadedImages = function() {
    return uploadedImages;
};
