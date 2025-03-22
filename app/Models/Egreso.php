<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha', 'monto', 'tipo', 'descripcion', 'partido_id', 'torneo_id'
    ];

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}