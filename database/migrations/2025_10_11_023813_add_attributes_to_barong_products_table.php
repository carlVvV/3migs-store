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
            $table->json('collar_type')->nullable()->after('sleeve_type');
            $table->json('design_details')->nullable()->after('collar_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barong_products', function (Blueprint $table) {
            $table->dropColumn(['collar_type', 'design_details']);
        });
    }
};