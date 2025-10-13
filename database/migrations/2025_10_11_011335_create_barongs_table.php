<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barong_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // Traditional Barong, Modern Barong, etc.
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // Images and Media
            $table->json('images')->nullable(); // Array of image paths
            $table->string('cover_image')->nullable(); // Main cover image
            $table->string('video_url')->nullable(); // Product video URL
            
            // Attributes
            $table->json('fabric')->nullable(); // Array of fabric types
            $table->json('embroidery_style')->nullable(); // Array of embroidery styles
            $table->json('colors')->nullable(); // Array of available colors
            $table->string('sleeve_type')->nullable(); // Long Sleeve, Short Sleeve
            
            // Pricing and Stock
            $table->decimal('base_price', 10, 2); // Base price in PHP
            $table->decimal('special_price', 10, 2)->nullable(); // Special/sale price
            $table->integer('stock')->default(0); // Base stock quantity
            $table->json('variations')->nullable(); // Array of size/color variations with prices and stock
            
            // Status and Metadata
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_variations')->default(false);
            $table->string('sku')->unique();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['is_available', 'is_featured']);
            $table->index(['type', 'is_available']);
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barong_products');
    }
};
