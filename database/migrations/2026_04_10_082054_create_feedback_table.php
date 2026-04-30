<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            // The sender (Donor or Doctor)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('subject');
            $table->text('message');
            
            // The private response from the Admin
            $table->text('admin_reply')->nullable();
            
            // Status tracking for notifications
            $table->enum('status', ['pending', 'replied', 'closed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};