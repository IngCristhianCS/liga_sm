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

    public function getJugadoresByEquipo($equipoId)
    {
        return \Illuminate\Support\Facades\DB::table('jugador as j')
            ->join('users as u', 'j.user_id', '=', 'u.id')
            ->join('equipo as e', 'e.id', '=', 'j.equipo_id')
            ->where('e.id', $equipoId)
            ->select('j.id', 'u.name as nombre', 'u.id as user_id')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->nombre,
                    'user_id' => $item->user_id
                ];
            });
    }
}