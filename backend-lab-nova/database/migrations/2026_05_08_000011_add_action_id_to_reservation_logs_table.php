<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Agregar foreign key de acciones de logs a reservation_logs
 *
 * Conecta la tabla reservation_logs con la tabla de referencia reservation_log_actions.
 * Permite registrar acciones de manera escalable.
 *
 * @see \App\Models\ReservationLogAction
 * @see \App\Models\ReservationLog
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('reservation_logs', function (Blueprint $table) {
            // Agregar columna para la foreign key
            $table->foreignId('reservation_log_action_id')
                ->nullable()
                ->after('reservation_id')
                ->constrained('reservation_log_actions')
                ->onDelete('set null')
                ->comment('Referencia a tipo de acción');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('reservation_logs', function (Blueprint $table) {
            try {
                $table->dropForeign(['reservation_log_action_id']);
            } catch (\Exception $e) {
                // La FK podría no existir
            }
            if (Schema::hasColumn('reservation_logs', 'reservation_log_action_id')) {
                $table->dropColumn('reservation_log_action_id');
            }
        });
    }
};
