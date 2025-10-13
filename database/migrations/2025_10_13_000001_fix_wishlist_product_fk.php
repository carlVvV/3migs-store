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
        Schema::table('wishlists', function (Blueprint $table) {
            // Drop existing foreign key on product_id if it exists (likely references products)
            try {
                $table->dropForeign(['product_id']);
            } catch (\Throwable $e) {
                // ignore if not present
            }

            // Recreate foreign key to barong_products
            $table->foreign('product_id')
                ->references('id')
                ->on('barong_products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            try {
                $table->dropForeign(['product_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            // Recreate original foreign to products table (best-effort)
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }
};


