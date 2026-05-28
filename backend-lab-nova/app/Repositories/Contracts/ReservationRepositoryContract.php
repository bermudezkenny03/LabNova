<?php

namespace App\Repositories\Contracts;

/**
 * Interface: ReservationRepositoryContract
 *
 * Define el contrato para operaciones relacionadas con reservas.
 *
 * @package App\Repositories\Contracts
 */
interface ReservationRepositoryContract extends BaseRepositoryContract
{
    /**
     * Obtener reservas por usuario
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByUser(int $userId, $perPage = 15);

    /**
     * Obtener reservas por equipo
     *
     * @param int $equipmentId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByEquipment(int $equipmentId, $perPage = 15);

    /**
     * Obtener reservas pendientes
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPending($perPage = 15);

    /**
     * Obtener reservas aprobadas
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getApproved($perPage = 15);

    /**
     * Verificar si existe conflicto de horario
     *
     * @param int $equipmentId
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     * @param int|null $excludeReservationId
     * @return bool
     */
    public function hasScheduleConflict(
        int $equipmentId,
        \DateTime $startTime,
        \DateTime $endTime,
        ?int $excludeReservationId = null
    ): bool;

    /**
     * Obtener reservas activas
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getActive($perPage = 15);
}
