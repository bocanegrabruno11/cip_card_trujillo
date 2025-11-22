@extends('inicio')

@section('title', 'Inicio - CIP La Libertad')

@section('styles')
<style>
    /* === ESTILOS GENERALES === */
    body { background-color: #f2f2f2; font-family: 'Arial', sans-serif; }

    .contenedor-home {
        display: flex;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
        gap: 20px;
    }

    /* === MENÚ LATERAL (Desktop) === */
    .menu-lateral {
        width: 250px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .menu-lateral .item {
        padding: 15px 10px;
        border-radius: 8px;
        color: white;
        text-align: center;
        text-decoration: none;
        display: flex; flex-direction: column; align-items: center;
        transition: transform 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .menu-lateral .item:hover { transform: translateY(-2px); filter: brightness(1.1); }
    .menu-lateral .item i { font-size: 24px; margin-bottom: 8px; }
    .menu-lateral .item span { font-size: 13px; margin-top: 5px; font-weight: normal; display: block; }
    
    .rojo { background-color: #B02E2D; }
    .dorado { background-color: #d9b04c; color: black !important; }
    .negro { background-color: #1f1f1f; }

    /* === SECCIÓN CENTRAL === */
    .seccion-central {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 30px;
        min-width: 0; 
    }

    /* === PORTADA HERO (DESKTOP) === */
    .portada-hero {
        display: flex; 
        background-color: white;
        border-radius: 4px; 
        overflow: hidden;
        height: 380px; /* Altura fija en PC */
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: relative;
    }

    /* Lado Izquierdo: SLIDER */
    .portada-imagen {
        flex: 1.8; /* Ocupa 65% en PC */
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    
    /* Carrusel */
    .hero-slider {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .hero-slide {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        object-fit: cover;
        object-position: center;
        z-index: 1;
    }
    
    .hero-slide.active { opacity: 1; z-index: 2; }

    /* Lado Derecho: Información */
    .portada-info {
        flex: 1; /* Ocupa 35% en PC */
        background-color: #CC0000; 
        padding: 40px 30px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center; 
        height: 100%;
        position: relative; z-index: 5;
    }
    
    .small-title { font-size: 14px; text-transform: uppercase; margin-bottom: 5px; opacity: 0.9; font-weight: normal; }
    .main-title { font-size: 28px; font-weight: 800; line-height: 1.2; margin-bottom: 15px; text-transform: uppercase; }
    .description { font-size: 15px; line-height: 1.5; margin-bottom: 30px; opacity: 0.9; }

    .hero-buttons { display: flex; gap: 10px; flex-wrap: wrap; }

    .btn-hero {
        padding: 12px 15px; border-radius: 4px; text-decoration: none;
        font-weight: bold; font-size: 13px; text-align: center;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .btn-red-light { background-color: #E31E24; color: white; flex: 1; }
    .btn-gold { background-color: #D7B56D; color: white; flex: 1.5; text-decoration: underline; }
    .btn-red-light:hover { background-color: #ff333a; }
    .btn-gold:hover { background-color: #e5c37a; }

    /* === GRID EVENTOS === */
    .titulo-seccion {
        background-color: #e0e0e0; color: #333;
        padding: 10px 20px; font-weight: bold; border-radius: 4px;
        text-align: center; text-transform: uppercase;
    }

    .grid-eventos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .card-evento {
        background: white; border-radius: 8px; overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    .card-evento:hover { transform: translateY(-5px); }
    .card-evento img { width: 100%; height: 250px; object-fit: contain; background: #f9f9f9; }
    .card-evento-info { padding: 15px; font-size: 14px; text-align: center; }
    .card-evento-info strong { color: #B02E2D; display: block; margin-bottom: 5px; }

    /* === BOTONES SLIDER === */
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        width: 40px; height: 40px; border-radius: 50%; font-size: 18px;
        cursor: pointer; z-index: 100; display: flex; align-items: center; justify-content: center;
        transition: background-color 0.3s ease;
    }
    .slider-arrow:hover { background-color: #B02E2D; }
    .prev-arrow { left: 15px; }
    .next-arrow { right: 15px; }

    /* === RESPONSIVIDAD CORREGIDA === */
    @media (max-width: 900px) {
        .contenedor-home { flex-direction: column; }
        
        /* Ajuste del menú lateral para móviles (Botones grandes como en tu imagen) */
        .menu-lateral { 
            width: 100%; 
            flex-direction: row; 
            flex-wrap: wrap;
            gap: 10px;
        }
        .menu-lateral .item { 
            flex: 1 1 100px; /* Crece para llenar espacio */
            min-height: 80px;
            justify-content: center;
        }
        
        /* CORRECCIÓN DEL SLIDER MÓVIL */
        .portada-hero { 
            display: flex;
            flex-direction: column; /* Uno debajo del otro */
            height: auto !important; /* Quitar altura fija de PC */
            min-height: auto;
        }
        
        /* FORZAR ALTURA A LA IMAGEN EN MÓVIL */
        .portada-imagen { 
            width: 100%; 
            height: 250px !important; /* Altura obligatoria para que se vea */
            flex: none; /* Desactivar proporción flex de escritorio */
            display: block;
        }
        
        .portada-info { 
            width: 100%; 
            padding: 30px; 
            height: auto; /* Altura automática según texto */
            flex: none; /* Desactivar proporción flex */
        }
    }
</style>
@endsection

@section('content')
<div class="contenedor-home">
    
    <aside class="menu-lateral">
        <a href="{{ route('presentacion') }}" class="item rojo">
            <i class="fas fa-building"></i> EL CARD<span>Institución</span>
        </a>
        <a href="{{ route('arbitral') }}" class="item rojo">
            <i class="fas fa-file-contract"></i> Cláusulas<span>Reglamentos</span>
        </a>
        <a href="#" class="item dorado">
            <i class="fas fa-gavel"></i> Servicios<span>Arbitraje</span>
        </a>
        <a href="{{ route('comunicados') }}" class="item negro">
            <i class="fas fa-bullhorn"></i> Comunicados<span>Noticias</span>
        </a>
        <a href="{{ route('eventos') }}" class="item dorado">
            <i class="fas fa-calendar-alt"></i> Eventos<span>Actividades</span>
        </a>
        <a href="{{ route('contactos') }}" class="item rojo">
            <i class="fas fa-phone"></i> Contactos<span>Info</span>
        </a>
        <a href="{{ route('login') }}" class="item negro">
            <i class="fas fa-envelope-open-text"></i> Mesa de Partes<span>Trámites</span>
        </a>
    </aside>

    <section class="seccion-central">
        
        <div class="portada-hero">
            
            <div class="portada-imagen">
                <div class="hero-slider">
                    @if(isset($sliderData) && $sliderData->detalles->count() > 0)
                        
                        @foreach($sliderData->detalles as $index => $img)
                        <a href="{{ $img->url_enlace }}" target="_blank">
                            <img src="{{ asset('storage/' . $img->ruta_imagen) }}" 
                                 alt="Slide {{ $index }}" 
                                 class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                        </a>
                        @endforeach

                        @if($sliderData->detalles->count() > 1)
                            <button class="slider-arrow prev-arrow" id="sliderPrev" type="button">❮</button>
                            <button class="slider-arrow next-arrow" id="sliderNext" type="button">❯</button>
                        @endif

                    @else
                        {{-- FALLBACK --}}
                        <img src="{{ asset('img/appmovil.jpg') }}" alt="Edificio CIP" class="hero-slide active">
                    @endif
                </div>
            </div>
            
            <div class="portada-info">
                @if(isset($sliderData))
                    <div class="small-title">NUESTRAS NOVEDADES</div>
                    <div class="main-title">{{ $sliderData->titulo }}</div>
                    <div class="description">
                        {!! strip_tags($sliderData->descripcion) !!}
                    </div>
                @else
                    <div class="small-title">CUMPLIMOS MÁS DE</div>
                    <div class="main-title">25 AÑOS EN LA SOLUCIÓN DE CONTROVERSIAS</div>
                    <div class="description">
                        Somos considerados uno de los mejores Centros de Arbitraje del Perú.
                    </div>
                @endif
                
                <div class="hero-buttons">
                    <a href="#" class="btn-hero btn-red-light">Arbitraje</a>
                    <a href="#" class="btn-hero btn-gold">Junta de Resolución de Disputas</a>
                    <a href="#" class="btn-hero btn-red-light">Dispute Boards</a>
                </div>
            </div>

        </div>

        <div class="titulo-seccion">Próximos Eventos - Ver Más</div>
        
        <div class="grid-eventos">
            <div class="card-evento">
                <img src="{{ asset('img/pop-up1.png') }}" alt="Evento 1">
                <div class="card-evento-info">
                    <strong>30/07/2025</strong>
                    WORKSHOP DE TECNOLOGÍAS
                </div>
            </div>
            <div class="card-evento">
                <img src="{{ asset('img/pop-up2.png') }}" alt="Evento 2">
                <div class="card-evento-info">
                    <strong>30/07/2025</strong>
                    DESARROLLO ENERGÉTICO
                </div>
            </div>
            <div class="card-evento">
                <img src="{{ asset('img/gestion.png') }}" alt="Evento 3">
                <div class="card-evento-info">
                    <strong>30/07/2025</strong>
                    AFRONTAR UNA DENUNCIA
                </div>
            </div>
            <div class="card-evento">
                <img src="{{ asset('img/denuncia.png') }}" alt="Evento 4">
                <div class="card-evento-info">
                    <strong>30/07/2025</strong>
                    GESTIÓN DE PROYECTOS
                </div>
            </div>
        </div>

    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    const btnPrev = document.getElementById('sliderPrev');
    const btnNext = document.getElementById('sliderNext');
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    let slideInterval;

    function changeSlide(direction) {
        slides[currentSlide].classList.remove('active');

        if (direction === 'next') {
            currentSlide = (currentSlide + 1) % totalSlides;
        } else {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        }

        slides[currentSlide].classList.add('active');
    }

    function resetTimer() {
        clearInterval(slideInterval);
        slideInterval = setInterval(() => changeSlide('next'), 5000);
    }

    if (totalSlides > 1) {
        slideInterval = setInterval(() => changeSlide('next'), 5000);

        if (btnNext && btnPrev) {
            btnNext.addEventListener('click', (e) => {
                e.preventDefault(); 
                changeSlide('next');
                resetTimer();
            });

            btnPrev.addEventListener('click', (e) => {
                e.preventDefault();
                changeSlide('prev');
                resetTimer();
            });
        }
    }
});
</script>
@endsection