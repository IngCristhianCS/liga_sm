<?php

namespace App\Policies;

use App\Models\EventoPartido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventoPartidoPolicy
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
        return true; // Anyone can view the list of eventos
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventoPartido  $eventoPartido
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EventoPartido $eventoPartido)
    {
        return true; // Anyone can view an evento
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isAdmin(); // Only admins can create
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventoPartido  $eventoPartido
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EventoPartido $eventoPartido)
    {
        return $user->isAdmin(); // Only admins can update
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventoPartido  $eventoPartido
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EventoPartido $eventoPartido)
    {
        return $user->isAdmin(); // Only admins can delete
    }
}