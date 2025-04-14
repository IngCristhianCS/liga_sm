<?php

namespace App\Repositories;

use App\Models\Equipo;

class EquipoRepository
{
    public function getAll()
    {
        return Equipo::with(['categoria', 'entrenador'])->get();
    }

    public function create(array $data)
    {
        return Equipo::create($data);
    }

    public function findById(int $id)
    {
        return Equipo::with(['categoria', 'entrenador'])->findOrFail($id);
    }

    public function findBy(string $field, $value)
    {
        return Equipo::where($field, $value)->get();
    }

    public function update(Equipo $equipo, array $data)
    {
        $equipo->update($data);
        return $equipo;
    }

    public function delete(Equipo $equipo)
    {
        $equipo->delete();
    }

    public function getEquiposByTorneo($torneoId)
    {
        return Equipo::with(['categoria', 'entrenador'])
            ->whereHas('torneos', function($query) use ($torneoId) {
                $query->where('torneo_id', $torneoId);
            })
            ->get();
    }
}