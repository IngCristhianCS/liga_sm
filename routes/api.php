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
use App\Http\Controllers\EventoPartidoController;
use App\Http\Controllers\JornadaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TorneoEquipoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas públicas de Torneos
Route::get('/torneos', [TorneoController::class, 'index']);
Route::get('/torneos/catalog', [TorneoController::class, 'catalog']);
Route::get('/torneos/{torneo}', [TorneoController::class, 'show']);
Route::get('/torneos/{torneoId}/jornadas', [TorneoController::class, 'jornadas']);
Route::get('/partidos/{id}', [PartidoController::class, 'show']);

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

    // Rutas protegidas (POST, PUT, DELETE)
    Route::post('/torneos', [TorneoController::class, 'store']);
    Route::put('/torneos/{torneo}', [TorneoController::class, 'update']);
    Route::delete('/torneos/{torneo}', [TorneoController::class, 'destroy']);

    Route::post('/partidos', [TorneoController::class, 'store']);
    Route::put('/partidos/{partido}', [TorneoController::class, 'update']);
    Route::delete('/partidos/{partido}', [TorneoController::class, 'destroy']);
    Route::get('/partidos', [PartidoController::class,'index']);

    Route::get('/jornadas/partidos-equipo', [PartidoController::class, 'obtenerPartidosPorEquipo']);
    Route::put('password', [PasswordController::class, 'update']);
    Route::patch('/profile', [ProfileController::class, 'update']);

    Route::apiResource('jornadas', JornadaController::class);
    Route::apiResource('ubicaciones', UbicacionController::class);
    Route::apiResource('eventos-partido', EventoPartidoController::class);
});

Route::get('/equipos/{equipo}/jugadores', [EquipoController::class, 'getJugadoresByEquipo']);
Route::get('/entrenadores', [UsuarioController::class, 'entrenadores']);

Route::get('/torneos/{torneo}/equipos', [TorneoEquipoController::class, 'getEquiposByTorneo']);
Route::post('/torneos/{torneo}/equipos', [TorneoEquipoController::class, 'assignEquipos']);

Route::get('/partidos/{partido}/eventos', [EventoPartidoController::class, 'getByPartido']);

// Add this route to your existing routes
Route::get('/arbitros', [App\Http\Controllers\ArbitroController::class, 'index']);

