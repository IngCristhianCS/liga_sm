<?php

namespace App\Repositories;

use App\Models\Jugador;

class JugadorRepository
{
    public function create(array $data)
    {
        return Jugador::create($data);
    }

    public function findById(int $id)
    {
        return Jugador::find($id);
    }

    public function update(Jugador $jugador, array $data)
    {
        $jugador->update($data);
        return $jugador;
    }

    public function delete(Jugador $jugador)
    {
        $jugador->delete();
    }

    public function getAll()
    {
        return Jugador::all();
    }

    public function findByUserId(int $userId)
    {
        return Jugador::where('user_id', $userId)->first();
    }

    public function findByEquipoId(int $equipoId)
    {
        return Jugador::where('equipo_id', $equipoId)->get();
    }

    // Puedes agregar más métodos según tus necesidades
}