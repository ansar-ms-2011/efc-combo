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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            $table->string('full_name');
            $table->string('identity_number', 15);
            $table->string('identity_type', 15);            //CNIC or Refugee
            $table->date('dob')->nullable()->comment('Date of Birth');
            $table->string('pob')->nullable()->comment('Place of Birth');
            $table->string('identity_symbol')->nullable();

            $table->string('father_name')->nullable();
            $table->string('father_identity_number', 15)->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();

            $table->string('occupation')->nullable();
            $table->string('wife_husband_name')->nullable();
            $table->foreignId('guardian_type_id')->nullable()->constrained('types','id');

            $table->string('state_subject_class')->nullable()->comment('Darjha');      //Darjha
            $table->string('residence_place')->nullable()->comment('Sakinah');         //Sakinah

            $table->text('address')->nullable();
            $table->text('address2')->nullable();
            $table->text('address3')->nullable();
            $table->text('address4')->nullable();

            $table->foreignId('region_id')->nullable()->constrained('demographies', 'id');
            $table->foreignId('district_id')->nullable()->constrained('demographies', 'id');
            $table->foreignId('tehsil_id')->nullable()->constrained('demographies', 'id');

            $table->foreignId('religion_id')->nullable()->constrained('types', 'id');
            $table->foreignId('gender_id')->nullable()->constrained('types', 'id');
            $table->foreignId('marital_status_id')->constrained('types', 'id');

            $table->string('location', 200)->nullable();
            $table->string('personal_image')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->foreignId('deleted_by')->nullable()->constrained('users', 'id');

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['identity_number', 'identity_type', 'deleted_at']);
            $table->index('region_id');
            $table->index('district_id');
            $table->index('tehsil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
