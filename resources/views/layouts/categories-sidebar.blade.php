<!-- Categories Sidebar -->
<div class="w-64 bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center mb-4">
        <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
        <h3 class="text-lg font-semibold text-gray-800">Categories</h3>
    </div>
    <ul class="space-y-2">
        @foreach($categories as $category)
            <li>
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="flex items-center justify-between text-gray-700 hover:text-red-600 hover:bg-red-50 px-3 py-2 rounded-md transition-colors duration-200 group">
                    <span class="text-sm font-medium">{{ $category->name }}</span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-red-600 transition-colors"></i>
                </a>
            </li>
        @endforeach
    </ul>
    
    <!-- Custom Barong Order Button -->
    <div class="mt-4 pt-4 border-t border-gray-200">
        <a href="{{ route('custom-design') }}" 
           class="flex items-center justify-center bg-red-600 text-white hover:bg-red-700 text-sm font-medium py-3 px-4 rounded-md transition-colors duration-200">
            <i class="fas fa-palette mr-2"></i>
            <span>Custom Barong Order</span>
            <i class="fas fa-arrow-right ml-2 text-xs"></i>
        </a>
    </div>
</div>
