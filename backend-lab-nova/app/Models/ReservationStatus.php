<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: ReservationStatus (Estado de Reserva)
 *
 * Tabla de referencia para los estados del ciclo de vida de una reserva.
 * Reemplaza el ENUM anterior permitiendo agregar estados dinámicamente.
 *
 * Estados predefinidos:
 * - pending (Pendiente)
 * - approved (Aprobada)
 * - rejected (Rechazada)
 * - cancelled (Cancelada)
 * - completed (Completada)
 *
 * @property int $id Identificador único
 * @property string $name Nombre del estado
 * @property string $code Código del estado
 * @property string|null $description Descripción del estado
 * @property string|null $color Color hexadecimal para UI
 * @property bool $is_terminal Indica si es un estado final (no puede cambiar)
 * @property bool $status Estado activo/inactivo
 * @property int $sort_order Orden de visualización
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - reservations() : HasMany -> Reservas con este estado
 *
 * @example
 * $status = ReservationStatus::where('code', 'pending')->first();
 * $status->reservations; // Todas las reservas pendientes
 * $status->is_terminal; // false (la reserva puede cambiar de estado)
 *
 * @see \App\Models\Reservation
 */
class ReservationStatus extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'reservation_statuses';

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
     * Relación: Un estado de reserva tiene muchas reservas
     *
     * @return HasMany<Reservation>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'reservation_status_id');
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
     * Scope: Obtener solo estados terminales
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTerminal($query)
    {
        return $query->where('is_terminal', true);
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
     * Verificar si este es un estado terminal (final)
     *
     * @return bool
     */
    public function isTerminal(): bool
    {
        return (bool) $this->is_terminal;
    }
}
