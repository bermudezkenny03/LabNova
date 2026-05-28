<?php

namespace App\Http\Controllers\Api;

use App\Models\ReportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $requests = ReportRequest::with('user')
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Listado de solicitudes de reportes',
                'data'    => $requests->items(),
                'meta'    => [
                    'total'        => $requests->total(),
                    'per_page'     => $requests->perPage(),
                    'current_page' => $requests->currentPage(),
                    'last_page'    => $requests->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al listar solicitudes', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type'       => 'required|in:reservations,equipment_usage,user_activity',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
                'filters'    => 'nullable|array',
            ], [
                'start_date.required'     => 'La fecha de inicio es obligatoria.',
                'end_date.required'       => 'La fecha de fin es obligatoria.',
                'end_date.after_or_equal' => 'El rango de fechas es inválido.',
            ]);

            $validated['user_id'] = $request->user()->id;
            $validated['status']  = 'pending';

            $reportRequest = ReportRequest::create($validated);
            $reportRequest->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Solicitud generada correctamente.',
                'data'    => $reportRequest,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear solicitud', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $reportRequest = ReportRequest::with(['user', 'reports'])->findOrFail($id);

            return response()->json(['success' => true, 'data' => $reportRequest]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Solicitud no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al consultar solicitud', 'error' => $e->getMessage()], 500);
        }
    }

    public function approve(int $id): JsonResponse
    {
        try {
            $reportRequest = ReportRequest::findOrFail($id);
            $reportRequest->update(['status' => 'processing']);

            return response()->json(['success' => true, 'message' => 'Solicitud aprobada', 'data' => $reportRequest]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Solicitud no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al aprobar solicitud', 'error' => $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        try {
            $reportRequest = ReportRequest::findOrFail($id);
            $reportRequest->update(['status' => 'failed']);

            return response()->json(['success' => true, 'message' => 'Solicitud rechazada', 'data' => $reportRequest]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Solicitud no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al rechazar solicitud', 'error' => $e->getMessage()], 500);
        }
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $reportRequest = ReportRequest::findOrFail($id);
            $reportRequest->update(['status' => 'completed']);

            return response()->json(['success' => true, 'message' => 'Solicitud completada', 'data' => $reportRequest]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Solicitud no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al completar solicitud', 'error' => $e->getMessage()], 500);
        }
    }
}
