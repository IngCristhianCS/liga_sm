<?php

namespace App\Repositories;

use App\Models\EventoPartido;

class EventoPartidoRepository
{
    protected $model;

    public function __construct(EventoPartido $eventoPartido)
    {
        $this->model = $eventoPartido;
    }

    public function getAll()
    {
        return $this->model->with(['partido', 'jugador.user'])->get();
    }

    public function getByPartidoId($partidoId)
    {
        return $this->model->with(['jugador.user'])
            ->where('partido_id', $partidoId)
            ->orderBy('minuto', 'asc')
            ->get();
    }

    public function findById($id)
    {
        return $this->model->with(['partido', 'jugador.user'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data)->load(['partido', 'jugador.user']);
    }

    public function update($eventoPartido, array $data)
    {
        $eventoPartido->update($data);
        return $eventoPartido->fresh()->load(['partido', 'jugador.user']);
    }

    public function delete($eventoPartido)
    {
        return $eventoPartido->delete();
    }
}