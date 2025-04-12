<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UbicacionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class UbicacionController extends Controller
{
    protected $ubicacionService;

    public function __construct(UbicacionService $ubicacionService)
    {
        $this->ubicacionService = $ubicacionService;
    }

    public function index(): JsonResponse
    {
        try {
            $ubicaciones = $this->ubicacionService->getAll();
            return response()->json([
                'status' => true,
                'data' => $ubicaciones
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $ubicacion = $this->ubicacionService->getById($id);
            return response()->json([
                'status' => true,
                'data' => $ubicacion
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $ubicacion = $this->ubicacionService->create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Ubicación creada exitosamente',
                'data' => $ubicacion
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $ubicacion = $this->ubicacionService->update($id, $request->all());
            return response()->json([
                'status' => true,
                'message' => 'Ubicación actualizada exitosamente',
                'data' => $ubicacion
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->ubicacionService->delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Ubicación eliminada exitosamente'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}