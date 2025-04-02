<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Torneo;
use Illuminate\Auth\Access\HandlesAuthorization;

class TorneoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true; // Cualquiera puede ver la lista de torneos
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Torneo  $torneo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Torneo $torneo)
    {
        return true; // Cualquiera puede ver un torneo específico
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isAdmin(); // Solo los administradores pueden crear torneos
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Torneo  $torneo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Torneo $torneo)
    {
        return $user->isAdmin(); // Solo los administradores pueden actualizar torneos
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Torneo  $torneo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Torneo $torneo)
    {
        return $user->isAdmin(); // Solo los administradores pueden eliminar torneos
    }
}