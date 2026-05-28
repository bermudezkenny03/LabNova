<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Agregar foreign key de estados de equipos a equipment
 *
 * Conecta la tabla equipment con la tabla de referencia equipment_statuses.
 * Reemplaza el ENUM de status con una relación normalizada.
 *
 * @see \App\Models\EquipmentStatus
 * @see \App\Models\Equipment
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
        Schema::table('equipment', function (Blueprint $table) {
            // Agregar columna para la foreign key ANTES de modificar el ENUM
            $table->foreignId('equipment_status_id')
                ->nullable()
                ->after('stock')
                ->constrained('equipment_statuses')
                ->onDelete('set null')
                ->comment('Referencia a estado del equipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            try {
                $table->dropForeign(['equipment_status_id']);
            } catch (\Exception $e) {
                // La FK podría no existir
            }
            if (Schema::hasColumn('equipment', 'equipment_status_id')) {
                $table->dropColumn('equipment_status_id');
            }
        });
    }
};
