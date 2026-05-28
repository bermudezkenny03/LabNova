<?php

namespace App\Repositories\Contracts;

/**
 * Interface: ReportRepositoryContract
 *
 * Define el contrato para operaciones relacionadas con reportes.
 *
 * @package App\Repositories\Contracts
 */
interface ReportRepositoryContract extends BaseRepositoryContract
{
    /**
     * Obtener reportes por usuario
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByUser(int $userId, $perPage = 15);

    /**
     * Obtener reportes por equipo
     *
     * @param int $equipmentId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByEquipment(int $equipmentId, $perPage = 15);

    /**
     * Obtener reportes pendientes
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPending($perPage = 15);

    /**
     * Obtener reportes completados
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCompleted($perPage = 15);

    /**
     * Obtener reportes por tipo
     *
     * @param int $reportTypeId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByType(int $reportTypeId, $perPage = 15);
}
