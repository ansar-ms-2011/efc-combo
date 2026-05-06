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
        Schema::create('service_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services','id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('instruction_title');
            $table->longText('instruction_description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users','id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()->constrained('users','id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_instructions');
    }
};
