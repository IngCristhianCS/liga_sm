<?php

namespace App\Services;

use App\Repositories\JornadaRepository;
use Illuminate\Support\Collection;

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
}