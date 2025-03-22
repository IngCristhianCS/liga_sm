<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    use HasFactory;

    protected $fillable = ['torneo_id', 'numero', 'fecha_inicio', 'fecha_fin'];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}