@extends('inicio')

@section('title', 'Inicio - CARD CD La Libertad')

@section('styles')
<style>
    /* === ESTILOS GENERALES === */
    body { background-color: #f2f2f2; font-family: 'Arial', sans-serif; }
    .contenedor-home { display: flex; max-width: 1400px; margin: 0 auto; padding: 20px; gap: 20px; position: relative; z-index: 1; }
    
    /* === MENÚ LATERAL === */
    .menu-lateral { width: 250px; flex-shrink: 0; display: flex; flex-direction: column; gap: 10px; }
    .menu-lateral .item { padding: 15px 10px; border-radius: 8px; color: white; text-align: center; text-decoration: none; display: flex; flex-direction: column; align-items: center; transition: transform 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .menu-lateral .item:hover { transform: translateY(-2px); filter: brightness(1.1); }
    .menu-lateral .item i { font-size: 24px; margin-bottom: 8px; }
    .menu-lateral .item span { font-size: 13px; margin-top: 5px; font-weight: normal; display: block; }
    .rojo { background-color: #B02E2D; }
    .dorado { background-color: #d9b04c; color: black !important; }
    .negro { background-color: #1f1f1f; }

    /* === SECCIÓN CENTRAL === */
    .seccion-central { flex: 1; display: flex; flex-direction: column; gap: 30px; min-width: 0; }

    /* === PORTADA HERO === */
    .portada-hero { display: flex; background-color: white; border-radius: 4px; overflow: hidden; height: 380px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; }
    .portada-imagen { flex: 1.8; position: relative; overflow: hidden; height: 100%; }
    .hero-slider { width: 100%; height: 100%; position: relative; }
    .hero-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out; object-fit: cover; object-position: center; z-index: 1; }
    .hero-slide.active { opacity: 1; z-index: 2; }
    
    .portada-info { flex: 1; background-color: #CC0000; padding: 40px 30px; color: white; display: flex; flex-direction: column; justify-content: center; height: 100%; position: relative; z-index: 5; }
    .small-title { font-size: 14px; text-transform: uppercase; margin-bottom: 5px; opacity: 0.9; font-weight: normal; }
    .main-title { font-size: 28px; font-weight: 800; line-height: 1.2; margin-bottom: 15px; text-transform: uppercase; }
    .description { font-size: 15px; line-height: 1.5; margin-bottom: 30px; opacity: 0.9; }
    .hero-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-hero { padding: 12px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; text-align: center; display: flex; align-items: center; justify-content: center; transition: all 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .btn-red-light { background-color: #E31E24; color: white; flex: 1; }
    .btn-gold { background-color: #D7B56D; color: white; flex: 1.5; text-decoration: underline; }
    .btn-red-light:hover { background-color: #ff333a; }
    .btn-gold:hover { background-color: #e5c37a; }
    
    .slider-arrow { position: absolute; top: 50%; transform: translateY(-50%); background-color: rgba(0, 0, 0, 0.5); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 18px; cursor: pointer; z-index: 100; display: flex; align-items: center; justify-content: center; transition: background-color 0.3s ease; }
    .slider-arrow:hover { background-color: #B02E2D; }
    .prev-arrow { left: 15px; }
    .next-arrow { right: 15px; }

    /* === GRID EVENTOS === */
    .titulo-seccion { background-color: #e0e0e0; color: #333; padding: 10px 20px; font-weight: bold; border-radius: 4px; text-align: center; text-transform: uppercase; display: flex; justify-content: space-between; align-items: center; }
    .link-ver-mas { font-size: 12px; color: #AD2B2E; text-decoration: none; }
    .link-ver-mas:hover { text-decoration: underline; }
    .grid-eventos { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
    .card-evento { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s; cursor: pointer; border: 1px solid #eee; }
    .card-evento:hover { transform: translateY(-5px); }
    .card-evento img { width: 100%; height: 250px; object-fit: contain; background: #f9f9f9; }
    .card-evento-info { padding: 15px; font-size: 14px; text-align: center; }
    .card-evento-info strong { color: #B02E2D; display: block; margin-bottom: 5px; }

    /* =================================================
       === ESTILOS DEL MODAL PERSONALIZADO (CORREGIDOS) === 
       ================================================= */
   .modal-overlay-fixed {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .modal-overlay-fixed.active { display: flex; }

    .modal-card {
        background-color: white;
        width: 100%;
        max-width: 1100px; /* Un poco más ancho para que quepa todo bien */
        height: 85vh;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        display: flex;
        flex-direction: column;
        position: relative;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .modal-event-header {
        background-color: #AD2B2E;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .modal-event-title { font-size: 18px; font-weight: bold; margin: 0; width: 90%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .modal-close-icon { background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; line-height: 1; }

    /* CUERPO DEL MODAL: FLEX ROW (Lado a Lado) */
    .modal-event-body {
        display: flex;
        flex-direction: row; /* CLAVE: Izquierda - Derecha */
        flex: 1;
        overflow: hidden;
    }

    /* COLUMNA IZQUIERDA (Imagen + Galería) */
    .modal-col-left {
        width: 55%; /* Más espacio para la imagen */
        background-color: white; /* Fondo negro elegante */
        display: flex;
        flex-direction: column;
        border-right: 1px solid #333;
    }

    .main-img-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        overflow: hidden;
    }

    .event-img-display {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* Imagen completa sin cortes */
    }

    .gallery-strip {
        height: 90px;
        padding: 10px;
        background: #222; /* Fondo oscuro para la galería */
        display: flex;
        gap: 10px;
        overflow-x: auto;
        flex-shrink: 0;
        justify-content: center; /* Centrar miniaturas */
    }
    
    .gallery-thumb-custom {
        width: 70px; height: 70px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        border: 2px solid transparent;
        opacity: 0.6;
        transition: 0.2s;
    }
    .gallery-thumb-custom:hover { opacity: 1; border-color: #AD2B2E; }

    /* COLUMNA DERECHA (Información) */
    .modal-col-right {
        width: 45%;
        padding: 30px;
        background-color: #fff;
        overflow-y: auto; /* Scroll solo si el texto es muy largo */
        display: flex;
        flex-direction: column;
    }

    .event-date-badge {
        background-color: #f8f9fa;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 20px;
        border-left: 4px solid #AD2B2E;
        color: #333;
        font-size: 14px;
        align-self: flex-start;
    }
    
    .event-location {
        margin-bottom: 20px;
        color: #555;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    
    .event-description {
        font-size: 15px;
        line-height: 1.8;
        color: #333;
        white-space: pre-line;
        text-align: justify;
    }
    .event-gallery-custom {
        margin-top: auto; /* Empuja la galería al final si hay espacio */
        padding-top: 20px;
        border-top: 1px solid #eee;
        display: flex;       
        flex-direction: column; /* CLAVE: Esto pone el título arriba y fotos abajo */
        gap: 10px;
    }

    .event-gallery-custom h4 {
        margin: 0;
        font-size: 16px;
        color: #AD2B2E;
        font-weight: bold;
        text-transform: uppercase;
    }

    .gallery-scroll-custom {
        display: flex;
        flex-wrap: wrap; /* Permite que bajen si son muchas, o usa nowrap para scroll horizontal */
        gap: 10px;
    }

    /* Reutilizamos tu estilo de thumbnail pero aseguramos tamaño */
    .gallery-thumb-mini {
        width: 60px; 
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        border: 2px solid #ddd;
        transition: all 0.2s;
    }

    .gallery-thumb-mini:hover, .gallery-thumb-mini.selected {
        border-color: #AD2B2E;
        transform: scale(1.05);
    }

    /* RESPONSIVE MODAL */
    @media (max-width: 900px) {
        .contenedor-home { flex-direction: column; }
        .menu-lateral { width: 100%; flex-direction: row; overflow-x: auto; padding-bottom: 10px; }
        .menu-lateral .item { min-width: 120px; flex: 0 0 auto; }
        .portada-hero { flex-direction: column; height: auto !important; min-height: auto; }
        .portada-imagen { width: 100%; height: 250px !important; flex: none; display: block; position: relative; }
        .portada-info { width: 100%; padding: 30px; height: auto; flex: none; }

        /* Modal en Móvil: Columna Única */
        .modal-card { height: auto; max-height: 95vh; }
        
        .modal-event-body { 
            flex-direction: column; /* Uno debajo del otro */
            overflow-y: auto; 
        }
        
        .modal-col-left { 
            width: 100%; 
            height: auto; 
            min-height: 300px;
        }
        
        .modal-col-right { 
            width: 100%; 
            height: auto; 
            padding: 20px;
        }
        
        .main-img-container { height: 300px; }
        .gallery-strip { justify-content: flex-start; }
    }
</style>
@endsection

@section('content')
<div class="contenedor-home">
    
    <aside class="menu-lateral">
        <a href="{{ route('presentacion') }}" class="item rojo"><i class="fas fa-building"></i> EL CARD<span>Institución</span></a>
        <a href="{{ route('arbitral') }}" class="item rojo"><i class="fas fa-file-contract"></i> Cláusulas<span>Reglamentos</span></a>
        <a href="{{ route('institucion-arbitral') }}" class="item dorado"><i class="fas fa-gavel"></i> Servicios<span>Arbitraje</span></a>
        <a href="{{ route('comunicados') }}" class="item negro"><i class="fas fa-bullhorn"></i> Comunicados<span>Noticias</span></a>
        <a href="{{ route('eventos') }}" class="item dorado"><i class="fas fa-calendar-alt"></i> Eventos<span>Actividades</span></a>
        <a href="{{ route('contactos') }}" class="item rojo"><i class="fas fa-phone"></i> Contactos<span>Info</span></a>
        <a href="{{ route('login') }}" class="item negro"><i class="fas fa-envelope-open-text"></i> Mesa de Partes<span>Trámites</span></a>
    </aside>

    <section class="seccion-central">
        
        <div class="portada-hero">
            <div class="portada-imagen">
                <div class="hero-slider">
                    @if(isset($sliderData) && $sliderData->detalles->count() > 0)
                        @foreach($sliderData->detalles as $index => $img)
                        <a href="{{ $img->url_enlace ?? '#' }}" target="{{ $img->url_enlace ? '_blank' : '_self' }}">
                            <img src="{{ asset('storage/' . $img->ruta_imagen) }}" class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                        </a>
                        @endforeach
                        @if($sliderData->detalles->count() > 1)
                            <button class="slider-arrow prev-arrow" id="sliderPrev">❮</button>
                            <button class="slider-arrow next-arrow" id="sliderNext">❯</button>
                        @endif
                    @else
                        <img src="{{ asset('img/main-site/1.png') }}" class="hero-slide active">
                    @endif
                </div>
            </div>
            <div class="portada-info">
                @if(isset($sliderData))
                    <div class="small-title">NUESTRAS NOVEDADES</div>
                    <div class="main-title">{{ $sliderData->titulo }}</div>
                    <div class="description">{!! strip_tags($sliderData->descripcion) !!}</div>
                @else
                  
                    <div class="description">Somos considerados uno de los mejores Centros de Arbitraje del Perú.</div>
                @endif
                <div class="hero-buttons">
                    <a href="{{ route('institucion-arbitral') }}" class="btn-hero btn-red-light">Arbitraje</a>
                    <a href="{{ route('junta-prevencion') }}" class="btn-hero btn-gold">Junta de Prevención</a>
                    <a href="{{ route('login') }}" class="btn-hero btn-red-light">Mesa de partes</a>
                </div>
            </div>
        </div>

        <div class="titulo-seccion">
            PRÓXIMOS EVENTOS
            <a href="{{ route('eventos') }}" class="link-ver-mas">VER MÁS</a>
        </div>
        
        <div class="grid-eventos">
            @forelse($proximosEventos as $evento)
                @php
                    $mainImg = $evento->detalles->where('tipo', 'principal')->first();
                    $imgUrl = $mainImg ? asset('storage/' . $mainImg->ruta_imagen) : asset('img/appmovil.jpg');
                    $galeria = $evento->detalles->where('tipo', 'galeria')->map(function($item){
                        return asset('storage/' . $item->ruta_imagen);
                    })->values();
                @endphp

                <div class="card-evento" 
                     onclick="openEventModal(
                        '{{ addslashes($evento->titulo) }}', 
                        '{{ \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y') }}', 
                        '{{ addslashes($evento->lugar ?? 'Virtual') }}', 
                        `{!! addslashes(nl2br(e($evento->descripcion))) !!}`, 
                        '{{ $imgUrl }}',
                        {{ json_encode($galeria) }}
                     )">
                    <img src="{{ $imgUrl }}" alt="{{ $evento->titulo }}">
                    <div class="card-evento-info">
                        <strong>{{ \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y') }}</strong>
                        {{ Str::limit($evento->titulo, 50) }}
                    </div>
                </div>
            @empty
                <p class="text-center text-muted w-100" style="grid-column: 1 / -1; padding: 30px;">No hay eventos próximos programados.</p>
            @endforelse
        </div>

    </section>
</div>
@endsection

@section('scripts')

<div class="modal-overlay-fixed" id="customEventModal">
    <div class="modal-card">
        
        <div class="modal-event-header">
            <h3 class="modal-event-title" id="cModalTitle">Título del Evento</h3>
            <button class="modal-close-icon" onclick="closeCustomModal()">✕</button>
        </div>
        
        <div class="modal-event-body">
            
            <div class="modal-col-left">
                <div class="main-img-container">
                    <img src="" id="cModalImg" class="event-img-display">
                </div>
            </div>
            
            <div class="modal-col-right">
                
                <div class="event-date-badge">
                    <i class="far fa-calendar-alt me-1"></i> <span id="cModalDate"></span>
                </div>
                
                <div class="event-location">
                    <i class="fas fa-map-marker-alt text-danger"></i> <span id="cModalPlace"></span>
                </div>
                
                <div class="event-description" id="cModalDesc"></div>
                
                <div class="event-gallery-custom" id="cModalGallerySection" style="display: none;">
                    <h4>Galería de Fotos</h4>
                    <div class="gallery-scroll-custom" id="cModalGallery"></div>
                </div>

            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Slider Portada
    const slides = document.querySelectorAll('.hero-slide');
    const btnPrev = document.getElementById('sliderPrev');
    const btnNext = document.getElementById('sliderNext');
    let currentSlide = 0;
    let slideInterval;

    function changeSlide(direction) {
        if(slides.length === 0) return;
        slides[currentSlide].classList.remove('active');
        if (direction === 'next') currentSlide = (currentSlide + 1) % slides.length;
        else currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    if (slides.length > 1) {
        slideInterval = setInterval(() => changeSlide('next'), 5000);
        if (btnNext && btnPrev) {
            btnNext.addEventListener('click', (e) => { e.preventDefault(); changeSlide('next'); clearInterval(slideInterval); slideInterval = setInterval(() => changeSlide('next'), 5000); });
            btnPrev.addEventListener('click', (e) => { e.preventDefault(); changeSlide('prev'); clearInterval(slideInterval); slideInterval = setInterval(() => changeSlide('next'), 5000); });
        }
    }

    // 2. Modal Custom Logic
    const modal = document.getElementById('customEventModal');

    window.openEventModal = function(titulo, fecha, lugar, descripcion, img, galeria) {
        // 1. Llenar textos
        document.getElementById('cModalTitle').innerText = titulo;
        document.getElementById('cModalDate').innerText = fecha;
        document.getElementById('cModalPlace').innerText = lugar;
        document.getElementById('cModalDesc').innerHTML = descripcion;
        
        // 2. Cargar imagen principal en el visualizador grande
        const mainImg = document.getElementById('cModalImg');
        mainImg.src = img;

        // 3. Lógica de Galería (MODIFICADA)
        const galleryContainer = document.getElementById('cModalGallery');
        const gallerySection = document.getElementById('cModalGallerySection');
        galleryContainer.innerHTML = '';

        // Creamos un arreglo unificado: [Imagen Principal] + [Resto de Galería]
        let todasLasImagenes = [];

        // Agregamos la principal primero
        if (img) {
            todasLasImagenes.push(img);
        }

        // Agregamos las demás si existen
        if (galeria && galeria.length > 0) {
            todasLasImagenes = todasLasImagenes.concat(galeria);
        }

        // Renderizamos las miniaturas
        if (todasLasImagenes.length > 0) {
            gallerySection.style.display = 'block'; // O 'flex' según tu CSS original, aquí se asegura que se vea
            
            todasLasImagenes.forEach(src => {
                const thumb = document.createElement('img');
                thumb.src = src;
                thumb.className = 'gallery-thumb-custom'; // Mantenemos tu clase de estilos
                
                // Al hacer click, la imagen grande cambia a esta
                thumb.onclick = function() { mainImg.src = src; };
                
                galleryContainer.appendChild(thumb);
            });
        } else {
            gallerySection.style.display = 'none';
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    window.closeCustomModal = function() {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeCustomModal();
    });
    
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape' && modal.classList.contains('active')) closeCustomModal();
    });
});
</script>
@endsection