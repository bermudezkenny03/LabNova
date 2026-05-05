<?php

namespace App\Http\Controllers\Api;

use App\Models\Reservation;
use App\Models\ReservationLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = $request->get('per_page', 15);

            // Estudiantes solo ven sus propias reservas
            if ($user->role->name === 'Estudiante') {
                $reservations = Reservation::with(['user', 'equipment', 'approver'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->paginate($perPage);
            } else {
                // Admin, Lab Manager y Docente ven todas
                $reservations = Reservation::with(['user', 'equipment', 'approver'])
                    ->latest()
                    ->paginate($perPage);
            }

            return response()->json([
                'success' => true,
                'message' => 'Listado de reservas',
                'data' => $reservations->items(),
                'meta' => [
                    'total'        => $reservations->total(),
                    'per_page'     => $reservations->perPage(),
                    'current_page' => $reservations->currentPage(),
                    'last_page'    => $reservations->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al listar reservas', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Estudiantes solo pueden crear para sí mismos
            if ($user->role->name === 'Estudiante' && $request->get('user_id') && $request->get('user_id') != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para crear reservas para otros usuarios'
                ], 403);
            }

            $validated = $request->validate([
                'equipment_id' => 'required|exists:equipment,id',
                'start_time'   => 'required|date',
                'end_time'     => 'required|date|after:start_time',
                'notes'        => 'nullable|string',
            ], [
                'equipment_id.required' => 'El equipo es obligatorio.',
                'equipment_id.exists'   => 'El equipo seleccionado no existe.',
                'start_time.required'   => 'La fecha de inicio es obligatoria.',
                'end_time.required'     => 'La fecha de fin es obligatoria.',
                'end_time.after'        => 'La hora final debe ser mayor a la inicial.',
            ]);

            if (Carbon::parse($validated['start_time'])->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se permiten reservas en fechas pasadas.',
                ], 422);
            }

            if ($this->hasConflict((int) $validated['equipment_id'], $validated['start_time'], $validated['end_time'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El equipo ya se encuentra reservado en el horario seleccionado.',
                ], 422);
            }

            $validated['user_id'] = $request->user()->id;
            $validated['status']  = 'pending';

            $reservation = Reservation::create($validated);
            $reservation->load(['user', 'equipment']);

            ReservationLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $request->user()->id,
                'action'         => 'created',
                'description'    => 'Reserva creada',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada exitosamente en estado pendiente.',
                'data'    => $reservation,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear reserva', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $reservation = Reservation::with(['user', 'equipment', 'approver', 'logs'])->findOrFail($id);

            return response()->json(['success' => true, 'message' => 'Detalle de reserva', 'data' => $reservation]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al consultar reserva', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $reservation = Reservation::findOrFail($id);

            $validated = $request->validate([
                'start_time' => 'sometimes|date',
                'end_time'   => 'sometimes|date|after:start_time',
                'notes'      => 'nullable|string',
            ]);

            $reservation->update($validated);

            return response()->json(['success' => true, 'message' => 'Reserva actualizada correctamente', 'data' => $reservation]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar reserva', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Reservation::findOrFail($id)->delete();

            return response()->json(['success' => true, 'message' => 'Reserva eliminada correctamente']);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar reserva', 'error' => $e->getMessage()], 500);
        }
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->update([
                'status'      => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            ReservationLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $request->user()->id,
                'action'         => 'approved',
                'description'    => 'Reserva aprobada',
            ]);

            return response()->json(['success' => true, 'message' => 'Reserva aprobada', 'data' => $reservation]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al aprobar reserva', 'error' => $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['reason' => 'required|string']);

            $reservation = Reservation::findOrFail($id);
            $reservation->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->reason,
            ]);

            ReservationLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $request->user()->id,
                'action'         => 'rejected',
                'description'    => 'Reserva rechazada: ' . $request->reason,
            ]);

            return response()->json(['success' => true, 'message' => 'Reserva rechazada', 'data' => $reservation]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al rechazar reserva', 'error' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $reservation = Reservation::findOrFail($id);

            // Solo el propietario o admin/lab manager pueden cancelar
            if ($reservation->user_id !== $user->id && !in_array($user->role->name, ['Super Admin', 'Administrador', 'Encargado de Laboratorio'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para cancelar esta reserva'
                ], 403);
            }

            if ($reservation->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'No es posible cancelar una reserva aprobada.',
                ], 422);
            }

            if ($reservation->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reserva no puede ser cancelada en su estado actual.',
                ], 400);
            }

            $reservation->update(['status' => 'cancelled']);

            ReservationLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $request->user()->id,
                'action'         => 'cancelled',
                'description'    => 'Reserva cancelada',
            ]);

            return response()->json(['success' => true, 'message' => 'Reserva cancelada correctamente.', 'data' => $reservation]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cancelar reserva', 'error' => $e->getMessage()], 500);
        }
    }

    public function logs(int $id): JsonResponse
    {
        try {
            $logs = ReservationLog::with('user')
                ->where('reservation_id', $id)
                ->latest()
                ->get();

            return response()->json(['success' => true, 'data' => $logs]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener logs', 'error' => $e->getMessage()], 500);
        }
    }

    public function availability(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'equipment_id' => 'required|exists:equipment,id',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date|after:start_date',
            ]);

            $conflict = $this->hasConflict(
                (int) $request->equipment_id,
                $request->start_date,
                $request->end_date
            );

            return response()->json(['available' => !$conflict]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al verificar disponibilidad', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verifica si existe alguna reserva activa que se superponga con el rango dado.
     * Cubre los 3 casos de solapamiento:
     *   1. La reserva existente empieza dentro del rango solicitado
     *   2. La reserva existente termina dentro del rango solicitado
     *   3. La reserva existente envuelve completamente el rango solicitado
     */
    private function hasConflict(int $equipmentId, string $start, string $end, ?int $excludeId = null): bool
    {
        return Reservation::where('equipment_id', $equipmentId)
            ->whereIn('status', ['pending', 'approved'])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function ($sub) use ($start, $end) {
                      // Reserva existente envuelve completamente el rango solicitado
                      $sub->where('start_time', '<=', $start)
                          ->where('end_time', '>=', $end);
                  });
            })->exists();
    }
}
