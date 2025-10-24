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
        Schema::create('psgc_barangays', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('region_code');
            $table->string('region_name');
            $table->string('province_code');
            $table->string('province_name');
            $table->string('city_code');
            $table->string('city_name');
            $table->timestamps();
            
            $table->index('region_code');
            $table->index('province_code');
            $table->index('city_code');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psgc_barangays');
    }
};