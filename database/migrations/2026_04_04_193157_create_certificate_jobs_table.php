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
        Schema::create('certificate_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained('applications', 'id');

            $table->enum('type', ['state', 'domicile', 'birth-certificate', 'death-certificate', 'arms-licence'])->comment('Certificate type');

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                're-initiated',
                'cancelled',
            ])->default('pending');

            $table->text('message')->nullable();

            $table->timestamps();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->index(['application_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_jobs');
    }
};
