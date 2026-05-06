<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demographies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('demographies')->nullOnDelete();
            $table->string('name');
            $table->string('urdu_name')->nullable();
            $table->string('code')->nullable();
            $table->string('type'); // Changed from enum to string to support all types
            $table->string('town_type')->nullable(); // Added for Town/MC/TC types
            $table->integer('population')->nullable(); // Added for village stats
            $table->integer('house_holds')->nullable(); // Added for village stats
            $table->boolean('is_ajk_district')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demographies');
    }
};
