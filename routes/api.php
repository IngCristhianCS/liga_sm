<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ProfileController;

Route::get('/user', function (Request $request) {return $request->user();})->middleware('auth:sanctum');

Route::get('/clasificacion', [ClasificacionController::class, 'index']);
Route::get('/torneos/{torneoId}/jornadas', [TorneoController::class, 'jornadas']);

// Rutas de Administración
Route::middleware(['can:admin-access'])->group(function () {
    /*Route::resource('arbitros', ArbitroController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('egresos', EgresoController::class);
    Route::resource('equipos', EquipoController::class);
    Route::resource('eventos-partido', EventoPartidoController::class);
    Route::resource('goleadores', GoleoController::class);
    Route::resource('ingresos', IngresoController::class);
    Route::resource('jornadas', JornadaController::class);
    Route::resource('jugadores', JugadorController::class);
    Route::resource('partidos', PartidoController::class);
    Route::resource('patrocinadores', PatrocinadorController::class);
    Route::resource('personas', PersonaController::class);
    Route::resource('premios', PremioController::class);
    Route::resource('reglas', ReglaController::class);
    Route::resource('temporadas', TemporadaController::class);
    Route::resource('torneos', TorneoController::class);
    Route::resource('ubicaciones', UbicacionController::class);*/
});

Route::middleware(['auth:sanctum'])->group(function () {    
    Route::apiResource('users', UsuarioController::class);
    Route::apiResource('equipo', EquipoController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('ingresos', IngresoController::class);

    Route::get('/jornadas/partidos-equipo', [PartidoController::class, 'obtenerPartidosPorEquipo']);

    Route::put('password', [PasswordController::class, 'update']);    
    Route::patch('/profile', [ProfileController::class, 'update']);
});