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

    public function getAll($catalog = false)
    {
        return $this->torneoRepository->getAll($catalog);
    }

    public function findById($id, $catalog = false)
    {
        return $this->torneoRepository->findById($id, $catalog);
    }

    public function create(array $data)
    {
        $validator = $this->validateTorneo($data);

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

        $validator = $this->validateTorneo($data, $id);

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

    /**
     * Validate torneo data
     * 
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateTorneo(array $data, $id = null)
    {
        return Validator::make($data, [
            'nombre' => 'required|string',
            'categoria_id' => 'required|exists:categoria,id',
            'temporada_id' => 'required|exists:temporada,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'estado' => 'required|string',
            'campeon_id' => 'nullable|exists:equipo,id',
        ]);
    }
}