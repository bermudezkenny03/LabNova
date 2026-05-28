<?php

namespace App\Services\Contracts;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;

/**
 * Interface: UserServiceContract
 *
 * Define el contrato para operaciones de lógica de negocio relacionadas con usuarios.
 * Establece las operaciones que deben ser implementadas por el UserService.
 *
 * @package App\Services\Contracts
 */
interface UserServiceContract
{
    /**
     * Crear un nuevo usuario
     *
     * Responsabilidades:
     * - Validar que el email no exista
     * - Crear usuario en BD
     * - Crear detalles de usuario
     * - Registrar en logs
     *
     * @param CreateUserDTO $dto
     * @return \App\Models\User
     * @throws \App\Exceptions\EmailAlreadyExistsException
     */
    public function create(CreateUserDTO $dto);

    /**
     * Actualizar un usuario existente
     *
     * @param UpdateUserDTO $dto
     * @return \App\Models\User
     */
    public function update(UpdateUserDTO $dto);

    /**
     * Obtener todos los usuarios
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15);

    /**
     * Obtener usuario por ID
     *
     * @param int $id
     * @return \App\Models\User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id);

    /**
     * Eliminar un usuario
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Buscar usuarios
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15);
}
