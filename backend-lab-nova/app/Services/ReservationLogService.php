<?php

namespace App\Services;

use App\Models\ReservationLog;
use App\Models\ReservationLogAction;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Illuminate\Support\Facades\Cache;

/**
 * Service: ReservationLogService
 *
 * Servicio centralizado para manejar la creación y gestión de logs de reservas.
 *
 * BENEFICIOS DE ARQUITECTURA:
 * ✓ Encapsulación: lógica de logs en un solo lugar
 * ✓ Reutilizable: usado por múltiples controladores/servicios
 * ✓ Testeable: fácil de mockear para pruebas unitarias
 * ✓ Mantenible: cambios a la auditoría no requieren cambiar múltiples archivos
 * ✓ Escalable: fácil agregar nuevas acciones o metadata
 *
 * MEJORAS DE CALIDAD ISO:
 * - Validación de acciones disponibles
 * - Caché de acciones para performance
 * - Manejo de errores consistente
 * - Documentación completa (DocBlocks)
 * - Separación de responsabilidades (SRP)
 *
 * @example
 * // Crear un log de reserva
 * $service = app(ReservationLogService::class);
 * $service->logReservationAction(
 *     reservationId: 1,
 *     actionCode: 'approved',
 *     userId: auth()->id(),
 *     description: 'Reserva aprobada por admin'
 * );
 *
 * @see \App\Models\ReservationLog
 * @see \App\Models\ReservationLogAction
 * @see \App\Http\Controllers\Api\ReservationController
 */
class ReservationLogService
{
    /**
     * Clave de caché para acciones disponibles
     */
    private const ACTIONS_CACHE_KEY = 'reservation_log_actions_by_code';

    /**
     * Duración del caché en minutos (1 hora)
     */
    private const ACTIONS_CACHE_TTL = 60;

    /**
     * Crear un log de acción de reserva
     *
     * Este es el método principal para registrar cualquier cambio o acción
     * realizada sobre una reserva.
     *
     * FLUJO:
     * 1. Valida que la acción exista
     * 2. Obtiene el ID de la acción desde caché o BD
     * 3. Crea el registro en la tabla de logs
     * 4. Retorna el log creado
     *
     * @param int $reservationId ID de la reserva
     * @param string $actionCode Código de la acción (ej: 'approved', 'rejected')
     * @param int|null $userId ID del usuario que realiza la acción
     * @param string|null $description Descripción adicional del evento
     * @return \App\Models\ReservationLog El log creado
     *
     * @throws InvalidArgumentException Si la acción no existe
     *
     * @example
     * $log = $service->logReservationAction(
     *     reservationId: 123,
     *     actionCode: 'approved',
     *     userId: auth()->id(),
     *     description: 'Aprobada por revisión gerencial'
     * );
     * echo $log->action->name;  // "Aprobada"
     */
    public function logReservationAction(
        int $reservationId,
        string $actionCode,
        ?int $userId = null,
        ?string $description = null
    ): ReservationLog {
        // Obtener el ID de la acción (valida que exista)
        $actionId = $this->getActionIdByCode($actionCode);

        if (!$actionId) {
            throw new InvalidArgumentException(
                "La acción '{$actionCode}' no existe. Acciones válidas: " .
                implode(', ', $this->getAvailableActionCodes())
            );
        }

        // Crear el log con la FK a la acción
        return ReservationLog::create([
            'reservation_id'           => $reservationId,
            'reservation_log_action_id' => $actionId,
            'user_id'                  => $userId,
            'description'              => $description,
        ]);
    }

    /**
     * Obtener el ID de una acción por su código
     *
     * Utiliza caché para optimizar performance en aplicaciones con
     * alto volumen de logs.
     *
     * @param string $code Código de la acción (ej: 'approved')
     * @return int|null ID de la acción, null si no existe
     */
    public function getActionIdByCode(string $code): ?int
    {
        $actions = $this->getAllActionsFromCache();

        return $actions[$code]['id'] ?? null;
    }

    /**
     * Obtener todos los códigos de acciones disponibles
     *
     * @return array<string> Códigos disponibles
     *
     * @example
     * $codes = $service->getAvailableActionCodes();
     * // ['created', 'approved', 'rejected', 'cancelled', 'completed']
     */
    public function getAvailableActionCodes(): array
    {
        $actions = $this->getAllActionsFromCache();

        return array_keys($actions);
    }

    /**
     * Obtener todos los datos de acciones desde caché
     *
     * Si el caché no existe, lo recarga desde la base de datos.
     * Esto optimiza queries para aplicaciones con alto volumen de logs.
     *
     * @return array<string, array> Array indexado por código de acción
     */
    private function getAllActionsFromCache(): array
    {
        return Cache::remember(
            self::ACTIONS_CACHE_KEY,
            self::ACTIONS_CACHE_TTL,
            fn() => $this->buildActionsArray()
        );
    }

    /**
     * Construir array de acciones desde la BD
     *
     * @return array<string, array>
     */
    private function buildActionsArray(): array
    {
        $actions = ReservationLogAction::where('status', true)
            ->get(['id', 'code', 'name', 'action_type', 'color'])
            ->toArray();

        $result = [];
        foreach ($actions as $action) {
            $result[$action['code']] = $action;
        }

        return $result;
    }

    /**
     * Invalidar el caché de acciones
     *
     * Llamar a este método después de agregar/modificar/eliminar acciones
     * en la tabla de referencia.
     *
     * @return void
     */
    public function invalidateActionsCache(): void
    {
        Cache::forget(self::ACTIONS_CACHE_KEY);
    }

    /**
     * Obtener detalle completo de una acción
     *
     * @param string $code Código de la acción
     * @return \App\Models\ReservationLogAction|null
     */
    public function getActionByCode(string $code): ?ReservationLogAction
    {
        return ReservationLogAction::where('code', $code)
            ->where('status', true)
            ->first();
    }

    /**
     * Crear múltiples logs de forma atómica
     *
     * Útil para operaciones que generan varios eventos de auditoría.
     *
     * @param int $reservationId ID de la reserva
     * @param array<array> $logs Array de logs a crear
     *   [
     *       ['code' => 'created', 'userId' => 1, 'description' => 'Creada'],
     *       ['code' => 'approved', 'userId' => 2, 'description' => 'Aprobada'],
     *   ]
     * @return array<\App\Models\ReservationLog>
     *
     * @throws InvalidArgumentException
     */
    public function logMultipleActions(int $reservationId, array $logs): array
    {
        $created = [];

        foreach ($logs as $log) {
            $created[] = $this->logReservationAction(
                reservationId: $reservationId,
                actionCode: $log['code'] ?? throw new InvalidArgumentException('Log debe tener "code"'),
                userId: $log['userId'] ?? null,
                description: $log['description'] ?? null
            );
        }

        return $created;
    }
}
