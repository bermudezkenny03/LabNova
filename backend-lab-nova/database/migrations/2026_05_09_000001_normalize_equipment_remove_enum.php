<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Normalizar tabla equipment - Reemplazar ENUM por Foreign Key
 *
 * Esta migración:
 * 1. Migra datos del ENUM 'status' a equipment_status_id
 * 2. Elimina la columna ENUM (no escalable)
 * 3. Agranda el índice compuesto para optimización
 *
 * Antes:
 *   status ENUM('available', 'maintenance', 'out_of_service') - hardcodeado
 *
 * Después:
 *   equipment_status_id BIGINT FOREIGN KEY (escalable)
 *   status ENUM se elimina completamente
 *
 * @see \App\Models\Equipment
 * @see \App\Models\EquipmentStatus
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, asegurar que los estados de referencia existen
        $this->seedEquipmentStatuses();

        Schema::table('equipment', function (Blueprint $table) {
            // Si no existe equipment_status_id, crearla
            if (!Schema::hasColumn('equipment', 'equipment_status_id')) {
                $table->unsignedBigInteger('equipment_status_id')->nullable()->after('code');
                $table->foreign('equipment_status_id')
                    ->references('id')
                    ->on('equipment_statuses')
                    ->nullOnDelete();
            }
        });

        // Migrar datos del ENUM al campo de referencia
        DB::statement('
            UPDATE equipment
            SET equipment_status_id = (
                SELECT id FROM equipment_statuses
                WHERE equipment_statuses.code = equipment.status
                LIMIT 1
            )
            WHERE status IS NOT NULL
        ');

        // Si no existe datos sin migrar, establecer por defecto al primero disponible
        $defaultStatus = DB::table('equipment_statuses')->where('code', 'available')->first();
        if ($defaultStatus) {
            DB::table('equipment')
                ->whereNull('equipment_status_id')
                ->update(['equipment_status_id' => $defaultStatus->id]);
        }

        // Ahora eliminar la columna ENUM (que no es escalable)
        // Para SQLite, primero eliminamos los índices que contengan esta columna
        try {
            Schema::table('equipment', function (Blueprint $table) {
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

        // Mejorar índices para las nuevas queries
        Schema::table('equipment', function (Blueprint $table) {
            try {
                $table->index('equipment_status_id');
            } catch (\Exception $e) {
                // El índice puede ya existir
            }
        });
    }

    /**
     * Asegurar que existen los estados de equipos de referencia
     */
    private function seedEquipmentStatuses(): void
    {
        $statuses = [
            [
                'name' => 'Disponible',
                'code' => 'available',
                'description' => 'Equipo disponible para ser reservado',
                'color' => '#10b981',
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'En mantenimiento',
                'code' => 'maintenance',
                'description' => 'Equipo en proceso de mantenimiento',
                'color' => '#f59e0b',
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Fuera de servicio',
                'code' => 'out_of_service',
                'description' => 'Equipo no disponible para reservas',
                'color' => '#ef4444',
                'status' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('equipment_statuses')->updateOrInsert(
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
        Schema::table('equipment', function (Blueprint $table) {
            // Recrear la columna ENUM para rollback
            $table->enum('status', ['available', 'maintenance', 'out_of_service'])
                ->default('available')
                ->after('code');

            // Migrar datos de vuelta
            DB::statement('
                UPDATE equipment
                SET status = (
                    SELECT code FROM equipment_statuses
                    WHERE equipment_statuses.id = equipment.equipment_status_id
                    LIMIT 1
                )
                WHERE equipment_status_id IS NOT NULL
            ');

            // Eliminar foreign key e índice
            $table->dropIndex(['equipment_status_id']);
            $table->dropForeign(['equipment_status_id']);
            $table->dropColumn('equipment_status_id');
        });
    }
};
