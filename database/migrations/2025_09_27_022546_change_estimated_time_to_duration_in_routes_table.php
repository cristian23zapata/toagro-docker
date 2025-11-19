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
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('estimated_time');
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->decimal('estimated_duration', 8, 2)->nullable()->after('distance_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('estimated_duration');
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->dateTime('estimated_time')->nullable()->after('distance_km');
        });
    }
};
