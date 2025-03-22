<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arbitro extends Model
{
    use HasFactory;

    protected $table = 'arbitro';
    protected $fillable = ['persona_id', 'licencia'];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}