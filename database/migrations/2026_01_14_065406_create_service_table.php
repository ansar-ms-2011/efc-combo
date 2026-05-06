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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('dept_id')->references('id')->on('departments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('no_of_days');
            $table->string('service_icon');
            $table->longText('service_description');
            $table->string('file');            
            $table->decimal('price');
            $table->foreignId('status')->nullable()->references('id')->on('types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ip_address')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
            
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
