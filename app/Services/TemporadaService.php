<?php

namespace App\Services;

use App\Repositories\TemporadaRepository;
use Illuminate\Support\Facades\Validator;

class TemporadaService
{
    protected $temporadaRepository;

    public function __construct(TemporadaRepository $temporadaRepository)
    {
        $this->temporadaRepository = $temporadaRepository;
    }

    public function getAll()
    {
        return $this->temporadaRepository->getAll();
    }

    public function findById($id)
    {
        return $this->temporadaRepository->findById($id);
    }

    public function create(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'estado' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->temporadaRepository->create($data);
    }

    public function update($id, array $data)
    {
        $temporada = $this->temporadaRepository->findById($id);
        if (!$temporada) {
            return null;
        }

        $validator = Validator::make($data, [
            'nombre' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'estado' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->temporadaRepository->update($temporada, $data);
    }

    public function delete($id)
    {
        $temporada = $this->temporadaRepository->findById($id);
        if (!$temporada) {
            return null;
        }
        $this->temporadaRepository->delete($temporada);
    }
}