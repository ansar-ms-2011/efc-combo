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
        Schema::table('certificate_jobs', function (Blueprint $table) {
            $table->boolean('re_initiated')->default(false)->after('status');
            $table->timestamp('re_initiated_at')->nullable()->after('re_initiated');
            $table->foreignId('re_initiated_by')->nullable()->after('re_initiated_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_jobs', function (Blueprint $table) {
            $table->dropColumn(['re_initiated', 're_initiated_at', 're_initiated_by']);
        });
    }
};
