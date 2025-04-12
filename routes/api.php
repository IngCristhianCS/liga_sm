<?php

use App\Http\Controllers\UbicacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\JornadaController;
use App\Http\Controllers\ProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas públicas de Torneos
Route::get('/torneos', [TorneoController::class, 'index']); // Listar todos
Route::get('/torneos/{torneo}', [TorneoController::class, 'show']); // Ver detalle
Route::get('/torneos/{torneoId}/jornadas', [TorneoController::class, 'jornadas']); // Jornadas

Route::get('/clasificacion/{torneoId}', [ClasificacionController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {    
    Route::apiResource('users', UsuarioController::class);
    Route::apiResource('equipo', EquipoController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('ingresos', IngresoController::class);    
    Route::apiResource('egresos', EgresoController::class);
    Route::apiResource('temporadas', TemporadaController::class);
    Route::apiResource('categorias', CategoriaController::class);    
    Route::apiResource('equipos', EquipoController::class);

    // Rutas protegidas de Torneos (POST, PUT, DELETE)
    Route::post('/torneos', [TorneoController::class, 'store']);
    Route::put('/torneos/{torneo}', [TorneoController::class, 'update']);
    Route::delete('/torneos/{torneo}', [TorneoController::class, 'destroy']);

    Route::get('/jornadas/partidos-equipo', [PartidoController::class, 'obtenerPartidosPorEquipo']);
    Route::put('password', [PasswordController::class, 'update']);    
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::apiResource('partidos', PartidoController::class);
    Route::apiResource('jornadas', JornadaController::class);
    Route::apiResource('ubicaciones', UbicacionController::class);
});