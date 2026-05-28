<?php

namespace App\Repositories\Eloquent;

use App\Models\User;

/**
 * Repository: UserRepository
 *
 * Encapsula todas las operaciones de base de datos relacionadas con usuarios.
 * Sigue el patrón Repository para separar la lógica de acceso a datos.
 *
 * Responsabilidades:
 * - Consultas a tabla users
 * - Relaciones con user_details, roles
 * - Filtrado y búsqueda de usuarios
 * - Operaciones CRUD de usuarios
 *
 * @see \App\Models\User
 * @see \App\Services\UserService
 * @package App\Repositories\Eloquent
 */
class UserRepository extends BaseRepository
{
    /**
     * Retorna la clase del modelo a usar en consultas
     *
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Obtener usuario con todas sus relaciones cargadas
     *
     * @param int $id
     * @return User|null
     */
    public function findWithRelations($id)
    {
        return $this->query()
            ->with(['userDetail', 'role'])
            ->find($id);
    }

    /**
     * Obtener usuario por email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        return $this->findBy(['email' => $email]);
    }

    /**
     * Obtener todos los usuarios activos con relaciones
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActive()
    {
        return $this->query()
            ->where('status', true)
            ->with(['userDetail', 'role'])
            ->get();
    }

    /**
     * Obtener usuarios por rol
     *
     * @param int $roleId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByRole(int $roleId, $perPage = 15)
    {
        return $this->query()
            ->where('role_id', $roleId)
            ->with(['userDetail', 'role'])
            ->paginate($perPage);
    }

    /**
     * Buscar usuarios por nombre, email o teléfono
     *
     * @param string $search Término de búsqueda
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15)
    {
        return $this->query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->with(['userDetail', 'role'])
            ->paginate($perPage);
    }

    /**
     * Verificar si un email existe
     *
     * @param string $email
     * @param int|null $excludeId Excluir un ID de la búsqueda (útil en actualización)
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = $this->query()->where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
