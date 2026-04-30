<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add if they don't exist to prevent the "Duplicate column" error
            if (!Schema::hasColumn('users', 'custom_id')) {
                $table->string('custom_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('profile_photo');
            }
            if (!Schema::hasColumn('users', 'district')) {
                $table->string('district')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'blood_type')) {
                $table->string('blood_type', 5)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['custom_id', 'profile_photo', 'phone', 'district', 'blood_type']);
        });
    }
};