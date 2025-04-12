<?php

namespace App\Services;

use App\Repositories\PartidoRepository;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PartidoService
{
    protected $partidoRepository;

    public function __construct(PartidoRepository $partidoRepository)
    {
        $this->partidoRepository = $partidoRepository;
    }

    public function getAll($torneoId = null)
    {
        return $this->partidoRepository->getAll($torneoId);
    }

    public function findById($id)
    {
        return $this->partidoRepository->findById($id);
    }

    public function create(array $data)
    {
        // Combine fecha and hora_inicio into a single datetime
        $data = $this->processDateTimeFields($data);
        
        $validator = $this->validatePartido($data);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $partido = $this->partidoRepository->create($data);

        return [
            'success' => true,
            'data' => $partido
        ];
    }

    public function update($id, array $data)
    {
        // Combine fecha and hora_inicio into a single datetime
        $data = $this->processDateTimeFields($data);
        
        $validator = $this->validatePartido($data, $id);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $partido = $this->partidoRepository->update($id, $data);

        return [
            'success' => true,
            'data' => $partido
        ];
    }

    public function delete($id)
    {
        return $this->partidoRepository->delete($id);
    }
    
    /**
     * Process date and time fields to combine them into a single datetime
     *
     * @param array $data
     * @return array
     */
    protected function processDateTimeFields(array $data)
    {
        if (isset($data['fecha']) && isset($data['hora_inicio'])) {
            try {
                // Combine date and time into a single datetime string
                $datetime = Carbon::parse($data['fecha'] . ' ' . $data['hora_inicio']);
                $data['fecha'] = $datetime->format('Y-m-d H:i:s');
                
                // We keep hora_inicio in the data for validation
            } catch (\Exception $e) {
                // If parsing fails, leave the original values
            }
        }
        
        return $data;
    }

    protected function validatePartido(array $data, $id = null)
    {
        $rules = [
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'torneo_id' => 'required|exists:torneo,id',
            'jornada_id' => 'required|exists:jornada,id',
            'equipo_local_id' => 'required|exists:equipo,id',
            'equipo_visitante_id' => 'required|exists:equipo,id|different:equipo_local_id',
            'goles_equipo_local' => 'nullable|integer|min:0',
            'goles_equipo_visitante' => 'nullable|integer|min:0',
            'ubicacion_id' => 'required|exists:ubicacion,id',
            'arbitro_principal_id' => 'nullable|exists:arbitro,id',
            'arbitro_asistente1_id' => 'nullable|exists:arbitro,id',
            'arbitro_asistente2_id' => 'nullable|exists:arbitro,id',
        ];

        return Validator::make($data, $rules);
    }
}