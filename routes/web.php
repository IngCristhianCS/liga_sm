<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArbitroController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\EventoPartidoController;
use App\Http\Controllers\GoleoController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\JornadaController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\PatrocinadorController;
use App\Http\Controllers\PremioController;
use App\Http\Controllers\ReglaController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UbicacionController;

Route::get('/', function () {return view('dashboard');})->name('dashboard');
Route::get('/clasificacion', function () {return view('dashboard');})->name('clasificacion');
Route::get('/resultados', function () {return view('dashboard');})->name('resultados');
Route::get('/reglamento', function () {return view('galeria');})->name('reglamento');
Route::get('/contacto', function () {return view('contacto');})->name('contacto');
Route::get('/patrocinadores', function () {return view('galeria');})->name('patrocinadores');


Route::middleware('auth')->group(function () {
    Route::get('/mi-perfil', function () {return view('dashboard');})->name('mi-perfil');
    Route::get('/mi-equipo', function () {return view('dashboard');})->name('mi-equipo');
    Route::get('/calendario', function () {return view('dashboard');})->name('calendario');    
    Route::get('/usuarios', function () {return view('dashboard');})->name('usuarios');
    
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Entrenador
    Route::middleware(['can:entrenador-access'])->group(function () {
        Route::get('/estadisticas', [EquipoController::class, 'estadisticas'])->name('estadisticas');
    });

    // Rutas de Jugador
    Route::middleware(['can:jugador-access'])->group(function () {
        Route::get('/mis-estadisticas', [JugadorController::class, 'misEstadisticas'])->name('mis-estadisticas');
    });

    //Route::get('/perfil', [PersonaController::class, 'perfil'])->name('perfil');
});

require __DIR__.'/auth.php';
