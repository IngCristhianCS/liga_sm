<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'fecha_nacimiento', 'genero'];

    public function arbitro()
    {
        return $this->hasOne(Arbitro::class);
    }

    public function jugador()
    {
        return $this->hasOne(Jugador::class);
    }
}