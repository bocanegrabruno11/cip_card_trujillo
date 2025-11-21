<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicacionController;

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

Route::get('/mision-vision', [PageController::class, 'misionVision'])->name('mision-vision');
Route::get('/presentacion', [PageController::class, 'presentacion'])->name('presentacion');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::get('/gestion-contenido', [PageController::class, 'gestionContenido'])->name('gestion-contenido');
Route::resource('publicaciones', PublicacionController::class);
Route::put('/gestor/publicaciones/{id}/estado', [PublicacionController::class, 'toggleEstado'])
         ->name('publicaciones.toggle');
