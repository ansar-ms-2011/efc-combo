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
        Schema::create('centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->nullable()->constrained('demographies','id');
            $table->foreignId('tehsil_id')->nullable()->constrained('demographies','id');
            $table->foreignId('working_days')->nullable()->constrained('types','id');
            $table->string('name');
            $table->string('number_of_counters')->nullable();
            $table->string('address')->nullable();
            $table->string('timing')->nullable();
            $table->string('lunch_break')->nullable();
            $table->string('jumma_break')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users','id');
            $table->foreignId('updated_by')->nullable()->constrained('users','id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centers');
    }
};


