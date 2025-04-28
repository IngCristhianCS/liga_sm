<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EventoPartido;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoPartidoTorneoController extends Controller
{
    /**
     * Obtiene los eventos de tipo 'gol' para un torneo específico
     * 
     * @param int $torneoId ID del torneo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventosByTorneo($torneoId)
    {
        // Verificar que el torneo existe
        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            return response()->json(['error' => 'Torneo no encontrado'], 404);
        }

        // Obtener los IDs de los partidos del torneo
        $partidosIds = Partido::where('torneo_id', $torneoId)->pluck('id');
        
        // Obtener los eventos de tipo 'gol' para esos partidos
        $eventos = EventoPartido::whereIn('partido_id', $partidosIds)
            ->where('tipo_evento', 'gol')
            ->with([
                'jugador',
                'partido.equipoLocal',
                'partido.equipoVisitante'
            ])
            ->get();

        // Agrupar los goles por jugador para crear la tabla de goleadores
        $goleadores = $eventos->groupBy('jugador_id')
            ->map(function ($items, $jugadorId) {
                $jugador = $items->first()->jugador;
                return [
                    'jugador_id' => $jugadorId,
                    'nombre' => $jugador->user ? $jugador->user->name : 'Jugador desconocido',
                    'equipo' => $jugador->equipo ? $jugador->equipo->nombre : 'Sin equipo',
                    'equipo_id' => $jugador->equipo_id,
                    'goles' => $items->count(),
                ];
            })
            ->sortByDesc('goles')
            ->values()
            ->all();

        return response()->json([
            'data' => $goleadores,
            'torneo' => $torneo->nombre
        ]);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
