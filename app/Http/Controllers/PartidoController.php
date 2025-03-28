<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use Illuminate\Http\Request;
use App\Services\JornadaService;

class PartidoController extends Controller
{

    protected $jornadaService;

    public function __construct(JornadaService $jornadaService)
    {
        $this->jornadaService = $jornadaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Partido $partido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partido $partido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partido $partido)
    {
        //
    }

    public function obtenerPartidosPorEquipo(Request $request)
    {
        $partidos = $this->jornadaService->obtenerPartidosPorJornadaEquipoTorneo();

        if (isset($partidos['error'])) {
            return response()->json(['success' => false, 'error' => $partidos['error']], 422);
        }

        return response()->json(['success' => true, 'data' => $partidos], 200);
    }
}
