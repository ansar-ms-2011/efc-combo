<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_certificates', function (Blueprint $table) {

            $table->dropForeign(['application_id']);

            $table->foreignId('application_id')->nullable()->change();
            $table->string('certificate_number')->nullable()->change();
            $table->date('issue_date')->nullable()->change();

            $table->enum('source', ['application', 'archive', 'import'])
                  ->default('application')
                  ->after('type');

            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->after('source')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreign('application_id')
                  ->references('id')
                  ->on('applications')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('application_certificates', function (Blueprint $table) {

            $table->dropForeign(['application_id']);
            $table->dropForeign(['uploaded_by']);

            $table->foreignId('application_id')->nullable(false)->change();
            $table->string('certificate_number')->nullable(false)->change();
            $table->date('issue_date')->nullable(false)->change();
            $table->string('qr_code_hash')->nullable(false)->change();

            $table->dropColumn(['source', 'uploaded_by']);

            $table->foreign('application_id')
                  ->references('id')
                  ->on('applications')
                  ->cascadeOnDelete();
        });
    }
};
