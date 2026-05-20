<?php

namespace App\DTOs;

use ReflectionClass;

/**
 * Class: BaseDTO
 *
 * Clase base para todos los Data Transfer Objects (DTOs).
 * Proporciona funcionalidad común para transferencia de datos entre capas.
 *
 * Los DTOs se utilizan para:
 * - Validar datos entre capas
 * - Evitar pasar Request directamente a Services
 * - Mantener tipo seguro (type safety) en PHP
 * - Documentar qué datos espera cada operación
 *
 * @see https://en.wikipedia.org/wiki/Data_transfer_object
 * @package App\DTOs
 */
abstract class BaseDTO
{
    /**
     * Convertir DTO a array (para almacenar en BD)
     *
     * @return array
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        $array = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);

            if ($value !== null) {
                $array[$property->getName()] = $value;
            }
        }

        return $array;
    }

    /**
     * Convertir DTO a JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Crear DTO desde array
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }
}
