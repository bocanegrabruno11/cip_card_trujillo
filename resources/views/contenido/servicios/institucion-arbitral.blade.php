@extends('inicio')

@section('title', 'Institución Arbitral - CARD CD La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .institucion-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #f9f9f9;
        min-height: 80vh;
    }

    .page-header { text-align: center; margin-bottom: 50px; }
    .page-header h1 { color: #333; font-size: 32px; font-weight: 700; margin: 0; }

    /* === LAYOUT PESTAÑAS (TABS) === */
    .tabs-wrapper {
        display: flex;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        min-height: 600px;
    }

    /* Menú Lateral */
    .tabs-nav {
        width: 300px;
        background-color: #f4f4f4;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .tab-btn {
        padding: 18px 25px;
        text-align: left;
        background: none;
        border: none;
        border-bottom: 1px solid #e0e0e0;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #555;
        transition: all 0.3s;
        text-transform: uppercase;
        outline: none;
    }

    .tab-btn:hover { background-color: #eaeaea; color: #333; }

    .tab-btn.active {
        background-color: #fff;
        color: #AD2B2E;
        border-left: 4px solid #AD2B2E;
        margin-right: -1px;
        border-right: 1px solid #fff;
    }

    /* Contenido */
    .tabs-content { 
        flex: 1; 
        padding: 40px; 
        background-color: #fff; 
        overflow-x: hidden; 
        
        /* === NUEVO: SCROLL VERTICAL === */
        max-height: 600px; /* Define la altura fija del cuadro */
        overflow-y: auto;  /* Activa el scroll si el contenido excede la altura */
        position: relative; /* Asegura que el contenido se mantenga en flujo */
    }
    .tabs-content::-webkit-scrollbar { width: 8px; }
    .tabs-content::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .tabs-content::-webkit-scrollbar-thumb { background: #AD2B2E; border-radius: 4px; }
    .tabs-content::-webkit-scrollbar-thumb:hover { background: #8f2225; }
    .tab-pane { display: none; animation: fadeIn 0.5s; }
    .tab-pane.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* === ESTILOS INTERNOS === */
    .presentacion-layout { display: flex; gap: 30px; align-items: center; }
    .presentacion-text { flex: 1; font-size: 15px; color: #444; line-height: 1.8; text-align: justify; }
    .presentacion-img { width: 100%; max-width: 400px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

    /* Títulos de Sección */
    .normativa-section-title {
        font-size: 16px; font-weight: bold; color: #333; text-transform: uppercase;
        text-align: center; margin: 30px 0 20px;
        display: flex; align-items: center; justify-content: center; gap: 15px;
    }
    .normativa-section-title::before, .normativa-section-title::after {
        content: ''; height: 1px; background: #AD2B2E; flex: 1; max-width: 100px;
    }

    /* === NUEVO: GRID PARA DOCUMENTOS (2 Columnas) === */
    .docs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Dos columnas iguales */
        gap: 15px 25px; /* Espacio vertical 15px, horizontal 25px */
        margin-bottom: 20px;
    }

    .doc-item {
        display: flex;
        flex-direction: column;
    }

    /* === BOTONES DE DOCUMENTOS === */
    .doc-btn {
        display: flex;
        align-items: center;
        background-color: #AD2B2E;
        color: white !important;
        padding: 12px 18px;
        border-radius: 6px;
        text-decoration: none;
        margin-bottom: 5px;
        transition: background 0.3s, transform 0.2s;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 13px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        width: 100%;
        border: none;
        height: 100%; /* Para que tengan la misma altura si el texto es largo */
    }
    .doc-btn:hover { 
        background-color: #8f2225; 
        transform: translateY(-2px);
    }
    .doc-btn i { font-size: 18px; margin-right: 12px; flex-shrink: 0; }

    .doc-meta { 
        font-size: 11px; 
        color: #666; 
        font-style: italic; 
        margin-left: 5px; 
        margin-bottom: 5px;
    }
    
    .empty-msg { 
        color: #888; 
        font-style: italic; 
        margin-top: 10px; 
        text-align: center; 
        grid-column: 1 / -1; /* Ocupar todo el ancho en grid */
    }

    /* === BOTONES CALCULADORA === */
    .btn-calc-red {
        display: block;
        width: 100%;
        background-color: #AD2B2E !important;
        color: white !important;
        border: none;
        padding: 18px 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(173, 43, 46, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        line-height: 1.4;
        white-space: normal;
    }
    .btn-calc-red:hover {
        background-color: #801a1d !important;
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(173, 43, 46, 0.4);
    }

    /* === CARRUSEL ÁRBITROS PAGINADO === */
    .carousel-wrapper-paginated {
        position: relative;
        padding: 0 50px;
        margin-top: 30px;
        min-height: 400px;
        display: flex; align-items: center;
    }
    .carousel-pages-container { width: 100%; position: relative; }
    
    .carousel-page {
        display: none;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px 20px;
        justify-items: center;
        animation: fadeInPage 0.4s ease-in-out;
        width: 100%;
    }
    .carousel-page.active-page { display: grid; }

    @keyframes fadeInPage { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }

    /* Tarjeta Árbitro */
    .arbitro-card { width: 140px; display: flex; flex-direction: column; align-items: center; cursor: pointer; transition: transform 0.3s; text-align: center; }
    .arbitro-card:hover { transform: translateY(-5px); }
    .arbitro-img-wrapper { width: 120px; height: 120px; border-radius: 50%; overflow: hidden; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 4px solid #fff; background-color: #eee; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
    .arbitro-img { width: 100%; height: 100%; object-fit: cover; }
    .initials-circle { width: 100%; height: 100%; background-color: #AD2B2E; color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; text-transform: uppercase; }
    .arbitro-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(173, 43, 46, 0.9); color: white; display: flex; align-items: center; justify-content: center; text-align: center; padding: 5px; font-size: 11px; font-weight: bold; opacity: 0; transition: opacity 0.3s; text-transform: uppercase; line-height: 1.2; }
    .arbitro-card:hover .arbitro-overlay { opacity: 1; }
    .arbitro-name-label { font-size: 13px; color: #333; font-weight: 600; line-height: 1.2; }

    /* Botones Navegación Carrusel */
    .nav-arrow { position: absolute; top: 50%; transform: translateY(-50%); background: #fff; border: 1px solid #ddd; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #AD2B2E; box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 10; transition: all 0.2s; }
    .nav-arrow:hover { background: #AD2B2E; color: white; }
    .nav-arrow:disabled { opacity: 0.3; cursor: not-allowed; background: #eee; color: #aaa; }
    .prev-page { left: 0; }
    .next-page { right: 0; }
    .carousel-indicators { display: flex; justify-content: center; gap: 8px; margin-top: 20px; }
    .indicator-dot { width: 10px; height: 10px; border-radius: 50%; background-color: #ddd; transition: background-color 0.3s; }
    .indicator-dot.active { background-color: #AD2B2E; }

    /* === MODAL PERFIL PROFESIONAL (NUEVO DISEÑO) === */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .custom-modal.show { opacity: 1; }

    .modal-content {
        background-color: #fff;
        border-radius: 8px;
        width: 95%;
        max-width: 800px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        display: flex;
        flex-direction: row;
        overflow: hidden;
        position: relative;
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .custom-modal.show .modal-content { transform: scale(1); }

    /* LADO IZQUIERDO (FOTO) */
    .modal-left {
        width: 35%;
        background-color: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        border-right: 1px solid #e0e0e0;
    }
    
    .modal-img-large {
        width: 100%; max-width: 200px; aspect-ratio: 1/1;
        object-fit: cover; border-radius: 50%;
        border: 5px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .modal-initials-large {
        width: 180px; height: 180px; background-color: #AD2B2E;
        color: white; border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        font-size: 60px; font-weight: 800; border: 5px solid white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    /* LADO DERECHO (DATOS) */
    .modal-right {
        width: 65%;
        padding: 40px 30px;
        display: flex; flex-direction: column;
        justify-content: center; text-align: left;
    }

    /* BOTÓN CERRAR FLOTANTE */
    .close-btn-float {
        position: absolute; top: 15px; right: 15px;
        width: 35px; height: 35px; background: #f1f1f1;
        color: #333; border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        cursor: pointer; font-size: 20px; transition: all 0.2s;
        border: none; z-index: 10;
    }
    .close-btn-float:hover { background: #AD2B2E; color: white; }

    /* TIPOGRAFÍA */
    .modal-name-title {
        font-size: 26px; font-weight: 800; color: #333;
        line-height: 1.2; margin-bottom: 5px;
    }

    .modal-cargo-badge {
        display: inline-block; background-color: #AD2B2E;
        color: white; padding: 5px 12px; border-radius: 20px;
        font-size: 12px; font-weight: bold; text-transform: uppercase;
        margin-bottom: 15px; align-self: flex-start;
    }

    /* LISTA DE DATOS */
    .modal-data-list { margin-top: 15px; width: 100%; }

    .data-row {
        display: flex; align-items: flex-start;
        margin-bottom: 12px; font-size: 14px; color: #555;
    }
    .data-icon {
        width: 25px; color: #AD2B2E; margin-right: 10px;
        text-align: center; font-size: 16px; margin-top: 2px;
    }
    .data-content strong {
        display: block; font-size: 11px; color: #999;
        text-transform: uppercase; margin-bottom: 2px;
    }
    .data-text { font-weight: 500; color: #333; word-break: break-word; }

    /* BOTÓN CV */
    .btn-download-cv {
        margin-top: 25px; display: inline-flex; align-items: center;
        justify-content: center; background-color: #333;
        color: white !important; padding: 12px 25px; border-radius: 6px;
        text-decoration: none; font-weight: bold; transition: background 0.3s;
        align-self: flex-start;
    }
    .btn-download-cv:hover { background-color: #AD2B2E; }
    .btn-download-cv i { margin-right: 10px; }

    /* RESPONSIVE */
    @media (max-width: 700px) {
        .modal-content { flex-direction: column; overflow-y: auto; max-height: 90vh; }
        .modal-left { width: 100%; padding: 30px 20px 20px; border-right: none; border-bottom: 1px solid #eee; }
        .modal-right { width: 100%; padding: 25px 20px; align-items: center; text-align: center; }
        .modal-img-large, .modal-initials-large { width: 140px; height: 140px; font-size: 50px; }
        .modal-name-title { font-size: 22px; }
        .modal-cargo-badge { align-self: center; }
        .data-row { justify-content: center; text-align: left; }
        .btn-download-cv { align-self: center; }
    }
    @keyframes modalSlideIn { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .tabs-wrapper { flex-direction: column; }
        .tabs-nav { width: 100%; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid #e0e0e0; }
        .tab-btn { white-space: nowrap; border-bottom: 4px solid transparent; border-left: none; padding: 15px; font-size: 13px; }
        .tab-btn.active { border-left: none; border-bottom-color: #AD2B2E; background-color: #fff; }
        .presentacion-layout { flex-direction: column; }
        .presentacion-img { max-width: 100%; }
        
        /* Ajuste Grid Documentos */
        .docs-grid { grid-template-columns: 1fr; }

        /* === AJUSTE NOMINA ARBITROS MOVIL === */
        .carousel-wrapper-paginated {
            padding: 0 35px; /* Reducimos padding lateral para ganar espacio */
        }

        .carousel-page { 
            grid-template-columns: repeat(2, 1fr); /* 2 columnas por defecto en tablet/movil horizontal */
            gap: 20px 10px; /* Gap vertical 20, horizontal 10 */
            justify-content: center; /* Centrar el grid */
        }

        .arbitro-card {
            width: 100%; /* Que ocupe el ancho de su celda */
            max-width: 130px; /* Límite para que no se vea gigante */
            margin: 0 auto; /* Centrado en su celda */
        }
    }
    @media (max-width: 480px) {
        .carousel-page {
            /* En pantallas muy angostas, también 2 columnas pero con ajustes finos */
            grid-template-columns: repeat(2, 1fr); 
            gap: 15px 5px;
        }
        
        .arbitro-img-wrapper {
            width: 100px; /* Reducir un poco la foto */
            height: 100px;
        }
        
        .initials-circle { font-size: 32px; }
        
        .nav-arrow {
            width: 30px; height: 30px; /* Flechas más pequeñas */
            font-size: 12px;
        }
        
        .carousel-wrapper-paginated {
            padding: 0 25px; /* Más espacio para contenido */
        }
    }
    .repo-modal-overlay {
        display: none; /* Oculto por defecto */
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.6); /* Fondo oscuro semitransparente */
        backdrop-filter: blur(3px);
        z-index: 20000; /* Por encima de todo */
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .repo-modal-overlay.show {
        display: flex;
        opacity: 1;
    }

    .repo-modal-box {
        background: #fff;
        width: 90%;
        max-width: 480px; /* Ancho ideal para formularios */
        border-radius: 12px;
        padding: 35px 30px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        position: relative;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .repo-modal-overlay.show .repo-modal-box {
        transform: scale(1);
    }

    /* Botón Cerrar (X) Flotante */
    .repo-close-btn {
        position: absolute;
        top: -15px;
        right: -15px;
        width: 36px;
        height: 36px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 50%;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: all 0.2s;
        color: #555;
    }
    .repo-close-btn:hover {
        background: #AD2B2E;
        color: white;
        border-color: #AD2B2E;
        transform: rotate(90deg);
    }

    /* Tipografía */
    .repo-title {
        text-align: center;
        font-size: 22px;
        font-weight: 800;
        color: #333;
        margin: 0 0 10px 0;
    }
    .repo-subtitle {
        text-align: center;
        font-size: 13px;
        color: #666;
        margin-bottom: 25px;
        line-height: 1.4;
    }

    /* Formulario */
    .repo-form-group {
        margin-bottom: 18px;
    }
    .repo-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #444;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
    .repo-form-group input[type="text"],
    .repo-form-group input[type="email"],
    .repo-form-group input[type="file"] {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .repo-form-group input:focus {
        border-color: #AD2B2E;
        outline: none;
        box-shadow: 0 0 0 3px rgba(173, 43, 46, 0.1);
    }
    
    /* Input de solo lectura (gris) */
    .readonly-input {
        background-color: #f2f2f2;
        color: #666;
        cursor: not-allowed;
    }

    .repo-help-text {
        font-size: 11px;
        color: #888;
        margin-top: 4px;
    }

    /* Botón Submit Rojo */
    .repo-submit-btn {
        width: 100%;
        background-color: #AD2B2E;
        color: white;
        border: none;
        padding: 14px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
        margin-top: 10px;
        box-shadow: 0 4px 6px rgba(173, 43, 46, 0.2);
    }
    .repo-submit-btn:hover {
        background-color: #8f2225;
        transform: translateY(-2px);
    }
    .repo-submit-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
</style>
@endsection

@section('content')

<div class="institucion-container">
    <div class="page-header">
        <h1>Institución Arbitral</h1>
    </div>

    <div class="tabs-wrapper">
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="openTab(event, 'presentacion')">Presentación</button>
            <button class="tab-btn" onclick="openTab(event, 'normativa')">Normativa</button>
            <button class="tab-btn" onclick="openTab(event, 'tarifario')">Tarifario y Calculadora</button>
            <button class="tab-btn" onclick="openTab(event, 'incorporacion')">Incorporación</button>
            <button class="tab-btn" onclick="openTab(event, 'nomina')">Nómina de Árbitros</button>
            <button class="tab-btn" onclick="openTab(event, 'requisitos')">Requisitos Árbitros</button>
            <button class="tab-btn" onclick="openTab(event, 'solicitar')">Solicitar el Servicio</button>
            <button class="tab-btn" onclick="openTab(event, 'repositorio')">Repositorio de Laudos</button>
        </div>

        <div class="tabs-content">
            
            {{-- 1. PRESENTACIÓN --}}
            <div id="presentacion" class="tab-pane active">
                <div class="presentacion-layout">
                    <div class="presentacion-text">
                        <p>El Centro de Arbitraje y Resolución de Disputas (CARD) del Consejo Departamental de La Libertad del Colegio de Ingenieros del Perú fue creado con el firme propósito de servir a la comunidad. Como órgano especializado de nuestra institución, somos los encargados de la administración y organización de arbitrajes institucionales, garantizando imparcialidad, eficiencia y transparencia en la resolución de controversias.</p>
                    </div>
                    <img src="{{ asset('img/main-site/2.jpg') }}" alt="Sala" class="presentacion-img">
                </div>
            </div>

            {{-- 2. NORMATIVA --}}
            <div id="normativa" class="tab-pane">
                <div class="normativa-section-title">DOCUMENTOS DE NORMATIVA</div>
                
                {{-- GRID DE DOCUMENTOS --}}
                <div class="docs-grid">
                    @php $docs = $docsPorCategoria->get('normativa', collect()); @endphp
                    @forelse($docs as $doc)
                        <div class="doc-item">
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-file-pdf"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                        </div>
                    @empty
                        <p class="empty-msg">No hay documentos normativos publicados.</p>
                    @endforelse
                </div>
            </div>

            {{-- 3. TARIFARIO Y CALCULADORA --}}
            <div id="tarifario" class="tab-pane">
                <div class="normativa-section-title">TARIFARIO Y CALCULADORA</div>
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="mb-3 text-center"><img src="{{ asset('img/cdlima_encabezado.jpg') }}" style="max-width: 100%; border-radius: 4px;"></div>
                        <div class="card shadow-sm border-0 bg-white">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-1 text-dark">Calculadoras del Centro de Arbitraje</h5>
                                <p class="text-muted small mb-4">CD La Libertad</p>
                                <button onclick="openCalculator('{{ route('calc.inst.det') }}')" class="btn-calc-red">CALCULADORA PARA CUANTÍA DETERMINADA</button>
                                <button onclick="openCalculator('{{ route('calc.inst.indet') }}')" class="btn-calc-red">CALCULADORA PARA CUANTÍA INDETERMINADA</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-5">
                        <h6 class="text-muted fw-bold mb-3 border-bottom pb-2 text-uppercase">Normativa Vigente</h6>
                        {{-- GRID DE DOCUMENTOS (Aquí también aplicamos el grid de 2 col) --}}
                        <div class="docs-grid">
                            @php $docs = $docsPorCategoria->get('tarifario', collect()); @endphp
                            @forelse($docs as $doc)
                                <div class="doc-item">
                                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                        <i class="fas fa-file-pdf"></i> {{ $doc->titulo }}
                                    </a>
                                    <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                                </div>
                            @empty
                                <p class="empty-msg">No hay tarifarios disponibles.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. INCORPORACION (SOLO DOCUMENTOS) --}}
            <div id="incorporacion" class="tab-pane">
                <div class="normativa-section-title">DOCUMENTOS DE INCORPORACIÓN</div>
                
                {{-- GRID DE DOCUMENTOS --}}
                <div class="docs-grid">
                    @php $docs = $docsPorCategoria->get('incorporacion', collect()); @endphp
                    @forelse($docs as $doc)
                        <div class="doc-item">
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-file-contract"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                        </div>
                    @empty
                        <p class="empty-msg">No hay documentos disponibles.</p>
                    @endforelse
                </div>
            </div>

            {{-- 5. NÓMINA DE ÁRBITROS --}}
            <div id="nomina" class="tab-pane">
                <div class="normativa-section-title">NÓMINA DE ÁRBITROS</div>
                @if(isset($arbitrosNomina) && $arbitrosNomina->count() > 0)
                    <div class="carousel-wrapper-paginated">
                        <button class="nav-arrow prev-page" onclick="changeArbitroPage(-1)" id="btnPrevArb" disabled><i class="fas fa-chevron-left"></i></button>
                        <button class="nav-arrow next-page" onclick="changeArbitroPage(1)" id="btnNextArb"><i class="fas fa-chevron-right"></i></button>

                        <div class="carousel-pages-container">
                            @foreach($arbitrosNomina->chunk(8) as $index => $grupo)
                                <div class="carousel-page {{ $index === 0 ? 'active-page' : '' }}" data-page="{{ $index }}">
                                    @foreach($grupo as $arb)
                                        <div class="arbitro-card" onclick="openModal(this)"
                                             data-name="{{ $arb->nombres }}"
                                             data-role="Árbitro"
                                             data-code="{{ $arb->codigo ?? '' }}"
                                             data-specialty="{{ $arb->especialidad ?? '' }}"
                                             data-email="{{ $arb->email ?? '' }}"
                                             data-phone="{{ $arb->telefono ?? '' }}"
                                             data-cv="{{ $arb->ruta_cv ? asset('storage/' . $arb->ruta_cv) : '' }}"
                                             data-img="{{ $arb->ruta_imagen ? asset('storage/' . $arb->ruta_imagen) : '' }}">
                                            
                                            <div class="arbitro-img-wrapper">
                                                @if($arb->ruta_imagen)
                                                    <img src="{{ asset('storage/' . $arb->ruta_imagen) }}" class="arbitro-img">
                                                @else
                                                    <div class="initials-circle">{{ substr($arb->nombres, 0, 1) }}</div>
                                                @endif
                                                <div class="arbitro-overlay">{{ $arb->nombres }}</div>
                                            </div>
                                            <div class="arbitro-name-label">{{ $arb->nombres }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if($arbitrosNomina->count() > 8)
                        <div class="carousel-indicators">
                            @foreach($arbitrosNomina->chunk(8) as $index => $grupo)
                                <div class="indicator-dot {{ $index === 0 ? 'active' : '' }}" id="dot-{{ $index }}"></div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="empty-msg">No hay árbitros registrados en la nómina.</p>
                @endif
            </div>

            {{-- 6. REQUISITOS --}}
            <div id="requisitos" class="tab-pane">
                <div class="normativa-section-title">REQUISITOS</div>
                
                {{-- GRID DE DOCUMENTOS --}}
                <div class="docs-grid">
                    @php $docs = $docsPorCategoria->get('requisitos', collect()); @endphp
                    @forelse($docs as $doc)
                        <div class="doc-item">
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-list-check"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">{{ $doc->descripcion }}</div>
                        </div>
                    @empty
                        <p class="empty-msg">Pendiente.</p>
                    @endforelse
                </div>
            </div>

            {{-- 7. SOLICITAR --}}
            <div id="solicitar" class="tab-pane">
                <div class="normativa-section-title">SOLICITAR</div>
                
                {{-- GRID DE DOCUMENTOS --}}
                <div class="docs-grid">
                    @php $docs = $docsPorCategoria->get('solicitar', collect()); @endphp
                    @forelse($docs as $doc)
                        <div class="doc-item">
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-file-signature"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">Publicado: {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                        </div>
                    @empty
                        <p class="empty-msg">No hay formularios.</p>
                    @endforelse
                </div>
            </div>

            {{-- 8. REPOSITORIO --}}
            <div id="repositorio" class="tab-pane">
                <div class="normativa-section-title">REPOSITORIO DE LAUDOS</div>
                
                <div style="text-align: center; padding: 40px 20px; max-width: 700px; margin: 0 auto;">
                    <p class="text-muted mb-4" style="line-height: 1.6;">
                        El repositorio de laudos arbitrales es un espacio de acceso restringido. 
                        Si usted es parte de un proceso o un usuario autorizado, verifique su acceso a continuación.
                        De lo contrario, deberá solicitar el permiso completando el formulario.
                    </p>

                    {{-- Botón con lógica JS --}}
                    <button type="button" onclick="handleRepoAccess()" class="doc-btn" style="justify-content: center; max-width: 400px; margin: 0 auto; padding: 15px 30px; font-size: 15px;">
                        <i class="fab fa-google-drive"></i> ACCEDER AL REPOSITORIO
                    </button>

                    <p class="small text-muted mt-3">
                        <i class="fas fa-lock me-1"></i> Acceso gestionado por el administrador del sistema.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL DETALLE --}}
{{-- MODAL PERFIL PROFESIONAL --}}
<div id="personModal" class="custom-modal">
    <div class="modal-content">
        <button class="close-btn-float" onclick="closeModal()">&times;</button>
        
        <div class="modal-left">
            <img id="mImg" src="" class="modal-img-large" style="display: none;">
            <div id="mInitials" class="modal-initials-large" style="display: none;"></div>
        </div>

        <div class="modal-right">
            <h2 id="mName" class="modal-name-title"></h2>
            
            <div id="mCargoGroup" style="display: none;">
                <span id="mCargo" class="modal-cargo-badge"></span>
            </div>

            <div class="modal-data-list">
                <div id="mCodeGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-id-card"></i></div>
                    <div class="data-content">
                        <strong>Registro / Código</strong>
                        <span id="mCode" class="data-text"></span>
                    </div>
                </div>

                <div id="mSpecialtyGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="data-content">
                        <strong>Especialidad</strong>
                        <span id="mSpecialty" class="data-text"></span>
                    </div>
                </div>

                <div id="mEmailGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-envelope"></i></div>
                    <div class="data-content">
                        <strong>Correo Electrónico</strong>
                        <span id="mEmail" class="data-text"></span>
                    </div>
                </div>

                <div id="mPhoneGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="data-content">
                        <strong>Teléfono / Contacto</strong>
                        <span id="mPhone" class="data-text"></span>
                    </div>
                </div>
            </div>

            <a id="mCvBtn" href="#" target="_blank" class="btn-download-cv" style="display: none;">
                <i class="fas fa-file-pdf"></i> Descargar Hoja de Vida
            </a>
        </div>
    </div>
</div>
{{-- MODAL SOLICITUD ACCESO REPOSITORIO (DISEÑO LIMPIO) --}}
{{-- MODAL SOLICITUD ACCESO REPOSITORIO --}}
<div id="repoModal" class="repo-modal-overlay">
    <div class="repo-modal-box">
        <button class="repo-close-btn" onclick="closeRepoModal()">&times;</button>
        
        <h3 class="repo-title">Solicitud de Acceso</h3>
        <p class="repo-subtitle">
            Complete sus datos para solicitar permiso a la carpeta de Laudos.
        </p>

        {{-- ZONA DE ALERTAS --}}
        @if(session('success'))
            <div class="alert alert-success text-center mb-3 p-2 small">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning text-center mb-3 p-2 small">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger text-center mb-3 p-2 small">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Errores de validación de Laravel --}}
        @if ($errors->any())
            <div class="alert alert-danger p-2 small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('solicitudes.store') }}" method="POST" enctype="multipart/form-data" id="formRepo">
            @csrf
            
            {{-- Nombres --}}
            <div class="repo-form-group">
                <label>Nombres Completos</label>
                <input type="text" name="nombres" required 
                       {{-- Usamos old() para no perder el dato si hay error --}}
                       value="{{ Auth::check() ? Auth::user()->name : old('nombres') }}" 
                       {{ Auth::check() ? 'readonly' : '' }}
                       class="{{ Auth::check() ? 'readonly-input' : '' }}"
                       oninput="this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, '').toUpperCase();">
            </div>

            {{-- Correo --}}
            <div class="repo-form-group">
                <label>Correo Electrónico (Gmail)</label>
                <input type="email" name="email" required 
                       value="{{ Auth::check() ? Auth::user()->email : old('email') }}" 
                       {{ Auth::check() ? 'readonly' : '' }}
                       class="{{ Auth::check() ? 'readonly-input' : '' }}">
                <div class="repo-help-text">Se dará acceso a este correo en Drive.</div>
            </div>

            {{-- DNI --}}
            <div class="repo-form-group">
                <label>Número de DNI</label>
                <input type="text" name="dni" required maxlength="8" pattern="[0-9]{8}" placeholder="8 dígitos" 
                       value="{{ old('dni') }}"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            {{-- Foto --}}
            <div class="repo-form-group">
                <label>Foto del DNI (Frontal)</label>
                <input type="file" name="foto_dni" accept="image/*" required class="file-input">
            </div>

            <button type="submit" id="btnRepoSubmit" class="repo-submit-btn">
                ENVIAR SOLICITUD
            </button>
        </form>
    </div>
</div>
@if(session('warning') || session('success') || session('error') || $errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Si hay una sesión con mensaje o errores, abrimos el modal automáticamente
        // para que el usuario vea el feedback
        openRepoModal();
    });
</script>
@endif
@endsection

@section('scripts')
<script>
    const DRIVE_LINK = "https://drive.google.com/drive/folders/1pql46UGfcFt7Z5lWvqCLIu5tzIVwN3cW?usp=drive_link";
    
    // Estado de la sesión y permisos (Pasados desde Laravel)
    const IS_LOGGED_IN = @json(Auth::check());
    // Si la variable no existe (fallback), asumimos false
    const HAS_PERMISSION = @json($tienePermisoRepo ?? false);

    // --- LÓGICA DE ACCESO AL REPOSITORIO ---
    function handleRepoAccess() {
        if (IS_LOGGED_IN) {
            if (HAS_PERMISSION) {
                // Caso 1: Logueado y Con Permiso -> Abrir Drive
                window.open(DRIVE_LINK, '_blank');
            } else {
                // Caso 2: Logueado pero SIN Permiso -> Mostrar Formulario
                openRepoModal();
            }
        } else {
            // Caso 3: No Logueado -> Mostrar Formulario (login/registro implícito o solicitud manual)
            openRepoModal();
        }
    }

    // --- CONTROL DEL MODAL REPOSITORIO ---
    function openRepoModal() {
        const modal = document.getElementById('repoModal');
        modal.style.display = 'flex'; // Necesario para que el flex funcione
        // Pequeño delay para permitir que el navegador renderice antes de la transición de opacidad
        requestAnimationFrame(() => {
            modal.classList.add('show');
        });
    }

    function closeRepoModal() {
        const modal = document.getElementById('repoModal');
        modal.classList.remove('show');
        setTimeout(() => { 
            modal.style.display = 'none'; 
        }, 300); // Esperar a que termine la transición CSS (0.3s)
    }   
    
    // Cerrar modal al hacer clic fuera (igual que el otro modal)
    window.addEventListener('click', function(event) {
        const repoModal = document.getElementById('repoModal');
        if (event.target == repoModal) {
            closeRepoModal();
        }
    });

    // --- ANIMACIÓN DE CARGA EN EL BOTÓN DEL FORMULARIO ---
    document.addEventListener('DOMContentLoaded', function() {
        const formRepo = document.getElementById('formRepo');
        const btnRepo = document.getElementById('btnRepoSubmit');

        if(formRepo && btnRepo) {
            formRepo.addEventListener('submit', function() {
                if(formRepo.checkValidity()) {
                    const width = btnRepo.offsetWidth;
                    btnRepo.style.width = width + 'px';
                    btnRepo.disabled = true;
                    btnRepo.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ENVIANDO...';
                }
            });
        }
    });
    // --- LÓGICA GLOBAL ---
    function openCalculator(url) {
        window.open(url, 'CalculadoraCIP', 'width=550,height=750,scrollbars=yes,resizable=yes');
    }

    function openTab(evt, tabName) {
        var i, tabPane, tabBtn;
        tabPane = document.getElementsByClassName("tab-pane");
        for (i = 0; i < tabPane.length; i++) {
            tabPane[i].classList.remove("active");
        }
        tabBtn = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tabBtn.length; i++) {
            tabBtn[i].className = tabBtn[i].className.replace(" active", "");
        }
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.className += " active";
    }

    function toggleDisplay(elementId, value) {
        const element = document.getElementById(elementId);
        if (value && value.trim() !== "") {
            element.style.display = 'flex';
            return true;
        } else {
            element.style.display = 'none';
            return false;
        }
    }

    function openModal(element) {
        // 1. Obtener datos
        const name = element.getAttribute('data-name');
        // Aquí usamos data-role porque es lo que viene en tu HTML actual
        const cargo = element.getAttribute('data-role'); 
        const img = element.getAttribute('data-img');
        const code = element.getAttribute('data-code');
        const specialty = element.getAttribute('data-specialty');
        const email = element.getAttribute('data-email');
        const phone = element.getAttribute('data-phone');
        const cv = element.getAttribute('data-cv');

        // 2. Llenar Nombre
        document.getElementById('mName').textContent = name;
        
        // 3. Imagen vs Iniciales
        const imgEl = document.getElementById('mImg');
        const initEl = document.getElementById('mInitials');
        
        if (img && img.trim() !== "") {
            imgEl.src = img;
            imgEl.style.display = 'block';
            initEl.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            initEl.textContent = name.charAt(0).toUpperCase();
            initEl.style.display = 'flex';
        }

        // 4. Llenar campos
        // Cargo (Badge Rojo)
        const cargoGroup = document.getElementById('mCargoGroup');
        if (cargo && cargo.trim() !== "") {
            document.getElementById('mCargo').textContent = cargo;
            cargoGroup.style.display = 'block';
        } else {
            cargoGroup.style.display = 'none';
        }

        // Código (Registro)
        const codeGroup = document.getElementById('mCodeGroup');
        if (code && code.trim() !== "") {
            document.getElementById('mCode').textContent = code;
            codeGroup.style.display = 'flex';
        } else {
            codeGroup.style.display = 'none';
        }

        // Resto de campos
        if(toggleDisplay('mSpecialtyGroup', specialty)) document.getElementById('mSpecialty').textContent = specialty;
        if(toggleDisplay('mEmailGroup', email)) document.getElementById('mEmail').textContent = email;
        if(toggleDisplay('mPhoneGroup', phone)) document.getElementById('mPhone').textContent = phone;

        // Botón CV
        const cvBtn = document.getElementById('mCvBtn');
        if (cv && cv.trim() !== "") {
            cvBtn.href = cv;
            cvBtn.style.display = 'inline-flex';
        } else {
            cvBtn.style.display = 'none';
        }

        // 5. Mostrar con animación
        const modal = document.getElementById('personModal');
        modal.style.display = 'flex';
        setTimeout(() => { modal.classList.add('show'); }, 10);
        document.body.style.overflow = 'hidden'; 
    }

    function closeModal() {
        const modal = document.getElementById('personModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }

    window.onclick = function(event) {
        const modal = document.getElementById('personModal');
        if (event.target == modal) closeModal();
    }

    // --- CARRUSEL PAGINADO ---
    let arbitroPageIdx = 0; 
    
    function updateArbitroPagination() {
        const pages = document.querySelectorAll('.carousel-page');
        const totalArbitroPages = pages.length;
        const prevBtn = document.getElementById('btnPrevArb');
        const nextBtn = document.getElementById('btnNextArb');
        const dots = document.querySelectorAll('.indicator-dot');

        pages.forEach((page, index) => {
            if(index === arbitroPageIdx) {
                page.classList.add('active-page');
            } else {
                page.classList.remove('active-page');
            }
        });

        if(dots.length > 0) {
            dots.forEach((dot, index) => {
                if(index === arbitroPageIdx) dot.classList.add('active');
                else dot.classList.remove('active');
            });
        }

        if(prevBtn) prevBtn.disabled = (arbitroPageIdx === 0);
        if(nextBtn) nextBtn.disabled = (arbitroPageIdx === totalArbitroPages - 1);
    }

    function changeArbitroPage(direction) {
        const pages = document.querySelectorAll('.carousel-page');
        const totalArbitroPages = pages.length;

        arbitroPageIdx += direction;

        if(arbitroPageIdx < 0) arbitroPageIdx = 0;
        if(arbitroPageIdx >= totalArbitroPages) arbitroPageIdx = totalArbitroPages - 1;

        updateArbitroPagination();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const pages = document.querySelectorAll('.carousel-page');
        if(pages.length > 0) {
            updateArbitroPagination();
        } else {
            const p = document.getElementById('btnPrevArb');
            const n = document.getElementById('btnNextArb');
            if(p) p.style.display = 'none';
            if(n) n.style.display = 'none';
        }
    });
</script>
@endsection