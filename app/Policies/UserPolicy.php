<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class UserPolicy
{
    public function viewAny(User $user): bool
    {        
        return $user->isAdmin() || $user->isEntrenador();
    }

    public function view(User $user, User $model): bool
    {
        return $user->role_id === Role::ADMIN || $user->id === $model->id;
    }

    public function create(User $user, User $model): bool
    {
        // Verificar si el creador es un entrenador y el usuario a crear es un jugador
        if ($user->role_id === Role::ENTRENADOR  && $model->role_id === Role::JUGADOR) {
            return true; // El entrenador puede crear un jugador
        }

        // Verificar si el creador es un administrador (si es necesario)
        if ($user->role_id === Role::ADMIN) {
            return true; // El administrador puede crear cualquier usuario
        }

        return false;
    }

    public function update(User $user, User $model): bool
    {
        return $user->role_id === Role::ADMIN || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role_id === Role::ADMIN;
    }
}