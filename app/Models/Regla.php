<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regla extends Model
{
    use HasFactory;

    protected $fillable = ['torneo_id', 'descripcion'];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}