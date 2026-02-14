<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Models\Documentacion;
use App\Models\Evento;
use App\Models\OrganizacionCard;
use App\Models\Publicacion;
use App\Models\SolicitudRepositorio;
use App\Models\TarifaConfiguracion;
use App\Models\TarifaEscala;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $documentoPresentacion = Documentacion::where('seccion', 'presentacion')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
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

        return view('contenido.presentacion', compact('imagenPrincipal', 'galeria','documentoPresentacion'));
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

    public function organoDireccion()
    {
        // 1. Órgano de Dirección
        $organoDireccion = OrganizacionCard::where('grupo', 'organo_direccion')
            ->where('activo', true)
            ->orderBy('orden', 'asc')
            ->get();

        // 2. Órgano de Decisión
        $organoDecision = OrganizacionCard::where('grupo', 'organo_decision')
            ->where('activo', true)
            ->orderBy('orden', 'asc')
            ->get();

        // 3. Órgano de Gestión - Secretaría Arbitral
        $secArbitral = OrganizacionCard::where('grupo', 'organo_gestion_secretaria_arbitral')
            ->where('activo', true)
            ->orderBy('orden', 'asc')
            ->get();

        // 4. Órgano de Gestión - Secretaría Técnica
        $secTecnica = OrganizacionCard::where('grupo', 'organo_gestion_secretaria_tecnica')
            ->where('activo', true)
            ->orderBy('orden', 'asc')
            ->get();

        // 5. Documento de Resolución (Se mantiene la lógica existente)
        $documentoResolucion = Documentacion::where('seccion', 'organizacion')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
            ->first();

        // Retornamos las nuevas variables a la vista
        return view('contenido.organo-direccion', compact(
            'organoDireccion', 
            'organoDecision', 
            'secArbitral', 
            'secTecnica',
            'documentoResolucion'
        ));
    }

    public function organigrama()
    {
        return view('contenido.organigrama');
    }

    public function organoDecision()
    {
        // Traemos todos los miembros activos
        $organoDecision = OrganizacionCard::where('activo', true)
            ->where('grupo', 'organo_decision')
            ->orderBy('orden', 'asc')
            ->get();

        return view('contenido.organo-decision', compact('organoDecision'));
    }
    public function organoGestion()
    {
        // Traemos todos los miembros activos
        $miembros = OrganizacionCard::where('activo', true)
            ->whereIn('grupo', [
                'organo_gestion_secretaria_arbitral',
                'organo_gestion_secretaria_tecnica'
            ])
            ->orderBy('orden', 'asc')
            ->get();

        // Filtramos las colecciones
        $secArbitral = $miembros->where('grupo', 'organo_gestion_secretaria_arbitral');
        $secTecnica = $miembros->where('grupo', 'organo_gestion_secretaria_tecnica');

        return view('contenido.organo-gestion', compact('secArbitral', 'secTecnica'));
    }

    public function certificaciones()
    {
        // Obtenemos documentos de la sección 'certificaciones' y categoría 'certificacion'
        $certificados = Documentacion::where('seccion', 'certificaciones')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        return view('contenido.certificaciones', compact('certificados'));
    }

    public function politicas()
    {
        // Documentos de sección 'politicas' y categoría 'politica'
        $politicas = Documentacion::where('seccion', 'politicas')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        return view('contenido.politicas', compact('politicas'));
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
       

        return view('contenido.servicios.institucion-arbitral');
    }
   
    public function arbitralNormativa()
    {
        $documentos = Documentacion::where('seccion', 'institucion')
            ->where('categoria', 'normativa')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
            ->get();
        return view('contenido.servicios.arbitral.normativa', compact('documentos'));
    }

    public function arbitralTarifario()
    {
        $tarifas = Documentacion::where('seccion', 'institucion')
            ->where('categoria', 'tarifario')
            ->where('activo', true)
           
            ->get();
        return view('contenido.servicios.arbitral.tarifario', compact('tarifas'));
    }

    public function arbitralNomina()
    {
        // Carga los documentos de la categoría incorporación/nómina
        $docsNomina = Documentacion::where('seccion', 'institucion')
            ->where('categoria', 'incorporacion')
            ->where('activo', true)
            ->get();

        // Carga los perfiles de los árbitros
        $arbitrosNomina = OrganizacionCard::where('grupo', 'arbitros-nomina')
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return view('contenido.servicios.arbitral.nomina-arbitros', compact('docsNomina', 'arbitrosNomina'));
    }

    public function arbitralIncorporacion()
    {
        $docsIncorporacion = Documentacion::where('seccion', 'institucion')
            ->where('categoria', 'incorporacion')
            ->where('activo', true)
            ->get();
            
        $requisitos = Documentacion::where('seccion', 'institucion')
            ->where('categoria', 'requisitos')
            ->where('activo', true)
            ->get();

        return view('contenido.servicios.arbitral.requisitos', compact('docsIncorporacion', 'requisitos'));
    }

    public function arbitralSolicitar()
    {
        $formularios = Documentacion::where('seccion', 'institucion')
            ->where('categoria', 'solicitar')
            ->where('activo', true)
            ->get();
        return view('contenido.servicios.arbitral.solicitar', compact('formularios'));
    }

    public function arbitralRepositorio()
    {

        
        $docsInformativos = Documentacion::where('seccion', 'institucion')
            ->where('categoria', 'repositorio')
            ->where('activo', true)
            ->get();

        return view('contenido.servicios.arbitral.laudos', compact('docsInformativos'));
    }

    public function juntaPrevencion()
    {

        return view('contenido.servicios.junta-prevencion');
    }

    public function juntaPrevencionNormativa()
    {
        $documentos = Documentacion::where('seccion', 'junta')
            ->where('categoria', 'normativa')
            ->where('activo', true)
            ->whereDate('fecha_publicacion', '<=', Carbon::today())
            ->orderBy('fecha_publicacion', 'desc')
            ->get();
        return view('contenido.servicios.jrd.normativa', compact('documentos'));
    }

    public function juntaPrevencionTarifario()
    {
        $tarifas = Documentacion::where('seccion', 'junta')
            ->where('categoria', 'tarifario')
            ->where('activo', true)
           
            ->get();
        return view('contenido.servicios.jrd.tarifario', compact('tarifas'));
    }

    public function juntaPrevencionNomina()
    {
        // Carga los documentos de la categoría incorporación/nómina
        $docsNomina = Documentacion::where('seccion', 'junta')
            ->where('categoria', 'incorporacion')
            ->where('activo', true)
            ->get();

        // Carga los perfiles de los árbitros
        $adjudicadoresNomina = OrganizacionCard::where('grupo', 'adjudicadores-nomina')
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return view('contenido.servicios.jrd.nomina-adjudicadores', compact('docsNomina', 'adjudicadoresNomina'));
    }

    public function juntaPrevencionIncorporacion()
    {
        $docsIncorporacion = Documentacion::where('seccion', 'junta')
            ->where('categoria', 'incorporacion')
            ->where('activo', true)
            ->get();
            
        $requisitos = Documentacion::where('seccion', 'junta')
            ->where('categoria', 'requisitos')
            ->where('activo', true)
            ->get();

        return view('contenido.servicios.jrd.requisitos', compact('docsIncorporacion', 'requisitos'));
    }

    public function juntaPrevencionSolicitar()
    {
        $formularios = Documentacion::where('seccion', 'junta')
            ->where('categoria', 'solicitar')
            ->where('activo', true)
            ->get();
        return view('contenido.servicios.jrd.solicitar', compact('formularios'));
    }

    public function juntaPrevencionRepositorio()
    {

        
        $docsInformativos = Documentacion::where('seccion', 'junta')
            ->where('categoria', 'repositorio')
            ->where('activo', true)
            ->get();

        return view('contenido.servicios.jrd.laudos', compact('docsInformativos'));
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
            'unico'    => $escalas->where('tipo_calculadora', 'servicio_arbitral')->where('tipo', 'arbitro_unico')->values(),
            'tribunal' => $escalas->where('tipo_calculadora', 'servicio_arbitral')->where('tipo', 'tribunal_arbitral')->values(),
            'gastos'   => $escalas->where('tipo_calculadora', 'servicio_arbitral')->where('tipo', 'gastos_administrativos')->values(),
        ];

        $configIgv = TarifaConfiguracion::where('clave', 'igv')->first();
        $igv = $configIgv ? floatval($configIgv->valor) : 18.00;

        return view('contenido.calculadoras.institucion-determinada', compact('data', 'igv'));
    }

    public function calcInstIndeterminada()
    {
        $escalas = TarifaEscala::where('activo', true)->get();
        $data = [
            'unico'    => $escalas->where('tipo_calculadora', 'servicio_arbitral')->where('tipo', 'arbitro_unico')->values(),
            'tribunal' => $escalas->where('tipo_calculadora', 'servicio_arbitral')->where('tipo', 'tribunal_arbitral')->values(),
            'gastos'   => $escalas->where('tipo_calculadora', 'servicio_arbitral')->where('tipo', 'gastos_administrativos')->values(),
        ];

        $config = TarifaConfiguracion::whereIn('clave', ['igv', 'porcentaje_indeterminado'])->pluck('valor', 'clave');
        
        $igv = floatval($config['igv'] ?? 18.00);
        $porcentajeIndeterminado = floatval($config['porcentaje_indeterminado'] ?? 5.00); // El 5% del PDF

        return view('contenido.calculadoras.institucion-indeterminada', compact('data', 'igv', 'porcentajeIndeterminado'));
    }

    public function calcJunta() {

         $escalas = TarifaEscala::where('activo', true)->get();
        $data = [
            'unico'    => $escalas->where('tipo_calculadora', 'junta_prevencion')->where('tipo', 'arbitro_unico')->values(),
            'tribunal' => $escalas->where('tipo_calculadora', 'junta_prevencion')->where('tipo', 'tribunal_arbitral')->values(),
            'gastos'   => $escalas->where('tipo_calculadora', 'junta_prevencion')->where('tipo', 'gastos_administrativos')->values(),
        ];

        $config = TarifaConfiguracion::whereIn('clave', ['igv', 'porcentaje_indeterminado'])->pluck('valor', 'clave');
        
        $igv = floatval($config['igv'] ?? 18.00);
        $porcentajeIndeterminado = floatval($config['porcentaje_indeterminado'] ?? 5.00); // El 5% del PDF
        return view('contenido.calculadoras.junta', compact('data', 'igv', 'porcentajeIndeterminado'));
    }

    
}
