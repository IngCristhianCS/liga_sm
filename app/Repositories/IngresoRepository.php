<?php

namespace App\Repositories;

use App\Models\Ingreso;
use Illuminate\Support\Facades\Auth;

class IngresoRepository
{
    public function getAll()
    {
        $user = Auth::user();

        if ($user && $user->isEntrenador()) {
            $equipo = $user->equipo;

            if ($equipo) {
                return Ingreso::with(['equipo', 'torneo', 'patrocinador'])
                    ->where('equipo_id', $equipo->id)
                    ->get();
            } else {
                return collect([]);
            }
        }

        return Ingreso::with(['equipo', 'torneo', 'patrocinador'])->get();
    }

    public function findById($id)
    {
        $user = Auth::user();

        if ($user && $user->isEntrenador()) {
            $equipo = $user->equipo;

            if ($equipo) {
                return Ingreso::with(['equipo', 'torneo', 'patrocinador'])
                    ->where('equipo_id', $equipo->id)
                    ->find($id);
            } else {
                return null;
            }
        }

        return Ingreso::with(['equipo', 'torneo', 'patrocinador'])->find($id);
    }

    public function create(array $data)
    {
        return Ingreso::create($data);
    }

    public function update(Ingreso $ingreso, array $data)
    {
        $ingreso->update($data);
        return $ingreso;
    }

    public function delete(Ingreso $ingreso)
    {
        $ingreso->delete();
    }
}