<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Partido;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartidoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Partido $partido)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Partido $partido)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Partido $partido)
    {
        return $user->isAdmin();
    }
}