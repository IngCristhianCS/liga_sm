<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    use HasFactory;

    protected $fillable = ['persona_id', 'equipo_id'];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}