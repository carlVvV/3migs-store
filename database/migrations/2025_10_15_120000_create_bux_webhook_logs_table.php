<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bux_webhook_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('provider')->default('bux');
            $table->text('raw_body');
            $table->json('headers')->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->nullable(); // e.g., paid, failed, pending
            $table->integer('http_status')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['provider', 'created_at']);
            $table->index(['order_number']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bux_webhook_logs');
    }
};


