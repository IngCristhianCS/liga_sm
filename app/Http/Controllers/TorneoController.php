<?php

namespace App\Http\Controllers;

use App\Services\JornadaService;
use App\Services\TorneoService;
use Illuminate\Http\Request;
use App\Models\Torneo;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TorneoController extends Controller
{
    use AuthorizesRequests;
    protected $jornadaService;
    protected $torneoService;

    public function __construct(TorneoService $torneoService, JornadaService $jornadaService)
    {
        $this->jornadaService = $jornadaService;
        $this->torneoService = $torneoService;
        // Eliminar $this->middleware('auth:sanctum'); para dejar jornadas publico
    }

    public function jornadas($torneoId)
    {
        $jornadas = $this->jornadaService->obtenerJornadasPorTorneo($torneoId);

        $resultadosPorJornada = [];

        foreach ($jornadas as $jornadaId) {
            $resultados = $this->jornadaService->obtenerResultadosPorJornada($torneoId, $jornadaId);

            $resultadosPorJornada[] = [
                'jornada_id' => $jornadaId,
                'resultados' => $resultados,
            ];
        }

        return response()->json($resultadosPorJornada);
    }

    public function index(Request $request)
    {
        //$this->authorize('viewAny', Torneo::class);

        $catalog = $request->has('catalog') && $request->catalog === 'true';
        $torneos = $this->torneoService->getAll($catalog);
        return response()->json(['success' => true, 'data' => $torneos]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Torneo::class);

        $result = $this->torneoService->create($request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Torneo creado exitosamente'], 201);
    }

    public function show(Request $request, $id)
    {
        $catalog = $request->has('catalog') && $request->catalog === 'true';
        $torneo = $this->torneoService->findById($id, $catalog);

        $this->authorize('view', $torneo);

        return response()->json(['success' => true, 'data' => $torneo]);
    }

    public function update(Request $request, $id)
    {
        $torneo = $this->torneoService->findById($id);

        $this->authorize('update', $torneo);

        $result = $this->torneoService->update($id, $request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Torneo actualizado exitosamente'], 200);
    }

    public function destroy($id)
    {
        $torneo = $this->torneoService->findById($id);

        $this->authorize('delete', $torneo);

        $this->torneoService->delete($id);

        return response()->json(['success' => true, 'message' => 'Torneo eliminado exitosamente'], 200);
    }
    
    public function catalog()
    {
        $torneos = $this->torneoService->getAll(true);
        return response()->json(['success' => true, 'data' => $torneos]);
    }
}