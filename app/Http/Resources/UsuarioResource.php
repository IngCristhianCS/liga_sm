<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'fecha_nacimiento' => $this->fecha_nacimiento ? $this->fecha_nacimiento->format('Y-m-d') : null,
            'genero' => $this->genero,
            'role' => new RoleResource($this->whenLoaded('role')),
            'is_admin' => $this->isAdmin(),
            'is_entrenador' => $this->isEntrenador(),
            'is_jugador' => $this->isJugador(),
            'is_arbitro' => $this->isArbitro(),
        ];
    }
}