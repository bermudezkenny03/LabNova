<?php

namespace App\DTOs;

/**
 * DTO: CreateReportDTO
 *
 * Objeto de transferencia de datos para crear un nuevo reporte de equipo.
 * Encapsula la información de un problema o condición de equipo.
 *
 * @package App\DTOs
 */
final class CreateReportDTO extends BaseDTO
{
    /**
     * @param int $equipment_id ID del equipo
     * @param int $user_id ID del usuario que reporta
     * @param string $report_type_code Código del tipo de reporte
     * @param string $description Descripción del problema
     * @param string|null $attachment_url URL de archivo adjunto
     */
    public function __construct(
        public int $equipment_id,
        public int $user_id,
        public string $report_type_code,
        public string $description,
        public ?string $attachment_url = null,
    ) {}
}
