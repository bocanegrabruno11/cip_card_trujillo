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
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function calculadoraArbitraje()
    {
        // Traemos SOLO las tarifas activas de arbitraje
        $escalas = TarifaEscala::where('activo', true)
            ->where('tipo_calculadora', 'servicio_arbitral')
            ->get();

        // Organizamos los datos para JS
        $data = [
            'unico'    => $escalas->where('tipo', 'arbitro_unico')->values(),
            'tribunal' => $escalas->where('tipo', 'tribunal_arbitral')->values(),
            'gastos'   => $escalas->where('tipo', 'gastos_administrativos')->values(),
        ];

        // Obtenemos el IGV (o usamos 18% por defecto)
        $configIgv = TarifaConfiguracion::where('clave', 'igv')->first();
        $igv = $configIgv ? floatval($configIgv->valor) : 18.00;

        return view('contenido.calculadoras.arbitraje', compact('data', 'igv'));
    }

 

    public function calcJunta() {

    // 1. Obtener tarifas activas SOLO de JPRD
    $escalas = TarifaEscala::where('activo', true)
        ->where('tipo_calculadora', 'junta_prevencion')
        ->get();

    // 2. Agrupar por tipo para facilitar el uso en JS
    $data = [
        'administrativos' => $escalas->where('tipo', 'gastos_administrativos')->values(),
        'unico'           => $escalas->where('tipo', 'arbitro_unico')->values(),
        'tribunal'        => $escalas->where('tipo', 'tribunal_arbitral')->values(),
    ];

    // 3. Obtener IGV global
    $configIgv = TarifaConfiguracion::where('clave', 'igv')->first();
    $igv = $configIgv ? floatval($configIgv->valor) : 18.00;

    return view('contenido.calculadoras.junta', compact('data', 'igv'));
    }
    public function exportarPdfJunta(Request $request)
    {
        $monto = floatval($request->input('monto'));
        $tipo = $request->input('tipo_miembro'); // 'unico' o 'tribunal'
        
        // 1. Obtener Tarifas (Reutilizamos lógica)
        $escalas = TarifaEscala::where('activo', true)
            ->where('tipo_calculadora', 'junta_prevencion')
            ->get();

        // 2. Lógica de Cálculo (Réplica del JS para seguridad)
        
        // A. Tasa Administrativa
        $tarifaAdmin = $escalas->where('tipo', 'gastos_administrativos')
            ->where('monto_min', '<=', $monto)
            ->filter(function($item) use ($monto) {
                return $item->monto_max === null || $monto <= $item->monto_max;
            })->first();
        $gastosAdmin = $tarifaAdmin ? $tarifaAdmin->monto_fijo : 0;

        // B. Honorarios
        $honorariosUnitario = 0;
        $honorariosTotal = 0;
        $rangoDetectado = $tarifaAdmin ? $tarifaAdmin->rango_letra : '-';
        $mensajeError = null;

        if ($tipo === 'unico') {
            $tarifaUnico = $escalas->where('tipo', 'arbitro_unico')
                ->where('monto_min', '<=', $monto)
                ->where('monto_max', '>=', $monto)
                ->first();
                
            if ($tarifaUnico) {
                $honorariosUnitario = $tarifaUnico->monto_fijo;
                $honorariosTotal = $honorariosUnitario; // Solo 1
            } else {
                $mensajeError = ($monto > 40000000) ? 'Corresponde Tribunal' : 'Fuera de rango';
            }
        } else { // Tribunal
            $tarifaTrib = $escalas->where('tipo', 'tribunal_arbitral')
                ->where('monto_min', '<=', $monto)
                ->filter(function($item) use ($monto) {
                    return $item->monto_max === null || $monto <= $item->monto_max;
                })->first();

            if ($tarifaTrib) {
                $honorariosUnitario = $tarifaTrib->monto_fijo;
                $honorariosTotal = $honorariosUnitario * 3;
            } else {
                $mensajeError = ($monto <= 40000000) ? 'Corresponde Miembro Único' : 'Fuera de rango';
            }
        }

        // 3. Obtener IGV
        $configIgv = TarifaConfiguracion::where('clave', 'igv')->first();
        $igv = $configIgv ? floatval($configIgv->valor) : 18.00;

        // 4. Generar PDF
        $data = [
            'monto' => $monto,
            'tipo' => $tipo,
            'rango' => $rangoDetectado,
            'gastosAdmin' => $gastosAdmin,
            'honoUnitario' => $honorariosUnitario,
            'honoTotal' => $honorariosTotal,
            'igv' => $igv,
            'error' => $mensajeError,
            'fecha' => date('d/m/Y H:i')
        ];

        $pdf = Pdf::loadView('contenido.calculadoras.pdf-junta', $data);
        return $pdf->download('Resultados_Calculadora_JRD_CARD_'.date('Y-m-d_H-i-s').'.pdf');
    }

    public function exportarPdfArbitraje(Request $request)
    {
        $montoInput = floatval($request->input('monto'));
        $tipoCuantia = $request->input('tipo_cuantia'); // 'determinada' o 'indeterminada'
        $tipoOrgano = $request->input('tipo_organo'); // 'unico' o 'tribunal'
        $cantidad = intval($request->input('cantidad_pretensiones'));
        
        // Validación básica
        if ($cantidad < 1) $cantidad = 1;

        // 1. Determinar base de cálculo (Lógica del 4%)
        $montoCalculo = $montoInput;
        if ($tipoCuantia === 'indeterminada') {
            $montoCalculo = $montoInput * 0.04;
        }

        // 2. Obtener Tarifas
        $escalas = TarifaEscala::where('activo', true)
            ->where('tipo_calculadora', 'servicio_arbitral')
            ->get();

        // Función auxiliar para calcular (reutilizable)
        $calcular = function($monto, $tipoTabla) use ($escalas) {
            // Buscar rango
            $rango = $escalas->where('tipo', $tipoTabla)
                ->where('monto_min', '<=', $monto)
                ->filter(function($item) use ($monto) {
                    return $item->monto_max === null || $monto <= $item->monto_max;
                })->first();

            if (!$rango) return 0;
            
            // Caso "A criterio"
            if ($rango->monto_fijo == 0 && $rango->porcentaje_exceso == 0) return -1;

            // Fórmula: Fijo + ((Monto - BaseExceso) * %)
            $variable = 0;
            if ($rango->porcentaje_exceso > 0) {
                $variable = ($monto - $rango->base_exceso) * ($rango->porcentaje_exceso / 100);
            }
            return $rango->monto_fijo + $variable;
        };

        // 3. Ejecutar Cálculos Base
        $tablaHonorarios = ($tipoOrgano === 'unico') ? 'arbitro_unico' : 'tribunal_arbitral';
        
        $honorariosBase = $calcular($montoCalculo, $tablaHonorarios);
        $gastosBase = $calcular($montoCalculo, 'gastos_administrativos');

        // 4. Aplicar Multiplicador (Solo si es indeterminada)
        $honorariosFinal = ($honorariosBase === -1) ? -1 : $honorariosBase;
        $gastosFinal = ($gastosBase === -1) ? -1 : $gastosBase;

        if ($tipoCuantia === 'indeterminada') {
            if ($honorariosFinal !== -1) $honorariosFinal *= $cantidad;
            if ($gastosFinal !== -1) $gastosFinal *= $cantidad;
        }

        // 5. Configuración IGV
        $configIgv = TarifaConfiguracion::where('clave', 'igv')->first();
        $igv = $configIgv ? floatval($configIgv->valor) : 18.00;

        // 6. Generar PDF
        $data = [
            'montoInput' => $montoInput,
            'montoCalculo' => $montoCalculo, // El valor del 4% (o el original)
            'tipoCuantia' => $tipoCuantia,
            'tipoOrgano' => $tipoOrgano,
            'cantidad' => $cantidad,
            'honorariosTotal' => $honorariosFinal,
            'gastosTotal' => $gastosFinal,
            'igv' => $igv,
            'fecha' => date('d/m/Y H:i')
        ];

        $pdf = Pdf::loadView('contenido.calculadoras.pdf-arbitraje', $data);
        return $pdf->download('Resultados_Calculadora_Arbitraje_CARD_'.date('Y-m-d_H-i-s').'.pdf');
    }

}
