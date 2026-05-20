<?php

namespace App\Services\Contracts;

use App\DTOs\CreateReservationDTO;

/**
 * Interface: ReservationServiceContract
 *
 * Define el contrato para operaciones de lógica de negocio relacionadas con reservas.
 *
 * @package App\Services\Contracts
 */
interface ReservationServiceContract
{
    /**
     * Crear una nueva reserva
     *
     * Responsabilidades:
     * - Validar disponibilidad del equipo
     * - Validar conflictos de horario
     * - Crear reserva en BD
     * - Registrar en logs
     *
     * @param CreateReservationDTO $dto
     * @return \App\Models\Reservation
     * @throws \App\Exceptions\EquipmentNotAvailableException
     * @throws \App\Exceptions\ReservationConflictException
     */
    public function create(CreateReservationDTO $dto);

    /**
     * Aprobar una reserva pendiente
     *
     * @param int $reservationId
     * @return \App\Models\Reservation
     */
    public function approve(int $reservationId);

    /**
     * Rechazar una reserva
     *
     * @param int $reservationId
     * @param string|null $reason Razón del rechazo
     * @return \App\Models\Reservation
     */
    public function reject(int $reservationId, ?string $reason = null);

    /**
     * Obtener todas las reservas
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15);

    /**
     * Obtener reservas por usuario
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByUser(int $userId, $perPage = 15);

    /**
     * Obtener reserva por ID
     *
     * @param int $id
     * @return \App\Models\Reservation
     */
    public function getById(int $id);
}
