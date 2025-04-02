<?php

namespace App\Repositories;

use App\Models\Temporada;

class TemporadaRepository
{
    public function getAll()
    {
        return Temporada::all();
    }

    public function findById($id)
    {
        return Temporada::find($id);
    }

    public function create(array $data)
    {
        return Temporada::create($data);
    }

    public function update(Temporada $temporada, array $data)
    {
        $temporada->update($data);
        return $temporada;
    }

    public function delete(Temporada $temporada)
    {
        $temporada->delete();
    }
}