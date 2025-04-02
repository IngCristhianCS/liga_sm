<?php

namespace App\Http\Controllers;

use App\Services\EgresoService;
use Illuminate\Http\Request;
use App\Models\Egreso;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EgresoController extends Controller
{
    use AuthorizesRequests;
    protected $egresoService;

    public function __construct(EgresoService $egresoService)
    {
        $this->egresoService = $egresoService;
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $this->authorize('viewAny', Egreso::class);

        $egresos = $this->egresoService->getAll();
        return response()->json(['success' => true, 'data' => $egresos]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Egreso::class);

        $result = $this->egresoService->create($request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Egreso creado exitosamente'], 201);
    }

    public function show($id)
    {
        $egreso = $this->egresoService->findById($id);

        $this->authorize('view', $egreso);

        return response()->json(['success' => true, 'data' => $egreso]);
    }

    public function update(Request $request, $id)
    {
        $egreso = $this->egresoService->findById($id);

        $this->authorize('update', $egreso);

        $result = $this->egresoService->update($id, $request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Egreso actualizado exitosamente'], 200);
    }

    public function destroy($id)
    {
        $egreso = $this->egresoService->findById($id);

        $this->authorize('delete', $egreso);

        $this->egresoService->delete($id);

        return response()->json(['success' => true, 'message' => 'Egreso eliminado exitosamente'], 200);
    }
}