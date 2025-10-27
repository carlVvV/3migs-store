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
        Schema::table('reviews', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['product_id']);
            
            // Add the correct foreign key constraint
            $table->foreign('product_id')->references('id')->on('barong_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            $table->dropForeign(['product_id']);
            
            // Add back the original foreign key constraint
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};