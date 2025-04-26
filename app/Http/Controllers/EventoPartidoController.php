<?php

namespace App\Http\Controllers;

use App\Models\EventoPartido;
use App\Services\EventoPartidoService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventoPartidoController extends Controller
{
    protected $eventoPartidoService;
    use AuthorizesRequests;

    public function __construct(EventoPartidoService $eventoPartidoService)
    {
        $this->eventoPartidoService = $eventoPartidoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', EventoPartido::class);
        $eventos = $this->eventoPartidoService->getAllEventos();
        return response()->json(['data' => $eventos]);
    }

    /**
     * Get eventos by partido ID
     */
    public function getByPartido($partidoId)
    {
        $eventos = $this->eventoPartidoService->getEventosByPartido($partidoId);
        return response()->json(['data' => $eventos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', EventoPartido::class);
        $data = $this->eventoPartidoService->createEvento($request->all());

        if (isset($data['errors'])) {
            return response()->json(['errors' => $data['errors']], 422);
        }

        return response()->json(['data' => $data], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $evento = $this->eventoPartidoService->getEventoById($id);
        $this->authorize('view', $evento);
        return response()->json(['data' => $evento]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $evento = $this->eventoPartidoService->getEventoById($id);
        $this->authorize('update', $evento);
        $data = $this->eventoPartidoService->updateEvento($id, $request->all());

        if (isset($data['errors'])) {
            return response()->json(['errors' => $data['errors']], 422);
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $evento = $this->eventoPartidoService->getEventoById($id);
        $this->authorize('delete', $evento);
        $this->eventoPartidoService->deleteEvento($id);
        return response()->json(null, 204);
    }
}

