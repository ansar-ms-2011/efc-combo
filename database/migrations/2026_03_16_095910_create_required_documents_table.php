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
        Schema::create('required_documents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('urdu_name')->nullable();
            $table->enum('service_name', ['domicile', 'state', 'both']);
            $table->enum('service_type', ['new', 'duplicate', 'both']);
            $table->enum('required_copy', ['original', 'photocopy', 'scanned']);
            $table->foreignId('reason_type_id')->nullable()->constrained('types', 'id')->nullOnDelete();
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('required_documents');
    }
};
