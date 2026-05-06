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
        Schema::create('application_certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['state', 'domicile', 'birth-certificate', 'death-certificate', 'arms-licence'])->comment('Certificate type');

            $table->string('certificate_number')->unique()->nullable();
            $table->date('issue_date')->nullable();

            // Path to generated PDF
            $table->string('pdf_path')->nullable();
            $table->string('preview_path')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->foreignId('application_certificate_id')->nullable()->constrained('application_certificates', 'id')->nullOnDelete();
            $table->timestamps();

            $table->index(['applicant_id', 'application_id', 'issue_date'],
                'app_cert_app_issue_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_certificates');
    }
};
