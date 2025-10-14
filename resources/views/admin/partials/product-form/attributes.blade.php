{{-- Attributes Section --}}
{{-- File: resources/views/admin/partials/product-form/attributes.blade.php --}}

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Product Attributes</h2>
            <p class="text-sm text-gray-500 mt-1">Define key characteristics for filtering and display</p>
        </div>
        <button type="button" id="setAttributesBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Set Attributes
        </button>
    </div>
    
    <!-- Enhanced Display Selected Attributes -->
    <div id="selectedAttributes" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        @if(isset($barongProduct))
            @if($barongProduct->fabric || $barongProduct->embroidery_style || $barongProduct->colors || $barongProduct->collar_type || $barongProduct->design_details)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($barongProduct->fabric)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <span class="font-medium text-gray-700 text-sm">Fabric</span>
                            </div>
                            <div class="flex flex-wrap gap-2 ml-4">
                                @foreach($barongProduct->fabric as $fabric)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 text-xs rounded-full font-medium">{{ $fabric }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($barongProduct->embroidery_style)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="font-medium text-gray-700 text-sm">Embroidery</span>
                            </div>
                            <div class="flex flex-wrap gap-2 ml-4">
                                @foreach($barongProduct->embroidery_style as $style)
                                    <span class="bg-green-100 text-green-800 px-3 py-1 text-xs rounded-full font-medium">{{ $style }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($barongProduct->colors)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                <span class="font-medium text-gray-700 text-sm">Colors</span>
                            </div>
                            <div class="flex flex-wrap gap-2 ml-4">
                                @foreach($barongProduct->colors as $color)
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 text-xs rounded-full font-medium">{{ $color }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($barongProduct->collar_type)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                <span class="font-medium text-gray-700 text-sm">Collar Type</span>
                            </div>
                            <div class="flex flex-wrap gap-2 ml-4">
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 text-xs rounded-full font-medium">{{ $barongProduct->collar_type }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($barongProduct->design_details)
                        <div class="space-y-2 md:col-span-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                                <span class="font-medium text-gray-700 text-sm">Design Details</span>
                            </div>
                            <div class="flex flex-wrap gap-2 ml-4">
                                @foreach($barongProduct->design_details as $detail)
                                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 text-xs rounded-full font-medium">{{ $detail }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No attributes configured yet</p>
                    <p class="text-gray-400 text-xs mt-1">Click "Set Attributes" to add fabric, embroidery, colors, and other details</p>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No attributes set</p>
                <p class="text-gray-400 text-xs mt-1">Click "Set Attributes" to configure fabric, embroidery, colors, and other details</p>
            </div>
        @endif
    </div>
</div>
