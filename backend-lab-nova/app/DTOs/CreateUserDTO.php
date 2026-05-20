<?php

namespace App\DTOs;

/**
 * DTO: CreateUserDTO
 *
 * Objeto de transferencia de datos para crear un nuevo usuario.
 * Utilizado en la capa de Presentación (Controller) para pasar datos
 * validados a la capa de Lógica de Negocio (Service).
 *
 * Propiedades:
 * - @property string $name Nombre completo del usuario
 * - @property string $email Email único
 * - @property string $password Contraseña (será hasheada en el Service)
 * - @property string $phone Número de teléfono
 * - @property int $role_id ID del rol
 * - @property string $gender_code Código del género
 * - @property string|null $identification Documento de identificación
 * - @property string|null $date_of_birth Fecha de nacimiento (YYYY-MM-DD)
 *
 * @package App\DTOs
 */
final class CreateUserDTO extends BaseDTO
{
    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $phone
     * @param int $role_id
     * @param string $gender_code
     * @param string|null $identification
     * @param string|null $date_of_birth
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $phone,
        public int $role_id,
        public string $gender_code,
        public ?string $identification = null,
        public ?string $date_of_birth = null,
    ) {}
}
