{{-- Basic Information Section --}}
{{-- File: resources/views/admin/partials/product-form/basic-info.blade.php --}}

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Barong Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $barongProduct->name ?? '') }}" 
                   maxlength="255" required autocomplete="off"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="mt-1 text-xs text-gray-500">Maximum 255 characters</p>
        </div>
        
        <div>
            <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
            <input type="text" id="sku" name="sku" value="{{ old('sku', $barongProduct->sku ?? '') }}" 
                   readonly autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
            <p class="mt-1 text-xs text-gray-500">Auto-generated if left empty</p>
        </div>
        
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
            <select id="category_id" name="category_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $barongProduct->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="sleeve_type" class="block text-sm font-medium text-gray-700 mb-2">Sleeve Type</label>
            <select id="sleeve_type" name="sleeve_type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Sleeve Type</option>
                <option value="Long Sleeve" {{ old('sleeve_type', $barongProduct->sleeve_type ?? '') == 'Long Sleeve' ? 'selected' : '' }}>Long Sleeve</option>
                <option value="Short Sleeve" {{ old('sleeve_type', $barongProduct->sleeve_type ?? '') == 'Short Sleeve' ? 'selected' : '' }}>Short Sleeve</option>
            </select>
        </div>
    </div>
    
    <div class="mt-6">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
        <textarea id="description" name="description" rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $barongProduct->description ?? '') }}</textarea>
    </div>
</div>
