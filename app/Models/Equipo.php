<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'imagen', 'categoria_id', 'entrenador_id'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function entrenador()
    {
        return $this->belongsTo(Persona::class, 'entrenador_id');
    }

    public function torneos()
    {
        return $this->belongsToMany(Torneo::class, 'torneo_equipo');
    }
}