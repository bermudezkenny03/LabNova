<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: EquipmentStatus (Estado de Equipo)
 *
 * Tabla de referencia para los estados que puede tener un equipo.
 * Reemplaza el ENUM anterior permitiendo agregar estados dinámicamente.
 *
 * Estados predefinidos:
 * - available (Disponible)
 * - maintenance (En mantenimiento)
 * - out_of_service (Fuera de servicio)
 *
 * @property int $id Identificador único
 * @property string $name Nombre del estado (ej: Disponible)
 * @property string $code Código del estado (ej: available)
 * @property string|null $description Descripción del estado
 * @property string|null $color Color hexadecimal para UI
 * @property bool $status Estado activo/inactivo
 * @property int $sort_order Orden de visualización
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - equipment() : HasMany -> Equipos con este estado
 *
 * @example
 * $status = EquipmentStatus::where('code', 'available')->first();
 * $status->equipment; // Todos los equipos disponibles
 *
 * @see \App\Models\Equipment
 */
class EquipmentStatus extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'equipment_statuses';

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
     * Relación: Un estado de equipo tiene muchos equipos
     *
     * @return HasMany<Equipment>
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'equipment_status_id');
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

    /**
     * Obtener código por nombre
     *
     * @param string $name Nombre del estado
     * @return string|null
     */
    public static function getCodeByName(string $name): ?string
    {
        return self::where('name', $name)->value('code');
    }

    /**
     * Obtener nombre por código
     *
     * @param string $code Código del estado
     * @return string|null
     */
    public static function getNameByCode(string $code): ?string
    {
        return self::where('code', $code)->value('name');
    }
}
