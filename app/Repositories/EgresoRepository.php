<?php

namespace App\Repositories;

use App\Models\Egreso;

class EgresoRepository
{
    public function getAll()
    {
        return Egreso::with(['partido', 'torneo'])->get();
    }

    public function findById($id)
    {
        return Egreso::with(['partido', 'torneo'])->find($id);
    }

    public function create(array $data)
    {
        return Egreso::create($data);
    }

    public function update(Egreso $egreso, array $data)
    {
        $egreso->update($data);
        return $egreso;
    }

    public function delete(Egreso $egreso)
    {
        $egreso->delete();
    }
}