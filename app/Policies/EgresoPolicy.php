<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use App\Models\Egreso;
use Illuminate\Auth\Access\HandlesAuthorization;

class EgresoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Todos los usuarios autenticados pueden ver la lista de Egresos
    }

    public function view(User $user, Egreso $Egreso): bool
    {
        return true; // Todos los usuarios autenticados pueden ver un Egreso específico
    }

    public function create(User $user): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden crear Egresos
    }

    public function update(User $user, Egreso $Egreso): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden actualizar Egresos
    }

    public function delete(User $user, Egreso $Egreso): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden eliminar Egresos
    }
}
