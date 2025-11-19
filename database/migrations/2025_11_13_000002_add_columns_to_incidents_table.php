<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds additional metadata columns to the incidents table.  These fields
     * allow incidents to track severity, a free‑text location, the current
     * resolution status and a description of actions taken.  All new columns
     * are nullable to maintain backward compatibility with existing records.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (! Schema::hasColumn('incidents', 'severity')) {
                $table->string('severity')->nullable()->after('type');
            }
            if (! Schema::hasColumn('incidents', 'location')) {
                $table->string('location')->nullable()->after('severity');
            }
            if (! Schema::hasColumn('incidents', 'resolution_status')) {
                $table->string('resolution_status')->nullable()->after('reported_at');
            }
            if (! Schema::hasColumn('incidents', 'actions_taken')) {
                $table->text('actions_taken')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (Schema::hasColumn('incidents', 'severity')) {
                $table->dropColumn('severity');
            }
            if (Schema::hasColumn('incidents', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('incidents', 'resolution_status')) {
                $table->dropColumn('resolution_status');
            }
            if (Schema::hasColumn('incidents', 'actions_taken')) {
                $table->dropColumn('actions_taken');
            }
        });
    }
};
