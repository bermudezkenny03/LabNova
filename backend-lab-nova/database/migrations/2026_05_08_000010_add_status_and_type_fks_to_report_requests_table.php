<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Agregar foreign key de estados de reportes a report_requests
 *
 * Conecta la tabla report_requests con las tablas de referencia:
 * - report_statuses: Para los estados de la solicitud
 * - report_types: Para categorizar el tipo de reporte
 *
 * Reemplaza los ENUMs con relaciones normalizadas.
 *
 * @see \App\Models\ReportStatus
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
        Schema::table('report_requests', function (Blueprint $table) {
            // Agregar foreign key para tipo de reporte
            $table->foreignId('report_type_id')
                ->nullable()
                ->after('user_id')
                ->constrained('report_types')
                ->onDelete('set null')
                ->comment('Referencia a tipo de reporte');

            // Agregar foreign key para estado del reporte
            $table->foreignId('report_status_id')
                ->nullable()
                ->after('report_type_id')
                ->constrained('report_statuses')
                ->onDelete('set null')
                ->comment('Referencia a estado de la solicitud');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('report_requests', function (Blueprint $table) {
            try {
                $table->dropForeign(['report_type_id']);
            } catch (\Exception $e) {
                // La FK podría no existir
            }
            try {
                $table->dropForeign(['report_status_id']);
            } catch (\Exception $e) {
                // La FK podría no existir
            }
            if (Schema::hasColumn('report_requests', 'report_type_id')) {
                $table->dropColumn('report_type_id');
            }
            if (Schema::hasColumn('report_requests', 'report_status_id')) {
                $table->dropColumn('report_status_id');
            }
        });
    }
};
