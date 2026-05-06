<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('application_biometrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants', 'id')->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('applications', 'id')->nullOnDelete();
            $table->enum('finger_type', ['thumb', 'index', 'middle', 'ring', 'little'])->comment('thumb, index, middle, ring, little');
            $table->string('image_path')->nullable();
            $table->longText('feature_set')->nullable();
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->foreignId('deleted_by')->nullable()->constrained('users', 'id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_biometrics');
    }
};
