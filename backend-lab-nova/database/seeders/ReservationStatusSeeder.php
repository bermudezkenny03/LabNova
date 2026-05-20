<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Poblar tabla de estados de reservas
 *
 * Inserta los estados predeterminados del ciclo de vida de una reserva:
 * - Pendiente (pending) - Estado intermedio
 * - Aprobada (approved) - Estado intermedio
 * - Rechazada (rejected) - Estado terminal
 * - Cancelada (cancelled) - Estado terminal
 * - Completada (completed) - Estado terminal
 *
 * @see \App\Models\ReservationStatus
 */
class ReservationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('reservation_statuses')->insertOrIgnore([
            [
                'name' => 'Pendiente',
                'code' => 'pending',
                'description' => 'Reserva pendiente de aprobación',
                'color' => '#6366f1',  // Índigo
                'is_terminal' => false,
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aprobada',
                'code' => 'approved',
                'description' => 'Reserva aprobada',
                'color' => '#10b981',  // Verde
                'is_terminal' => false,
                'status' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rechazada',
                'code' => 'rejected',
                'description' => 'Reserva rechazada',
                'color' => '#ef4444',  // Rojo
                'is_terminal' => true,
                'status' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cancelada',
                'code' => 'cancelled',
                'description' => 'Reserva cancelada',
                'color' => '#6b7280',  // Gris
                'is_terminal' => true,
                'status' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Completada',
                'code' => 'completed',
                'description' => 'Reserva completada',
                'color' => '#8b5cf6',  // Púrpura
                'is_terminal' => true,
                'status' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
