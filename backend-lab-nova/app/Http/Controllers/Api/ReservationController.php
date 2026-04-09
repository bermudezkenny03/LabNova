<?php

namespace App\Http\Controllers\Api;

use App\Models\Reservation;
use App\Models\ReservationLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $reservations = Reservation::with(['user', 'equipment', 'approver'])
                ->latest()
                ->paginate($perPage);

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
            $validated = $request->validate([
                'equipment_id' => 'required|exists:equipment,id',
                'start_time'   => 'required|date',
                'end_time'     => 'required|date|after:start_time',
                'notes'        => 'nullable|string',
            ]);

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
                'message' => 'Reserva creada correctamente',
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
            $reservation = Reservation::findOrFail($id);
            $reservation->update(['status' => 'cancelled']);

            ReservationLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $request->user()->id,
                'action'         => 'cancelled',
                'description'    => 'Reserva cancelada',
            ]);

            return response()->json(['success' => true, 'message' => 'Reserva cancelada', 'data' => $reservation]);
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

            $conflict = Reservation::where('equipment_id', $request->equipment_id)
                ->whereIn('status', ['pending', 'approved'])
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_time', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_time', [$request->start_date, $request->end_date]);
                })->exists();

            return response()->json(['available' => !$conflict]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al verificar disponibilidad', 'error' => $e->getMessage()], 500);
        }
    }
}
