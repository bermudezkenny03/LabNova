<?php

namespace App\Repositories\Contracts;

/**
 * Interface: UserRepositoryContract
 *
 * Define el contrato para operaciones relacionadas con usuarios.
 * Implementa métodos específicos para búsqueda y filtrado de usuarios.
 *
 * @package App\Repositories\Contracts
 */
interface UserRepositoryContract extends BaseRepositoryContract
{
    /**
     * Obtener usuario por email
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email);

    /**
     * Obtener todos los usuarios activos con relaciones
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActive();

    /**
     * Obtener usuarios por rol
     *
     * @param int $roleId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByRole(int $roleId, $perPage = 15);

    /**
     * Buscar usuarios
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15);

    /**
     * Verificar si un email existe
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool;
}
