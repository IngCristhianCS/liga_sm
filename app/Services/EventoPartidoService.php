<?php

namespace App\Services;

use App\Repositories\EventoPartidoRepository;
use App\Repositories\PartidoRepository;
use Illuminate\Support\Facades\Validator;

class EventoPartidoService
{
    protected $eventoPartidoRepository;
    protected $partidoRepository;

    public function __construct(EventoPartidoRepository $eventoPartidoRepository, PartidoRepository $partidoRepository)
    {
        $this->eventoPartidoRepository = $eventoPartidoRepository;
        $this->partidoRepository = $partidoRepository;
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

        $evento = $this->eventoPartidoRepository->create($data);
        
        // Actualizar marcador si es un gol
        if ($data['tipo_evento'] === 'gol') {
            $this->actualizarMarcadorPartido($data['partido_id']);
        }
        
        return $evento;
    }

    public function updateEvento($id, array $data)
    {
        $evento = $this->eventoPartidoRepository->findById($id);
        $validator = $this->validateEvento($data);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $partidoId = $evento->partido_id;
        $tipoEventoAnterior = $evento->tipo_evento;
        
        $eventoActualizado = $this->eventoPartidoRepository->update($evento, $data);
        
        // Actualizar marcador si el evento anterior o el nuevo es un gol
        if ($tipoEventoAnterior === 'gol' || $data['tipo_evento'] === 'gol') {
            $this->actualizarMarcadorPartido($partidoId);
        }
        
        return $eventoActualizado;
    }

    public function deleteEvento($id)
    {
        $evento = $this->eventoPartidoRepository->findById($id);
        $partidoId = $evento->partido_id;
        $tipoEvento = $evento->tipo_evento;
        
        $this->eventoPartidoRepository->delete($evento);
        
        // Actualizar marcador si era un gol
        if ($tipoEvento === 'gol') {
            $this->actualizarMarcadorPartido($partidoId);
        }
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
    
    /**
     * Actualiza el marcador del partido basado en los eventos de gol
     * 
     * @param int $partidoId
     * @return void
     */
    protected function actualizarMarcadorPartido($partidoId)
    {
        // Obtener el partido
        $partido = $this->partidoRepository->findById($partidoId);
        
        // Obtener todos los eventos de gol para este partido
        $eventos = $this->eventoPartidoRepository->getByPartidoId($partidoId);
        
        // Contar goles por equipo
        $golesLocal = 0;
        $golesVisitante = 0;
        
        foreach ($eventos as $evento) {
            if ($evento->tipo_evento === 'gol') {
                // Determinar si el jugador pertenece al equipo local o visitante
                $jugador = $evento->jugador;
                
                if ($jugador->equipo_id === $partido->equipo_local_id) {
                    $golesLocal++;
                } elseif ($jugador->equipo_id === $partido->equipo_visitante_id) {
                    $golesVisitante++;
                }
            }
        }
        
        // Actualizar el partido con los nuevos marcadores
        $this->partidoRepository->update($partido->id, [
            'goles_equipo_local' => $golesLocal,
            'goles_equipo_visitante' => $golesVisitante
        ]);
    }
}