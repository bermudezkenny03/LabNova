<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Gender (Género)
 *
 * Tabla de referencia para manejar los géneros de manera escalable y normalizada.
 * Permite agregar nuevos géneros sin modificar la estructura de la base de datos.
 *
 * @property int $id Identificador único
 * @property string $name Nombre del género (ej: Masculino, Femenino)
 * @property string $code Código único del género (ej: male, female)
 * @property bool $status Estado activo/inactivo
 * @property int $sort_order Orden de visualización
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - userDetails() : HasMany -> Detalles de usuarios que tienen este género
 *
 * @example
 * $gender = Gender::where('code', 'male')->first();
 * $gender->userDetails; // Todos los usuarios con este género
 *
 * @see \App\Models\UserDetail
 */
class Gender extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'genders';

    /**
     * Atributos que pueden ser asignados en masa
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
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
     * Relación: Un género tiene muchos detalles de usuario
     *
     * @return HasMany<UserDetail>
     */
    public function userDetails(): HasMany
    {
        return $this->hasMany(UserDetail::class, 'gender_id');
    }

    /**
     * Scope: Obtener solo géneros activos
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
