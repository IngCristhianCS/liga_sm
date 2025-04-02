<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Categoria;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Categoria $categoria)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Categoria $categoria)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Categoria $categoria)
    {
        return $user->isAdmin();
    }
}