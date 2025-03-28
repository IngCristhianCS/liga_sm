<?php

namespace App\Services;

use App\Repositories\EquipoRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EquipoService
{
    protected $equipoRepository;

    public function __construct(EquipoRepository $equipoRepository)
    {
        $this->equipoRepository = $equipoRepository;
    }

    public function getAllEquipos()
    {
        $user = Auth::user();

        if ($user && $user->isEntrenador()) {
            return $this->equipoRepository->findBy('entrenador_id', $user->id);
        }
        return $this->equipoRepository->getAll();
    }

    public function createEquipo(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:100',
            'imagen' => 'nullable|string',
            'categoria_id' => 'required|exists:categoria,id',
            'entrenador_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if (isset($data['imagen'])) {
            $imagenBase64 = $data['imagen'];
            $imagenData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagenBase64));

            if ($imagenData !== false) {
                $nombreArchivo = Str::random(40) . '.png';
                Storage::disk('public')->put('equipos/' . $nombreArchivo, $imagenData);
                $data['imagen'] = 'equipos/' . $nombreArchivo;
            }
        }

        return $this->equipoRepository->create($data);
    }

    public function getEquipoById(int $id)
    {
        return $this->equipoRepository->findById($id);
    }

    public function updateEquipo(int $id, array $data)
    {
        $equipo = $this->equipoRepository->findById($id);

        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:100',
            'imagen' => 'nullable|string',
            'categoria_id' => 'required|exists:categoria,id',
            'entrenador_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if (isset($data['imagen'])) {
            $imagenBase64 = $data['imagen'];
            $imagenData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagenBase64));

            if ($imagenData !== false) {
                if ($equipo->imagen) {
                    Storage::disk('public')->delete($equipo->imagen);
                }
                $nombreArchivo = Str::random(40) . '.png';
                Storage::disk('public')->put('equipos/' . $nombreArchivo, $imagenData);
                $data['imagen'] = 'equipos/' . $nombreArchivo;
            }
        }

        return $this->equipoRepository->update($equipo, $data);
    }

    public function deleteEquipo(int $id)
    {
        $equipo = $this->equipoRepository->findById($id);
        $this->equipoRepository->delete($equipo);
    }
}