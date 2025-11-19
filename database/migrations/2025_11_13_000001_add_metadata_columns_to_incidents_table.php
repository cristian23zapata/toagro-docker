<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Si la columna severity no existe, la creamos
            if (!Schema::hasColumn('incidents', 'severity')) {
                $table->string('severity', 200)
                    ->nullable()
                    ->after('resolved');
            }

            // location
            if (!Schema::hasColumn('incidents', 'location')) {
                // sin ->after('severity') para evitar errores
                $table->string('location')
                    ->nullable();
            }

            // resolution_status
            if (!Schema::hasColumn('incidents', 'resolution_status')) {
                $table->enum('resolution_status', ['pendiente', 'en_proceso', 'resuelto'])
                    ->nullable();
            }

            // actions_taken
            if (!Schema::hasColumn('incidents', 'actions_taken')) {
                $table->text('actions_taken')
                    ->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (Schema::hasColumn('incidents', 'actions_taken')) {
                $table->dropColumn('actions_taken');
            }

            if (Schema::hasColumn('incidents', 'resolution_status')) {
                $table->dropColumn('resolution_status');
            }

            if (Schema::hasColumn('incidents', 'location')) {
                $table->dropColumn('location');
            }

            // OJO: solo borra severity si fue creada por esta migración
            if (Schema::hasColumn('incidents', 'severity')) {
                // Si prefieres no tocar severity porque ya existía antes,
                // puedes comentar estas líneas.
                // $table->dropColumn('severity');
            }
        });
    }
};
