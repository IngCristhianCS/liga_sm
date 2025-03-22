<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ClasificacionRepository
{
    public function obtenerClasificacion(): Collection
    {
        return DB::table('equipo AS e')
        ->leftJoin('partido AS p', function ($join) {
            $join->on('e.id', '=', 'p.equipo_local_id')
                ->orOn('e.id', '=', 'p.equipo_visitante_id');
        })
        ->join('torneo AS t', 'p.torneo_id', '=', 't.id')
        ->where('t.nombre', 'Varonil libre') // Reemplaza 'NombreDelTorneo' con el nombre de tu torneo
        ->select(
            'e.nombre AS equipo',
            DB::raw('COUNT(p.id) AS PJ'),
            DB::raw("SUM(CASE WHEN p.equipo_local_id = e.id AND p.goles_equipo_local > p.goles_equipo_visitante THEN 1 WHEN p.equipo_visitante_id = e.id AND p.goles_equipo_visitante > p.goles_equipo_local THEN 1 ELSE 0 END) AS PG"),
            DB::raw('0 AS PE'), // Partidos Empatados (siempre 0)
            DB::raw("SUM(CASE WHEN p.equipo_local_id = e.id AND p.goles_equipo_local < p.goles_equipo_visitante THEN 1 WHEN p.equipo_visitante_id = e.id AND p.goles_equipo_visitante < p.goles_equipo_local THEN 1 ELSE 0 END) AS PP"),
            DB::raw("SUM(CASE WHEN p.equipo_local_id = e.id THEN p.goles_equipo_local WHEN p.equipo_visitante_id = e.id THEN p.goles_equipo_visitante ELSE 0 END) AS GF"),
            DB::raw("SUM(CASE WHEN p.equipo_local_id = e.id THEN p.goles_equipo_visitante WHEN p.equipo_visitante_id = e.id THEN p.goles_equipo_local ELSE 0 END) AS GC"),
            DB::raw("(SUM(CASE WHEN p.equipo_local_id = e.id THEN p.goles_equipo_local WHEN p.equipo_visitante_id = e.id THEN p.goles_equipo_visitante ELSE 0 END) - SUM(CASE WHEN p.equipo_local_id = e.id THEN p.goles_equipo_visitante WHEN p.equipo_visitante_id = e.id THEN p.goles_equipo_local ELSE 0 END)) AS DIF"),
            DB::raw("SUM(CASE WHEN p.equipo_local_id = e.id AND p.goles_equipo_local > p.goles_equipo_visitante THEN 3 WHEN p.equipo_visitante_id = e.id AND p.goles_equipo_visitante > p.goles_equipo_local THEN 3 ELSE 0 END) AS PTS")
        )
        ->groupBy('e.nombre')
        ->orderBy('PTS', 'DESC')
        ->orderBy('DIF', 'DESC')
        ->get();
    }
}