<?php

namespace App\Services;

use App\Repositories\EgresoRepository;

class EgresoService
{
    protected $egresoRepository;

    public function __construct(EgresoRepository $egresoRepository)
    {
        $this->egresoRepository = $egresoRepository;
    }

    public function getAll()
    {
        return $this->egresoRepository->getAll();
    }

    public function findById($id)
    {
        return $this->egresoRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->egresoRepository->create($data);
    }

    public function update($id, array $data)
    {
        $egreso = $this->egresoRepository->findById($id);
        if (!$egreso) {
            return null;
        }
        return $this->egresoRepository->update($egreso, $data);
    }

    public function delete($id)
    {
        $egreso = $this->egresoRepository->findById($id);
        if (!$egreso) {
            return false;
        }
        $this->egresoRepository->delete($egreso);
        return true;
    }
}