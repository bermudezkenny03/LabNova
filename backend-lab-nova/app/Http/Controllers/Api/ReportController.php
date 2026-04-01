<?php

namespace App\Http\Controllers\Api;

use App\Models\Report;
use App\Models\Reservation;
use App\Models\Equipment;
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
}
