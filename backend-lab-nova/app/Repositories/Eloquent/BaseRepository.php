<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class: BaseRepository
 * 
 * Implementación base del patrón Repository.
 * Proporciona operaciones CRUD comunes para todos los repositories.
 * 
 * Todos los repositories específicos deben extender esta clase
 * e implementar el método model().
 * 
 * @example
 * class UserRepository extends BaseRepository
 * {
 *     public function model(): string
 *     {
 *         return User::class;
 *     }
 * 
 *     public function getAdmins()
 *     {
 *         return $this->model->where('role_id', 1)->get();
 *     }
 * }
 */
abstract class BaseRepository implements BaseRepositoryContract
{
    /**
     * Instancia del modelo
     * 
     * @var Model
     */
    protected Model $model;

    /**
     * Constructor: Instancia el modelo
     */
    public function __construct()
    {
        $this->model = app($this->model());
    }

    /**
     * Obtener la clase del modelo a usar
     * 
     * DEBE ser implementado por cada repository específico
     * 
     * @return string Nombre completo de la clase del modelo
     * 
     * @example
     * public function model(): string
     * {
     *     return User::class;
     * }
     */
    abstract public function model(): string;

    /**
     * Obtener instancia fresh del query builder
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query()
    {
        return $this->model->query();
    }

    /**
     * Obtener todos los registros
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->query()->get();
    }

    /**
     * Obtener registros con paginación
     * 
     * @param int $perPage Registros por página (default: 15)
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15)
    {
        return $this->query()->paginate($perPage);
    }

    /**
     * Obtener un registro por ID
     * 
     * @param int|string $id Identificador
     * @return Model|null
     */
    public function find($id)
    {
        return $this->query()->find($id);
    }

    /**
     * Obtener un registro por ID o lanzar excepción
     * 
     * @param int|string $id Identificador
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id)
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * Obtener el primer registro que cumpla una condición
     * 
     * @param array $attributes Atributos a buscar (clave => valor)
     * @return Model|null
     * 
     * @example
     * $repository->findBy(['email' => 'user@example.com']);
     */
    public function findBy(array $attributes)
    {
        return $this->query()
            ->where($attributes)
            ->first();
    }

    /**
     * Obtener todos los registros que cumplan una condición
     * 
     * @param array $attributes Atributos a buscar (clave => valor)
     * @return \Illuminate\Database\Eloquent\Collection
     * 
     * @example
     * $repository->findAllBy(['status' => 1]);
     */
    public function findAllBy(array $attributes)
    {
        $query = $this->query();

        foreach ($attributes as $key => $value) {
            $query = $query->where($key, $value);
        }

        return $query->get();
    }

    /**
     * Crear un nuevo registro
     * 
     * @param array $attributes Atributos del nuevo registro
     * @return Model El registro creado
     * 
     * @example
     * $user = $repository->create([
     *     'name' => 'John',
     *     'email' => 'john@example.com'
     * ]);
     */
    public function create(array $attributes)
    {
        return $this->query()->create($attributes);
    }

    /**
     * Actualizar un registro
     * 
     * @param int|string $id Identificador del registro
     * @param array $attributes Atributos a actualizar
     * @return bool true si se actualizó, false en caso contrario
     * 
     * @example
     * $repository->update(1, ['name' => 'Jane']);
     */
    public function update($id, array $attributes)
    {
        $model = $this->findOrFail($id);
        return $model->update($attributes);
    }

    /**
     * Actualizar o crear un registro
     * 
     * Si el registro existe, se actualiza; si no, se crea.
     * 
     * @param array $attributes Atributos para buscar el registro
     * @param array $values Valores a actualizar o crear
     * @return Model
     * 
     * @example
     * $user = $repository->updateOrCreate(
     *     ['email' => 'john@example.com'],
     *     ['name' => 'John', 'status' => 1]
     * );
     */
    public function updateOrCreate(array $attributes, array $values)
    {
        return $this->query()->updateOrCreate($attributes, $values);
    }

    /**
     * Eliminar un registro
     * 
     * @param int|string $id Identificador del registro
     * @return bool true si se eliminó, false en caso contrario
     * 
     * @example
     * $repository->delete(1);
     */
    public function delete($id)
    {
        $model = $this->findOrFail($id);
        return $model->delete();
    }

    /**
     * Eliminar múltiples registros por IDs
     * 
     * @param array $ids Array de identificadores
     * @return int Cantidad de registros eliminados
     * 
     * @example
     * $deleted = $repository->deleteMany([1, 2, 3]);
     */
    public function deleteMany(array $ids)
    {
        return $this->query()
            ->whereIn('id', $ids)
            ->delete();
    }

    /**
     * Contar el total de registros
     * 
     * @return int Cantidad total
     */
    public function count()
    {
        return $this->query()->count();
    }

    /**
     * Verificar si existe un registro con ese ID
     * 
     * @param int|string $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->query()->where('id', $id)->exists();
    }
}
