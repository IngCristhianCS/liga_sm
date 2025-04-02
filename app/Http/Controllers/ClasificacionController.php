<?php

namespace App\Http\Controllers;

use App\Services\ClasificacionService;
use Illuminate\Http\JsonResponse;

class ClasificacionController extends Controller
{
    protected ClasificacionService $service;

    public function __construct(ClasificacionService $service)
    {
        $this->service = $service;
    }

    public function index($torneoId): JsonResponse
    {
        $tablaPosiciones = $this->service->obtenerClasificacion($torneoId);
        return response()->json($tablaPosiciones);
    }
}