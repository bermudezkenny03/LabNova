<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Crear tabla de acciones de logs de reservas
 *
 * Tabla de referencia para categorizar las acciones que se registran
 * en el historial de reservas.
 *
 * Acciones predeterminadas:
 * - created (Creada)
 * - approved (Aprobada)
 * - rejected (Rechazada)
 * - cancelled (Cancelada)
 * - completed (Completada)
 *
 * @see \App\Models\ReservationLogAction
 * @see \App\Models\ReservationLog
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
        Schema::create('reservation_log_actions', function (Blueprint $table) {
            // Identificador único
            $table->id();

            // Nombre descriptivo en español
            $table->string('name', 100)->unique()->comment('Nombre de la acción');

            // Código de referencia en inglés
            $table->string('code', 50)->unique()->comment('Código único de la acción');

            // Descripción de la acción
            $table->text('description')->nullable()->comment('Descripción de la acción');

            // Color para interfaz
            $table->string('color', 20)->nullable()->comment('Color hexadecimal para UI');

            // Tipo de acción (system, user, admin)
            $table->string('action_type', 50)->default('user')->comment('Tipo de acción: system, user, admin');

            // Estado activo/inactivo
            $table->boolean('status')->default(true)->comment('1: Activo, 0: Inactivo');

            // Orden de visualización
            $table->integer('sort_order')->default(0);

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index('status');
            $table->index('code');
            $table->index('action_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_log_actions');
    }
};
