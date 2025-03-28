<?php

namespace App\Services;

use App\Models\Jugador;
use App\Repositories\JugadorRepository;
use Illuminate\Support\Facades\Gate;

class JugadorService
{
    protected $jugadorRepository;

    public function __construct(JugadorRepository $jugadorRepository)
    {
        $this->jugadorRepository = $jugadorRepository;
    }

    public function createJugador(array $data)
    {
        if (Gate::allows('create', Jugador::class)) {
            return $this->jugadorRepository->create($data);
        }

        abort(403, 'No tienes permiso para crear jugadores.');
    }

    public function getJugadorById(int $id)
    {
        $jugador = $this->jugadorRepository->findById($id);

        if (!$jugador) {
            abort(404, 'Jugador no encontrado.');
        }

        return $jugador;
    }

    public function updateJugador(Jugador $jugador, array $data)
    {
        if (Gate::allows('update', $jugador)) {
            return $this->jugadorRepository->update($jugador, $data);
        }

        abort(403, 'No tienes permiso para actualizar este jugador.');
    }

    public function deleteJugador(Jugador $jugador)
    {
        if (Gate::allows('delete', $jugador)) {
            $this->jugadorRepository->delete($jugador);
            return;
        }

        abort(403, 'No tienes permiso para eliminar este jugador.');
    }

    public function getAllJugadores()
    {
        return $this->jugadorRepository->getAll();
    }

    public function getJugadorByUserId(int $userId)
    {
        return $this->jugadorRepository->findByUserId($userId);
    }

    public function getJugadoresByEquipoId(int $equipoId)
    {
        return $this->jugadorRepository->findByEquipoId($equipoId);
    }
}