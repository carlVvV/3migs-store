{{-- Attributes Modal --}}
{{-- File: resources/views/admin/partials/product-form/attributes-modal.blade.php --}}

<!-- Small & User-Friendly Attributes Modal -->
<div id="attributesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto border w-full max-w-xl shadow-xl rounded-lg bg-white max-h-[60vh] overflow-y-auto">
        <div class="p-5">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Product Attributes</h3>
                    <p class="text-xs text-gray-500 mt-1">Select fabric, embroidery, colors & details</p>
                </div>
                <button type="button" id="closeAttributesModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                <!-- User-Friendly Fabric Selection -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Fabric</label>
                    </div>
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_jusi" name="fabric[]" value="Jusi" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Jusi</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_pinya" name="fabric[]" value="Pinya" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Pinya</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_organza" name="fabric[]" value="Organza" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Organza</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_ramie" name="fabric[]" value="Ramie" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Ramie</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_silk_cocoon" name="fabric[]" value="Silk Cocoon" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Silk Cocoon</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_linen" name="fabric[]" value="Linen" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Linen</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_cotton" name="fabric[]" value="Cotton" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Cotton</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="fabric_other" name="fabric[]" value="Other" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Other</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Embroidery Selection -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Embroidery</label>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_u_shape" name="embroidery_style[]" value="U-Shape" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">U-Shape</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_full_front" name="embroidery_style[]" value="Full Front" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Full Front</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_computerized" name="embroidery_style[]" value="Computerized" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Computerized</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_hand_embroidered" name="embroidery_style[]" value="Hand-Embroidered" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Hand-Embroidered</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="embroidery_calado" name="embroidery_style[]" value="Calado" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Calado</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="embroidery_burda" name="embroidery_style[]" value="Burda" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Burda</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Color Selection -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Colors</label>
                    </div>
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_ecru" name="colors[]" value="Ecru" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Ecru</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_white" name="colors[]" value="White" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">White</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_beige" name="colors[]" value="Beige" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Beige</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_black" name="colors[]" value="Black" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Black</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_blue" name="colors[]" value="Blue" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Blue</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="color_brown" name="colors[]" value="Brown" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Brown</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="color_green" name="colors[]" value="Green" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Green</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="color_red" name="colors[]" value="Red" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Red</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Collar Type -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Collar Type</label>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_chinese" name="collar_type" value="Chinese Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Chinese Collar</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_sport" name="collar_type" value="Sport Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Sport Collar</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_mandarin" name="collar_type" value="Mandarin Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Mandarin Collar</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                            <input type="radio" id="collar_standing" name="collar_type" value="Standing Collar" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            <span class="ml-2 text-xs text-gray-700">Standing Collar</span>
                        </label>
                    </div>
                </div>

                <!-- User-Friendly Design Details -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                        <label class="text-sm font-semibold text-gray-900">Design Details</label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="design_side_slits" name="design_details[]" value="Side Slits" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Side Slits</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="design_buttons" name="design_details[]" value="Buttons" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Buttons</span>
                        </label>
                        <label class="flex items-center p-2 bg-gray-50 rounded border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" id="design_cufflinks" name="design_details[]" value="Cufflinks Ready" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">Cufflinks Ready</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- User-Friendly Modal Footer -->
            <div class="flex justify-end space-x-2 mt-3 pt-3 border-t border-gray-200">
                <button type="button" id="cancelAttributes" 
                        class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded text-xs font-medium hover:bg-gray-200 transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" id="saveAttributes" 
                        class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
