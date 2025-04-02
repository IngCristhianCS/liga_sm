<?php

namespace App\Services;

use App\Repositories\CategoriaRepository;
use Illuminate\Support\Facades\Validator;

class CategoriaService
{
    protected $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    public function getAll()
    {
        return $this->categoriaRepository->getAll();
    }

    public function findById($id)
    {
        return $this->categoriaRepository->findById($id);
    }

    public function create(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|string',
            'tipo' => 'required|string',
            'edad_maxima' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->categoriaRepository->create($data);
    }

    public function update($id, array $data)
    {
        $categoria = $this->categoriaRepository->findById($id);
        if (!$categoria) {
            return null;
        }

        $validator = Validator::make($data, [
            'nombre' => 'required|string',
            'tipo' => 'required|string',
            'edad_maxima' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->categoriaRepository->update($categoria, $data);
    }

    public function delete($id)
    {
        $categoria = $this->categoriaRepository->findById($id);
        if (!$categoria) {
            return null;
        }
        $this->categoriaRepository->delete($categoria);
    }
}