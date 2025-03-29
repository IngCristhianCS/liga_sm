<?php

use Illuminate\Support\Facades\Route;

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
    Route::get('/ingresos', function () {return view('dashboard');})->name('ingresos');
});

require __DIR__.'/auth.php';
