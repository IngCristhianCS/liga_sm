<?php

namespace App\Repositories;

use App\Models\Equipo;
use Illuminate\Support\Facades\Storage;

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
}