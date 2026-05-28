<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: ReportStatus (Estado de Reporte)
 *
 * Tabla de referencia para los estados del ciclo de vida de una solicitud de reporte.
 * Reemplaza el ENUM anterior permitiendo agregar estados dinámicamente.
 *
 * Estados predefinidos:
 * - pending (Pendiente)
 * - processing (En procesamiento)
 * - completed (Completado)
 * - failed (Falló)
 *
 * @property int $id Identificador único
 * @property string $name Nombre del estado
 * @property string $code Código del estado
 * @property string|null $description Descripción del estado
 * @property string|null $color Color hexadecimal para UI
 * @property bool $is_terminal Indica si es un estado final
 * @property bool $status Estado activo/inactivo
 * @property int $sort_order Orden de visualización
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - reportRequests() : HasMany -> Solicitudes de reporte con este estado
 *
 * @example
 * $status = ReportStatus::where('code', 'completed')->first();
 * $status->reportRequests; // Todos los reportes completados
 *
 * @see \App\Models\ReportRequest
 */
class ReportStatus extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'report_statuses';

    /**
     * Atributos que pueden ser asignados en masa
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_terminal',
        'status',
        'sort_order',
    ];

    /**
     * Casting automático de tipos de atributos
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_terminal' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relación: Un estado de reporte tiene muchas solicitudes de reporte
     *
     * @return HasMany<ReportRequest>
     */
    public function reportRequests(): HasMany
    {
        return $this->hasMany(ReportRequest::class, 'report_status_id');
    }

    /**
     * Scope: Obtener solo estados activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Ordenar por sort_order
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
