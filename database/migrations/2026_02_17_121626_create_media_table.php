<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('media', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('url');
        $table->string('seo')->nullable();
        $table->morphs('mediable'); // For polymorphic relation (if needed)
           $table->timestamps();   // created_at & updated_at
            $table->softDeletes();  // deleted_at (for soft delete)
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
