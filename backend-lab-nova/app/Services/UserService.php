<?php

namespace App\Services;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use App\Services\Contracts\UserServiceContract;
use Illuminate\Support\Facades\Hash;

/**
 * Service: UserService
 *
 * Encapsula toda la lógica de negocio relacionada con usuarios.
 * Coordina entre Repository y Models para las operaciones de usuario.
 *
 * Responsabilidades:
 * - Validar reglas de negocio (email único, etc.)
 * - Coordinar operaciones en múltiples repositorios
 * - Manejar transacciones
 * - Registrar eventos de auditoría
 *
 * @package App\Services
 */
class UserService implements UserServiceContract
{
    /**
     * Constructor: Inyectar dependencias
     *
     * @param UserRepository $userRepository
     */
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * Crear un nuevo usuario
     *
     * Validaciones:
     * - Email debe ser único
     * - Password se hashea automáticamente
     * - Se crea registro en user_details
     *
     * @param CreateUserDTO $dto
     * @return User
     * @throws \Exception
     */
    public function create(CreateUserDTO $dto): User
    {
        // Validar que el email no exista
        if ($this->userRepository->emailExists($dto->email)) {
            throw new \Exception("El email '{$dto->email}' ya está registrado.");
        }

        // Crear usuario
        $userData = [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'phone' => $dto->phone,
            'role_id' => $dto->role_id,
            'status' => true,
        ];

        $user = $this->userRepository->create($userData);

        // Crear detalles de usuario
        $user->userDetail()->create([
            'gender_code' => $dto->gender_code,
            'identification' => $dto->identification,
            'date_of_birth' => $dto->date_of_birth,
        ]);

        return $user;
    }

    /**
     * Actualizar un usuario
     *
     * @param UpdateUserDTO $dto
     * @return User
     * @throws \Exception
     */
    public function update(UpdateUserDTO $dto): User
    {
        $user = $this->userRepository->findOrFail($dto->id);

        // Validar email si está siendo actualizado
        if ($dto->email && $dto->email !== $user->email) {
            if ($this->userRepository->emailExists($dto->email, $dto->id)) {
                throw new \Exception("El email '{$dto->email}' ya está registrado.");
            }
        }

        // Preparar datos para actualizar
        $userData = [];
        if ($dto->name) $userData['name'] = $dto->name;
        if ($dto->email) $userData['email'] = $dto->email;
        if ($dto->phone) $userData['phone'] = $dto->phone;
        if ($dto->password) $userData['password'] = Hash::make($dto->password);

        $this->userRepository->update($dto->id, $userData);

        // Actualizar detalles si existen
        if ($user->userDetail) {
            $detailData = [];
            if ($dto->gender_code) $detailData['gender_code'] = $dto->gender_code;
            if ($dto->identification) $detailData['identification'] = $dto->identification;
            if ($dto->date_of_birth) $detailData['date_of_birth'] = $dto->date_of_birth;

            if (!empty($detailData)) {
                $user->userDetail()->update($detailData);
            }
        }

        return $this->userRepository->findOrFail($dto->id);
    }

    /**
     * Obtener todos los usuarios (paginados)
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }

    /**
     * Obtener usuario por ID
     *
     * @param int $id
     * @return User
     */
    public function getById(int $id): User
    {
        return $this->userRepository->findOrFail($id);
    }

    /**
     * Eliminar un usuario
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->userRepository->findOrFail($id);
        return (bool) $this->userRepository->update($id, ['status' => false]);
    }

    /**
     * Buscar usuarios
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search, $perPage = 15)
    {
        return $this->userRepository->search($search, $perPage);
    }
}
