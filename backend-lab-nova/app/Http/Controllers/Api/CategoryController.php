<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $categories = Category::latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Listado de categorías',
                'data' => CategoryResource::collection($categories),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar categorías',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(CategoryStoreRequest $request): JsonResponse
    {
        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'status' => $request->status ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada correctamente',
                'data' => new CategoryResource($category),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear categoría',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $category): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Detalle de categoría',
                'data' => new CategoryResource(Category::findOrFail($category)),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Categoría no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al consultar categoría', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(CategoryUpdateRequest $request, int $category): JsonResponse
    {
        try {
            $model = Category::findOrFail($category);

            $model->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'status' => $request->status ?? $model->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente',
                'data' => new CategoryResource($model),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Categoría no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar categoría', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $category): JsonResponse
    {
        try {
            Category::findOrFail($category)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada correctamente',
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Categoría no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar categoría', 'error' => $e->getMessage()], 500);
        }
    }
}
