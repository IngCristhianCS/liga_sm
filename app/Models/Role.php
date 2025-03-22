<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMIN = 1;
    const ENTRENADOR = 2;
    const JUGADOR = 3;
    const ARBITRO = 4;

    protected $fillable = ['name', 'slug'];
}