<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Services\PartidoService;
use App\Services\JornadaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PartidoController extends Controller
{
    protected $partidoService;
    protected $jornadaService;
    use AuthorizesRequests;

    public function __construct(PartidoService $partidoService, JornadaService $jornadaService)
    {
        $this->partidoService = $partidoService;
        $this->jornadaService = $jornadaService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Partido::class);
        $torneoId = $request->query('torneo_id');
        $partidos = $this->partidoService->getAll($torneoId);
        return response()->json(['data' => $partidos]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Partido::class);
        $result = $this->partidoService->create($request->all());

        if (!$result['success']) {
            return response()->json(['errors' => $result['errors']], 422);
        }

        return response()->json([
            'message' => 'Partido creado exitosamente',
            'data' => $result['data']
        ], 201);
    }

    public function show($id)
    {
        $partido = $this->partidoService->findById($id);
        //$this->authorize('view', $partido);
        return response()->json(['data' => $partido]);
    }

    public function update(Request $request, $id)
    {
        $partido = $this->partidoService->findById($id);
        $this->authorize('update', $partido);

        $result = $this->partidoService->update($id, $request->all());

        if (!$result['success']) {
            return response()->json(['errors' => $result['errors']], 422);
        }

        return response()->json([
            'message' => 'Partido actualizado exitosamente',
            'data' => $result['data']
        ]);
    }

    public function destroy($id)
    {
        $partido = $this->partidoService->findById($id);
        $this->authorize('delete', $partido);
        
        $this->partidoService->delete($id);
        return response()->json(['message' => 'Partido eliminado exitosamente']);
    }
    
    public function obtenerPartidosPorEquipo(Request $request)
    {
        $equipoId = $request->query('equipo_id');
        $partidos = $this->jornadaService->obtenerPartidosPorJornadaEquipoTorneo($equipoId);
        return response()->json(['data' => $partidos]);
    }
}
