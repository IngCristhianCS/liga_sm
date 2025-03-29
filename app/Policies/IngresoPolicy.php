<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ingreso;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class IngresoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Todos los usuarios autenticados pueden ver la lista de ingresos
    }

    public function view(User $user, Ingreso $ingreso): bool
    {
        return true; // Todos los usuarios autenticados pueden ver un ingreso específico
    }

    public function create(User $user): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden crear ingresos
    }

    public function update(User $user, Ingreso $ingreso): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden actualizar ingresos
    }

    public function delete(User $user, Ingreso $ingreso): bool
    {
        return $user->role_id === Role::ADMIN; // Solo los administradores pueden eliminar ingresos
    }
}