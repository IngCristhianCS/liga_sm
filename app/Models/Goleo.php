<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goleo extends Model
{
    use HasFactory;

    protected $fillable = ['jugador_id', 'partido_id', 'torneo_id', 'cantidad_goles', 'fecha'];

    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}