<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Poblar tabla de acciones de logs de reservas
 *
 * Inserta los tipos de acciones que se pueden registrar en el historial
 * de cambios de una reserva:
 * - Creada (created)
 * - Aprobada (approved)
 * - Rechazada (rejected)
 * - Cancelada (cancelled)
 * - Completada (completed)
 *
 * @see \App\Models\ReservationLogAction
 */
class ReservationLogActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('reservation_log_actions')->insertOrIgnore([
            [
                'name' => 'Creada',
                'code' => 'created',
                'description' => 'Reserva fue creada',
                'color' => '#6366f1',  // Índigo
                'action_type' => 'user',
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aprobada',
                'code' => 'approved',
                'description' => 'Reserva fue aprobada',
                'color' => '#10b981',  // Verde
                'action_type' => 'admin',
                'status' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rechazada',
                'code' => 'rejected',
                'description' => 'Reserva fue rechazada',
                'color' => '#ef4444',  // Rojo
                'action_type' => 'admin',
                'status' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cancelada',
                'code' => 'cancelled',
                'description' => 'Reserva fue cancelada',
                'color' => '#6b7280',  // Gris
                'action_type' => 'user',
                'status' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Completada',
                'code' => 'completed',
                'description' => 'Reserva fue completada',
                'color' => '#8b5cf6',  // Púrpura
                'action_type' => 'system',
                'status' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
