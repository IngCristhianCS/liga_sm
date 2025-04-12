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

    public function createEquipo(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:100',
            'imagen' => 'nullable',
            'categoria_id' => 'required|exists:categoria,id',
            'entrenador_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if (isset($data['imagen'])) {
            // Check if it's a base64 string
            if (is_string($data['imagen']) && strpos($data['imagen'], 'data:image/') === 0) {
                // It's a base64 image
                $imagenBase64 = $data['imagen'];
                $imagenData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagenBase64));
                
                if ($imagenData !== false) {
                    $nombreArchivo = Str::random(40) . '.png';
                    $path = 'equipos/' . $nombreArchivo;
                    
                    // Save the image and log the result
                    $success = Storage::disk('public')->put($path, $imagenData);
                    Log::info('Image saved: ' . ($success ? 'Yes' : 'No') . ' to path: ' . $path);
                    
                    if ($success) {
                        $data['imagen'] = $path;
                    } else {
                        Log::error('Failed to save image to storage');
                        unset($data['imagen']); // Remove imagen if we couldn't save it
                    }
                } else {
                    Log::error('Failed to decode base64 image data');
                    unset($data['imagen']);
                }
            } else if (is_file($data['imagen'])) {
                // It's a file upload
                $file = $data['imagen'];
                $nombreArchivo = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('equipos', $nombreArchivo, 'public');
                Log::info('File uploaded to: ' . $path);
                $data['imagen'] = $path;
            }
        }

        return $this->equipoRepository->create($data);
    }

    public function updateEquipo(int $id, array $data)
    {
        $equipo = $this->equipoRepository->findById($id);

        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:100',
            'imagen' => 'nullable',
            'categoria_id' => 'required|exists:categoria,id',
            'entrenador_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if (isset($data['imagen'])) {
            // Check if it's a base64 string
            if (is_string($data['imagen']) && strpos($data['imagen'], 'data:image/') === 0) {
                // It's a base64 image
                $imagenBase64 = $data['imagen'];
                $imagenData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagenBase64));
                
                if ($imagenData !== false) {
                    // Delete old image if exists
                    if ($equipo->imagen) {
                        Storage::disk('public')->delete($equipo->imagen);
                    }
                    
                    $nombreArchivo = Str::random(40) . '.png';
                    $path = 'equipos/' . $nombreArchivo;
                    
                    // Save the image and log the result
                    $success = Storage::disk('public')->put($path, $imagenData);
                    Log::info('Image updated: ' . ($success ? 'Yes' : 'No') . ' to path: ' . $path);
                    
                    if ($success) {
                        $data['imagen'] = $path;
                    } else {
                        Log::error('Failed to save updated image to storage');
                        unset($data['imagen']); // Keep old image if we couldn't save the new one
                    }
                } else {
                    Log::error('Failed to decode base64 image data for update');
                    unset($data['imagen']);
                }
            } else if (is_file($data['imagen'])) {
                // It's a file upload
                // Delete old image if exists
                if ($equipo->imagen) {
                    Storage::disk('public')->delete($equipo->imagen);
                }
                
                $file = $data['imagen'];
                $nombreArchivo = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('equipos', $nombreArchivo, 'public');
                Log::info('File updated to: ' . $path);
                $data['imagen'] = $path;
            }
        }

        return $this->equipoRepository->update($equipo, $data);
    }

    public function getEquipoById(int $id)
    {
        return $this->equipoRepository->findById($id);
    }

    public function deleteEquipo(int $id)
    {
        $equipo = $this->equipoRepository->findById($id);
        $this->equipoRepository->delete($equipo);
    }
}