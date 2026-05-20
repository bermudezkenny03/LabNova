<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'report_type_id', 'report_status_id', 'start_date', 'end_date', 'filters'])]

/**
 * Model: ReportRequest (Solicitud de Reporte)
 *
 * Representa una solicitud de generación de reporte realizada por un usuario.
 *
 * @property int $id Identificador único
 * @property int $user_id ID del usuario que solicita el reporte
 * @property int|null $report_type_id ID del tipo de reporte
 * @property int|null $report_status_id ID del estado de la solicitud
 * @property \Illuminate\Support\Carbon|null $start_date Fecha de inicio del rango
 * @property \Illuminate\Support\Carbon|null $end_date Fecha de fin del rango
 * @property array|null $filters Filtros adicionales en formato JSON
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - user() : BelongsTo -> Usuario que solicita
 * - reportType() : BelongsTo -> Tipo de reporte
 * - reportStatus() : BelongsTo -> Estado de la solicitud
 * - reports() : HasMany -> Reportes generados
 *
 * @example
 * $request = ReportRequest::with(['user', 'reportType', 'reportStatus'])->find(1);
 * $request->user->name; // Nombre del usuario
 * $request->reportType->name; // "Reportes de reservas"
 * $request->reportStatus->name; // "Completado"
 * $request->reports; // Reportes generados
 *
 * @see \App\Models\User
 * @see \App\Models\ReportType
 * @see \App\Models\ReportStatus
 * @see \App\Models\Report
 */
class ReportRequest extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'report_requests';

    /**
     * Casting automático de tipos de atributos
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'filters' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relación: Una solicitud pertenece a un usuario
     *
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una solicitud tiene un tipo de reporte
     *
     * @return BelongsTo<ReportType>
     */
    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class, 'report_type_id');
    }

    /**
     * Relación: Una solicitud tiene un estado
     *
     * @return BelongsTo<ReportStatus>
     */
    public function reportStatus(): BelongsTo
    {
        return $this->belongsTo(ReportStatus::class, 'report_status_id');
    }

    /**
     * Relación: Una solicitud puede generar muchos reportes
     *
     * @return HasMany<Report>
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'report_request_id');
    }

    /**
     * Scope: Obtener solicitudes pendientes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->whereHas('reportStatus', fn($q) => $q->where('code', 'pending'));
    }

    /**
     * Scope: Obtener solicitudes completadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->whereHas('reportStatus', fn($q) => $q->where('code', 'completed'));
    }

    /**
     * Scope: Obtener solicitudes por tipo
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $typeId ID del tipo de reporte
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $typeId)
    {
        return $query->where('report_type_id', $typeId);
    }

    /**
     * Scope: Obtener solicitudes de un usuario
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId ID del usuario
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Verificar si la solicitud está pendiente
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->reportStatus?->code === 'pending';
    }

    /**
     * Verificar si la solicitud está completada
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->reportStatus?->code === 'completed';
    }

    /**
     * Obtener el nombre del estado
     *
     * @return string|null
     */
    public function getStatusName(): ?string
    {
        return $this->reportStatus?->name;
    }

    /**
     * Obtener el nombre del tipo de reporte
     *
     * @return string|null
     */
    public function getTypeName(): ?string
    {
        return $this->reportType?->name;
    }
}
