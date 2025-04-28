<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return view('dashboard');})->name('dashboard');
Route::get('/clasificacion', function () {return view('dashboard');})->name('clasificacion');
Route::get('/resultados', function () {return view('dashboard');})->name('resultados');
Route::get('/estadisticas', function () {return view('dashboard');})->name('estadisticas');
Route::get('/contacto', function () {return view('contacto');})->name('contacto');
Route::get('/patrocinadores', function () {return view('galeria');})->name('patrocinadores');
Route::get('/partidos/{id}/detalles', function () {return view('dashboard');})->name('partido.detalles');
   

Route::middleware('auth')->group(function () {
    Route::get('/mi-perfil', function () {return view('dashboard');})->name('mi-perfil');
    Route::get('/mi-equipo', function () {return view('dashboard');})->name('mi-equipo');
    Route::get('/calendario', function () {return view('dashboard');})->name('calendario');
    Route::get('/usuarios', function () {return view('dashboard');})->name('usuarios');
    Route::get('/ingresos', function () {return view('dashboard');})->name('ingresos');
    Route::get('/egresos', function () {return view('dashboard');})->name('egresos');
    Route::get('/torneos', function () {return view('dashboard');})->name('torneos');
    Route::get('/temporadas', function () {return view('dashboard');})->name('temporadas');
    Route::get('/categorias', function () {return view('dashboard');})->name('categorias');
    Route::get('/equipos', function () {return view('dashboard');})->name('equipos');
    Route::get('/partidos', function () {return view('dashboard');})->name('partidos');
    Route::get('/jornadas', function () {return view('dashboard');})->name('jornadas');
});

require __DIR__.'/auth.php';
