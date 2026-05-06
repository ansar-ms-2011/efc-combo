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
        Schema::create('refugee_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();

            $table->string('refugee_from')->nullable();
            $table->year('refugee_year')->nullable();
            $table->string('refugee_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refugee_details');
    }
};
