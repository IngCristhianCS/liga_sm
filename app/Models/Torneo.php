<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    use HasFactory;
    
    protected $table = 'torneo'; 

    protected $fillable = [
        'nombre',
        'categoria_id',
        'temporada_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'campeon_id'
    ];

    // Relación con Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relación con Temporada
    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    // Relación con Equipos (tabla pivot torneo_equipo)
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'torneo_equipo');
    }

    // Relación con el equipo campeón
    public function campeon()
    {
        return $this->belongsTo(Equipo::class, 'campeon_id');
    }

    // Relación con Partidos
    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }
}