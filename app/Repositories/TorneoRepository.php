<?php

namespace App\Repositories;

use App\Models\Torneo;

class TorneoRepository
{
    public function getAll()
    {
        return Torneo::with(['categoria', 'temporada', 'equipos', 'campeon'])->get();
    }

    public function findById($id)
    {
        return Torneo::with(['categoria', 'temporada', 'equipos', 'campeon'])->find($id);
    }

    public function create(array $data)
    {
        return Torneo::create($data);
    }

    public function update(Torneo $torneo, array $data)
    {
        $torneo->update($data);
        return $torneo;
    }

    public function delete(Torneo $torneo)
    {
        $torneo->delete();
    }
}