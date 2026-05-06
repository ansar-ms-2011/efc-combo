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
        Schema::table('required_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('max_size_in_mb')->nullable()->after('file_type');
            $table->unsignedBigInteger('max_size_in_bytes')->nullable()->after('max_size_in_mb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('required_documents', function (Blueprint $table) {
            $table->dropColumn(['max_size_in_mb', 'max_size_in_bytes']);
        });
    }
};
