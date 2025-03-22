<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha', 'ubicacion_id', 'equipo_local_id', 'equipo_visitante_id',
        'torneo_id', 'goles_equipo_local', 'goles_equipo_visitante',
        'arbitro_principal_id', 'arbitro_asistente1_id', 'arbitro_asistente2_id',
        'hora_inicio', 'jornada_id'
    ];

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}