<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Poblar tabla de estados de reportes
 *
 * Inserta los estados predeterminados del ciclo de vida de una solicitud de reporte:
 * - Pendiente (pending) - Estado intermedio
 * - En procesamiento (processing) - Estado intermedio
 * - Completado (completed) - Estado terminal
 * - Falló (failed) - Estado terminal
 *
 * @see \App\Models\ReportStatus
 */
class ReportStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('report_statuses')->insertOrIgnore([
            [
                'name' => 'Pendiente',
                'code' => 'pending',
                'description' => 'Reporte pendiente de procesar',
                'color' => '#6366f1',  // Índigo
                'is_terminal' => false,
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En procesamiento',
                'code' => 'processing',
                'description' => 'Reporte siendo generado',
                'color' => '#f59e0b',  // Amarillo/Naranja
                'is_terminal' => false,
                'status' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Completado',
                'code' => 'completed',
                'description' => 'Reporte completado exitosamente',
                'color' => '#10b981',  // Verde
                'is_terminal' => true,
                'status' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Falló',
                'code' => 'failed',
                'description' => 'Fallo durante la generación del reporte',
                'color' => '#ef4444',  // Rojo
                'is_terminal' => true,
                'status' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
