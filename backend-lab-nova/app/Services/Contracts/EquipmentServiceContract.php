<?php

namespace App\Services\Contracts;

use App\DTOs\CreateEquipmentDTO;

/**
 * Interface: EquipmentServiceContract
 *
 * Define el contrato para operaciones de lógica de negocio relacionadas con equipos.
 *
 * @package App\Services\Contracts
 */
interface EquipmentServiceContract
{
    /**
     * Crear un nuevo equipo
     *
     * @param CreateEquipmentDTO $dto
     * @return \App\Models\Equipment
     */
    public function create(CreateEquipmentDTO $dto);

    /**
     * Obtener todos los equipos
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15);

    /**
     * Obtener equipos disponibles
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAvailable($perPage = 15);

    /**
     * Obtener equipo por ID
     *
     * @param int $id
     * @return \App\Models\Equipment
     */
    public function getById(int $id);

    /**
     * Buscar equipos
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15);
}
