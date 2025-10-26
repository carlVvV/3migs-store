<!-- Categories Sidebar -->
<div class="w-64 bg-white rounded-lg shadow-md p-4 max-h-[calc(100vh-8rem)] flex flex-col sticky top-20" style="align-self: start;">
    <div class="flex items-center mb-3">
        <span class="w-2 h-6 bg-red-500 mr-2 rounded-sm"></span>
        <h3 class="text-base font-semibold text-gray-800">Categories</h3>
    </div>
    <ul class="space-y-1 flex-1 overflow-y-auto">
        @foreach($categories as $category)
            <li>
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="flex items-center justify-between text-gray-700 hover:text-red-600 hover:bg-red-50 px-2 py-2 rounded-md transition-colors duration-200 group">
                    <span class="text-xs font-medium">{{ $category->name }}</span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-red-600 transition-colors"></i>
                </a>
            </li>
        @endforeach
    </ul>
    
    <!-- Custom Barong Order Button -->
    <div class="mt-3 pt-3 border-t border-gray-200">
        <a href="{{ route('custom-design') }}" 
           class="flex items-center justify-center bg-red-600 text-white hover:bg-red-700 text-xs font-medium py-2 px-3 rounded-md transition-colors duration-200">
            <i class="fas fa-palette mr-1 text-xs"></i>
            <span>Custom Barong Order</span>
            <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
</div>
