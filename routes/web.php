<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\OrganizacionCardController;

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

Route::get('/mision-vision', [PageController::class, 'misionVision'])->name('mision-vision');
Route::get('/presentacion', [PageController::class, 'presentacion'])->name('presentacion');
Route::get('/comunicados', [PageController::class, 'comunicados'])->name('comunicados');
Route::get('/eventos', [PageController::class, 'eventos'])->name('eventos');
Route::get('/detalle-evento/{id}', [PageController::class, 'detalleEvento'])->name('detalle-evento');
Route::get('/organizacion-card', [PageController::class, 'organizacionCard'])->name('organizacion-card');
Route::get('/organigrama', [PageController::class, 'organigrama'])->name('organigrama');
Route::get('/nuestro-equipo', [PageController::class, 'nuestroEquipo'])->name('nuestro-equipo');
Route::get('/certificaciones', [PageController::class, 'certificaciones'])->name('certificaciones');
Route::get('/licencias', [PageController::class, 'licencias'])->name('licencias');
Route::get('/contactos', [PageController::class, 'contactos'])->name('contactos');
Route::get('/arbitral', [PageController::class, 'arbitral'])->name('arbitral');
Route::get('/junta-res-disputas', [PageController::class, 'juntaResDisputas'])->name('junta-res-disputas');
Route::get('/dispute-review', [PageController::class, 'disputeReview'])->name('dispute-review');
Route::get('/dispute-avoidance-res', [PageController::class, 'disputeAvoidanceRes'])->name('dispute-avoidance-res');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::get('/gestion-contenido', [PageController::class, 'gestionContenido'])->name('gestion-contenido');
Route::resource('publicaciones', PublicacionController::class);
Route::put('/gestor/publicaciones/{id}/estado', [PublicacionController::class, 'toggleEstado'])
         ->name('publicaciones.toggle');

Route::resource('gestor/comunicados', ComunicadoController::class)->names('comunicados');
    
// Ruta para el interruptor de estado
Route::put('/gestor/comunicados/{id}/estado', [ComunicadoController::class, 'toggleEstado'])
         ->name('comunicados.toggle');
Route::resource('gestor/eventos', EventoController::class)->names('eventos');
Route::put('/gestor/eventos/{id}/estado', [EventoController::class, 'toggleEstado'])->name('eventos.toggle');
Route::resource('gestor/organizacion', OrganizacionCardController::class)->names('organizacion-gestion');
Route::put('/gestor/organizacion/{id}/estado', [OrganizacionCardController::class, 'toggleEstado'])->name('organizacion-gestion.toggle');
