<?php

namespace App\Repositories;

use App\Models\Ubicacion;

class UbicacionRepository
{
    protected $model;

    public function __construct(Ubicacion $ubicacion)
    {
        $this->model = $ubicacion;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $ubicacion = $this->getById($id);
        $ubicacion->update($data);
        return $ubicacion;
    }

    public function delete($id)
    {
        $ubicacion = $this->getById($id);
        return $ubicacion->delete();
    }
}