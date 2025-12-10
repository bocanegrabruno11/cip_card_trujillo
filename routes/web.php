<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\OrganizacionCardController;
use App\Http\Controllers\CalculadoraController;
use App\Http\Controllers\DocumentacionController;
use App\Http\Controllers\TarifaConfiguracionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonaController;

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

Route::get('/mision-vision', [PageController::class, 'misionVision'])->name('mision-vision');
Route::get('/presentacion', [PageController::class, 'presentacion'])->name('presentacion');
Route::get('/comunicados', [PageController::class, 'comunicados'])->name('comunicados');
Route::get('/eventos', [PageController::class, 'eventos'])->name('eventos');
Route::get('/detalle-evento/{id}', [PageController::class, 'detalleEvento'])->name('detalle-evento');
Route::get('/organizacion-card', [PageController::class, 'organizacionCard'])->name('organizacion-card');
Route::get('/organigrama', [PageController::class, 'organigrama'])->name('organigrama');
Route::get('/institucion-arbitral', [PageController::class, 'institucionArbitral'])->name('institucion-arbitral');
Route::get('/junta-prevencion', [PageController::class, 'juntaPrevencion'])->name('junta-prevencion');
Route::get('/nuestro-equipo', [PageController::class, 'nuestroEquipo'])->name('nuestro-equipo');
Route::get('/certificaciones', [PageController::class, 'certificaciones'])->name('certificaciones');
Route::get('/politicas', [PageController::class, 'politicas'])->name('politicas');
Route::get('/licencias', [PageController::class, 'licencias'])->name('licencias');
Route::get('/contactos', [PageController::class, 'contactos'])->name('contactos');
Route::get('/arbitral', [PageController::class, 'arbitral'])->name('arbitral');
Route::get('/junta-res-disputas', [PageController::class, 'juntaResDisputas'])->name('junta-res-disputas');
Route::get('/dispute-review', [PageController::class, 'disputeReview'])->name('dispute-review');
Route::get('/dispute-avoidance-res', [PageController::class, 'disputeAvoidanceRes'])->name('dispute-avoidance-res');

Route::get('/convocatoria', [PageController::class, 'convocatoria'])->name('convocatoria');

Route::get('/calculadora/institucion/determinada', [PageController::class, 'calcInstDeterminada'])->name('calc.inst.det');
Route::get('/calculadora/institucion/indeterminada', [PageController::class, 'calcInstIndeterminada'])->name('calc.inst.indet');
Route::get('/calculadora/junta/calc', [PageController::class, 'calcJunta'])->name('calc.junta');

Route::middleware(['auth', 'checkrole:gestor_contenido'])->group(function () {
    Route::get('/gestion-contenido', [PageController::class, 'gestionContenido'])->name('gestion-contenido');
    Route::resource('publicaciones', PublicacionController::class);
    Route::put('/gestor/publicaciones/{id}/estado', [PublicacionController::class, 'toggleEstado'])
            ->name('publicaciones.toggle');
    Route::resource('gestor/comunicados', ComunicadoController::class)->names('comunicados');
    Route::put('/gestor/comunicados/{id}/estado', [ComunicadoController::class, 'toggleEstado'])
            ->name('comunicados.toggle');
    Route::resource('gestor/eventos', EventoController::class)->names('eventos');
    Route::put('/gestor/eventos/{id}/estado', [EventoController::class, 'toggleEstado'])->name('eventos.toggle');
    Route::resource('gestor/organizacion', OrganizacionCardController::class)->names('organizacion-gestion');
    Route::put('/gestor/organizacion/{id}/estado', [OrganizacionCardController::class, 'toggleEstado'])->name('organizacion-gestion.toggle');
    Route::resource('gestor/documentos', DocumentacionController::class)->names('documentos-gestion');
    Route::put('/gestor/documentos/{id}/estado', [DocumentacionController::class, 'toggleEstado'])->name('documentos-gestion.toggle');
    Route::resource('gestor/calculadoras', CalculadoraController::class)->names('calculadoras-gestion');
    Route::resource('gestor/tarifas_config', TarifaConfiguracionController::class)->names('tarifas_config');
});


Route::middleware(['auth', 'checkrole:mesa_partes'])->prefix('mesa-partes')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('mesa-partes.dashboard');
    })->name('dashboard');

    // Actualizar Información
    Route::get('/actualizar', function () {
        return view('mesa-partes.actualizar');
    })->name('actualizar');

    // Arbitraje
    Route::get('/arbitraje', function () {
        return view('mesa-partes.arbitraje');
    })->name('arbitraje');

    // JRD
    Route::get('/jrd', function () {
        return view('mesa-partes.jrd');
    })->name('jrd');
Route::get('/actualizar', [PersonaController::class, 'actualizar'])->name('persona.actualizar');
Route::post('/persona/store', [PersonaController::class, 'store'])->name('persona.store');
Route::post('/persona/update', [PersonaController::class, 'update'])->name('persona.update');

});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/dashboard', function () {
    return view('Admin/dashboard');
})->middleware(['auth','checkrole:admin'])
  ->name('Admin.dashboard');

require __DIR__.'/auth.php';

