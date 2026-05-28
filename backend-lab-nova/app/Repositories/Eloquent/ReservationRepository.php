<?php

namespace App\Repositories\Eloquent;

use App\Models\Reservation;

/**
 * Repository: ReservationRepository
 *
 * Encapsula todas las operaciones de base de datos relacionadas con reservas.
 *
 * Responsabilidades:
 * - Consultas a tabla reservations
 * - Validaciones de conflictos de horario
 * - Filtrado de reservas por estado, usuario, equipo
 * - Operaciones CRUD de reservas
 *
 * @see \App\Models\Reservation
 * @see \App\Services\ReservationService
 * @package App\Repositories\Eloquent
 */
class ReservationRepository extends BaseRepository
{
    /**
     * Retorna la clase del modelo a usar
     *
     * @return string
     */
    public function model(): string
    {
        return Reservation::class;
    }

    /**
     * Obtener reserva con todas sus relaciones
     *
     * @param int $id
     * @return Reservation|null
     */
    public function findWithRelations($id)
    {
        return $this->query()
            ->with(['user', 'equipment', 'status', 'logs'])
            ->find($id);
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
        return $this->query()
            ->where('user_id', $userId)
            ->with(['equipment', 'status'])
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener reservas por equipo
     *
     * @param int $equipmentId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByEquipment(int $equipmentId, $perPage = 15)
    {
        return $this->query()
            ->where('equipment_id', $equipmentId)
            ->with(['user', 'status'])
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener reservas pendientes
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPending($perPage = 15)
    {
        return $this->query()
            ->whereHas('status', function ($query) {
                $query->where('code', 'pending');
            })
            ->with(['user', 'equipment'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Obtener reservas aprobadas
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getApproved($perPage = 15)
    {
        return $this->query()
            ->whereHas('status', function ($query) {
                $query->where('code', 'approved');
            })
            ->with(['user', 'equipment'])
            ->paginate($perPage);
    }

    /**
     * Verificar si existe conflicto de horario para un equipo
     *
     * Una reserva entra en conflicto si el equipo está reservado en el mismo período.
     *
     * @param int $equipmentId
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     * @param int|null $excludeReservationId Excluir una reserva de la búsqueda
     * @return bool
     */
    public function hasScheduleConflict(
        int $equipmentId,
        \DateTime $startTime,
        \DateTime $endTime,
        ?int $excludeReservationId = null
    ): bool {
        $query = $this->query()
            ->where('equipment_id', $equipmentId)
            ->whereIn('reservation_status_id', [1, 2]) // pending (1) y approved (2)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->exists();
    }

    /**
     * Obtener reservas activas (no canceladas ni completadas)
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getActive($perPage = 15)
    {
        return $this->query()
            ->whereNotIn('reservation_status_id', [3, 5]) // exclude rejected (3) and completed (5)
            ->with(['user', 'equipment', 'status'])
            ->orderBy('start_time', 'asc')
            ->paginate($perPage);
    }
}
