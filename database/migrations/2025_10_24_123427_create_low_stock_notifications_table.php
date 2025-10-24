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
        Schema::create('low_stock_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->string('product_sku');
            $table->integer('current_stock');
            $table->integer('threshold')->default(5);
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('notified_at');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('barong_products')->onDelete('cascade');
            $table->index(['is_resolved', 'notified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('low_stock_notifications');
    }
};
