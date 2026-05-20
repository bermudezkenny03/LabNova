<?php

namespace App\Services;

use App\DTOs\CreateReportDTO;
use App\Models\Report;
use App\Repositories\Eloquent\ReportRepository;
use App\Services\Contracts\ReportServiceContract;

/**
 * Service: ReportService
 *
 * Encapsula toda la lógica de negocio relacionada con reportes de equipos.
 *
 * @package App\Services
 */
class ReportService implements ReportServiceContract
{
    /**
     * Constructor: Inyectar dependencias
     *
     * @param ReportRepository $reportRepository
     */
    public function __construct(
        private ReportRepository $reportRepository,
    ) {}

    /**
     * Crear un nuevo reporte
     *
     * @param CreateReportDTO $dto
     * @return Report
     */
    public function create(CreateReportDTO $dto): Report
    {
        // Obtener ID del tipo de reporte por código
        $reportTypeId = 1; // Por defecto
        // TODO: Resolver tipo de reporte desde tabla de referencia

        // Crear reporte (estado por defecto: pending = 1)
        $reportData = [
            'equipment_id' => $dto->equipment_id,
            'user_id' => $dto->user_id,
            'report_type_id' => $reportTypeId,
            'description' => $dto->description,
            'attachment_url' => $dto->attachment_url,
            'report_status_id' => 1, // pending
        ];

        return $this->reportRepository->create($reportData);
    }

    /**
     * Marcar un reporte como completado
     *
     * @param int $reportId
     * @param string|null $resolution
     * @return Report
     */
    public function complete(int $reportId, ?string $resolution = null): Report
    {
        $updateData = [
            'report_status_id' => 3, // completed
        ];

        if ($resolution) {
            $updateData['description'] = $resolution;
        }

        $this->reportRepository->update($reportId, $updateData);

        return $this->reportRepository->findOrFail($reportId);
    }

    /**
     * Obtener todos los reportes
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15)
    {
        return $this->reportRepository->paginate($perPage);
    }

    /**
     * Obtener reportes pendientes
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPending($perPage = 15)
    {
        return $this->reportRepository->getPending($perPage);
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
        return $this->reportRepository->getByUser($userId, $perPage);
    }

    /**
     * Obtener reporte por ID
     *
     * @param int $id
     * @return Report
     */
    public function getById(int $id): Report
    {
        return $this->reportRepository->findOrFail($id);
    }
}
