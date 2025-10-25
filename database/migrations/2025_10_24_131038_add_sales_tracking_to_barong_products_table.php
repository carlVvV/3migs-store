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
            $table->integer('sales_count')->default(0)->after('sort_order');
            $table->integer('monthly_sales')->default(0)->after('sales_count');
            $table->timestamp('last_sale_at')->nullable()->after('monthly_sales');
            
            // Add indexes for better performance
            $table->index(['sales_count', 'is_available']);
            $table->index(['monthly_sales', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barong_products', function (Blueprint $table) {
            $table->dropIndex(['sales_count', 'is_available']);
            $table->dropIndex(['monthly_sales', 'is_available']);
            $table->dropColumn(['sales_count', 'monthly_sales', 'last_sale_at']);
        });
    }
};
