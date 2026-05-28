<?php

namespace App\Services;

use App\DTOs\CreateReservationDTO;
use App\Models\Reservation;
use App\Repositories\Eloquent\ReservationRepository;
use App\Repositories\Eloquent\EquipmentRepository;
use App\Services\Contracts\ReservationServiceContract;

/**
 * Service: ReservationService
 *
 * Encapsula toda la lógica de negocio relacionada con reservas.
 *
 * Validaciones de negocio:
 * - Equipo debe estar disponible
 * - No puede haber conflicto de horario
 * - Fecha de fin > fecha de inicio
 *
 * @package App\Services
 */
class ReservationService implements ReservationServiceContract
{
    /**
     * Constructor: Inyectar dependencias
     *
     * @param ReservationRepository $reservationRepository
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(
        private ReservationRepository $reservationRepository,
        private EquipmentRepository $equipmentRepository,
    ) {}

    /**
     * Crear una nueva reserva
     *
     * Validaciones:
     * - Equipo existe y está disponible
     * - No hay conflicto de horario
     * - Fecha de fin es posterior a fecha de inicio
     *
     * @param CreateReservationDTO $dto
     * @return Reservation
     * @throws \Exception
     */
    public function create(CreateReservationDTO $dto): Reservation
    {
        // Validar que el equipo existe y está disponible
        $equipment = $this->equipmentRepository->findOrFail($dto->equipment_id);

        if ($equipment->status->code !== 'available') {
            throw new \Exception("El equipo '{$equipment->name}' no está disponible.");
        }

        // Validar que la fecha de fin es posterior a fecha de inicio
        if ($dto->end_time <= $dto->start_time) {
            throw new \Exception("La fecha de fin debe ser posterior a la fecha de inicio.");
        }

        // Validar que no hay conflicto de horario
        if ($this->reservationRepository->hasScheduleConflict(
            $dto->equipment_id,
            $dto->start_time,
            $dto->end_time
        )) {
            throw new \Exception("El equipo tiene una reserva que entra en conflicto con el horario solicitado.");
        }

        // Crear reserva (estado por defecto: pending = 1)
        $reservationData = [
            'user_id' => $dto->user_id,
            'equipment_id' => $dto->equipment_id,
            'start_time' => $dto->start_time,
            'end_time' => $dto->end_time,
            'notes' => $dto->notes,
            'reservation_status_id' => 1, // pending
        ];

        return $this->reservationRepository->create($reservationData);
    }

    /**
     * Aprobar una reserva pendiente
     *
     * @param int $reservationId
     * @return Reservation
     */
    public function approve(int $reservationId): Reservation
    {
        $this->reservationRepository->update($reservationId, [
            'reservation_status_id' => 2, // approved
        ]);

        return $this->reservationRepository->findOrFail($reservationId);
    }

    /**
     * Rechazar una reserva
     *
     * @param int $reservationId
     * @param string|null $reason
     * @return Reservation
     */
    public function reject(int $reservationId, ?string $reason = null): Reservation
    {
        $this->reservationRepository->update($reservationId, [
            'reservation_status_id' => 3, // rejected
            'notes' => $reason,
        ]);

        return $this->reservationRepository->findOrFail($reservationId);
    }

    /**
     * Obtener todas las reservas
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15)
    {
        return $this->reservationRepository->paginate($perPage);
    }

    /**
     * Obtener reservas por usuario
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByUser(int $userId, $perPage = 15)
    {
        return $this->reservationRepository->getByUser($userId, $perPage);
    }

    /**
     * Obtener reserva por ID
     *
     * @param int $id
     * @return Reservation
     */
    public function getById(int $id): Reservation
    {
        return $this->reservationRepository->findOrFail($id);
    }
}
