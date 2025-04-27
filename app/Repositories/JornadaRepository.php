<?php

namespace App\Repositories;

use App\Models\Jornada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class JornadaRepository
{
    protected $model;

    public function __construct(Jornada $jornada)
    {
        $this->model = $jornada;
    }

    public function getAll($torneoId = null)
    {
        $query = $this->model->query();
        
        if ($torneoId) {
            $query->where('torneo_id', $torneoId);
        }

        return $query->with('torneo')->get();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $jornada = $this->findById($id);
        $jornada->update($data);
        return $jornada->fresh();
    }

    public function delete($id)
    {
        $jornada = $this->findById($id);
        return $jornada->delete();
    }

    public function obtenerResultadosPorJornada(int $torneoId, int $jornadaId): Collection
    {
        return DB::table('partido AS p')
            ->join('equipo AS el', 'p.equipo_local_id', '=', 'el.id')
            ->join('equipo AS ev', 'p.equipo_visitante_id', '=', 'ev.id')
            ->where('p.torneo_id', $torneoId)
            ->where('p.jornada_id', $jornadaId)
            ->select(
                'p.id',
                'el.nombre AS equipo_local',
                'el.imagen AS imagen_local',
                'p.goles_equipo_local',
                'ev.nombre AS equipo_visitante',
                'ev.imagen AS imagen_visitante',
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

    public function obtenerPartidosPorJornadaEquipoTorneo(int $torneoId, int $equipoId): Collection
    {
        return DB::table('partido AS p')
            ->join('equipo AS el', 'p.equipo_local_id', '=', 'el.id')
            ->join('equipo AS ev', 'p.equipo_visitante_id', '=', 'ev.id')
            ->join('jornada AS j', 'p.jornada_id', '=', 'j.id')
            ->leftJoin('torneo AS t', 'j.torneo_id', '=', 't.id') // Opcional: si tienes tabla torneo
            ->where('p.torneo_id', $torneoId)
            ->where(function ($query) use ($equipoId) {
                $query->where('p.equipo_local_id', $equipoId)
                    ->orWhere('p.equipo_visitante_id', $equipoId);
            })
            ->select(
                'p.jornada_id',
                'el.imagen AS imagen_local',
                'el.nombre AS equipo_local',
                'p.goles_equipo_local',
                'ev.imagen AS imagen_visitante',
                'ev.nombre AS equipo_visitante',
                'p.goles_equipo_visitante',
                't.nombre AS torneo_nombre' // Opcional
            )
            ->orderBy('p.jornada_id')
            ->get();
    }
}