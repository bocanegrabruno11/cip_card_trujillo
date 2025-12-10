@extends('inicio')

@section('title', 'Junta de Prevención - CARD CD La Libertad')

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

    /* === CONTENIDO CON SCROLL === */
    .tabs-content { 
        flex: 1; 
        padding: 40px; 
        background-color: #fff; 
        overflow-x: hidden;
        
        /* Scroll Vertical */
        max-height: 600px;
        overflow-y: auto;
        position: relative;
    }

    /* Estilo del Scrollbar */
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

    /* === GRID PARA DOCUMENTOS (2 Columnas) === */
    .docs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px 25px;
        margin-bottom: 20px;
    }

    .doc-item { display: flex; flex-direction: column; }

    /* Botones Documentos */
    .doc-btn {
        display: flex; align-items: center;
        background-color: #AD2B2E; color: white !important;
        padding: 12px 18px; border-radius: 6px;
        text-decoration: none; margin-bottom: 5px;
        transition: background 0.3s, transform 0.2s;
        font-weight: bold; text-transform: uppercase;
        font-size: 13px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        width: 100%; border: none; height: 100%;
    }
    .doc-btn:hover { background-color: #8f2225; transform: translateY(-2px); }
    .doc-btn i { font-size: 18px; margin-right: 12px; flex-shrink: 0; }

    .doc-meta { font-size: 11px; color: #666; font-style: italic; margin-left: 5px; margin-bottom: 5px; }
    .empty-msg { color: #888; font-style: italic; margin-top: 10px; text-align: center; grid-column: 1 / -1; }

    /* === BOTONES CALCULADORA === */
    .btn-calc-red {
        display: block; width: 100%; background-color: #AD2B2E !important;
        color: white !important; border: none; padding: 18px 15px; margin-bottom: 15px;
        border-radius: 8px; font-weight: 800; text-transform: uppercase; font-size: 14px;
        box-shadow: 0 4px 10px rgba(173, 43, 46, 0.3); transition: all 0.3s ease;
        cursor: pointer; text-align: center; text-decoration: none; line-height: 1.4; white-space: normal;
    }
    .btn-calc-red:hover { background-color: #801a1d !important; transform: translateY(-3px); box-shadow: 0 6px 15px rgba(173, 43, 46, 0.4); }

    /* === CARRUSEL ADJUDICADORES PAGINADO === */
    .carousel-wrapper-paginated { position: relative; padding: 0 50px; margin-top: 30px; min-height: 400px; display: flex; align-items: center; }
    .carousel-pages-container { width: 100%; position: relative; }
    
    .carousel-page {
        display: none; grid-template-columns: repeat(4, 1fr);
        gap: 30px 20px; justify-items: center;
        animation: fadeInPage 0.4s ease-in-out; width: 100%;
    }
    .carousel-page.active-page { display: grid; }
    @keyframes fadeInPage { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }

    /* Tarjeta Adjudicador */
    .arbitro-card { width: 140px; display: flex; flex-direction: column; align-items: center; cursor: pointer; transition: transform 0.3s; text-align: center; }
    .arbitro-card:hover { transform: translateY(-5px); }
    .arbitro-img-wrapper { width: 120px; height: 120px; border-radius: 50%; overflow: hidden; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 4px solid #fff; background-color: #eee; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
    .arbitro-img { width: 100%; height: 100%; object-fit: cover; }
    .initials-circle { width: 100%; height: 100%; background-color: #AD2B2E; color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; text-transform: uppercase; }
    .arbitro-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(173, 43, 46, 0.9); color: white; display: flex; align-items: center; justify-content: center; text-align: center; padding: 5px; font-size: 11px; font-weight: bold; opacity: 0; transition: opacity 0.3s; text-transform: uppercase; line-height: 1.2; }
    .arbitro-card:hover .arbitro-overlay { opacity: 1; }
    .arbitro-name-label { font-size: 13px; color: #333; font-weight: 600; line-height: 1.2; }

    /* Navegación Carrusel */
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
        background-color: rgba(0,0,0,0.7); /* Fondo más oscuro */
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .custom-modal.show {
        opacity: 1; /* Para efecto fade-in */
    }

    .modal-content {
        background-color: #fff;
        border-radius: 8px;
        width: 95%;
        max-width: 800px; /* Mucho más ancho */
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        display: flex; /* Diseño flex horizontal */
        flex-direction: row; /* Izquierda | Derecha */
        overflow: hidden;
        position: relative;
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .custom-modal.show .modal-content {
        transform: scale(1);
    }

    /* COLUMNA IZQUIERDA: FOTO */
    .modal-left {
        width: 35%;
        background-color: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        border-right: 1px solid #e0e0e0;
        position: relative;
    }
    
    .modal-img-large {
        width: 100%;
        max-width: 200px;
        aspect-ratio: 1/1; /* Cuadrado perfecto */
        object-fit: cover;
        border-radius: 50%; /* Foto redonda */
        border: 5px solid white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .modal-initials-large {
        width: 180px;
        height: 180px;
        background-color: #AD2B2E;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        font-weight: 800;
        border: 5px solid white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    /* COLUMNA DERECHA: DATOS */
    .modal-right {
        width: 65%;
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
    }

    /* Botón cerrar flotante */
    .close-btn-float {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 35px;
        height: 35px;
        background: #f1f1f1;
        color: #333;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 20px;
        transition: all 0.2s;
        border: none;
        z-index: 10;
    }
    .close-btn-float:hover {
        background: #AD2B2E;
        color: white;
    }

    /* Tipografía */
    .modal-name-title {
        font-size: 26px;
        font-weight: 800;
        color: #333;
        line-height: 1.2;
        margin-bottom: 5px;
    }

    .modal-cargo-badge {
        display: inline-block;
        background-color: #AD2B2E;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 15px;
        align-self: flex-start;
    }

    /* Lista de datos */
    .modal-data-list {
        margin-top: 15px;
        width: 100%;
    }

    .data-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 12px;
        font-size: 14px;
        color: #555;
    }
    .data-icon {
        width: 25px;
        color: #AD2B2E;
        margin-right: 10px;
        text-align: center;
        font-size: 16px;
        margin-top: 2px; /* Alineación óptica */
    }
    .data-content strong {
        display: block;
        font-size: 11px;
        color: #999;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .data-text {
        font-weight: 500;
        color: #333;
        word-break: break-word;
    }

    /* Botón CV */
    .btn-download-cv {
        margin-top: 25px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #333;
        color: white !important;
        padding: 12px 25px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s;
        align-self: flex-start; /* Para que no ocupe todo el ancho */
    }
    .btn-download-cv:hover {
        background-color: #AD2B2E;
    }
    .btn-download-cv i { margin-right: 10px; }

    /* RESPONSIVE: En celular se apila */
    @media (max-width: 700px) {
        .modal-content { flex-direction: column; overflow-y: auto; max-height: 90vh; }
        .modal-left { width: 100%; padding: 30px 20px 20px; border-right: none; border-bottom: 1px solid #eee; }
        .modal-right { width: 100%; padding: 25px 20px; }
        .modal-img-large, .modal-initials-large { width: 140px; height: 140px; font-size: 50px; }
        .modal-name-title { font-size: 22px; text-align: center; }
        .modal-cargo-badge { align-self: center; margin-bottom: 20px; }
        .modal-right { align-items: center; text-align: center; } /* Centrar todo en móvil */
        .data-row { justify-content: center; text-align: left; }
    }
    .info-list { text-align: left; margin-top: 10px; font-size: 14px; color: #555; }
    .info-item { display: flex; align-items: center; margin-bottom: 8px; padding: 8px 10px; background-color: #f8f9fa; border-radius: 6px; }
    .info-icon { width: 25px; text-align: center; color: #AD2B2E; margin-right: 10px; }
    .info-text { flex: 1; font-weight: 500; word-break: break-word; }
    .info-label { font-size: 10px; color: #999; display: block; line-height: 1; margin-bottom: 2px; text-transform: uppercase; font-weight: 700; }
    .btn-cv-compact { display: block; width: 100%; background-color: #AD2B2E; color: white; padding: 10px 0; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 13px; margin-top: 20px; transition: background 0.3s; }
    .btn-cv-compact:hover { background-color: #8f2225; color: white; }
    @keyframes modalSlideIn { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .tabs-wrapper { flex-direction: column; }
        .tabs-nav { width: 100%; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid #e0e0e0; }
        .tab-btn { white-space: nowrap; border-bottom: 4px solid transparent; border-left: none; padding: 15px; font-size: 13px; }
        .tab-btn.active { border-left: none; border-bottom-color: #AD2B2E; background-color: #fff; }
        .presentacion-layout { flex-direction: column; }
        .presentacion-img { max-width: 100%; }
        
        .docs-grid { grid-template-columns: 1fr; }

        /* === AJUSTE NÓMINA ADJUDICADORES MÓVIL === */
        .carousel-wrapper-paginated {
            padding: 0 35px; /* Más espacio lateral */
        }

        .carousel-page { 
            grid-template-columns: repeat(2, 1fr); /* 2 columnas en tablet/móvil */
            gap: 20px 10px; 
            justify-content: center;
        }

        .arbitro-card {
            width: 100%;
            max-width: 130px; /* Tamaño controlado */
            margin: 0 auto; /* Centrado automático */
        }
    }
    @media (max-width: 480px) {
        .carousel-page {
            grid-template-columns: repeat(2, 1fr); 
            gap: 15px 5px;
        }
        
        .arbitro-img-wrapper {
            width: 100px; 
            height: 100px;
        }
        
        .initials-circle { font-size: 32px; }
        
        .nav-arrow {
            width: 30px; height: 30px;
            font-size: 12px;
        }
        
        .carousel-wrapper-paginated {
            padding: 0 25px;
        }
    }
</style>
@endsection

@section('content')

<div class="institucion-container">
    <div class="page-header">
        <h1>Junta de Prevención y Resolución de Disputas</h1>
    </div>

    <div class="tabs-wrapper">
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="openTab(event, 'presentacion')">Presentación</button>
            <button class="tab-btn" onclick="openTab(event, 'normativa')">Normativa</button>
            <button class="tab-btn" onclick="openTab(event, 'tarifario')">Tarifario y Calculadora</button>
            <button class="tab-btn" onclick="openTab(event, 'incorporacion')">Incorporación</button>
            <button class="tab-btn" onclick="openTab(event, 'nomina')">Nómina de Adjudicadores</button>
            <button class="tab-btn" onclick="openTab(event, 'requisitos')">Requisitos Adjudicadores</button>
            <button class="tab-btn" onclick="openTab(event, 'solicitar')">Solicitar el Servicio</button>
            <button class="tab-btn" onclick="openTab(event, 'repositorio')">Repositorio de Decisiones</button>
        </div>

        <div class="tabs-content">
            
            {{-- 1. PRESENTACIÓN --}}
            <div id="presentacion" class="tab-pane active">
                <div class="presentacion-layout">
                    <div class="presentacion-text">
                        <p>El servicio de Juntas de Resolución de Disputas (JRD) del CARD CD La Libertad fue creado con la vocación de servir a la comunidad. Como órgano especializado, nos encargamos de la administración y organización de estas Juntas, garantizando un acompañamiento técnico, imparcial y transparente que asegura la eficiencia en la ejecución de los proyectos.</p>
                    </div>
                    <img src="{{ asset('img/main-site/2.jpg') }}" alt="Sala de Reuniones" class="presentacion-img">
                </div>
            </div>

            {{-- 2. NORMATIVA --}}
            <div id="normativa" class="tab-pane">
                <div class="normativa-section-title">DOCUMENTOS DE NORMATIVA</div>
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

            {{-- 3. TARIFARIO --}}
            <div id="tarifario" class="tab-pane">
                <div class="normativa-section-title">TARIFARIO Y CALCULADORA</div>
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="mb-3 text-center"><img src="{{ asset('img/cdlima_encabezado.jpg') }}" style="max-width: 100%; border-radius: 4px;"></div>
                        <div class="card shadow-sm border-0 bg-white">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-1 text-dark">Calculadoras de la Junta de Resolución</h5>
                                <p class="text-muted small mb-4">CD La Libertad</p>
                                <button onclick="openCalculator('{{ route('calc.junta') }}')" class="btn-calc-red">Calculadora Junta de Prevención y Resolución de Disputas</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <h6 class="text-muted fw-bold mb-3 border-bottom pb-2 text-uppercase">Normativa Vigente</h6>
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

            {{-- 4. INCORPORACION (DOCUMENTOS) --}}
            <div id="incorporacion" class="tab-pane">
                <div class="normativa-section-title">DOCUMENTOS DE INCORPORACIÓN</div>
                <div class="docs-grid">
                    @php $docs = $docsPorCategoria->get('incorporacion', collect()); @endphp
                    @forelse($docs as $doc)
                        <div class="doc-item">
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-users"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                        </div>
                    @empty
                        <p class="empty-msg">No hay documentos disponibles.</p>
                    @endforelse
                </div>
            </div>

            {{-- 5. NUEVA SECCIÓN: NÓMINA DE ADJUDICADORES --}}
           {{-- 5. NÓMINA DE ADJUDICADORES (CORREGIDO) --}}
            <div id="nomina" class="tab-pane">
                <div class="normativa-section-title">NÓMINA DE ADJUDICADORES</div>
                @if(isset($adjudicadoresNomina) && $adjudicadoresNomina->count() > 0)
                    <div class="carousel-wrapper-paginated">
                        {{-- NOTA: He cambiado los IDs y las funciones onclick a 'Adjudicador' --}}
                        <button class="nav-arrow prev-page" onclick="changeAdjudicadorPage(-1)" id="btnPrevAdj" disabled><i class="fas fa-chevron-left"></i></button>
                        <button class="nav-arrow next-page" onclick="changeAdjudicadorPage(1)" id="btnNextAdj"><i class="fas fa-chevron-right"></i></button>

                        <div class="carousel-pages-container">
                            @foreach($adjudicadoresNomina->chunk(8) as $index => $grupo)
                                <div class="carousel-page {{ $index === 0 ? 'active-page' : '' }}" data-page="{{ $index }}">
                                    @foreach($grupo as $adj)
                                        <div class="arbitro-card" onclick="openModal(this)"
                                             data-name="{{ $adj->nombres }}"
                                             data-cargo="{{ $adj->cargo ?? 'Adjudicador' }}"
                                             data-code="{{ $adj->codigo ?? '' }}"
                                             data-specialty="{{ $adj->especialidad ?? '' }}"
                                             data-email="{{ $adj->email ?? '' }}"
                                             data-phone="{{ $adj->telefono ?? '' }}"
                                             data-cv="{{ $adj->ruta_cv ? asset('storage/' . $adj->ruta_cv) : '' }}"
                                             data-img="{{ $adj->ruta_imagen ? asset('storage/' . $adj->ruta_imagen) : '' }}">
                                             
                                            <div class="arbitro-img-wrapper">
                                                @if($adj->ruta_imagen)
                                                    <img src="{{ asset('storage/' . $adj->ruta_imagen) }}" class="arbitro-img">
                                                @else
                                                    <div class="initials-circle">{{ substr($adj->nombres, 0, 1) }}</div>
                                                @endif
                                                <div class="arbitro-overlay">VER PERFIL</div>
                                            </div>
                                            <div class="arbitro-name-label">{{ $adj->nombres }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if($adjudicadoresNomina->count() > 8)
                        <div class="carousel-indicators">
                            @foreach($adjudicadoresNomina->chunk(8) as $index => $grupo)
                                <div class="indicator-dot {{ $index === 0 ? 'active' : '' }}" id="dot-{{ $index }}"></div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="empty-msg">No hay adjudicadores registrados en la nómina.</p>
                @endif
            </div>

            {{-- 6. REQUISITOS --}}
            <div id="requisitos" class="tab-pane">
                <div class="normativa-section-title">REQUISITOS PARA ADJUDICADORES</div>
                <div class="docs-grid">
                    @php $docs = $docsPorCategoria->get('requisitos', collect()); @endphp
                    @forelse($docs as $doc)
                        <div class="doc-item">
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-list-check"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">{{ $doc->descripcion ?? 'Publicado el ' . $doc->fecha_publicacion->format('d/m/Y') }}</div>
                        </div>
                    @empty
                        <p class="empty-msg">Pendiente.</p>
                    @endforelse
                </div>
            </div>

            {{-- 7. SOLICITAR --}}
            <div id="solicitar" class="tab-pane">
                <div class="normativa-section-title">SOLICITAR EL SERVICIO</div>
                <div class="docs-grid">
                    @php $docs = $docsPorCategoria->get('solicitar', collect()); @endphp
                    @forelse($docs as $doc)
                        <div class="doc-item">
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-file-signature"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                        </div>
                    @empty
                        <p class="empty-msg">No hay formularios disponibles.</p>
                    @endforelse
                </div>
            </div>

            {{-- 8. REPOSITORIO --}}
            <div id="repositorio" class="tab-pane">
                <div class="normativa-section-title">REPOSITORIO DE DECISIONES</div>
                <div style="text-align: center; padding: 30px 0;">
                    <a href="https://drive.google.com/drive/folders/1aRepyc-6g5MhhtFqvnqUcJdTjrLqNqvV?usp=drive_link" target="_blank" class="doc-btn" style="justify-content: center; max-width: 400px; margin: 0 auto;">ACCEDER AL REPOSITORIO</a>
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

@endsection
@section('scripts')
<script>
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

    // --- LÓGICA DEL MODAL ---
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
        const cargo = element.getAttribute('data-cargo');
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
        const cargoGroup = document.getElementById('mCargoGroup');
        if (cargo && cargo.trim() !== "") {
            document.getElementById('mCargo').textContent = cargo;
            cargoGroup.style.display = 'block';
        } else {
            cargoGroup.style.display = 'none';
        }

        const codeGroup = document.getElementById('mCodeGroup');
        if (code && code.trim() !== "") {
            document.getElementById('mCode').textContent = code;
            codeGroup.style.display = 'flex';
        } else {
            codeGroup.style.display = 'none';
        }

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

        // 5. Mostrar
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

    // --- CARRUSEL PAGINADO (Renombrado para Adjudicadores) ---
    // Usamos var para evitar error de redeclaración si el script se carga múltiple veces
    var adjudicadorPageIdx = 0; 
    
    function updateAdjudicadorPagination() {
        const pages = document.querySelectorAll('.carousel-page');
        const totalPages = pages.length;
        // Ojo: IDs actualizados a Adj
        const prevBtn = document.getElementById('btnPrevAdj');
        const nextBtn = document.getElementById('btnNextAdj');
        const dots = document.querySelectorAll('.indicator-dot');

        pages.forEach((page, index) => {
            if(index === adjudicadorPageIdx) {
                page.classList.add('active-page');
            } else {
                page.classList.remove('active-page');
            }
        });

        if(dots.length > 0) {
            dots.forEach((dot, index) => {
                if(index === adjudicadorPageIdx) dot.classList.add('active');
                else dot.classList.remove('active');
            });
        }

        if(prevBtn) prevBtn.disabled = (adjudicadorPageIdx === 0);
        if(nextBtn) nextBtn.disabled = (adjudicadorPageIdx === totalPages - 1);
    }

    function changeAdjudicadorPage(direction) {
        const pages = document.querySelectorAll('.carousel-page');
        const totalPages = pages.length;

        adjudicadorPageIdx += direction;

        if(adjudicadorPageIdx < 0) adjudicadorPageIdx = 0;
        if(adjudicadorPageIdx >= totalPages) adjudicadorPageIdx = totalPages - 1;

        updateAdjudicadorPagination();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const pages = document.querySelectorAll('.carousel-page');
        if(pages.length > 0) {
            updateAdjudicadorPagination();
        } else {
            const p = document.getElementById('btnPrevAdj');
            const n = document.getElementById('btnNextAdj');
            if(p) p.style.display = 'none';
            if(n) n.style.display = 'none';
        }
    });
</script>
@endsection