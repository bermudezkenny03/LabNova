<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Agregar foreign key de estados de reservas a reservations
 *
 * Conecta la tabla reservations con la tabla de referencia reservation_statuses.
 * Reemplaza el ENUM de status con una relación normalizada.
 *
 * @see \App\Models\ReservationStatus
 * @see \App\Models\Reservation
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
        Schema::table('reservations', function (Blueprint $table) {
            // Agregar columna para la foreign key
            $table->foreignId('reservation_status_id')
                ->nullable()
                ->after('end_time')
                ->constrained('reservation_statuses')
                ->onDelete('set null')
                ->comment('Referencia a estado de la reserva');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            try {
                $table->dropForeign(['reservation_status_id']);
            } catch (\Exception $e) {
                // La FK podría no existir
            }
            if (Schema::hasColumn('reservations', 'reservation_status_id')) {
                $table->dropColumn('reservation_status_id');
            }
        });
    }
};
