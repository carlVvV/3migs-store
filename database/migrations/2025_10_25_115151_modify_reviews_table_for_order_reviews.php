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
        Schema::table('reviews', function (Blueprint $table) {
            // Add order_id column if it doesn't exist
            if (!Schema::hasColumn('reviews', 'order_id')) {
                $table->unsignedBigInteger('order_id')->after('product_id');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            }

            // Add review_text column if it doesn't exist (rename from comment if exists)
            if (Schema::hasColumn('reviews', 'comment')) {
                $table->renameColumn('comment', 'review_text');
            } elseif (!Schema::hasColumn('reviews', 'review_text')) {
                $table->text('review_text')->nullable()->after('rating');
            }

            // Add is_approved column if it doesn't exist
            if (!Schema::hasColumn('reviews', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('is_verified_purchase');
            }

            // Remove columns that we don't need
            if (Schema::hasColumn('reviews', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('reviews', 'helpful_count')) {
                $table->dropColumn('helpful_count');
            }
            if (Schema::hasColumn('reviews', 'images')) {
                $table->dropColumn('images');
            }

            // Add indexes
            $table->index(['product_id', 'is_approved']);
            $table->index(['user_id', 'product_id']);
            $table->index(['order_id']);

            // Add unique constraint to prevent duplicate reviews
            $table->unique(['user_id', 'product_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Remove the unique constraint
            $table->dropUnique(['user_id', 'product_id', 'order_id']);
            
            // Remove indexes
            $table->dropIndex(['product_id', 'is_approved']);
            $table->dropIndex(['user_id', 'product_id']);
            $table->dropIndex(['order_id']);

            // Add back removed columns
            $table->string('title')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->json('images')->nullable();

            // Remove added columns
            if (Schema::hasColumn('reviews', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
            if (Schema::hasColumn('reviews', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
        });
    }
};