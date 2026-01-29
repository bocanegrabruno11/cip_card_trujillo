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
use App\Http\Controllers\ArbitrajeRegistroController;
use App\Http\Controllers\ArbitrajeController;
use App\Http\Controllers\AdminArbitrajeController;
use App\Http\Controllers\ProcesoArbitrajeController;
use App\Http\Controllers\ProcesoArbitrajeDocumentoController;
use App\Http\Controllers\JrdRegistroController;
 use App\Http\Controllers\JrdController;
 use App\Http\Controllers\AdminJrdController;
 use App\Http\Controllers\JrdDocumentoController;
  use App\Http\Controllers\JrdProcesoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RepoSolicitudController;

Route::post('/solicitudes-repo', [RepoSolicitudController::class, 'store'])->name('solicitudes.store');
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


// En web.php
Route::get('/documentos/ver/{filename}', [DocumentoController::class, 'mostrar'])
    ->name('documentos.mostrar');

Route::get('/documentos/descargar/{filename}', [DocumentoController::class, 'descargar'])
    ->name('documentos.descargar');



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
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

    // Actualizar Información
    Route::get('/actualizar', function () {
        return view('mesa-partes.actualizar');
    })->name('actualizar');

    // Arbitraje
    Route::get('/arbitraje', function () {
        return view('mesa-partes.arbitraje');
    })->name('arbitraje');

        Route::get('/RegistrosArbitraje', function () {
        return view('mesa-partes.RegistrosArbitraje');
    })->name('RegistrosArbitraje');

    // JRD
    Route::get('/jrd', function () {
        return view('mesa-partes.jrd');
    })->name('jrd');

// Ruta para procesar la actualización - DEBE SER PUT
Route::put('mesa-partes/persona/update', [PersonaController::class, 'update'])
    ->name('persona.update');  // Asegúrate que este nombre coincida
    
Route::get('/actualizar', [PersonaController::class, 'actualizar'])->name('persona.actualizar');
Route::post('/persona/store', [PersonaController::class, 'store'])->name('persona.store');

Route::get('/persona/buscar', [PersonaController::class, 'buscarPorUsuario'])->name('persona.buscar');
Route::post('/arbitraje/registrar', [ArbitrajeRegistroController::class, 'store'])
    ->name('arbitraje.store');

Route::get('/arbitraje/registros', [ArbitrajeController::class, 'registros'])
    ->name('RegistrosArbitraje2');

Route::get('/arbitraje/obtener', [ArbitrajeController::class, 'obtenerArbitrajes'])
    ->name('arbitrajes.obtener');

Route::get('/jrd', function () {
    return view('mesa-partes.jrd');
})->name('jrd');

Route::get('/registros-jrd', function () {
    return view('mesa-partes.RegistrosJRD');
})->name('registros.jrd'); // Cambia 'jrd.index' por 'registros.jrd'
Route::get('/jrd/registrar', [JrdRegistroController::class, 'create'])->name('jrd.create');
Route::post('/jrd', [JrdRegistroController::class, 'store'])->name('jrd.store');

    Route::get('/jrd/obtener', [JrdController::class, 'obtenerJrd'])->name('jrd.obtener');
    Route::get('/jrd/mis-jrd', [JrdController::class, 'misJrd'])->name('jrd.mis');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'checkrole:admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('Admin.dashboard');
    })->name('Admin.dashboard');

    Route::get('/admin/arbitraje', function () {
        return view('Admin.Arbitraje');
    })->name('Admin.Arbitraje');
    
    // Vista principal de arbitrajes
    Route::get('/arbitrajes', [AdminArbitrajeController::class, 'index'])
        ->name('admin.arbitrajes.index');
    
    // API para obtener arbitrajes
    Route::get('/arbitrajes/obtener', [AdminArbitrajeController::class, 'obtenerTodos'])
        ->name('admin.arbitrajes.obtener');
    
    // Vista detalle de un arbitraje
    Route::get('/arbitrajes/{id}/detalle', [AdminArbitrajeController::class, 'detalle'])
        ->name('admin.arbitrajes.detalle');

    Route::post('/arbitrajes/{id}/rechazar', [AdminArbitrajeController::class, 'rechazar'])
        ->name('admin.arbitrajes.rechazar');
    Route::post('/arbitrajes/{id}/aceptar', [AdminArbitrajeController::class, 'aceptar'])
    ->name('admin.arbitrajes.aceptar');
    Route::post('/arbitraje/{id_arbitraje}/documentos',[ProcesoArbitrajeDocumentoController::class, 'store'])->name('arbitraje.documentos.store');
    Route::post(
        '/arbitraje/{id_arbitraje}/siguiente-proceso',
        [ProcesoArbitrajeController::class, 'pasarSiguienteProceso']
    )->name('arbitraje.siguiente.proceso');
   
    Route::get('/admin/Jrd', function () {
        return view('Admin.Jrd');
    })->name('Admin.Jrd');
    

    // Documentos JRD
    Route::post(
        '/jrd/{id_jrd}/documentos',
        [JrdDocumentoController::class, 'store']
    )->name('jrd.documento.store');

 // Rutas para JRD
    Route::get('/jrd', [AdminJrdController::class, 'index'])->name('admin.jrd.index');
    Route::get('/jrd/obtener', [AdminJrdController::class, 'obtenerJrd'])->name('admin.jrd.obtener');
    Route::get('/jrd/{id}', [AdminJrdController::class, 'detalle'])->name('admin.jrd.detalle');
    Route::get('/jrd/obtener/{id}', [AdminJrdController::class, 'obtenerUno'])->name('admin.jrd.obtener.uno');
    Route::post('/jrd/{id_jrd}/rechazar', [AdminJrdController::class, 'rechazar'])->name('admin.jrd.rechazar');
    Route::post('/jrd/{id_jrd}/aceptar', [AdminJrdController::class, 'aceptar'])->name('admin.jrd.aceptar');


    Route::post('/jrd/{id_jrd}/proceso/siguiente', [JrdProcesoController::class, 'pasarSiguienteProceso'])->name('jrd.proceso.siguiente');
    Route::post('/jrd/{id_jrd}/proceso/crear', [JrdProcesoController::class, 'crearProceso'])->name('jrd.proceso.crear');
    Route::post('/jrd/{id_jrd}/proceso/{id_proceso}/actualizar', [JrdProcesoController::class, 'actualizarEstadoProceso'])->name('jrd.proceso.actualizar');

    // Documentos JRD

    Route::prefix('admin/gestion-permisos')->name('admin.solicitudes.')->group(function () {
        Route::get('/', [RepoSolicitudController::class, 'index'])->name('index');
        Route::put('/{id}', [RepoSolicitudController::class, 'updateState'])->name('update');
        Route::delete('/{id}', [RepoSolicitudController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [RepoSolicitudController::class, 'show'])->name('show');
    });

});
Route::get('/sherlock-holmes', function () {
    echo "<h1>🕵️‍♂️ Sherlock Holmes: Detective de Archivos</h1>";
    
    // --- PRUEBA 1: ¿Tenemos permiso de escritura? ---
    $testPath = storage_path('app/public/prueba_escritura.txt');
    echo "<h3>1. Prueba de Escritura en el Disco Público</h3>";
    try {
        file_put_contents($testPath, "Hola Railway, puedo escribir aquí " . date('Y-m-d H:i:s'));
        echo "<span style='color:green'>✅ ÉXITO: Pude escribir un archivo en: $testPath</span><br>";
        echo "Permisos de la carpeta: " . substr(sprintf('%o', fileperms(dirname($testPath))), -4) . "<br>";
    } catch (\Exception $e) {
        echo "<span style='color:red'>❌ FALLO CRÍTICO: No puedo escribir. Error: " . $e->getMessage() . "</span><br>";
        echo "Esto explica por qué no se guardan las fotos.";
    }

    // --- PRUEBA 2: Búsqueda Global de Imágenes ---
    echo "<h3>2. Búsqueda de archivos .webp en TODO el proyecto (/app)</h3>";
    echo "<i>Buscando... esto puede tardar un poco...</i><br><br>";
    
    // Buscamos desde la raíz del proyecto
    $rootPath = base_path(); 
    $found = false;

    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            // Filtramos solo archivos .webp para no llenar la pantalla
            if ($item->isFile() && $item->getExtension() == 'webp') {
                echo "📸 <strong>ENCONTRADO:</strong> " . $item->getRealPath() . "<br>";
                echo "&nbsp;&nbsp;&nbsp;➡️ Permisos: " . substr(sprintf('%o', fileperms($item->getRealPath())), -4);
                echo " | Dueño: " . fileowner($item->getRealPath()) . "<br>";
                $found = true;
            }
        }
    } catch (\Exception $e) {
        echo "Error durante la búsqueda: " . $e->getMessage();
    }

    if (!$found) {
        echo "<br><strong style='color:orange'>🔍 RESULTADO: No encontré ningún archivo .webp en todo el proyecto.</strong><br>";
        echo "Conclusión: La subida está fallando silenciosamente o la base de datos tiene nombres de archivos que físicamente no existen.";
    }
});
require __DIR__.'/auth.php';

