<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Models\Documentacion;
use App\Models\Evento;
use App\Models\OrganizacionCard;
use App\Models\Publicacion;
use App\Models\TarifaConfiguracion;
use App\Models\TarifaEscala;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function misionVision()
    {
        return view('contenido.mision-vision');
    }

    public function welcome()
    {
        $popupData = Publicacion::with('detalles')
        ->where('seccion', 'inicio_popup')
        ->where('activo', true)
        ->where('activo', 1)
        ->latest()
        ->first();

        // 2. SLIDER PRINCIPAL (Nueva consulta)
        // Buscamos la publicación más reciente de 'inicio_slider'
        $sliderData = Publicacion::with('detalles')
            ->where('seccion', 'inicio_slider')
            ->where('activo', 1)
            ->latest()
            ->first();

        $proximosEventos = Evento::with('detalles')
            ->where('activo', true)
            ->whereDate('fecha_evento', '>=', \Carbon\Carbon::today())
            ->orderBy('fecha_evento', 'asc')
            ->take(4)
            ->get();

        // Pasamos ambas variables
        return view('welcome', compact('popupData', 'sliderData', 'proximosEventos'));
    }

    public function presentacion()
    {
        // 1. Buscamos LA ÚLTIMA publicación registrada para la sección 'presentacion'
        $ultimaPublicacion = Publicacion::with('detalles')
            ->where('seccion', 'presentacion')
            ->where('activo', true)
            ->latest() // Ordena por created_at desc
            ->first();

        // 2. Inicializamos variables vacías por si no hay registros aún
        $imagenPrincipal = null;
        $galeria = collect([]); 

        // 3. Si existe la publicación, separamos las imágenes
        if ($ultimaPublicacion) {
            // Buscamos en la relación 'detalles' la que tenga grupo 'principal'
            $imagenPrincipal = $ultimaPublicacion->detalles->where('grupo', 'principal')->first();
            
            // Buscamos las que tengan grupo 'galeria'
            $galeria = $ultimaPublicacion->detalles->where('grupo', 'galeria');
        }

        return view('contenido.presentacion', compact('imagenPrincipal', 'galeria'));
    }

    public function gestionContenido()
    {
        return view('gestion-contenido.main');
    }

    public function comunicados()
    {
        // Obtenemos solo los activos, ordenados del más reciente al más antiguo
        // Paginamos de 12 en 12 por si hay muchos
        $comunicados = Comunicado::where('activo', true)
                                ->latest()
                                ->paginate(12);

        return view('contenido.comunicados', compact('comunicados'));
    }

    public function eventos()
    {
        // 1. Obtener el evento DESTACADO (el último activo)
        $hoy = now()->format('Y-m-d');
        $destacado = Evento::with('detalles')
        ->where('activo', true)
        ->whereDate('fecha_evento', '>=', $hoy) // Solo eventos futuros o de hoy
        ->orderBy('fecha_evento', 'asc') // Ordenar: El más cercano primero
        ->first();

        // 2. Obtener el resto de eventos (excluyendo el destacado para no repetir)
        $listaEventos = Evento::with('detalles')
            ->where('activo', true)
            ->when($destacado, function ($query) use ($destacado) {
                return $query->where('id', '!=', $destacado->id);
            })
            ->latest('fecha_evento')
            ->paginate(10);

        return view('contenido.eventos', compact('destacado', 'listaEventos'));
    }

    // Vista Detalle: Un solo evento
    public function detalleEvento($id)
    {
        $evento = Evento::with('detalles')
            ->where('activo', true)
            ->findOrFail($id);
            
        // Separar imágenes
        $imagenPrincipal = $evento->detalles->where('tipo', 'principal')->first();
        $galeria = $evento->detalles->where('tipo', 'galeria');

        return view('contenido.detalle-evento', compact('evento', 'imagenPrincipal', 'galeria'));
    }

    public function organizacionCard()
    {
        // 1. Órgano Directivo (Decano y directivos)
        $directivos = OrganizacionCard::where('grupo', 'directivo')
            ->where('activo', true)
            ->orderBy('orden', 'asc')
            ->get();

        // 2. Órgano Decisorio - Presidente (Suele ser uno solo)
        $decisorioPresidente = OrganizacionCard::where('grupo', 'decisorio_presidente')
            ->where('activo', true)
            ->first();

        // 3. Órgano Decisorio - Miembros
        $decisorioMiembros = OrganizacionCard::where('grupo', 'decisorio_miembros')
            ->where('activo', true)
            ->orderBy('orden', 'asc')
            ->get();

        // 4. Secretaría General
        $secretaria = OrganizacionCard::where('grupo', 'secretaria')
            ->where('activo', true)
            ->first();

        // Retornamos todas las colecciones a la vista
        return view('contenido.organizacion-card', compact(
            'directivos', 
            'decisorioPresidente', 
            'decisorioMiembros', 
            'secretaria'
        ));
    }

    public function organigrama()
    {
        return view('contenido.organigrama');
    }

    public function nuestroEquipo()
    {
        // Traemos todos los miembros activos de los grupos requeridos, ordenados por su 'orden'
        $miembros = OrganizacionCard::where('activo', true)
            ->whereIn('grupo', [
                'secretaria', 
                'secretarios_arbitrales', 
                'apoyo', 
                'administrativo'
            ])
            ->orderBy('orden', 'asc')
            ->get();

        // Filtramos las colecciones para enviarlas separadas a la vista
        $secretaria = $miembros->where('grupo', 'secretaria')->first();
        $secretariosArbitrales = $miembros->where('grupo', 'secretarios_arbitrales');
        $personalApoyo = $miembros->where('grupo', 'apoyo');
        $soporteAdmin = $miembros->where('grupo', 'administrativo');

        return view('contenido.nuestro-equipo', compact(
            'secretaria', 
            'secretariosArbitrales', 
            'personalApoyo', 
            'soporteAdmin'
        ));
    }

    public function certificaciones()
    {
        return view('contenido.certificaciones');
    }

    public function licencias()
    {
        return view('contenido.licencias');
    }
    
    public function contactos()
    {
        return view('contenido.contactos');
    }
    
    public function arbitral()
    {
        return view('contenido.clausulas.arbitral');
    }
    
    public function disputeAvoidanceRes()
    {
        return view('contenido.clausulas.dispute-avoidance-res');
    }
    
    public function disputeReview()
    {
        return view('contenido.clausulas.dispute-review');
    }
    
    public function juntaResDisputas()
    {
        return view('contenido.clausulas.junta-res-disputas');
    }
    
    public function institucionArbitral()
    {
        // 1. Obtenemos documentos de 'institucion', que estén activos, ordenados por fecha
        $documentos = Documentacion::where('seccion', 'institucion')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        // 2. Los agrupamos por la columna 'categoria'
        // Esto crea un array asociativo donde la llave es 'normativa', 'tarifario', etc.
        $docsPorCategoria = $documentos->groupBy('categoria');

        return view('contenido.servicios.institucion-arbitral', compact('docsPorCategoria'));
    }

    public function juntaPrevencion()
    {
        // Misma lógica para la Junta
        $documentos = Documentacion::where('seccion', 'junta')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        $docsPorCategoria = $documentos->groupBy('categoria');

        return view('contenido.servicios.junta-prevencion', compact('docsPorCategoria'));
    }

    public function convocatoria()
    {
        // Obtenemos solo los documentos de la sección 'convocatorias' que estén activos
        // Ordenamos descendente para que el último subido (ej: Resultados Finales) salga arriba o primero
        $documentos = Documentacion::where('seccion', 'convocatorias')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc') 
            ->get();

        return view('contenido.convocatoria', compact('documentos'));
    }

    public function calcInstDeterminada()
    {
        $escalas = TarifaEscala::where('activo', true)->get();

        $data = [
            'unico'    => $escalas->where('tipo', 'arbitro_unico')->values(),
            'tribunal' => $escalas->where('tipo', 'tribunal_arbitral')->values(),
            'gastos'   => $escalas->where('tipo', 'gastos_administrativos')->values(),
        ];

        $configIgv = TarifaConfiguracion::where('clave', 'igv')->first();
        $igv = $configIgv ? floatval($configIgv->valor) : 18.00;

        return view('contenido.calculadoras.institucion-determinada', compact('data', 'igv'));
    }

    public function calcInstIndeterminada()
    {
        $escalas = TarifaEscala::where('activo', true)->get();
        $data = [
            'unico'    => $escalas->where('tipo', 'arbitro_unico')->values(),
            'tribunal' => $escalas->where('tipo', 'tribunal_arbitral')->values(),
            'gastos'   => $escalas->where('tipo', 'gastos_administrativos')->values(),
        ];

        $config = TarifaConfiguracion::whereIn('clave', ['igv', 'porcentaje_indeterminado'])->pluck('valor', 'clave');
        
        $igv = floatval($config['igv'] ?? 18.00);
        $porcentajeIndeterminado = floatval($config['porcentaje_indeterminado'] ?? 5.00); // El 5% del PDF

        return view('contenido.calculadoras.institucion-indeterminada', compact('data', 'igv', 'porcentajeIndeterminado'));
    }

    public function calcJunta() {
        return view('contenido.calculadoras.junta');
    }

    
}
