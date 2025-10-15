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
        Schema::create('custom_design_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_method')->default('ewallet'); // ewallet, cod
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('PHP');
            
            // Custom design specifications
            $table->string('fabric');
            $table->string('color');
            $table->string('embroidery')->nullable();
            $table->integer('quantity')->default(1);
            $table->json('measurements'); // chest, waist, length, shoulder_width, sleeve_length
            $table->decimal('fabric_yardage', 5, 2);
            $table->json('pricing'); // fabricCost, embroideryCost, totalCost, etc.
            $table->text('additional_notes')->nullable();
            
            // Billing information
            $table->json('billing_address'); // full_name, company_name, street_address, etc.
            $table->json('shipping_address')->nullable(); // Can be different from billing
            
            // Payment and transaction details
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_provider')->nullable(); // bux, gcash, etc.
            
            // Order tracking
            $table->timestamp('estimated_completion_date')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('assigned_to')->nullable(); // Admin user assigned to handle this order
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_design_orders');
    }
};