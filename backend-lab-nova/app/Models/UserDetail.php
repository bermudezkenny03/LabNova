<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['gender_id', 'birthdate', 'address', 'addon_address', 'notes', 'user_id'])]
#[Hidden([])]

/**
 * Model: UserDetail (Detalles de Usuario)
 *
 * Almacena información adicional y personalizada de cada usuario.
 *
 * @property int $id Identificador único
 * @property int $user_id ID del usuario propietario
 * @property int|null $gender_id ID del género (referencia a tabla genders)
 * @property \Illuminate\Support\Carbon|null $birthdate Fecha de nacimiento
 * @property string|null $address Dirección principal
 * @property string|null $addon_address Dirección adicional
 * @property string|null $notes Notas adicionales
 * @property \Illuminate\Support\Carbon|null $deleted_at Fecha de eliminación (soft delete)
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 *
 * @relationships
 * - user() : BelongsTo -> Usuario propietario
 * - gender() : BelongsTo -> Género del usuario
 *
 * @example
 * $detail = UserDetail::find(1);
 * $detail->user; // Usuario propietario
 * $detail->gender->name; // "Masculino", "Femenino", etc.
 *
 * @see \App\Models\User
 * @see \App\Models\Gender
 */
class UserDetail extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'user_details';

    /**
     * Casting automático de tipos de atributos
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relación: Un detalle de usuario pertenece a un usuario
     *
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Un detalle de usuario tiene un género
     *
     * @return BelongsTo<Gender>
     */
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    /**
     * Crear un nuevo detalle de usuario
     *
     * @param array $validated Datos validados
     * @param int $userId ID del usuario
     * @return self
     */
    public static function createUserDetail($validated, $userId)
    {
        $userDetailData = array_filter(
            $validated,
            fn($key) => in_array($key, (new self)->getFillable()),
            ARRAY_FILTER_USE_KEY
        );
        $userDetailData['user_id'] = $userId;

        return self::create($userDetailData);
    }

    /**
     * Actualizar detalles de usuario existente
     *
     * @param array $validated Datos validados
     * @param User $user Usuario propietario
     * @return void
     */
    public static function updateUserDetail($validated, $user)
    {
        if ($user->userDetail) {
            $user->userDetail->update(array_filter(
                $validated,
                fn($key) => in_array($key, (new self)->getFillable()),
                ARRAY_FILTER_USE_KEY
            ));
        }
    }

    /**
     * Obtener el nombre del género
     *
     * @return string|null
     */
    public function getGenderName(): ?string
    {
        return $this->gender?->name;
    }

    /**
     * Obtener la edad del usuario
     *
     * @return int|null Edad en años
     */
    public function getAge(): ?int
    {
        if (!$this->birthdate) {
            return null;
        }
        return $this->birthdate->diffInYears(now());
    }
}
