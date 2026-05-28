<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Crear tabla de estados de equipos
 *
 * Tabla de referencia para los estados de equipos.
 * Reemplaza el ENUM anterior permitiendo agregar estados dinámicamente.
 *
 * Estados predeterminados:
 * - available (Disponible)
 * - maintenance (En mantenimiento)
 * - out_of_service (Fuera de servicio)
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
        Schema::create('equipment_statuses', function (Blueprint $table) {
            // Identificador único
            $table->id();

            // Nombre descriptivo en español
            $table->string('name', 100)->unique()->comment('Nombre del estado (ej: Disponible, En mantenimiento)');

            // Código de referencia en inglés
            $table->string('code', 50)->unique()->comment('Código único del estado');

            // Descripción del estado
            $table->text('description')->nullable()->comment('Descripción del estado del equipo');

            // Color para interfaz (opcional, para UI)
            $table->string('color', 20)->nullable()->comment('Color hexadecimal para visualización en UI');

            // Estado activo/inactivo
            $table->boolean('status')->default(true)->comment('1: Activo, 0: Inactivo');

            // Orden de visualización
            $table->integer('sort_order')->default(0);

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index('status');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_statuses');
    }
};
