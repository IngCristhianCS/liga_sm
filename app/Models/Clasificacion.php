<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'torneo_id', 'equipo_id', 'partidos_jugados', 'partidos_ganados',
        'partidos_empatados', 'partidos_perdidos', 'goles_a_favor',
        'goles_en_contra', 'puntos', 'posicion'
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}