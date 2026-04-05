@extends('inicio')

@section('title', 'Convocatoria JRD - CARD CD La Libertad')

@section('styles')
<style>
    /* === FONDO DORADO GLOBAL === */
    .convocatoria-wrapper {
        /* Degradado similar al de la imagen (marrón dorado a dorado claro) */
        background: linear-gradient(135deg, #a67c37 0%, #cc9c48 50%, #916a2e 100%);
        min-height: 100vh;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        color: white;
    }

    .container-custom {
        max-width: 1100px;
        margin: 0 auto;
    }

    /* === ENCABEZADO === */
    .header-convocatoria {
        text-align: center;
        margin-bottom: 50px;
        text-transform: uppercase;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }

    .icon-top {
        background-color: #000;
        color: white;
        display: inline-block;
        padding: 5px 20px;
        border-radius: 20px;
        font-weight: bold;
        margin-bottom: 15px;
        font-size: 14px;
        letter-spacing: 1px;
    }

    .main-title {
        font-size: 38px;
        font-weight: 800;
        line-height: 1.2;
        margin: 0;
    }

    .subtitle {
        font-size: 28px;
        font-weight: 700;
        margin-top: 5px;
        opacity: 0.95;
    }

    /* === SECCIÓN CENTRAL (CALENDARIO Y BOTONES ESTÁTICOS) === */
    .content-grid {
        display: grid;
        grid-template-columns: 1.8fr 1fr; /* Izquierda más ancha */
        gap: 40px;
        margin-bottom: 50px;
    }

    /* Caja del Calendario */
    .calendar-box {
        background-color: #fff; /* Fondo blanco como la imagen */
        color: #333; /* Texto oscuro */
        padding: 30px;
        border-radius: 4px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .calendar-title {
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 20px;
        text-transform: uppercase;
        /* El título está fuera de la caja blanca en la imagen, ajustamos visualmente: */
        position: absolute;
        margin-top: -45px; 
    }

    .calendar-list {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 14px;
        line-height: 2.2;
    }

    .calendar-list li {
        position: relative;
        padding-left: 15px;
    }
    
    .calendar-list li::before {
        content: '•';
        color: #cc0000;
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    .date-highlight {
        color: #cc0000; /* Rojo para las fechas */
        font-weight: bold;
    }

    /* Botones Estáticos (Derecha) */
    .static-buttons-col {
        display: flex;
        flex-direction: column;
        gap: 15px;
        justify-content: center;
        border-left: 1px solid rgba(255,255,255,0.5);
        padding-left: 40px;
    }

    .btn-static {
        background: linear-gradient(to bottom, #444, #222);
        color: white;
        text-decoration: none;
        padding: 15px 20px;
        border-radius: 6px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        transition: transform 0.2s;
        border: 1px solid #555;
        font-size: 13px;
    }

    .btn-static:hover {
        transform: translateY(-2px);
        background: linear-gradient(to bottom, #555, #333);
        color: white;
    }

    /* === SECCIÓN INFERIOR (DINÁMICA - DOCUMENTOS) === */
    .dynamic-section {
        border-top: 1px solid rgba(255,255,255,0.3);
        padding-top: 40px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
    }

    .doc-wrapper {
        width: 100%;
        max-width: 700px;
        text-align: center;
    }

    .publish-date {
        font-size: 12px;
        font-style: italic;
        color: #fff; /* Blanco o amarillo muy claro */
        margin-bottom: 5px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .btn-dynamic {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background-color: #ff7f75; /* Color Salmon/Coral de la imagen */
        color: white;
        text-decoration: none;
        padding: 15px 30px;
        border-radius: 6px;
        font-weight: bold;
        text-transform: uppercase;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: background 0.3s;
        width: 100%;
        font-size: 14px;
        border: none;
    }

    .btn-dynamic:hover {
        background-color: #ff6b61;
        color: white;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .content-grid { grid-template-columns: 1fr; gap: 60px; }
        .static-buttons-col { border-left: none; padding-left: 0; }
        .calendar-title { position: static; margin-top: 0; color: #fff; margin-bottom: 10px; }
        .main-title { font-size: 28px; }
        .subtitle { font-size: 20px; }
    }
</style>
@endsection

@section('content')

<div class="convocatoria-wrapper">
    <div class="container-custom">
        
        {{-- ENCABEZADO --}}
        <div class="header-convocatoria">
            <div class="icon-top">
                <i class="fas fa-bullhorn me-2"></i> CONVOCATORIA
            </div>
            <h1 class="main-title">VI PROCESO DE SELECCIÓN DE</h1>
            <div class="subtitle">ADJUDICADORES DE JUNTA DE RESOLUCIÓN DE DISPUTAS</div>
        </div>

        {{-- CUERPO CENTRAL (Izquierda: Calendario | Derecha: Botones estáticos) --}}
        <div class="content-grid">
            
            {{-- COLUMNA IZQUIERDA: CALENDARIO (ESTÁTICO SEGÚN IMAGEN) --}}
            <div>
                <div class="calendar-title">
                    CALENDARIO DEL PROCESO DE POSTULACIÓN DE INGENIEROS Y ARQUITECTOS PARA LA SELECCIÓN EN LA LISTA DE JPRD DEL CARD CIP LIMA
                </div>
                <div class="calendar-box">
                    <ul class="calendar-list">
                        <li>Publicación de convocatoria para inscripción de participantes: <span class="date-highlight">24/03/2025</span></li>
                        <li>Presentación de consultas: <span class="date-highlight">28/03/2025 (hasta 7:00pm)</span></li>
                        <li>Absolución de consultas: <span class="date-highlight">03/04/2025</span></li>
                        <li>Presentación de solicitudes: <span class="date-highlight">10/04/2025 y 11/04/2025</span></li>
                        <li>Listado de postulantes aptos (resultado de evaluación): <span class="date-highlight">22/04/2025</span></li>
                        <li>Inicio del curso: <span class="date-highlight">07/05/2025</span></li>
                        <li>Término de presentaciones y evaluaciones: <span class="date-highlight">28/06/2025</span></li>
                        <li>Resultados finales: <span class="date-highlight">Del 30/06/2025 al 4/07/2025</span></li>
                    </ul>
                </div>
            </div>

            {{-- COLUMNA DERECHA: BOTONES ESTÁTICOS (DARK) --}}
            <div class="static-buttons-col">
                <a href="#" class="btn-static">
                    <i class="fas fa-file-alt"></i> BASES DEL PROCESO
                </a>
                <a href="#" class="btn-static">
                    <i class="fas fa-dollar-sign"></i> INVERSIÓN Y CURSO
                </a>
                <a href="#" class="btn-static">
                    <i class="fas fa-list-ol"></i> PROGRAMA
                </a>
                <a href="#" class="btn-static">
                    <i class="fas fa-folder-open"></i> ANEXOS - FORMATOS
                </a>
            </div>

        </div>

        {{-- SECCIÓN INFERIOR: DOCUMENTOS DINÁMICOS (COLOR SALMÓN) --}}
        <div class="dynamic-section">
            
            @forelse($documentos as $doc)
                <div class="doc-wrapper">
                    {{-- Fecha de publicación y descripción opcional pequeña arriba --}}
                    <div class="publish-date">
                        @if($doc->descripcion)
                            {{ $doc->descripcion }} <br>
                        @endif
                        Publicado el {{ \Carbon\Carbon::parse($doc->fecha_publicacion)->isoFormat('D [de] MMMM [de] YYYY') }}
                    </div>

                    {{-- Botón dinámico al archivo --}}
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="btn-dynamic">
                        <i class="fas fa-file-pdf"></i> {{ $doc->titulo }}
                    </a>
                </div>
            @empty
                <div class="text-white text-center fst-italic opacity-75">
                    No hay comunicados adicionales publicados por el momento.
                </div>
            @endforelse

        </div>

    </div>
</div>

@endsection