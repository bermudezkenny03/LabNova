<?php

namespace App\Repositories\Eloquent;

use App\Models\Equipment;

/**
 * Repository: EquipmentRepository
 *
 * Encapsula todas las operaciones de base de datos relacionadas con equipos.
 *
 * Responsabilidades:
 * - Consultas a tabla equipment
 * - Relaciones con categorías, estados, imágenes, reservas
 * - Filtrado y búsqueda de equipos
 * - Operaciones CRUD de equipos
 *
 * @see \App\Models\Equipment
 * @see \App\Services\EquipmentService
 * @package App\Repositories\Eloquent
 */
class EquipmentRepository extends BaseRepository
{
    /**
     * Retorna la clase del modelo a usar
     *
     * @return string
     */
    public function model(): string
    {
        return Equipment::class;
    }

    /**
     * Obtener equipo con todas sus relaciones
     *
     * @param int $id
     * @return Equipment|null
     */
    public function findWithRelations($id)
    {
        return $this->query()
            ->with(['category', 'status', 'images', 'reservations'])
            ->find($id);
    }

    /**
     * Obtener equipo por código
     *
     * @param string $code
     * @return Equipment|null
     */
    public function findByCode(string $code)
    {
        return $this->findBy(['code' => $code]);
    }

    /**
     * Obtener equipos por categoría
     *
     * @param int $categoryId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, $perPage = 15)
    {
        return $this->query()
            ->where('category_id', $categoryId)
            ->with(['category', 'status', 'images'])
            ->paginate($perPage);
    }

    /**
     * Obtener equipos disponibles (status = 'available')
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAvailable($perPage = 15)
    {
        return $this->query()
            ->whereHas('status', function ($query) {
                $query->where('code', 'available');
            })
            ->with(['category', 'status', 'images'])
            ->paginate($perPage);
    }

    /**
     * Obtener equipos activos
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getActive($perPage = 15)
    {
        return $this->query()
            ->where('is_active', true)
            ->with(['category', 'status', 'images'])
            ->paginate($perPage);
    }

    /**
     * Buscar equipos por nombre o código
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15)
    {
        return $this->query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->with(['category', 'status', 'images'])
            ->paginate($perPage);
    }

    /**
     * Verificar si un código de equipo existe
     *
     * @param string $code
     * @param int|null $excludeId
     * @return bool
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $query = $this->query()->where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
