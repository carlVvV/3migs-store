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
        Schema::table('id_documents', function (Blueprint $table) {
            if (Schema::hasColumn('id_documents', 'file_path')) {
                $table->dropColumn('file_path');
            }

            if (Schema::hasColumn('id_documents', 'file_public_id')) {
                $table->dropColumn('file_public_id');
            }

            $table->string('veriff_session_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('id_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('id_documents', 'file_path')) {
                $table->string('file_path')->nullable();
            }

            if (!Schema::hasColumn('id_documents', 'file_public_id')) {
                $table->string('file_public_id')->nullable();
            }

            if (Schema::hasColumn('id_documents', 'veriff_session_id')) {
                $table->dropIndex(['veriff_session_id']);
                $table->dropColumn('veriff_session_id');
            }
        });
    }
};

