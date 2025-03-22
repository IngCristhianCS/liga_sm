<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenador extends Model
{
    use HasFactory;

    protected $table = 'entrenadores';

    protected $fillable = [
        'user_id',
        'equipo_id',
        'licencia',
        'experiencia'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function jugadores()
    {
        return $this->hasManyThrough(
            Jugador::class,
            Equipo::class,
            'entrenador_id', // Foreign key on equipos table
            'equipo_id',    // Foreign key on jugadores table
            'id',           // Local key on entrenadores table
            'id'           // Local key on equipos table
        );
    }
}