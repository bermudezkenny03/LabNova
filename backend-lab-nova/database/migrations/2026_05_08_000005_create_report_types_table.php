<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Crear tabla de tipos de reportes
 *
 * Tabla de referencia para categorizar los tipos de reportes disponibles.
 *
 * Tipos predeterminados:
 * - reservations (Reportes de reservas)
 * - equipment_usage (Uso de equipos)
 * - user_activity (Actividad de usuarios)
 *
 * @see \App\Models\ReportType
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
        Schema::create('report_types', function (Blueprint $table) {
            // Identificador único
            $table->id();

            // Nombre descriptivo en español
            $table->string('name', 100)->unique()->comment('Nombre del tipo de reporte');

            // Código de referencia en inglés
            $table->string('code', 50)->unique()->comment('Código único del tipo');

            // Descripción del tipo de reporte
            $table->text('description')->nullable()->comment('Descripción del tipo de reporte');

            // Clase PHP que maneja la generación de este reporte
            $table->string('handler_class', 255)->nullable()->comment('Clase handler para generar el reporte');

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
        Schema::dropIfExists('report_types');
    }
};
