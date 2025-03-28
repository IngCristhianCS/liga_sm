<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipo;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Todos los usuarios autenticados pueden ver la lista de equipos
    }

    public function view(User $user, Equipo $equipo): bool
    {
        return true; // Todos los usuarios autenticados pueden ver un equipo específico
    }

    public function create(User $user): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden crear equipos
    }

    public function update(User $user, Equipo $equipo): bool
    {
        return $user->role_id === Role::ADMIN || $user->id === $equipo->entrenador_id; // Administradores y entrenadores del equipo pueden actualizarlo
    }

    public function delete(User $user, Equipo $equipo): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden eliminar equipos
    }
}