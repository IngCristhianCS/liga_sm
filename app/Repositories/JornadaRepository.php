<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class JornadaRepository
{
    public function obtenerResultadosPorJornada(int $torneoId, int $jornadaId): Collection
    {
        return DB::table('partido AS p')
            ->join('equipo AS el', 'p.equipo_local_id', '=', 'el.id')
            ->join('equipo AS ev', 'p.equipo_visitante_id', '=', 'ev.id')
            ->where('p.torneo_id', $torneoId)
            ->where('p.jornada_id', $jornadaId)
            ->select(
                'el.nombre AS equipo_local',
                'p.goles_equipo_local',
                'ev.nombre AS equipo_visitante',
                'p.goles_equipo_visitante'
            )
            ->orderBy('p.fecha')
            ->get();
    }

    public function obtenerJornadasPorTorneo(int $torneoId): Collection
    {
        return DB::table('partido AS p')
            ->where('p.torneo_id', $torneoId)
            ->distinct()
            ->orderBy('p.jornada_id')
            ->pluck('p.jornada_id');
    }
}