<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Agregar foreign key de géneros a user_details
 *
 * Conecta la tabla user_details con la tabla de referencia genders.
 * Permite manejar géneros de manera escalable.
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
        Schema::table('user_details', function (Blueprint $table) {
            // Agregar columna para la foreign key
            $table->foreignId('gender_id')
                ->nullable()
                ->after('id')
                ->constrained('genders')
                ->onDelete('set null')
                ->comment('Referencia a género');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            try {
                $table->dropForeign(['gender_id']);
            } catch (\Exception $e) {
                // La FK podría no existir
            }
            if (Schema::hasColumn('user_details', 'gender_id')) {
                $table->dropColumn('gender_id');
            }
        });
    }
};
