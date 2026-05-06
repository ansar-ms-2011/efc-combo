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
    
        Schema::table('application_certificates', function (Blueprint $table) {
            $table->string('misal_no')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       

        Schema::table('application_certificates', function (Blueprint $table) {
            $table->dropColumn('misal_no');
        });
    }
};