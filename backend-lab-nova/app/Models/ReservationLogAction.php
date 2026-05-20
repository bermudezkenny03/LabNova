<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: ReservationLogAction (Acción de Log de Reserva)
 *
 * Tabla de referencia para categorizar las acciones que se pueden registrar
 * en el historial de cambios de una reserva.
 *
 * Acciones predefinidas:
 * - created (Creada)
 * - approved (Aprobada)
 * - rejected (Rechazada)
 * - cancelled (Cancelada)
 * - completed (Completada)
 *
 * @property int $id Identificador único
 * @property string $name Nombre de la acción
 * @property string $code Código de la acción
 * @property string|null $description Descripción de la acción
 * @property string|null $color Color hexadecimal para UI
 * @property string $action_type Tipo de acción (system, user, admin)
 * @property bool $status Estado activo/inactivo
 * @property int $sort_order Orden de visualización
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - reservationLogs() : HasMany -> Logs de reserva con esta acción
 *
 * @example
 * $action = ReservationLogAction::where('code', 'approved')->first();
 * $action->reservationLogs; // Todos los logs donde ocurrió esta acción
 * $action->action_type; // 'admin'
 *
 * @see \App\Models\ReservationLog
 */
class ReservationLogAction extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'reservation_log_actions';

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
        'action_type',
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
     * Relación: Una acción tiene muchos logs de reserva
     *
     * @return HasMany<ReservationLog>
     */
    public function reservationLogs(): HasMany
    {
        return $this->hasMany(ReservationLog::class, 'reservation_log_action_id');
    }

    /**
     * Scope: Obtener solo acciones activas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Obtener solo acciones de tipo específico
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type Tipo de acción (system, user, admin)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('action_type', $type);
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
