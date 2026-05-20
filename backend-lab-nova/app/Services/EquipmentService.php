<?php

namespace App\Services;

use App\DTOs\CreateEquipmentDTO;
use App\Models\Equipment;
use App\Repositories\Eloquent\EquipmentRepository;
use App\Services\Contracts\EquipmentServiceContract;

/**
 * Service: EquipmentService
 *
 * Encapsula toda la lógica de negocio relacionada con equipos.
 *
 * @package App\Services
 */
class EquipmentService implements EquipmentServiceContract
{
    /**
     * Constructor: Inyectar dependencias
     *
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(
        private EquipmentRepository $equipmentRepository,
    ) {}

    /**
     * Crear un nuevo equipo
     *
     * @param CreateEquipmentDTO $dto
     * @return Equipment
     * @throws \Exception
     */
    public function create(CreateEquipmentDTO $dto): Equipment
    {
        // Validar que el código sea único
        if ($this->equipmentRepository->codeExists($dto->code)) {
            throw new \Exception("El código '{$dto->code}' ya está registrado.");
        }

        // Obtener ID del estado por código
        $statusId = 1; // Por defecto 'available' tiene ID 1
        // TODO: Esto debería venir de una tabla de referencia

        // Crear equipo
        $equipmentData = [
            'name' => $dto->name,
            'code' => $dto->code,
            'description' => $dto->description,
            'category_id' => $dto->category_id,
            'serial_number' => $dto->serial_number,
            'model' => $dto->model,
            'brand' => $dto->brand,
            'equipment_status_id' => $statusId,
            'is_active' => true,
            'stock' => 1,
        ];

        return $this->equipmentRepository->create($equipmentData);
    }

    /**
     * Obtener todos los equipos
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15)
    {
        return $this->equipmentRepository->paginate($perPage);
    }

    /**
     * Obtener equipos disponibles
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAvailable($perPage = 15)
    {
        return $this->equipmentRepository->getAvailable($perPage);
    }

    /**
     * Obtener equipo por ID
     *
     * @param int $id
     * @return Equipment
     */
    public function getById(int $id): Equipment
    {
        return $this->equipmentRepository->findOrFail($id);
    }

    /**
     * Buscar equipos
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15)
    {
        return $this->equipmentRepository->search($search, $perPage);
    }
}
