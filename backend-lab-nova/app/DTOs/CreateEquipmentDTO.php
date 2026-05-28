<?php

namespace App\DTOs;

/**
 * DTO: CreateEquipmentDTO
 *
 * Objeto de transferencia de datos para crear un nuevo equipo.
 * Encapsula toda la información necesaria para el registro de equipos.
 *
 * @package App\DTOs
 */
final class CreateEquipmentDTO extends BaseDTO
{
    /**
     * @param string $name Nombre del equipo
     * @param string|null $description Descripción
     * @param string $code Código único del equipo
     * @param int $category_id ID de la categoría
     * @param string|null $serial_number Número de serie
     * @param string|null $model Modelo del equipo
     * @param string|null $brand Marca
     * @param string $equipment_status_code Código de estado inicial (ej: 'available')
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public string $code,
        public int $category_id,
        public ?string $serial_number = null,
        public ?string $model = null,
        public ?string $brand = null,
        public string $equipment_status_code = 'available',
    ) {}
}
