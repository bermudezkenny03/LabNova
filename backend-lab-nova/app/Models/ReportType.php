<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: ReportType (Tipo de Reporte)
 *
 * Tabla de referencia para categorizar los tipos de reportes disponibles en el sistema.
 *
 * Tipos predefinidos:
 * - reservations (Reportes de reservas)
 * - equipment_usage (Uso de equipos)
 * - user_activity (Actividad de usuarios)
 *
 * @property int $id Identificador único
 * @property string $name Nombre del tipo de reporte
 * @property string $code Código del tipo
 * @property string|null $description Descripción del tipo
 * @property string|null $handler_class Clase PHP que maneja la generación
 * @property bool $status Estado activo/inactivo
 * @property int $sort_order Orden de visualización
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - reportRequests() : HasMany -> Solicitudes de reporte de este tipo
 *
 * @example
 * $type = ReportType::where('code', 'reservations')->first();
 * $type->reportRequests; // Todas las solicitudes de reporte de reservas
 * $handler = app($type->handler_class); // Instanciar el handler dinámicamente
 *
 * @see \App\Models\ReportRequest
 */
class ReportType extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'report_types';

    /**
     * Atributos que pueden ser asignados en masa
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'handler_class',
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
            'status' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relación: Un tipo de reporte tiene muchas solicitudes
     *
     * @return HasMany<ReportRequest>
     */
    public function reportRequests(): HasMany
    {
        return $this->hasMany(ReportRequest::class, 'report_type_id');
    }

    /**
     * Scope: Obtener solo tipos activos
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

    /**
     * Obtener el handler de este tipo de reporte
     *
     * @return object|null La instancia del handler
     */
    public function getHandler()
    {
        if (!$this->handler_class || !class_exists($this->handler_class)) {
            return null;
        }
        return app($this->handler_class);
    }
}
