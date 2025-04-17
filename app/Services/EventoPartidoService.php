<?php

namespace App\Services;

use App\Repositories\EventoPartidoRepository;
use Illuminate\Support\Facades\Validator;

class EventoPartidoService
{
    protected $eventoPartidoRepository;

    public function __construct(EventoPartidoRepository $eventoPartidoRepository)
    {
        $this->eventoPartidoRepository = $eventoPartidoRepository;
    }

    public function getAllEventos()
    {
        return $this->eventoPartidoRepository->getAll();
    }

    public function getEventosByPartido($partidoId)
    {
        return $this->eventoPartidoRepository->getByPartidoId($partidoId);
    }

    public function getEventoById($id)
    {
        return $this->eventoPartidoRepository->findById($id);
    }

    public function createEvento(array $data)
    {
        $validator = $this->validateEvento($data);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->eventoPartidoRepository->create($data);
    }

    public function updateEvento($id, array $data)
    {
        $evento = $this->eventoPartidoRepository->findById($id);
        $validator = $this->validateEvento($data);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->eventoPartidoRepository->update($evento, $data);
    }

    public function deleteEvento($id)
    {
        $evento = $this->eventoPartidoRepository->findById($id);
        $this->eventoPartidoRepository->delete($evento);
    }

    /**
     * Validate evento data
     * 
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateEvento(array $data)
    {
        return Validator::make($data, [
            'partido_id' => 'required|exists:partido,id',
            'jugador_id' => 'required|exists:users,id',
            'minuto' => 'required|integer|min:0|max:120',
            'tipo_evento' => 'required|string|in:gol,tarjeta_amarilla,tarjeta_roja,tarjeta_azul,falta,lesion,penal',
        ]);
    }
}