<?php

namespace App\Http\Controllers;

use App\Models\Jornada;
use App\Services\JornadaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JornadaController extends Controller
{
    protected $jornadaService;
    use AuthorizesRequests;

    public function __construct(JornadaService $jornadaService)
    {
        $this->jornadaService = $jornadaService;
        $this->middleware('auth:sanctum');
    }

    // Keep existing methods and add these if they don't exist
    public function index(Request $request)
    {
        $this->authorize('viewAny', Jornada::class);
        $torneoId = $request->query('torneo_id');
        return response()->json(['data' => $this->jornadaService->getAll($torneoId)]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Jornada::class);
        $result = $this->jornadaService->create($request->all());

        if (!$result['success']) {
            return response()->json(['errors' => $result['errors']], 422);
        }

        return response()->json([
            'message' => 'Jornada creada exitosamente',
            'data' => $result['data']
        ], 201);
    }

    public function show($id)
    {
        $jornada = $this->jornadaService->findById($id);
        $this->authorize('view', $jornada);
        return response()->json(['data' => $jornada]);
    }

    public function update(Request $request, $id)
    {
        $jornada = $this->jornadaService->findById($id);
        $this->authorize('update', $jornada);

        $result = $this->jornadaService->update($id, $request->all());

        if (!$result['success']) {
            return response()->json(['errors' => $result['errors']], 422);
        }

        return response()->json([
            'message' => 'Jornada actualizada exitosamente',
            'data' => $result['data']
        ]);
    }

    public function destroy($id)
    {
        $jornada = $this->jornadaService->findById($id);
        $this->authorize('delete', $jornada);
        
        $this->jornadaService->delete($id);
        return response()->json(['message' => 'Jornada eliminada exitosamente']);
    }
}
