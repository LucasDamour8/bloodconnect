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
    Schema::create('center_capacities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('location_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->integer('max_donors');
        $table->timestamps();

        // Prevent double entries for the same day at the same center
        $table->unique(['location_id', 'date']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_capacities');
    }
};
