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
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            
            $table->foreignId('from_region_id')->nullable()->constrained('demographies');
            $table->foreignId('from_district_id')->nullable()->constrained('demographies');
            $table->foreignId('from_tehsil_id')->nullable()->constrained('demographies');

            
            $table->foreignId('to_region_id')->nullable()->constrained('demographies');
            $table->foreignId('to_district_id')->nullable()->constrained('demographies');
            $table->foreignId('to_tehsil_id')->nullable()->constrained('demographies');

           
            $table->foreignId('center_id')->nullable()->constrained('centers');

            
            $table->string('posting_letter')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_details');
    }
};
