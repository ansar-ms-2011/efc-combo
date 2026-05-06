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
        
        Schema::create('center_working_days', function (Blueprint $table) {
            $table->id(); // Primary ID

            // Foreign Key for Centers
            $table->foreignId('center_id')
                  ->constrained('centers')
                  ->onDelete('cascade');

            // Foreign Key for Working Days
            $table->foreignId('working_day_id')
                  ->constrained('types')
                  ->onDelete('cascade');

            // Time column (e.g., "09:00 - 17:00")
            // $table->string('time')->nullable();

           
            $table->softDeletes(); // Deleted_at column
              $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_working_days');
    }
};