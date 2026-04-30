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
    Schema::create('locations', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('address');
        $table->string('city');
        $table->string('hours')->default('08:00 - 17:00'); // Needed for line 31
        
        // Add these columns specifically:
        $table->boolean('is_active')->default(true);       // Needed for line 26
        $table->string('availability')->default('high');   // Needed for line 23 & 30
        $table->boolean('walk_ins')->default(true);       // Needed for line 29
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
