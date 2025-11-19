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

    /* === MENÚ LATERAL === */
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
    
    /* Colores utilitarios */
    .rojo { background-color: #B02E2D; }
    .dorado { background-color: #d9b04c; color: black !important; }
    .negro { background-color: #1f1f1f; }

    /* === SECCIÓN CENTRAL === */
    .seccion-central {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    /* === PORTADA HERO (DISEÑO SPLIT / DIVIDIDO) === */
    .portada-hero {
        display: flex; /* Activar Flexbox para dividir izquierda/derecha */
        background-color: white;
        border-radius: 4px; /* Bordes menos redondeados como la foto */
        overflow: hidden;
        min-height: 380px; /* Altura fija para que se vea imponente */
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Lado Izquierdo: Imagen del Edificio */
    .portada-imagen {
        flex: 1.8; /* Ocupa aprox el 65% del ancho */
        position: relative;
    }
    
    .portada-imagen img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* La imagen cubre todo el espacio sin deformarse */
        display: block;
    }

    /* Lado Derecho: Información Roja */
    .portada-info {
        flex: 1; /* Ocupa aprox el 35% del ancho */
        background-color: #CC0000; /* Rojo intenso similar a la imagen */
        padding: 40px 30px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center; /* Centrar contenido verticalmente */
    }
    
    .small-title {
        font-size: 14px;
        text-transform: uppercase;
        margin-bottom: 5px;
        opacity: 0.9;
        font-weight: normal;
    }

    .main-title {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .description {
        font-size: 15px;
        line-height: 1.5;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    /* Botones del Hero */
    .hero-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-hero {
        padding: 12px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        font-size: 13px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    /* Colores específicos de los botones de la imagen */
    .btn-red-light {
        background-color: #E31E24; /* Rojo un poco más claro que el fondo */
        color: white;
        flex: 1; /* Para que se estiren un poco */
    }
    
    .btn-gold {
        background-color: #D7B56D; /* Dorado/Beige */
        color: white;
        flex: 1.5; /* El del centro es más ancho en la foto */
        text-decoration: underline; /* En la foto parece tener subrayado */
    }

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
    
    .card-evento img {
        width: 100%; height: 250px; object-fit: contain; background: #f9f9f9;
    }
    .card-evento-info { padding: 15px; font-size: 14px; text-align: center; }
    .card-evento-info strong { color: #B02E2D; display: block; margin-bottom: 5px; }

    /* === RESPONSIVIDAD === */
    @media (max-width: 900px) {
        .contenedor-home { flex-direction: column; }
        .menu-lateral { width: 100%; flex-direction: row; overflow-x: auto; padding-bottom: 10px; }
        .menu-lateral .item { min-width: 120px; }
        
        /* En tablet/móvil, la portada se pone una debajo de otra */
        .portada-hero { flex-direction: column; height: auto; }
        .portada-imagen { height: 250px; width: 100%; }
        .portada-info { width: 100%; padding: 30px; }
    }
</style>
@endsection

@section('content')
<div class="contenedor-home">
    
    <aside class="menu-lateral">
        <a href="#" class="item rojo">
            <i class="fas fa-building"></i>
            EL CARD<span>Institución</span>
        </a>
        <a href="#" class="item rojo">
            <i class="fas fa-file-contract"></i>
            Cláusulas<span>Reglamentos</span>
        </a>
        <a href="#" class="item dorado">
            <i class="fas fa-gavel"></i>
            Servicios<span>Arbitraje</span>
        </a>
        <a href="#" class="item negro">
            <i class="fas fa-bullhorn"></i>
            Comunicados<span>Noticias</span>
        </a>
        <a href="#" class="item dorado">
            <i class="fas fa-calendar-alt"></i>
            Eventos<span>Actividades</span>
        </a>
        <a href="#" class="item rojo">
            <i class="fas fa-phone"></i>
            Contactos<span>Info</span>
        </a>
        <a href="#" class="item negro">
            <i class="fas fa-envelope-open-text"></i>
            Mesa de Partes<span>Trámites</span>
        </a>
    </aside>

    <section class="seccion-central">
        
        <div class="portada-hero">
            <div class="portada-imagen">
                <img src="{{ asset('img/appmovil.jpg') }}" alt="Edificio CIP">
            </div>
            
            <div class="portada-info">
                <div class="small-title">CUMPLIMOS MÁS DE</div>
                <div class="main-title">25 AÑOS EN LA SOLUCIÓN DE CONTROVERSIAS</div>
                <div class="description">
                    Somos considerados uno de los mejores Centros de Arbitraje del Perú.
                </div>
                
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
@endsection