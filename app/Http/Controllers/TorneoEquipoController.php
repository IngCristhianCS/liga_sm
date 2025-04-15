<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TorneoEquipoController extends Controller
{
    /**
     * Get all equipos for a specific torneo
     */
    public function getEquiposByTorneo($torneoId)
    {
        $torneo = Torneo::findOrFail($torneoId);
        $equipos = $torneo->equipos;
        
        return response()->json([
            'data' => $equipos
        ]);
    }
    
    /**
     * Assign equipos to a torneo
     */
    public function assignEquipos(Request $request, $torneoId)
    {
        $request->validate([
            'equipos' => 'required|array',
            'equipos.*' => 'exists:equipo,id'
        ]);
        
        $torneo = Torneo::findOrFail($torneoId);
        $equipoIds = $request->input('equipos');
        
        DB::beginTransaction();
        
        try {
            // Sync the equipos with the torneo
            $torneo->equipos()->sync($equipoIds);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Equipos asignados correctamente',
                'data' => $torneo->equipos
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error al asignar equipos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}