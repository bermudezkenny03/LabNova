<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Crear tabla de géneros
 *
 * Tabla de referencia para manejar los géneros de manera escalable.
 * Permite agregar nuevos géneros sin modificar la estructura de la BD.
 *
 * @see \App\Models\Gender
 * @see \App\Models\UserDetail
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
        Schema::create('genders', function (Blueprint $table) {
            // Identificador único
            $table->id();

            // Nombre del género en español (Masculino, Femenino, Otro, Prefiero no responder)
            $table->string('name', 50)->unique()->comment('Nombre del género (ej: Masculino, Femenino)');

            // Código de referencia en inglés (male, female, other, prefer_not_to_say)
            $table->string('code', 50)->unique()->comment('Código del género para uso interno (ej: male, female)');

            // Estado activo/inactivo
            $table->boolean('status')->default(true)->comment('1: Activo, 0: Inactivo');

            // Orden de visualización en listas
            $table->integer('sort_order')->default(0)->comment('Orden de visualización');

            // Timestamps
            $table->timestamps();

            // Índices para optimización
            $table->index('status');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('genders');
    }
};
