<?php

namespace App\Repositories\Eloquent;

use App\Models\Report;

/**
 * Repository: ReportRepository
 *
 * Encapsula todas las operaciones de base de datos relacionadas con reportes.
 *
 * Responsabilidades:
 * - Consultas a tabla reports
 * - Filtrado de reportes por estado, tipo, usuario
 * - Operaciones CRUD de reportes
 *
 * @see \App\Models\Report
 * @see \App\Services\ReportService
 * @package App\Repositories\Eloquent
 */
class ReportRepository extends BaseRepository
{
    /**
     * Retorna la clase del modelo a usar
     *
     * @return string
     */
    public function model(): string
    {
        return Report::class;
    }

    /**
     * Obtener reporte con todas sus relaciones
     *
     * @param int $id
     * @return Report|null
     */
    public function findWithRelations($id)
    {
        return $this->query()
            ->with(['equipment', 'user', 'type', 'status'])
            ->find($id);
    }

    /**
     * Obtener reportes por usuario
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByUser(int $userId, $perPage = 15)
    {
        return $this->query()
            ->where('user_id', $userId)
            ->with(['equipment', 'type', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener reportes por equipo
     *
     * @param int $equipmentId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByEquipment(int $equipmentId, $perPage = 15)
    {
        return $this->query()
            ->where('equipment_id', $equipmentId)
            ->with(['user', 'type', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener reportes pendientes
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
            ->with(['equipment', 'user', 'type'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Obtener reportes completados
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCompleted($perPage = 15)
    {
        return $this->query()
            ->whereHas('status', function ($query) {
                $query->where('code', 'completed');
            })
            ->with(['equipment', 'user', 'type'])
            ->paginate($perPage);
    }

    /**
     * Obtener reportes por tipo
     *
     * @param int $reportTypeId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByType(int $reportTypeId, $perPage = 15)
    {
        return $this->query()
            ->where('report_type_id', $reportTypeId)
            ->with(['equipment', 'user', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
