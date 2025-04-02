<?php

namespace App\Services;

use App\Repositories\ClasificacionRepository;
use Illuminate\Support\Collection;

class ClasificacionService
{
    protected ClasificacionRepository $repository;

    public function __construct(ClasificacionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obtenerClasificacion($torneoId): Collection
    {
        return $this->repository->obtenerClasificacion($torneoId);
    }
}