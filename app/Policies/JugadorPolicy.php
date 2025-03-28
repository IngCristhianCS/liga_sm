<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jugador;
use Illuminate\Auth\Access\HandlesAuthorization;

class JugadorPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isEntrenador(); // Administradores y entrenadores pueden crear jugadores
    }

    public function update(User $user)
    {
        return $user->isAdmin() || $user->isEntrenador(); // Administradores y entrenadores pueden actualizar jugadores
    }

    public function delete(User $user)
    {
        return $user->isAdmin(); // Solo administradores pueden eliminar jugadores
    }
}