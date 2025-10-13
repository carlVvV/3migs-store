@extends('layouts.app')

@section('title', 'All Categories - 3Migs Gowns & Barong')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-3">
            <nav class="text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900 font-medium">All Categories</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <div class="flex items-center justify-center mb-4">
                <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                <h1 class="text-4xl font-bold text-gray-900">All Categories</h1>
            </div>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Discover our complete collection of traditional and modern Filipino barong designs. 
                From formal occasions to casual wear, find the perfect barong for every event.
            </p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
            @foreach($categories as $category)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 group">
                    <!-- Category Image -->
                    <div class="aspect-w-16 aspect-h-12 bg-gray-200 relative overflow-hidden">
                        @if($category->image && file_exists(public_path($category->image)))
                            <img src="{{ asset($category->image) }}" 
                                 alt="{{ $category->name }}" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-red-50 to-red-100">
                                <i class="fas fa-tshirt text-6xl text-red-300"></i>
                            </div>
                        @endif
                        
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $category->products_count ?? 0 }} Products
                            </span>
                        </div>
                    </div>
                    
                    <!-- Category Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $category->description }}
                        </p>
                        
                        <!-- Category Stats -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-tag mr-1"></i>
                                <span>{{ $category->products_count ?? 0 }} items</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>4.8</span>
                            </div>
                        </div>
                        
                        <!-- View Category Button -->
                        <a href="{{ route('category.show', $category->slug) }}" 
                           class="w-full bg-black text-white py-3 px-4 rounded-md hover:bg-gray-800 transition-colors duration-200 text-center block font-semibold">
                            <i class="fas fa-arrow-right mr-2"></i>
                            View Category
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Featured Categories Section -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-12">
            <div class="flex items-center mb-6">
                <span class="w-2 h-8 bg-red-500 mr-3 rounded-sm"></span>
                <h2 class="text-2xl font-bold text-gray-900">Featured Categories</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($categories->take(3) as $category)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tshirt text-2xl text-red-500"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $category->products_count ?? 0 }} products available</p>
                        </div>
                        <a href="{{ route('category.show', $category->slug) }}" 
                           class="text-red-600 hover:text-red-700">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Can't Find What You're Looking For?</h3>
            <p class="text-red-100 mb-6 max-w-2xl mx-auto">
                Our collection is constantly growing. Contact us for custom barong designs or special requests.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#contact" 
                   class="bg-white text-red-600 px-6 py-3 rounded-md font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-phone mr-2"></i>
                    Contact Us
                </a>
                <a href="{{ route('home') }}" 
                   class="border-2 border-white text-white px-6 py-3 rounded-md font-semibold hover:bg-white hover:text-red-600 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
