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
            $table->decimal('wholesale_price', 10, 2)->nullable()->after('special_price');
            $table->integer('wholesale_minimum_quantity')->default(20)->after('wholesale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barong_products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'wholesale_minimum_quantity']);
        });
    }
};
