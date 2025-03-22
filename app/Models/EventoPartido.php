<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoPartido extends Model
{
    use HasFactory;

    protected $fillable = ['partido_id', 'minuto', 'tipo_evento', 'jugador_id'];

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
}