<?php

namespace App\Repositories\Contracts;

/**
 * Interface: EquipmentRepositoryContract
 *
 * Define el contrato para operaciones relacionadas con equipos.
 *
 * @package App\Repositories\Contracts
 */
interface EquipmentRepositoryContract extends BaseRepositoryContract
{
    /**
     * Obtener equipos por categoría
     *
     * @param int $categoryId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, $perPage = 15);

    /**
     * Obtener equipos disponibles
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAvailable($perPage = 15);

    /**
     * Obtener equipos activos
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getActive($perPage = 15);

    /**
     * Buscar equipos
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15);

    /**
     * Verificar si un código existe
     *
     * @param string $code
     * @param int|null $excludeId
     * @return bool
     */
    public function codeExists(string $code, ?int $excludeId = null): bool;
}
