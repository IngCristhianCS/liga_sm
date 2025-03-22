<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TorneoEquipo extends Model
{
    use HasFactory;

    protected $table = 'torneo_equipo';
    
    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}