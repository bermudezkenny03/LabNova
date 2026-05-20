<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Poblar tabla de estados de equipos
 *
 * Inserta los estados predeterminados:
 * - Disponible (available)
 * - En mantenimiento (maintenance)
 * - Fuera de servicio (out_of_service)
 *
 * @see \App\Models\EquipmentStatus
 */
class EquipmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('equipment_statuses')->insertOrIgnore([
            [
                'name' => 'Disponible',
                'code' => 'available',
                'description' => 'Equipo disponible para ser reservado',
                'color' => '#10b981',  // Verde
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En mantenimiento',
                'code' => 'maintenance',
                'description' => 'Equipo en proceso de mantenimiento',
                'color' => '#f59e0b',  // Amarillo/Naranja
                'status' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fuera de servicio',
                'code' => 'out_of_service',
                'description' => 'Equipo no disponible para reservas',
                'color' => '#ef4444',  // Rojo
                'status' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
