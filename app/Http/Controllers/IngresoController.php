<?php

namespace App\Http\Controllers;

use App\Services\IngresoService;
use Illuminate\Http\Request;
use App\Models\Ingreso;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class IngresoController extends Controller
{
    use AuthorizesRequests;
    protected $ingresoService;

    public function __construct(IngresoService $ingresoService)
    {
        $this->ingresoService = $ingresoService;
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $this->authorize('viewAny', Ingreso::class);

        $ingresos = $this->ingresoService->getAll();
        return response()->json(['success' => true, 'data' => $ingresos]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Ingreso::class);

        $result = $this->ingresoService->create($request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Ingreso creado exitosamente'], 201);
    }

    public function show($id)
    {
        $ingreso = $this->ingresoService->findById($id);

        $this->authorize('view', $ingreso);

        return response()->json(['success' => true, 'data' => $ingreso]);
    }

    public function update(Request $request, $id)
    {
        $ingreso = $this->ingresoService->findById($id);

        $this->authorize('update', $ingreso);

        $result = $this->ingresoService->update($id, $request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Ingreso actualizado exitosamente'], 200);
    }

    public function destroy($id)
    {
        $ingreso = $this->ingresoService->findById($id);

        $this->authorize('delete', $ingreso);

        $this->ingresoService->delete($id);

        return response()->json(['success' => true, 'message' => 'Ingreso eliminado exitosamente'], 200);
    }
}