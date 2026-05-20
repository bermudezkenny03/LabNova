<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Poblar tabla de tipos de reportes
 *
 * Inserta los tipos de reportes disponibles en el sistema:
 * - Reportes de reservas (reservations)
 * - Uso de equipos (equipment_usage)
 * - Actividad de usuarios (user_activity)
 *
 * @see \App\Models\ReportType
 */
class ReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('report_types')->insertOrIgnore([
            [
                'name' => 'Reportes de reservas',
                'code' => 'reservations',
                'description' => 'Reporte que muestra el historial de reservas en un rango de fechas',
                'handler_class' => 'App\\Reports\\ReservationsReportHandler',
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Uso de equipos',
                'code' => 'equipment_usage',
                'description' => 'Reporte que muestra el uso y disponibilidad de equipos',
                'handler_class' => 'App\\Reports\\EquipmentUsageReportHandler',
                'status' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Actividad de usuarios',
                'code' => 'user_activity',
                'description' => 'Reporte que muestra la actividad y comportamiento de usuarios',
                'handler_class' => 'App\\Reports\\UserActivityReportHandler',
                'status' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
