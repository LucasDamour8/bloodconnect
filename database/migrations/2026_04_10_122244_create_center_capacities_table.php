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
        // Links the capacity to the specific location
        $table->foreignId('location_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->integer('max_donors');
        $table->timestamps();

        // Prevents creating two different limits for the same center on the same day
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
