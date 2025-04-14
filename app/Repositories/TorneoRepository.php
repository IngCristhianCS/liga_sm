<?php

namespace App\Repositories;

use App\Models\Torneo;

class TorneoRepository
{
    public function getAll($catalog = false)
    {
        if ($catalog) {
            return Torneo::select('id', 'nombre', 'estado', 'fecha_inicio', 'fecha_fin')
                ->orderBy('fecha_inicio', 'asc')
                ->get();
        }
        
        return Torneo::with(['categoria', 'temporada', 'campeon'])
            ->orderBy('fecha_inicio', 'asc')
            ->get();
    }

    public function findById($id, $catalog = false)
    {
        if ($catalog) {
            return Torneo::select('id', 'nombre', 'estado', 'fecha_inicio', 'fecha_fin')
                ->where('id', $id)
                ->first();
        }
        
        return Torneo::with(['categoria', 'temporada', 'campeon'])
            ->where('id', $id)
            ->first();
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