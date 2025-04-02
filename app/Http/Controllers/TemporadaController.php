<?php

namespace App\Http\Controllers;

use App\Services\TemporadaService;
use Illuminate\Http\Request;
use App\Models\Temporada;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TemporadaController extends Controller
{
    use AuthorizesRequests;

    protected $temporadaService;

    public function __construct(TemporadaService $temporadaService)
    {
        $this->temporadaService = $temporadaService;
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $this->authorize('viewAny', Temporada::class);
        $temporadas = $this->temporadaService->getAll();
        return response()->json(['success' => true, 'data' => $temporadas]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Temporada::class);
        $result = $this->temporadaService->create($request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Temporada creada exitosamente'], 201);
    }

    public function show($id)
    {
        $temporada = $this->temporadaService->findById($id);
        $this->authorize('view', $temporada);
        return response()->json(['success' => true, 'data' => $temporada]);
    }

    public function update(Request $request, $id)
    {
        $temporada = $this->temporadaService->findById($id);
        $this->authorize('update', $temporada);
        $result = $this->temporadaService->update($id, $request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Temporada actualizada exitosamente'], 200);
    }

    public function destroy($id)
    {
        $temporada = $this->temporadaService->findById($id);
        $this->authorize('delete', $temporada);
        $this->temporadaService->delete($id);
        return response()->json(['success' => true, 'message' => 'Temporada eliminada exitosamente'], 200);
    }
}