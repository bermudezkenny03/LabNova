<?php

namespace App\DTOs;

use DateTime;

/**
 * DTO: CreateReservationDTO
 *
 * Objeto de transferencia de datos para crear una nueva reserva de equipo.
 * Contiene la información necesaria para validar y registrar una reserva.
 *
 * @package App\DTOs
 */
final class CreateReservationDTO extends BaseDTO
{
    /**
     * @param int $user_id ID del usuario que hace la reserva
     * @param int $equipment_id ID del equipo a reservar
     * @param DateTime $start_time Fecha/hora de inicio
     * @param DateTime $end_time Fecha/hora de fin
     * @param string|null $notes Notas adicionales
     */
    public function __construct(
        public int $user_id,
        public int $equipment_id,
        public DateTime $start_time,
        public DateTime $end_time,
        public ?string $notes = null,
    ) {}
}
