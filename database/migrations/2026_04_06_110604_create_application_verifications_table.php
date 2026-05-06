<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_verifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('application_certificate_id')
                ->constrained('application_certificates')
                ->onDelete('cascade');

            $table->string('status')->default('pending')->comment('verified, rejected, pending');

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('img_upload_by')
                ->nullable()
                ->constrained('users')->nullOnDelete();

            $table->foreignId('data_enter_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_verifications');
    }
};
