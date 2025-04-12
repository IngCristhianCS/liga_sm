<?php

namespace App\Services;

use App\Repositories\UbicacionRepository;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UbicacionService
{
    protected $ubicacionRepository;

    public function __construct(UbicacionRepository $ubicacionRepository)
    {
        $this->ubicacionRepository = $ubicacionRepository;
    }

    public function getAll()
    {
        return $this->ubicacionRepository->getAll();
    }

    public function getById($id)
    {
        return $this->ubicacionRepository->getById($id);
    }

    public function create(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:20',
            'capacidad' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->ubicacionRepository->create($data);
    }

    public function update($id, array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'sometimes|required|string|max:255',
            'direccion' => 'sometimes|required|string|max:255',
            'ciudad' => 'sometimes|required|string|max:100',
            'codigo_postal' => 'nullable|string|max:20',
            'capacidad' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->ubicacionRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->ubicacionRepository->delete($id);
    }
}