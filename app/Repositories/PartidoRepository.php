<?php

namespace App\Repositories;

use App\Models\Partido;

class PartidoRepository
{
    protected $model;

    public function __construct(Partido $partido)
    {
        $this->model = $partido;
    }

    public function getAll($torneoId = null)
    {
        $query = $this->model->with([
            'ubicacion',
            'equipoLocal',
            'equipoVisitante',
            'torneo'
        ]);

        if ($torneoId) {
            $query->where('torneo_id', $torneoId);
        }

        return $query->get();
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
        $partido = $this->findById($id);
        $partido->update($data);
        return $partido->fresh();
    }

    public function delete($id)
    {
        $partido = $this->findById($id);
        return $partido->delete();
    }
}