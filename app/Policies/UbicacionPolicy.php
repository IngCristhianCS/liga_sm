<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ubicacion;
use Illuminate\Auth\Access\HandlesAuthorization;

class UbicacionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // All authenticated users can view the list
    }

    public function view(User $user, Ubicacion $ubicacion)
    {
        return true; // All authenticated users can view details
    }

    public function create(User $user)
    {
        return $user->isAdmin(); // Only admins can create
    }

    public function update(User $user, Ubicacion $ubicacion)
    {
        return $user->isAdmin(); // Only admins can update
    }

    public function delete(User $user, Ubicacion $ubicacion)
    {
        return $user->isAdmin(); // Only admins can delete
    }
}