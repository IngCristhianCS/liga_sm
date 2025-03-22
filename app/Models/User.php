<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'fecha_nacimiento',
        'genero'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
        ];
    }

    // Relación con el modelo Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Métodos helper para verificación de roles
    public function isAdmin(): bool
    {
        return $this->role_id === Role::ADMIN;
    }

    public function isEntrenador(): bool
    {
        return $this->role_id === Role::ENTRENADOR;
    }

    public function isJugador(): bool
    {
        return $this->role_id === Role::JUGADOR;
    }

    public function isArbitro(): bool
    {
        return $this->role_id === Role::ARBITRO;
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->role->slug === $roleSlug;
    }
}