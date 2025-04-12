<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jornada;
use Illuminate\Auth\Access\HandlesAuthorization;

class JornadaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Jornada $jornada)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Jornada $jornada)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Jornada $jornada)
    {
        return $user->isAdmin();
    }
}