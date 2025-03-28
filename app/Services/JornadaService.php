<?php

namespace App\Services;

use App\Repositories\JornadaRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class JornadaService
{
    protected $jornadaRepository;

    public function __construct(JornadaRepository $jornadaRepository)
    {
        $this->jornadaRepository = $jornadaRepository;
    }

    public function obtenerResultadosPorJornada(int $torneoId, int $jornadaId): Collection
    {
        return $this->jornadaRepository->obtenerResultadosPorJornada($torneoId, $jornadaId);
    }

    public function obtenerJornadasPorTorneo(int $torneoId): Collection
    {
        return $this->jornadaRepository->obtenerJornadasPorTorneo($torneoId);
    }

    public function obtenerPartidosPorJornadaEquipoTorneo()
    {
        $user = Auth::user();

        if (!$user || !$user->isEntrenador() || !$user->equipo) {
            return ['error' => 'No autorizado.'];
        }

        $equipoId = $user->equipo->id;

        // Obtener los torneos activos en los que participa el equipo del entrenador
        $torneosEquipo = \DB::table('torneo_equipo')
            ->join('torneo', 'torneo_equipo.torneo_id', '=', 'torneo.id')
            ->where('torneo_equipo.equipo_id', $equipoId)
            ->where('torneo.estado', 'activo')
            ->pluck('torneo_equipo.torneo_id');

        if ($torneosEquipo->isEmpty()) {
            return ['error' => 'El equipo del entrenador no está asociado a ningún torneo activo.'];
        }

        // Asumimos que el entrenador solo tiene un torneo asignado.
        // Si tiene más de uno, habría que ajustar la lógica.
        $torneoId = $torneosEquipo->first();

        // Obtener los partidos del equipo en el torneo
        $partidos = $this->jornadaRepository->obtenerPartidosPorJornadaEquipoTorneo($torneoId, $equipoId);

        return $partidos;
    }
}