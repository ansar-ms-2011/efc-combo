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
        Schema::create('approval_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();

            $table->foreignId('officer_id')->constrained('users', 'id');
            $table->string('officer_name')->nullable();
            $table->string('designation')->nullable();

            // e-signature file / hash / reference
            $table->mediumText('esign')->nullable();
            $table->timestamp('sign_date')->nullable();

            $table->string('level')->nullable()->comment('e.g. AC, ACR, DC'); // e.g., AC, DC

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_details');
    }
};
