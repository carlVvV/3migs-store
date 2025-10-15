<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processed_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->index();
            $table->string('idempotency_key')->unique();
            $table->timestamp('processed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_webhooks');
    }
};


