<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class UserPolicy
{
    public function viewAny(User $user): bool
    {

        \Log::info('Verificando acceso para usuario:', [
            'user_id' => $user->id,
            'role_id' => $user->role_id,
            'is_admin' => $user->isAdmin()
        ]);
        
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->role_id === Role::ADMIN || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->role_id === Role::ADMIN;
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