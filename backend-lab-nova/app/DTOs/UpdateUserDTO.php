<?php

namespace App\DTOs;

/**
 * DTO: UpdateUserDTO
 *
 * Objeto de transferencia de datos para actualizar un usuario existente.
 * Solo incluye los campos que pueden ser actualizados.
 *
 * Propiedades opcionales:
 * - @property string|null $name Nombre completo
 * - @property string|null $email Email
 * - @property string|null $phone Teléfono
 * - @property string|null $password Contraseña
 * - @property string|null $gender_code Código del género
 * - @property string|null $identification Documento
 * - @property string|null $date_of_birth Fecha de nacimiento
 *
 * @package App\DTOs
 */
final class UpdateUserDTO extends BaseDTO
{
    /**
     * @param int $id ID del usuario a actualizar
     * @param string|null $name
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $password
     * @param string|null $gender_code
     * @param string|null $identification
     * @param string|null $date_of_birth
     */
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $password = null,
        public ?string $gender_code = null,
        public ?string $identification = null,
        public ?string $date_of_birth = null,
    ) {}
}
