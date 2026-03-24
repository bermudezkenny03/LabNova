<?php

namespace App\Http\Controllers\Api;

use App\Models\Equipment;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\EquipmentStoreRequest;
use App\Http\Requests\EquipmentUpdateRequest;

class EquipmentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $equipment = Equipment::with(['category', 'images'])->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Listado de equipos',
                'data' => EquipmentResource::collection($equipment),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al listar equipos', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(EquipmentStoreRequest $request): JsonResponse
    {
        try {
            $equipment = Equipment::create($request->validated());
            $equipment->load(['category', 'images']);

            return response()->json([
                'success' => true,
                'message' => 'Equipo creado correctamente',
                'data' => new EquipmentResource($equipment),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear equipo', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $equipment): JsonResponse
    {
        try {
            $equipment = Equipment::with(['category', 'images', 'reservations'])->findOrFail($equipment);

            return response()->json([
                'success' => true,
                'message' => 'Detalle del equipo',
                'data' => new EquipmentResource($equipment),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al consultar equipo', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(EquipmentUpdateRequest $request, int $equipment): JsonResponse
    {
        try {
            $model = Equipment::findOrFail($equipment);
            $model->update($request->validated());
            $model->load(['category', 'images']);

            return response()->json([
                'success' => true,
                'message' => 'Equipo actualizado correctamente',
                'data' => new EquipmentResource($model),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar equipo', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $equipment): JsonResponse
    {
        try {
            Equipment::findOrFail($equipment)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Equipo eliminado correctamente',
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar equipo', 'error' => $e->getMessage()], 500);
        }
    }
}
