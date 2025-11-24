@extends('inicio')

@section('title', 'Institución Arbitral - CIP La Libertad')

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
        min-height: 500px;
    }

    /* Menú Lateral */
    .tabs-nav {
        width: 300px;
        background-color: #f4f4f4;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
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
        color: #AD2B2E; /* Rojo Institucional */
        border-left: 4px solid #AD2B2E;
        margin-right: -1px;
        border-right: 1px solid #fff;
    }

    /* Contenido */
    .tabs-content { flex: 1; padding: 40px; background-color: #fff; }
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

    /* === BOTONES DE DOCUMENTOS (TODOS ROJOS) === */
    .doc-btn {
        display: flex;
        align-items: center;
        background-color: #AD2B2E; /* Rojo Institucional */
        color: white !important;
        padding: 15px 20px;
        border-radius: 6px;
        text-decoration: none;
        margin-bottom: 5px;
        transition: background 0.3s, transform 0.2s;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        width: 100%; /* Ocupar ancho disponible */
        border: none;
    }
    .doc-btn:hover { 
        background-color: #8f2225; /* Rojo más oscuro */
        transform: translateY(-2px);
    }
    .doc-btn i { font-size: 20px; margin-right: 15px; }

    .doc-meta { font-size: 12px; color: #666; font-style: italic; margin-bottom: 25px; margin-left: 5px; }
    .empty-msg { color: #888; font-style: italic; margin-top: 10px; text-align: center;}

    /* === BOTONES CALCULADORA (TAMBIÉN ROJOS) === */
    .btn-calc-red {
        display: block;
        width: 100%;
        background-color: #AD2B2E !important; /* Mismo rojo */
        color: white !important;
        border: none;
        padding: 15px 10px;
        margin-bottom: 15px;
        border-radius: 6px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 14px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
    }
    .btn-calc-red:hover {
        background-color: #8f2225 !important;
        transform: translateY(-2px);
    }

    @media (max-width: 900px) {
        .tabs-wrapper { flex-direction: column; }
        .tabs-nav { width: 100%; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid #e0e0e0; }
        .tab-btn { white-space: nowrap; border-bottom: 4px solid transparent; border-left: none; padding: 15px; font-size: 13px; }
        .tab-btn.active { border-left: none; border-bottom-color: #AD2B2E; background-color: #fff; }
        .presentacion-layout { flex-direction: column; }
        .presentacion-img { max-width: 100%; }
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
            <button class="tab-btn" onclick="openTab(event, 'incorporacion')">Incorporación y Nómina</button>
            <button class="tab-btn" onclick="openTab(event, 'requisitos')">Requisitos Árbitros</button>
            <button class="tab-btn" onclick="openTab(event, 'solicitar')">Solicitar el Servicio</button>
            <button class="tab-btn" onclick="openTab(event, 'repositorio')">Repositorio de Laudos</button>
        </div>

        <div class="tabs-content">
            
            {{-- 1. PRESENTACIÓN --}}
            <div id="presentacion" class="tab-pane active">
                <div class="presentacion-layout">
                    <div class="presentacion-text">
                        <p>Con Resolución de Consejo CDL CIP, el servicio de arbitraje en el Centro de Arbitraje y Resolución de Disputas (CARD) del Consejo Departamental de La Libertad del Colegio de Ingenieros del Perú nació para servir a la comunidad.</p>
                        <p>En el actual estatuto del Colegio de Ingenieros del Perú, en el artículo 4.02 literal c., el CARD se encuentra como su órgano especializado encargado de la administración y organización de arbitrajes institucionales, garantizando imparcialidad, eficiencia y transparencia en la resolución de controversias.</p>
                    </div>
                    <img src="{{ asset('img/appmovil.jpg') }}" alt="Sala de Reuniones" class="presentacion-img">
                </div>
            </div>

            {{-- 2. NORMATIVA --}}
            <div id="normativa" class="tab-pane">
                <div class="normativa-section-title">DOCUMENTOS DE NORMATIVA</div>
                
                @php $docs = $docsPorCategoria->get('normativa', collect()); @endphp

                @forelse($docs as $doc)
                    @php
                        $ext = pathinfo($doc->ruta_archivo, PATHINFO_EXTENSION);
                        $icon = match($ext) { 'pdf' => 'fa-file-pdf', 'doc', 'docx' => 'fa-file-word', 'xls', 'xlsx' => 'fa-file-excel', default => 'fa-file-alt' };
                    @endphp
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                        <i class="fas {{ $icon }}"></i> {{ $doc->titulo }}
                    </a>
                    <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                @empty
                    <p class="empty-msg">No hay documentos normativos publicados actualmente.</p>
                @endforelse
            </div>

            {{-- 3. TARIFARIO Y CALCULADORA --}}
            <div id="tarifario" class="tab-pane">
                <div class="normativa-section-title">TARIFARIO Y CALCULADORA</div>

                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="mb-3 text-center">
                            <img src="{{ asset('img/cdlima_encabezado.jpg') }}" style="max-width: 100%; height: auto; display: block; margin: 0 auto; border-radius: 4px;"> 
                        </div>

                        <div class="card shadow-sm border-0 bg-white">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-1 text-dark">Calculadoras del Centro de Arbitraje</h5>
                                <p class="text-muted small mb-4">Colegio de Ingenieros del Perú - CD La Libertad</p>

                                <button onclick="openCalculator('{{ route('calc.inst.det') }}')" class="btn-calc-red">
                                    CALCULADORA PARA CUANTÍA DETERMINADA
                                </button>

                                <button onclick="openCalculator('{{ route('calc.inst.indet') }}')" class="btn-calc-red">
                                    CALCULADORA PARA CUANTÍA INDETERMINADA
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <h6 class="text-muted fw-bold mb-3 border-bottom pb-2 text-uppercase">Normativa Vigente</h6>
                        
                        @php $docs = $docsPorCategoria->get('tarifario', collect()); @endphp
                        @forelse($docs as $doc)
                            {{-- Quitamos el style background inline para que tome el CSS .doc-btn (Rojo) --}}
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                                <i class="fas fa-file-pdf"></i> {{ $doc->titulo }}
                            </a>
                            <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                        @empty
                            <p class="empty-msg">No hay tarifarios disponibles.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 4. INCORPORACION --}}
            <div id="incorporacion" class="tab-pane">
                <div class="normativa-section-title">INCORPORACIÓN Y NÓMINA</div>
                
                @php $docs = $docsPorCategoria->get('incorporacion', collect()); @endphp
                @forelse($docs as $doc)
                    {{-- Clase doc-btn asegura que sea ROJO --}}
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                        <i class="fas fa-users"></i> {{ $doc->titulo }}
                    </a>
                    <div class="doc-meta">Actualizado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                @empty
                    <p class="empty-msg">No hay documentos disponibles.</p>
                @endforelse
            </div>

            {{-- 5. REQUISITOS --}}
            <div id="requisitos" class="tab-pane">
                <div class="normativa-section-title">REQUISITOS PARA ÁRBITROS</div>

                @php $docs = $docsPorCategoria->get('requisitos', collect()); @endphp
                @forelse($docs as $doc)
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                        <i class="fas fa-list-check"></i> {{ $doc->titulo }}
                    </a>
                    <div class="doc-meta">{{ $doc->descripcion ?? 'Publicado el ' . $doc->fecha_publicacion->format('d/m/Y') }}</div>
                @empty
                    <p class="empty-msg">Información pendiente de carga.</p>
                @endforelse
            </div>

             {{-- 6. SOLICITAR --}}
             <div id="solicitar" class="tab-pane">
                <div class="normativa-section-title">SOLICITAR EL SERVICIO</div>

                @php $docs = $docsPorCategoria->get('solicitar', collect()); @endphp
                @forelse($docs as $doc)
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                        <i class="fas fa-file-signature"></i> {{ $doc->titulo }}
                    </a>
                    <div class="doc-meta">Publicado el {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                @empty
                    <p class="empty-msg">No hay formularios disponibles.</p>
                @endforelse
            </div>

             {{-- 7. REPOSITORIO --}}
             <div id="repositorio" class="tab-pane">
                <div class="normativa-section-title">REPOSITORIO DE LAUDOS</div>

                @php $docs = $docsPorCategoria->get('repositorio', collect()); @endphp
                @forelse($docs as $doc)
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="doc-btn">
                        <i class="fas fa-book"></i> {{ $doc->titulo }}
                    </a>
                    <div class="doc-meta">Fecha: {{ $doc->fecha_publicacion->format('d/m/Y') }}</div>
                @empty
                    <p class="empty-msg">No se encontraron laudos archivados.</p>
                @endforelse
            </div>

        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
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
</script>
@endsection