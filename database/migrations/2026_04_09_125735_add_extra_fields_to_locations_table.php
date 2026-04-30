<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
/**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'phone')) {
                $table->string('phone')->nullable()->after('city');
            }
            if (!Schema::hasColumn('locations', 'hours')) {
                $table->string('hours')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('locations', 'availability')) {
                $table->string('availability')->default('medium')->after('hours');
            }
            if (!Schema::hasColumn('locations', 'walk_ins')) {
                $table->boolean('walk_ins')->default(false)->after('availability');
            }
            if (!Schema::hasColumn('locations', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('walk_ins');
            }
            if (!Schema::hasColumn('locations', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('locations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('locations', 'phone') ? 'phone' : null,
                Schema::hasColumn('locations', 'hours') ? 'hours' : null,
                Schema::hasColumn('locations', 'availability') ? 'availability' : null,
                Schema::hasColumn('locations', 'walk_ins') ? 'walk_ins' : null,
                Schema::hasColumn('locations', 'latitude') ? 'latitude' : null,
                Schema::hasColumn('locations', 'longitude') ? 'longitude' : null,
                Schema::hasColumn('locations', 'is_active') ? 'is_active' : null,
            ]));
        });
    }
};
