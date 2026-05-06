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
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->enum('from_status', ['pending', 'submitted', 'verified', 'approved', 'ready_for_delivery', 'delivered', 'objected'])->nullable();
            $table->enum('to_status', ['pending', 'submitted', 'verified', 'approved', 'ready_for_delivery', 'delivered', 'objected'])->nullable();
            $table->enum('action', ['created', 'updated', 'forward', 'objected', 'rollback'])->default('forward');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
