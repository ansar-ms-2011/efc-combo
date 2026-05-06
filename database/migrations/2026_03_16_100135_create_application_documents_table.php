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
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications','id');
            $table->foreignId('required_document_id')->constrained('required_documents','id');

            $table->enum('upload_method', ['manual', 'scanner'])->default('manual');
            $table->string('file_path');
            $table->string('mime_type', 100)->nullable();
            $table->string('original_name')->nullable();

            $table->boolean('ac_acr_verified')->default(false);
            $table->date('ac_acr_verified_date')->nullable();
            $table->boolean('dc_verified')->default(false);
            $table->date('dc_verified_date')->nullable();
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users','id');
            $table->foreignId('updated_by')->nullable()->constrained('users','id');
            $table->foreignId('deleted_by')->nullable()->constrained('users','id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};
