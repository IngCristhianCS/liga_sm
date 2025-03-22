<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrocinador extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'contacto', 'tipo_apoyo'];

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class);
    }
}