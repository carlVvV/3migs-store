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
        Schema::table('custom_design_orders', function (Blueprint $table) {
            $table->string('reference_image')->nullable()->after('fabric_yardage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_design_orders', function (Blueprint $table) {
            $table->dropColumn('reference_image');
        });
    }
};
