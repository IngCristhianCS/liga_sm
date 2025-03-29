<?php

namespace App\Services;

use App\Repositories\IngresoRepository;

class IngresoService
{
    protected $ingresoRepository;

    public function __construct(IngresoRepository $ingresoRepository)
    {
        $this->ingresoRepository = $ingresoRepository;
    }

    public function getAll()
    {
        return $this->ingresoRepository->getAll();
    }

    public function findById($id)
    {
        return $this->ingresoRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->ingresoRepository->create($data);
    }

    public function update($id, array $data)
    {
        $ingreso = $this->ingresoRepository->findById($id);
        if (!$ingreso) {
            return null;
        }
        return $this->ingresoRepository->update($ingreso, $data);
    }

    public function delete($id)
    {
        $ingreso = $this->ingresoRepository->findById($id);
        if (!$ingreso) {
            return null;
        }
        $this->ingresoRepository->delete($ingreso);
    }
}