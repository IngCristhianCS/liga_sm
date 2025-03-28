<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Services\JugadorService;
use Illuminate\Http\Request;

class JugadorController extends Controller
{
    protected $jugadorService;

    public function __construct(JugadorService $jugadorService)
    {
        $this->jugadorService = $jugadorService;
    }

    public function index()
    {
        $jugadores = $this->jugadorService->getAllJugadores();
        return response()->json(['data' => $jugadores]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Jugador::class);
        $jugador = $this->jugadorService->createJugador($request->all());
        return response()->json(['data' => $jugador], 201);
    }

    public function show(Jugador $jugador)
    {
        return response()->json(['data' => $jugador]);
    }

    public function update(Request $request, Jugador $jugador)
    {
        $this->authorize('update', $jugador);
        $jugador = $this->jugadorService->updateJugador($jugador, $request->all());
        return response()->json(['data' => $jugador]);
    }

    public function destroy(Jugador $jugador)
    {
        $this->authorize('delete', $jugador);
        $this->jugadorService->deleteJugador($jugador);
        return response()->json(null, 204);
    }
}