<?php

namespace App\Services\Contracts;

use App\DTOs\CreateReportDTO;

/**
 * Interface: ReportServiceContract
 *
 * Define el contrato para operaciones de lógica de negocio relacionadas con reportes.
 *
 * @package App\Services\Contracts
 */
interface ReportServiceContract
{
    /**
     * Crear un nuevo reporte
     *
     * @param CreateReportDTO $dto
     * @return \App\Models\Report
     */
    public function create(CreateReportDTO $dto);

    /**
     * Marcar un reporte como completado
     *
     * @param int $reportId
     * @param string|null $resolution Descripción de la solución
     * @return \App\Models\Report
     */
    public function complete(int $reportId, ?string $resolution = null);

    /**
     * Obtener todos los reportes
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15);

    /**
     * Obtener reportes pendientes
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPending($perPage = 15);

    /**
     * Obtener reportes por usuario
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByUser(int $userId, $perPage = 15);

    /**
     * Obtener reporte por ID
     *
     * @param int $id
     * @return \App\Models\Report
     */
    public function getById(int $id);
}
