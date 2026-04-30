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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('location_id')->constrained()->onDelete('cascade');
        $table->string('tracking_id')->unique(); // For donor reference
        $table->date('appointment_date');
        $table->time('appointment_time');
        $table->string('donation_type'); // e.g., whole_blood, plasma
        $table->enum('status', ['scheduled', 'pending', 'cancelled', 'completed'])->default('scheduled');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
