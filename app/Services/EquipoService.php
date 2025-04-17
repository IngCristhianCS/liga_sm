<?php

namespace App\Services;

use App\Repositories\EquipoRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function getEquiposByTorneo($torneoId)
    {
        return $this->equipoRepository->getEquiposByTorneo($torneoId);
    }

    public function getEquipoById(int $id)
    {
        return $this->equipoRepository->findById($id);
    }

    public function createEquipo(array $data)
    {
        $validator = $this->validateEquipo($data);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Process image if present
        if (isset($data['imagen'])) {
            $result = $this->processImage($data['imagen']);
            if ($result) {
                $data['imagen'] = $result;
            } else {
                unset($data['imagen']);
            }
        }

        return $this->equipoRepository->create($data);
    }

    public function updateEquipo(int $id, array $data)
    {
        $equipo = $this->equipoRepository->findById($id);
        $validator = $this->validateEquipo($data);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Process image if present
        if (isset($data['imagen'])) {
            // Delete old image if exists
            if ($equipo->imagen) {
                Storage::disk('public')->delete($equipo->imagen);
            }
            
            $result = $this->processImage($data['imagen']);
            if ($result) {
                $data['imagen'] = $result;
            } else {
                unset($data['imagen']);
            }
        }

        return $this->equipoRepository->update($equipo, $data);
    }

    public function deleteEquipo(int $id)
    {
        $equipo = $this->equipoRepository->findById($id);
        
        // Delete image if exists
        if ($equipo->imagen) {
            Storage::disk('public')->delete($equipo->imagen);
        }
        
        $this->equipoRepository->delete($equipo);
    }

    /**
     * Validate equipo data
     * 
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateEquipo(array $data)
    {
        return Validator::make($data, [
            'nombre' => 'required|string|max:100',
            'imagen' => 'nullable',
            'categoria_id' => 'required|exists:categoria,id',
            'entrenador_id' => 'nullable|exists:users,id',
            'torneo_id' => 'nullable|exists:torneo,id',
        ]);
    }

    /**
     * Process and store image
     *
     * @param mixed $imagen
     * @return string|null Path to stored image or null on failure
     */
    protected function processImage($imagen)
    {
        // Handle base64 image
        if (is_string($imagen) && strpos($imagen, 'data:image/') === 0) {
            $imagenData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagen));
            
            if ($imagenData === false) {
                Log::error('Failed to decode base64 image data');
                return null;
            }
            
            $nombreArchivo = Str::random(40) . '.png';
            $path = 'equipos/' . $nombreArchivo;
            
            $success = Storage::disk('public')->put($path, $imagenData);
            Log::info('Image saved: ' . ($success ? 'Yes' : 'No') . ' to path: ' . $path);
            
            return $success ? $path : null;
        }
        // Handle file upload
        elseif (is_file($imagen)) {
            $file = $imagen;
            $nombreArchivo = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('equipos', $nombreArchivo, 'public');
            Log::info('File uploaded to: ' . $path);
            
            return $path;
        }
        
        return null;
    }

    public function getJugadoresByEquipo($equipoId)
    {
        return $this->equipoRepository->getJugadoresByEquipo($equipoId);
    }
}