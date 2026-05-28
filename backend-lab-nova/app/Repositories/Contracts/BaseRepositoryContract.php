<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;

/**
 * Interface: BaseRepositoryContract
 * 
 * Define las operaciones CRUD básicas que todo repository debe implementar.
 * Establece el contrato para operaciones comunes de acceso a datos.
 * 
 * @template TModel
 */
interface BaseRepositoryContract
{
    /**
     * Obtener el modelo a usar en consultas
     * 
     * @return string
     */
    public function model(): string;

    /**
     * Obtener todos los registros
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Obtener registros con paginación
     * 
     * @param int $perPage Registros por página
     * @return Paginator
     */
    public function paginate($perPage = 15);

    /**
     * Obtener un registro por ID
     * 
     * @param int|string $id
     * @return Model|null
     */
    public function find($id);

    /**
     * Obtener un registro por ID o lanzar excepción
     * 
     * @param int|string $id
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id);

    /**
     * Obtener el primer registro que cumpla la condición
     * 
     * @param array $attributes
     * @return Model|null
     */
    public function findBy(array $attributes);

    /**
     * Obtener todos los registros que cumplan la condición
     * 
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllBy(array $attributes);

    /**
     * Crear un nuevo registro
     * 
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes);

    /**
     * Actualizar un registro
     * 
     * @param int|string $id
     * @param array $attributes
     * @return bool
     */
    public function update($id, array $attributes);

    /**
     * Actualizar o crear un registro
     * 
     * @param array $attributes Atributos para buscar
     * @param array $values Valores a actualizar/crear
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values);

    /**
     * Eliminar un registro
     * 
     * @param int|string $id
     * @return bool
     */
    public function delete($id);

    /**
     * Eliminar múltiples registros
     * 
     * @param array $ids
     * @return int Cantidad de registros eliminados
     */
    public function deleteMany(array $ids);

    /**
     * Contar registros totales
     * 
     * @return int
     */
    public function count();

    /**
     * Verificar si existe un registro
     * 
     * @param int|string $id
     * @return bool
     */
    public function exists($id);
}
