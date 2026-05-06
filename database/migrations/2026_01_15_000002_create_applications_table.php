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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('applicant_id')->constrained('applicants','id')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('centers')->cascadeOnDelete();
            $table->string('current_status')->default('pending')->index();
            $table->enum('on_desk', ['DEO', 'DC', 'AC', 'ACR'])->default('DEO');

            $table->foreignId('application_type_id')->nullable()->constrained('types','id'); // New / Duplicate (Fade out, Guardian Change, Lost)
            $table->foreignId('application_for_id')->nullable()->constrained('types','id');
            $table->string('missal_no', 20)->nullable();

            $table->dateTime('entry_datetime')->nullable();

            $table->decimal('amount', 10)->default(0);

            $table->text('personal_image')->nullable();
            $table->foreignId('guardian_type_id')->nullable()->constrained('types','id');

            $table->foreignId('tehsil_id')->nullable()->constrained('demographies', 'id');
            $table->foreignId('district_id')->nullable()->constrained('demographies', 'id');
            $table->foreignId('region_id')->nullable()->nullable()->constrained('demographies', 'id');

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
        Schema::dropIfExists('applications');
    }
};
