<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Services\EquipoService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EquipoController extends Controller
{
    protected $equipoService;
    use AuthorizesRequests;
    public function __construct(EquipoService $equipoService)
    {
        $this->equipoService = $equipoService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Equipo::class);
        
        // Check if torneo_id is provided in the request
        $torneoId = $request->input('torneo_id');
        
        if ($torneoId) {
            $equipos = $this->equipoService->getEquiposByTorneo($torneoId);
        } else {
            $equipos = $this->equipoService->getAllEquipos();
        }
        
        return response()->json(['data' => $equipos]);
    }

    public function show(int $id)
    {
        $equipo = $this->equipoService->getEquipoById($id);
        $this->authorize('view', $equipo);
        return response()->json(['data' => $equipo]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Equipo::class);
        $data = $this->equipoService->createEquipo($request->all());

        if (isset($data['errors'])) {
            return response()->json(['errors' => $data['errors']], 422);
        }

        return response()->json(['data' => $data], 201);
    }

    public function update(Request $request, int $id)
    {
        $equipo = $this->equipoService->getEquipoById($id);
        $this->authorize('update', $equipo);
        $data = $this->equipoService->updateEquipo($id, $request->all());

        if (isset($data['errors'])) {
            return response()->json(['errors' => $data['errors']], 422);
        }

        return response()->json(['data' => $data]);
    }

    public function destroy(int $id)
    {
        $equipo = $this->equipoService->getEquipoById($id);
        $this->authorize('delete', $equipo);
        $this->equipoService->deleteEquipo($id);
        return response()->json(null, 204);
    }
}