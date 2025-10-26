<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3Migs Gowns & Barong - Categories Test</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Sidebar Navigation -->
            <aside class="w-full lg:w-1/4">
                @include('layouts.categories-sidebar', ['categories' => $categories])
            </aside>

            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-6">3Migs Gowns & Barong - Categories Test</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($allProducts as $product)
                        <div class="bg-white rounded-lg shadow-md p-4">
                            <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $product->category->name ?? 'No Category' }}</p>
                            <p class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3Migs Gowns & Barong - Categories Test</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <!-- Main Header Navigation -->
    @include('layouts.header')

    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Sidebar Navigation -->
            <aside class="w-full lg:w-1/4">
                @include('layouts.categories-sidebar', ['categories' => $categories])
            </aside>

            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-6">3Migs Gowns & Barong - Categories Test</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($allProducts as $product)
                        <div class="bg-white rounded-lg shadow-md p-4">
                            <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $product->category->name ?? 'No Category' }}</p>
                            <p class="text-red-500 font-bold">₱{{ number_format($product->current_price, 0) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>
</html>


