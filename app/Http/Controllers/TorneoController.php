<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JornadaService;

class TorneoController extends Controller
{
    protected $jornadaService;

    public function __construct(JornadaService $jornadaService)
    {
        $this->jornadaService = $jornadaService;
    }

    public function jornadas($torneoId)
    {
        $jornadas = $this->jornadaService->obtenerJornadasPorTorneo($torneoId);

        $resultadosPorJornada = [];

        foreach ($jornadas as $jornadaId) {
            $resultados = $this->jornadaService->obtenerResultadosPorJornada($torneoId, $jornadaId);

            $resultadosPorJornada[] = [
                'jornada_id' => $jornadaId,
                'resultados' => $resultados,
            ];
        }

        return response()->json($resultadosPorJornada);
    }
}