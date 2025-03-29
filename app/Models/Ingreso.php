<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;
    protected $table = 'ingreso';

    protected $fillable = [
        'fecha', 'monto', 'tipo', 'descripcion',
        'equipo_id', 'torneo_id', 'patrocinador_id', 'estatus'
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function patrocinador()
    {
        return $this->belongsTo(Patrocinador::class);
    }
}