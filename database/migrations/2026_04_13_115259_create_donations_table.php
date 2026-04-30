<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');

            // Blood Details
            $table->string('blood_type'); // Combined Group + Rhesus (e.g., A+)

            // Vital Signs
            $table->integer('age');
            $table->decimal('weight', 5, 2);
            $table->string('blood_pressure');
            $table->integer('pulse_rate');
            $table->decimal('temperature', 4, 2);
            $table->decimal('hemoglobin', 4, 2);

            // Medical Screening
            $table->string('hiv_test')->default('negative');
            $table->string('hep_b')->default('negative');
            $table->string('hep_c')->default('negative');
            $table->string('syphilis')->default('negative');
            
            // Results & Notes
            $table->text('general_health')->nullable();
            $table->text('conclusion')->nullable();
            $table->enum('status', ['pending_collection', 'completed', 'deferred', 'cancelled'])
                  ->default('pending_collection');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
