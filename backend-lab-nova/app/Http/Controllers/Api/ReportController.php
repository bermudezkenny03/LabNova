<?php

namespace App\Http\Controllers\Api;

use App\Models\Report;
use App\Models\Reservation;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $reports = Report::with('reportRequest')
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Listado de reportes',
                'data'    => $reports->items(),
                'meta'    => [
                    'total'        => $reports->total(),
                    'per_page'     => $reports->perPage(),
                    'current_page' => $reports->currentPage(),
                    'last_page'    => $reports->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al listar reportes', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'report_request_id' => 'required|exists:report_requests,id',
                'file_path'         => 'required|string',
                'file_name'         => 'nullable|string',
                'file_type'         => 'nullable|string',
            ]);

            $validated['generated_at'] = now();
            $report = Report::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Reporte creado correctamente',
                'data'    => $report,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear reporte', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $report = Report::with('reportRequest')->findOrFail($id);

            return response()->json(['success' => true, 'data' => $report]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reporte no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al consultar reporte', 'error' => $e->getMessage()], 500);
        }
    }

    public function download(int $id): JsonResponse
    {
        try {
            $report = Report::findOrFail($id);

            return response()->json(['success' => true, 'data' => $report]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reporte no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al descargar reporte', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Report::findOrFail($id)->delete();

            return response()->json(['success' => true, 'message' => 'Reporte eliminado correctamente']);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Reporte no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar reporte', 'error' => $e->getMessage()], 500);
        }
    }

    public function statsReservations(Request $request): JsonResponse
    {
        try {
            $start = $request->get('start_date');
            $end   = $request->get('end_date');

            $query = Reservation::query();
            if ($start) $query->where('start_time', '>=', $start);
            if ($end)   $query->where('end_time', '<=', $end);

            $stats = [
                'total'     => $query->count(),
                'pending'   => (clone $query)->where('status', 'pending')->count(),
                'approved'  => (clone $query)->where('status', 'approved')->count(),
                'rejected'  => (clone $query)->where('status', 'rejected')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener estadísticas', 'error' => $e->getMessage()], 500);
        }
    }

    public function statsEquipment(): JsonResponse
    {
        try {
            $stats = [
                'total'          => Equipment::count(),
                'available'      => Equipment::where('status', 'available')->count(),
                'maintenance'    => Equipment::where('status', 'maintenance')->count(),
                'out_of_service' => Equipment::where('status', 'out_of_service')->count(),
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener estadísticas', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Genera los datos completos para un reporte y los retorna como JSON.
     * El PDF se construye en el cliente (frontend).
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type'       => 'required|in:reservations,equipment_usage,user_activity',
                'start_date' => 'nullable|date',
                'end_date'   => 'nullable|date|after_or_equal:start_date',
            ]);

            $type  = $validated['type'];
            $start = $validated['start_date'] ?? null;
            $end   = $validated['end_date'] ?? null;
            $user  = $request->user();

            $payload = match ($type) {
                'reservations'    => $this->buildReservationsData($start, $end),
                'equipment_usage' => $this->buildEquipmentData($start, $end),
                'user_activity'   => $this->buildUserActivityData($start, $end),
            };

            if (empty($payload['records'] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existen datos para generar el reporte.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reporte generado correctamente.',
                'data'    => array_merge([
                    'type'         => $type,
                    'start_date'   => $start,
                    'end_date'     => $end,
                    'generated_at' => now()->toISOString(),
                    'generated_by' => trim($user->name . ' ' . ($user->last_name ?? '')),
                ], $payload),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error durante la generación del reporte.'], 500);
        }
    }

    private function buildReservationsData(?string $start, ?string $end): array
    {
        $records = Reservation::with(['user', 'equipment'])
            ->when($start, fn ($q) => $q->whereDate('start_time', '>=', $start))
            ->when($end,   fn ($q) => $q->whereDate('start_time', '<=', $end))
            ->latest('start_time')
            ->get();

        return [
            'stats' => [
                'total'     => $records->count(),
                'pending'   => $records->where('status', 'pending')->count(),
                'approved'  => $records->where('status', 'approved')->count(),
                'rejected'  => $records->where('status', 'rejected')->count(),
                'cancelled' => $records->where('status', 'cancelled')->count(),
                'completed' => $records->where('status', 'completed')->count(),
            ],
            'records' => $records->map(fn ($r) => [
                'id'      => $r->id,
                'usuario' => $r->user ? trim($r->user->name . ' ' . ($r->user->last_name ?? '')) : "#{$r->user_id}",
                'equipo'  => $r->equipment?->name ?? "#{$r->equipment_id}",
                'inicio'  => $r->start_time,
                'fin'     => $r->end_time,
                'estado'  => $r->status,
                'notas'   => $r->notes ?? '',
            ])->values(),
        ];
    }

    private function buildEquipmentData(?string $start, ?string $end): array
    {
        $equipments = Equipment::with('category')
            ->withCount(['reservations as reservations_count' => function ($q) use ($start, $end) {
                if ($start) $q->whereDate('start_time', '>=', $start);
                if ($end)   $q->whereDate('start_time', '<=', $end);
            }])
            ->get();

        return [
            'stats' => [
                'total'          => $equipments->count(),
                'available'      => $equipments->where('status', 'available')->count(),
                'maintenance'    => $equipments->where('status', 'maintenance')->count(),
                'out_of_service' => $equipments->where('status', 'out_of_service')->count(),
            ],
            'records' => $equipments->map(fn ($e) => [
                'id'        => $e->id,
                'nombre'    => $e->name,
                'codigo'    => $e->code,
                'categoria' => $e->category?->name ?? 'Sin categoría',
                'estado'    => $e->status,
                'reservas'  => $e->reservations_count,
            ])->values(),
        ];
    }

    private function buildUserActivityData(?string $start, ?string $end): array
    {
        $users = User::with('role')
            ->withCount(['reservations as reservations_count' => function ($q) use ($start, $end) {
                if ($start) $q->whereDate('start_time', '>=', $start);
                if ($end)   $q->whereDate('start_time', '<=', $end);
            }])
            ->get();

        return [
            'stats' => [
                'total_usuarios'   => $users->count(),
                'usuarios_activos' => $users->where('reservations_count', '>', 0)->count(),
                'total_reservas'   => (int) $users->sum('reservations_count'),
            ],
            'records' => $users->sortByDesc('reservations_count')->map(fn ($u) => [
                'id'      => $u->id,
                'nombre'  => trim($u->name . ' ' . ($u->last_name ?? '')),
                'email'   => $u->email,
                'rol'     => $u->role?->name ?? 'Sin rol',
                'reservas' => $u->reservations_count,
            ])->values(),
        ];
    }
}
