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
            $table->enum('file_type', ['pdf', 'image', 'both'])->nullable()->after('required_copy');   
                 });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('required_documents', function (Blueprint $table) {
            $table->dropColumn('file_type');
        });
    }
};
