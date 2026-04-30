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
    Schema::table('users', function (Blueprint $table) {
        // REMOVE the 'role' line since it already exists
        $table->string('national_id')->unique()->nullable()->after('role');
        $table->string('district')->nullable()->after('national_id');
        $table->string('sector')->nullable()->after('district');
        $table->boolean('is_active')->default(false)->after('sector');
    });
}
public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'national_id', 'district', 'sector', 'is_active']);
    });
}
    /**
     * Reverse the migrations.
     */

};
