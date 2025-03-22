<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premio extends Model
{
    use HasFactory;

    protected $fillable = ['torneo_id', 'tipo', 'descripcion'];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}