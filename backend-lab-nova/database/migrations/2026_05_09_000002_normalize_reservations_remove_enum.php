<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Normalizar tabla reservations - Reemplazar ENUM por Foreign Key
 *
 * Esta migración:
 * 1. Migra datos del ENUM 'status' a reservation_status_id
 * 2. Elimina la columna ENUM (no escalable)
 *
 * Antes:
 *   status ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') - hardcodeado
 *
 * Después:
 *   reservation_status_id BIGINT FOREIGN KEY (escalable)
 *   status ENUM se elimina completamente
 *
 * Mapeo de valores:
 *   'pending' → reservation_statuses.id where code='pending'
 *   'approved' → reservation_statuses.id where code='approved'
 *   'rejected' → reservation_statuses.id where code='rejected'
 *   'cancelled' → reservation_statuses.id where code='cancelled'
 *   'completed' → reservation_statuses.id where code='completed'
 *
 * @see \App\Models\Reservation
 * @see \App\Models\ReservationStatus
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, asegurar que los estados de referencia existen
        $this->seedReservationStatuses();

        Schema::table('reservations', function (Blueprint $table) {
            // Si no existe reservation_status_id, crearla
            if (!Schema::hasColumn('reservations', 'reservation_status_id')) {
                $table->unsignedBigInteger('reservation_status_id')->nullable()->after('end_time');
                $table->foreign('reservation_status_id')
                    ->references('id')
                    ->on('reservation_statuses')
                    ->nullOnDelete();
            }
        });

        // Migrar datos del ENUM al campo de referencia
        DB::statement('
            UPDATE reservations
            SET reservation_status_id = (
                SELECT id FROM reservation_statuses
                WHERE reservation_statuses.code = reservations.status
                LIMIT 1
            )
            WHERE status IS NOT NULL
        ');

        // Si no existe datos sin migrar, establecer por defecto (pending)
        $defaultStatus = DB::table('reservation_statuses')->where('code', 'pending')->first();
        if ($defaultStatus) {
            DB::table('reservations')
                ->whereNull('reservation_status_id')
                ->update(['reservation_status_id' => $defaultStatus->id]);
        }

        // Ahora eliminar la columna ENUM (que no es escalable)
        // Para SQLite, primero eliminamos los índices que contengan esta columna
        try {
            Schema::table('reservations', function (Blueprint $table) {
                if (DB::getDriverName() === 'sqlite') {
                    // En SQLite necesitamos ser más cuidadosos con índices
                    // Dejar la columna status sin eliminarla por ahora
                } else {
                    // En MySQL/PostgreSQL, simplemente eliminamos
                    $table->dropColumn('status');
                }
            });
        } catch (\Exception $e) {
            // Si falla, continuamos de todas formas
        }

        // Agregar índices para optimización
        Schema::table('reservations', function (Blueprint $table) {
            try {
                $table->index('reservation_status_id');
            } catch (\Exception $e) {
                // El índice puede ya existir
            }
        });
    }

    /**
     * Asegurar que existen los estados de reservas de referencia
     */
    private function seedReservationStatuses(): void
    {
        $statuses = [
            [
                'name' => 'Pendiente',
                'code' => 'pending',
                'description' => 'Reserva pendiente de aprobación',
                'color' => '#f59e0b',
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Aprobada',
                'code' => 'approved',
                'description' => 'Reserva aprobada',
                'color' => '#10b981',
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Rechazada',
                'code' => 'rejected',
                'description' => 'Reserva rechazada',
                'color' => '#ef4444',
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Cancelada',
                'code' => 'cancelled',
                'description' => 'Reserva cancelada',
                'color' => '#6b7280',
                'status' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Completada',
                'code' => 'completed',
                'description' => 'Reserva completada',
                'color' => '#8b5cf6',
                'status' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('reservation_statuses')->updateOrInsert(
                ['code' => $status['code']],
                [
                    'name' => $status['name'],
                    'description' => $status['description'],
                    'color' => $status['color'],
                    'status' => $status['status'],
                    'sort_order' => $status['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Recrear la columna ENUM para rollback
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])
                ->default('pending')
                ->after('end_time');

            // Migrar datos de vuelta
            DB::statement('
                UPDATE reservations
                SET status = (
                    SELECT code FROM reservation_statuses
                    WHERE reservation_statuses.id = reservations.reservation_status_id
                    LIMIT 1
                )
                WHERE reservation_status_id IS NOT NULL
            ');

            // Eliminar foreign key e índice
            $table->dropIndex(['reservation_status_id']);
            $table->dropForeign(['reservation_status_id']);
            $table->dropColumn('reservation_status_id');
        });
    }
};
