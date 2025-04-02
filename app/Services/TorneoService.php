<?php

namespace App\Services;

use App\Repositories\TorneoRepository;
use Illuminate\Support\Facades\Validator;

class TorneoService
{
    protected $torneoRepository;

    public function __construct(TorneoRepository $torneoRepository)
    {
        $this->torneoRepository = $torneoRepository;
    }

    public function getAll()
    {
        return $this->torneoRepository->getAll();
    }

    public function findById($id)
    {
        return $this->torneoRepository->findById($id);
    }

    public function create(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|string',
            'categoria_id' => 'required|exists:categoria,id',
            'temporada_id' => 'required|exists:temporada,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'estado' => 'required|string',
            'campeon_id' => 'nullable|exists:equipo,id',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->torneoRepository->create($data);
    }

    public function update($id, array $data)
    {
        $torneo = $this->torneoRepository->findById($id);
        if (!$torneo) {
            return null;
        }

        $validator = Validator::make($data, [
            'nombre' => 'required|string',
            'categoria_id' => 'required|exists:categorias,id',
            'temporada_id' => 'required|exists:temporadas,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'estado' => 'required|string',
            'campeon_id' => 'nullable|exists:equipos,id',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->torneoRepository->update($torneo, $data);
    }

    public function delete($id)
    {
        $torneo = $this->torneoRepository->findById($id);
        if (!$torneo) {
            return null;
        }
        $this->torneoRepository->delete($torneo);
    }
}