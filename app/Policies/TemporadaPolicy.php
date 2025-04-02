<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Temporada;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemporadaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Temporada $temporada)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Temporada $temporada)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Temporada $temporada)
    {
        return $user->isAdmin();
    }
}
