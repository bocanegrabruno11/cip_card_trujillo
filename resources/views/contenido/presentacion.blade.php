@extends('inicio')

@section('title', 'Presentación - CARD CD La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .presentation-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
        position: relative;
    }

    .page-title {
        margin-bottom: 40px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .page-title h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    /* === LAYOUT DE CONTENIDO === */
    .content-grid {
        display: flex;
        gap: 50px;
        margin-bottom: 60px;
        align-items: flex-start;
    }

    .left-column {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .right-column {
        flex: 1.5;
    }

    .btn-download {
        background-color: #FF6B6B;
        color: white;
        text-decoration: none;
        padding: 15px 20px;
        border-radius: 4px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background 0.3s;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .btn-download:hover { background-color: #ff4c4c; }

    .main-building-img {
        width: 100%;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        object-fit: cover;
        min-height: 250px; 
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .main-building-img:hover { transform: scale(1.02); }

    .text-content p {
        color: #555;
        line-height: 1.8;
        font-size: 15px;
        margin-bottom: 25px;
        text-align: justify;
    }

    .certification-logos {
        display: flex;
        justify-content: flex-end;
        gap: 20px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .cert-logo {
        height: 60px;
        width: auto;
    }

    /* === GALERÍA TIPO SLIDER === */
    .gallery-section {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #eee;
        position: relative;
    }

    .gallery-container-wrapper {
        position: relative;
        padding: 0 40px;
    }

    .bottom-gallery {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        padding-bottom: 10px;
    }
    
    .bottom-gallery::-webkit-scrollbar { display: none; }

    .gallery-img-wrapper {
        flex: 0 0 calc(25% - 15px); 
        min-width: 200px;
        height: 150px;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        position: relative;
    }

    .gallery-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
        background-color: #f9f9f9;
        cursor: pointer;
    }

    .gallery-img:hover { transform: scale(1.1); }

    /* Botones del Slider Galería */
    .gallery-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #333;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: background 0.3s;
    }
    
    .gallery-btn:hover { background-color: #AD2B2E; }
    .g-prev { left: 0; }
    .g-next { right: 0; }

    /* === MODAL LIGHTBOX (PURO CSS) === */
    .lightbox-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95); /* Fondo muy oscuro para enfoque total */
        z-index: 10000;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(5px); /* Efecto borroso en el fondo */
    }

    .lightbox-overlay.active {
        display: flex;
        opacity: 1;
    }

    .lightbox-content {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px; /* Espacio para no pegar a los bordes */
    }

    .lightbox-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* CLAVE: Muestra toda la imagen sin recortar */
        border-radius: 4px;
        box-shadow: 0 0 50px rgba(0,0,0,0.8);
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 40px;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        z-index: 10001;
        line-height: 1;
        padding-bottom: 5px; /* Ajuste visual del caracter X */
    }

    .lightbox-close:hover {
        background-color: #FF6B6B;
        border-color: #FF6B6B;
        transform: rotate(90deg);
    }

    /* === RESPONSIVIDAD === */
    @media (max-width: 900px) {
        .content-grid { flex-direction: column; gap: 30px; }
        .left-column, .right-column { width: 100%; flex: none; }
        .btn-download { max-width: 100%; }
        .certification-logos { justify-content: center; }
        .gallery-img-wrapper { flex: 0 0 calc(50% - 10px); }
    }

    @media (max-width: 600px) {
        .gallery-img-wrapper { flex: 0 0 80%; }
        .lightbox-close { top: 10px; right: 10px; background-color: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px; }
    }
</style>
@endsection

@section('content')

<div class="presentation-container">
    
    <div class="page-title">
        <h1>Presentación</h1>
    </div>

    <div class="content-grid">
        
        <div class="left-column">
            @if(isset($documentoPresentacion) && $documentoPresentacion)
                <a href="{{ asset('storage/' . $documentoPresentacion->ruta_archivo) }}" target="_blank" class="btn-download">
                    <i class="fas fa-file-pdf"></i> {{ $documentoPresentacion->titulo ?? 'CREACIÓN DEL CARD' }}
                </a>
            @else
                {{-- Botón deshabilitado o mensaje si no hay documento --}}
                <button class="btn-download" style="opacity: 0.6; cursor: not-allowed;" disabled>
                    <i class="fas fa-file-pdf"></i> DOCUMENTO PENDIENTE
                </button>
            @endif
            
            @if($imagenPrincipal)
                @php $imgUrl = asset('storage/' . $imagenPrincipal->ruta_imagen); @endphp
                @if($imagenPrincipal->url_enlace)
                    <a href="{{ $imagenPrincipal->url_enlace }}" target="_blank">
                        <img src="{{ $imgUrl }}" alt="Principal" class="main-building-img">
                    </a>
                @else
                    <img src="{{ $imgUrl }}" alt="Principal" class="main-building-img" onclick="openLightbox('{{ $imgUrl }}')">
                @endif
            @else
                @php $imgUrl = asset('img/main-site/2.jpg'); @endphp
                <img src="{{ $imgUrl }}" alt="Default" class="main-building-img" onclick="openLightbox('{{ $imgUrl }}')">
            @endif
        </div>

        <div class="right-column">
            <div class="text-content">
                <p>
                    El Centro de Arbitraje y Resolución de Disputas del Colegio de Ingenieros del Perú Consejo
                     Departamental La Libertad, inició sus actividades el 19 de junio del año 2000 y ha ido ingresando
                      en el competitivo ámbito del Arbitraje y Resolución de Disputas para la prevención y solución 
                      de controversias, asimismo de manera particular realiza capacitaciones brindadas a los interesados
                       y público en general que requieren conocimientos en temas de arbitrajes y Juntas de prevención 
                       y solución de controversias.

                </p>
                <p>
                    El Centro de Arbitraje y Resolución de Disputas del Colegio de Ingenieros del Perú Consejo Departamental
                     La Libertad, desempeña sus actividades en cumplimiento a la Directiva 02-2025-OECE-CB del 19-04-2025 
                     (Nueva Ley de General de Contrataciones Públicas) y sus modificatorias atendiendo los requerimientos 
                     de organizaciones que desarrollan contratos de obra y suministro para situaciones que surgen como parte 
                     de la interacción entre estas organizaciones y el Estado Peruano.

                </p>
            </div>

           
        </div>
    </div>

    <div class="gallery-section">
        <div class="gallery-container-wrapper">
            <button class="gallery-btn g-prev" id="gPrev"><i class="fas fa-chevron-left"></i></button>
            <button class="gallery-btn g-next" id="gNext"><i class="fas fa-chevron-right"></i></button>

            <div class="bottom-gallery" id="galleryScroll">
                @if($galeria->count() > 0)
                    @foreach($galeria as $img)
                        @php $gImgUrl = asset('storage/' . $img->ruta_imagen); @endphp
                        <div class="gallery-img-wrapper">
                            @if($img->url_enlace)
                                <a href="{{ $img->url_enlace }}" target="_blank">
                                    <img src="{{ $gImgUrl }}" class="gallery-img">
                                </a>
                            @else
                                <img src="{{ $gImgUrl }}" class="gallery-img" onclick="openLightbox('{{ $gImgUrl }}')">
                            @endif
                        </div>
                    @endforeach
                @else
                    {{-- Fallback --}}
                    @php $statics = ['main-site/1.png', 'main-site/2.jpg']; @endphp
                    @foreach($statics as $sImg)
                        @php $sUrl = asset('img/' . $sImg); @endphp
                        <div class="gallery-img-wrapper">
                            <img src="{{ $sUrl }}" class="gallery-img" onclick="openLightbox('{{ $sUrl }}')">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

{{-- SCRIPTS Y MODAL AL FINAL --}}
@section('scripts')

<div class="lightbox-overlay" id="lightboxOverlay">
    <div class="lightbox-content">
        <button class="lightbox-close" id="lightboxClose">✕</button>
        <img src="" id="lightboxImage" class="lightbox-img">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Slider Logic (Mejorado)
        const scrollContainer = document.getElementById('galleryScroll');
        const btnPrev = document.getElementById('gPrev');
        const btnNext = document.getElementById('gNext');

        if (scrollContainer && btnPrev && btnNext) {
            
            // Cantidad de desplazamiento (aprox el ancho de una tarjeta + gap)
            const scrollAmount = 220; 

            btnNext.addEventListener('click', () => {
                // Verificamos si se puede scrollear a la derecha
                if (scrollContainer.scrollWidth > (scrollContainer.scrollLeft + scrollContainer.clientWidth)) {
                    scrollContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                } else {
                    // Opcional: Volver al inicio si llegó al final (Loop)
                    scrollContainer.scrollTo({ left: 0, behavior: 'smooth' });
                }
            });

            btnPrev.addEventListener('click', () => {
                // Verificamos si se puede scrollear a la izquierda
                if (scrollContainer.scrollLeft > 0) {
                    scrollContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                } else {
                     // Opcional: Ir al final si está al principio
                     scrollContainer.scrollTo({ left: scrollContainer.scrollWidth, behavior: 'smooth' });
                }
            });
        }

        // 2. Lightbox Logic (Sin cambios, funciona bien)
        const lightboxOverlay = document.getElementById('lightboxOverlay');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxClose = document.getElementById('lightboxClose');

        window.openLightbox = function(src) { // Hacemos la función global para que funcione el onclick del HTML
            lightboxImage.src = src;
            lightboxOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        function closeLightbox() {
            lightboxOverlay.classList.remove('active');
            setTimeout(() => { lightboxImage.src = ''; }, 300);
            document.body.style.overflow = 'auto';
        }

        if(lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        
        if(lightboxOverlay) {
            lightboxOverlay.addEventListener('click', (e) => {
                if(e.target === lightboxOverlay) closeLightbox();
            });
        }

        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape' && lightboxOverlay.classList.contains('active')) {
                closeLightbox();
            }
        });
    });
</script>
@endsection