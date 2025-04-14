<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TorneoEquipo extends Model
{
    use HasFactory;

    protected $table = 'torneo_equipo';
    
    // Add fillable properties
    protected $fillable = [
        'torneo_id',
        'equipo_id',
        'fecha_inscripcion',
        'estado'
    ];
    
    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
    
    /**
     * Associate an equipo with a torneo
     *
     * @param int $equipoId
     * @param int $torneoId
     * @param array $attributes Additional attributes
     * @return TorneoEquipo
     */
    public static function associateEquipoWithTorneo($equipoId, $torneoId, $attributes = [])
    {
        $data = array_merge([
            'equipo_id' => $equipoId,
            'torneo_id' => $torneoId,
            'fecha_inscripcion' => now(),
            'estado' => 'activo'
        ], $attributes);
        
        return self::updateOrCreate(
            ['equipo_id' => $equipoId, 'torneo_id' => $torneoId],
            $data
        );
    }
    
    /**
     * Get all equipos for a specific torneo
     *
     * @param int $torneoId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getEquiposByTorneo($torneoId)
    {
        return self::where('torneo_id', $torneoId)
            ->with('equipo')
            ->get()
            ->pluck('equipo');
    }
    
    /**
     * Get all torneos for a specific equipo
     *
     * @param int $equipoId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTorneosByEquipo($equipoId)
    {
        return self::where('equipo_id', $equipoId)
            ->with('torneo')
            ->get()
            ->pluck('torneo');
    }
}