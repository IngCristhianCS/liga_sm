<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UsuarioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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

Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {    
    Route::apiResource('users', UsuarioController::class);
});