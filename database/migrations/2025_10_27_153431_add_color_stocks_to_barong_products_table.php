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
        Schema::table('barong_products', function (Blueprint $table) {
            $table->json('color_stocks')->nullable()->after('size_stocks')->comment('Color and size-specific stock quantities in format: {size: {color: quantity}}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barong_products', function (Blueprint $table) {
            $table->dropColumn('color_stocks');
        });
    }
};
