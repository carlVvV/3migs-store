{{-- Images and Media Section --}}
{{-- File: resources/views/admin/partials/product-form/images-media.blade.php --}}

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Images and Media</h2>
    
    <!-- Drag and Drop Upload Area -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">Product Images (Up to 8 images)</label>
            <div class="relative inline-block" id="chooseImagesWrapper">
                <span class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors duration-200 inline-block select-none">Choose Images</span>
                <!-- File input sits on top of the styled span to guarantee a real user gesture triggers the dialog -->
                <input type="file" id="imageInput" name="images[]" multiple accept="image/*"
                       class="absolute inset-0 opacity-0 cursor-pointer" aria-hidden="false" />
            </div>
        </div>
        <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer">
            <div class="space-y-3 select-none">
                <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div>
                    <p class="text-lg font-medium text-gray-900">Upload Images</p>
                    <p class="text-sm text-gray-500">Drag and drop here or click “Choose Images”</p>
                    <p class="text-xs text-gray-400 mt-1">Supports: JPG, PNG, GIF (Max 2MB each, up to 8 images)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Previews -->
    <div id="imagePreviews" class="flex flex-wrap gap-3 mb-6" style="min-height: 80px;">
        <!-- Image Counter -->
        <div id="imageCounter" class="w-full mb-2 text-sm text-gray-600 hidden">
            <span id="imageCount">0</span> images uploaded (max 8)
        </div>
        
        <!-- Placeholder -->
        <div id="debugPlaceholder" class="w-full text-center text-gray-300 text-sm py-2">
            No images uploaded yet
        </div>
        
        <!-- Existing images for edit mode -->
        @if(isset($barongProduct) && $barongProduct->images)
            @foreach($barongProduct->images as $index => $image)
                <div class="image-preview-item relative group" data-image-index="{{ $index }}">
                    <!-- Square image container -->
                    <div class="w-20 h-20 rounded-lg border-2 {{ $barongProduct->cover_image == $image ? 'border-blue-500' : 'border-gray-200' }} overflow-hidden bg-gray-100">
                        <img src="{{ asset('storage/' . $image) }}" alt="Product Image" 
                             class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Cover Badge -->
                    @if($barongProduct->cover_image == $image)
                        <div class="absolute top-1 left-1 bg-blue-600 text-white px-2 py-1 text-xs rounded font-medium cover-badge">
                            COVER
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="absolute inset-0 bg-gray-800 bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex flex-col items-center justify-center space-y-1">
                        <button type="button" onclick="setCoverImage(this)" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 text-xs rounded font-medium">
                            {{ $barongProduct->cover_image == $image ? 'Cover' : 'Set Cover' }}
                        </button>
                        <button type="button" onclick="removeImage({{ $index }})" 
                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded font-medium">
                            Remove
                        </button>
                    </div>
                    
                    <!-- Drag Handle -->
                    <div class="absolute top-1 right-1 cursor-move text-white opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                    </div>
                </div>
            @endforeach
            
            <!-- Initialize cover image index for existing products -->
            @if(isset($barongProduct) && $barongProduct->cover_image)
                @php
                    $coverIndex = array_search($barongProduct->cover_image, $barongProduct->images);
                @endphp
                @if($coverIndex !== false)
                    <input type="hidden" name="cover_image_index" value="{{ $coverIndex }}">
                @endif
            @endif
        @endif
    </div>

    <!-- Video Upload -->
    <div class="mt-6">
        <div class="flex items-center mb-4">
            <input type="checkbox" id="enable_video" name="enable_video" 
                   {{ old('enable_video', isset($barongProduct) && $barongProduct->video_url ? 'checked' : '') }}
                   class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="enable_video" class="ml-2 text-sm font-medium text-gray-700">Enable Video</label>
        </div>
        <div id="videoSection" class="{{ old('enable_video', isset($barongProduct) && $barongProduct->video_url ? '' : 'hidden') }}">
            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">Video URL</label>
            <input type="url" id="video_url" name="video_url" 
                   value="{{ old('video_url', $barongProduct->video_url ?? '') }}" 
                   placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..."
                   autocomplete="url"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="mt-1 text-xs text-gray-500">YouTube or Vimeo URL</p>
            
            <!-- Video Preview -->
            <div id="videoPreview" class="mt-4 hidden">
                <iframe id="videoFrame" class="w-full h-64 rounded-lg" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
