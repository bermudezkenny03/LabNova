<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Crear tabla de estados de reportes
 *
 * Tabla de referencia para los estados de solicitudes/reportes.
 * Reemplaza el ENUM anterior permitiendo agregar estados dinámicamente.
 *
 * Estados predeterminados:
 * - pending (Pendiente)
 * - processing (En procesamiento)
 * - completed (Completado)
 * - failed (Falló)
 *
 * @see \App\Models\ReportStatus
 * @see \App\Models\ReportRequest
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
        Schema::create('report_statuses', function (Blueprint $table) {
            // Identificador único
            $table->id();

            // Nombre descriptivo en español
            $table->string('name', 100)->unique()->comment('Nombre del estado');

            // Código de referencia en inglés
            $table->string('code', 50)->unique()->comment('Código único del estado');

            // Descripción del estado
            $table->text('description')->nullable()->comment('Descripción del estado');

            // Color para interfaz
            $table->string('color', 20)->nullable()->comment('Color hexadecimal para UI');

            // Indica si es un estado terminal
            $table->boolean('is_terminal')->default(false)->comment('1: Estado final, 0: Intermedio');

            // Estado activo/inactivo
            $table->boolean('status')->default(true)->comment('1: Activo, 0: Inactivo');

            // Orden de visualización
            $table->integer('sort_order')->default(0);

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index('status');
            $table->index('code');
            $table->index('is_terminal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('report_statuses');
    }
};
