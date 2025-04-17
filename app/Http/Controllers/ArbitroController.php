<?php

namespace App\Http\Controllers;

use App\Models\Arbitro;
use Illuminate\Http\Request;

class ArbitroController extends Controller
{
    /**
     * Get all arbitros
     */
    public function index()
    {
        $arbitros = Arbitro::all();
        
        return response()->json([
            'data' => $arbitros
        ]);
    }
}
