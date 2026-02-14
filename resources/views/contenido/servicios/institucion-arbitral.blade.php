@extends('inicio')

@section('styles')
<style>
    /* === VARIABLES DE COLOR === */
    :root {
        --rojo-primario: #AD2B2E;
        --rojo-oscuro: #8a2225;
        --blanco-fondo: #f8f9fa;
        --gris-texto: #444;
    }

    /* === CONTENEDOR PRINCIPAL === */
    .institucion-main-container {
        padding-bottom: 80px; /* Evita que el footer se pegue a los botones */
    }

    /* === SECCIÓN PRESENTACIÓN === */
    .presentacion-hero {
        background-color: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 60px;
        border-top: 5px solid var(--rojo-primario);
    }
    .presentacion-layout {
        display: flex;
        gap: 40px;
        align-items: center;
    }
    .presentacion-text {
        flex: 1.2;
        font-size: 16px;
        color: var(--gris-texto);
        line-height: 1.8;
        text-align: justify;
    }
    .presentacion-text h2 {
        color: var(--rojo-primario);
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 25px;
        font-size: 24px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .presentacion-text h2::after {
        content: '';
        flex: 1;
        height: 2px;
        background: #eee;
    }
    .presentacion-img {
        width: 100%;
        max-width: 480px;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        object-fit: cover;
    }

    /* === GRID DE TARJETAS === */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .info-card {
        background-color: var(--rojo-primario); /* Cambio de azul a rojo */
        color: white;
        border-radius: 12px;
        padding: 45px 35px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 320px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
    }

    /* Efecto decorativo de fondo */
    .info-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.05);
        transform: rotate(45deg);
        pointer-events: none;
    }

    .info-card:hover { 
        transform: translateY(-12px); 
        background-color: var(--rojo-oscuro);
        box-shadow: 0 15px 35px rgba(173, 43, 46, 0.3);
    }

    .info-card h3 { 
        font-size: 21px; 
        font-weight: 800; 
        margin-bottom: 20px; 
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .info-card p { 
        font-size: 15px; 
        line-height: 1.6;
        opacity: 0.95; 
        margin-bottom: 30px; 
        flex-grow: 1; 
    }

    .btn-card {
        background-color: white; 
        color: var(--rojo-primario);
        padding: 14px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: 2px solid white;
    }
    .btn-card:hover { 
        background-color: transparent; 
        color: white; 
        text-decoration: none;
    }

    /* Tarjeta Especial (Solicitar) */
    .card-highlight {
        background-color: #333; /* Gris muy oscuro para resaltar */
    }
    .card-highlight:hover {
        background-color: #000;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

    /* === RESPONSIVE === */
    @media (max-width: 992px) {
        .presentacion-layout { flex-direction: column; text-align: center; }
        .presentacion-text { text-align: justify; }
        .presentacion-img { max-width: 100%; }
        .presentacion-text h2::after { display: none; }
    }

    @media (max-width: 480px) {
        .presentacion-hero { padding: 25px; }
        .info-card { padding: 35px 25px; min-height: auto; }
        .card-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="container py-5 institucion-main-container">
    
    <section class="presentacion-hero">
        <div class="presentacion-layout">
            <div class="presentacion-text">
                <h2><i class="fas fa-university"></i> Presentación</h2>
                <p>
                    El Centro de Arbitraje y Resolución de Disputas (CARD) del Consejo Departamental de La Libertad 
                    del Colegio de Ingenieros del Perú fue creado con el firme propósito de servir a la comunidad. 
                    Como órgano especializado de nuestra institución, somos los encargados de la administración 
                    y organización de arbitrajes institucionales, garantizando imparcialidad, eficiencia y 
                    transparencia en la resolución de controversias.
                </p>
            </div>
            <div class="presentacion-image-container text-center">
                <img src="{{ asset('img/main-site/2.jpg') }}" alt="CARD CD La Libertad" class="presentacion-img">
            </div>
        </div>
    </section>

    <div class="card-grid">
        <div class="info-card">
            <h3>Normativa</h3>
            <p>Acceda a las leyes, reglamentos internos y decretos que rigen el marco legal de nuestras contrataciones y procesos arbitrales.</p>
            <a href="{{ route('institucion-arbitral.normativa') }}" class="btn-card">Ver Documentos</a>
        </div>

        <div class="info-card">
            <h3>Tarifario y Calculadora</h3>
            <p>Utilice nuestras herramientas interactivas para calcular los costos de arbitraje de manera rápida según la cuantía de su caso.</p>
            <a href="{{ route('institucion-arbitral.tarifario') }}" class="btn-card">Calcular Costos</a>
        </div>

        <div class="info-card">
            <h3>Nómina de Árbitros</h3>
            <p>Consulte la lista actualizada de profesionales certificados y sus especialidades dentro de nuestro centro de arbitraje.</p>
            <a href="{{ route('institucion-arbitral.nomina') }}" class="btn-card">Ver Profesionales</a>
        </div>

        <div class="info-card">
            <h3>Repositorio de Laudos</h3>
            <p>Espacio dedicado a la consulta de resoluciones y laudos emitidos por la institución (Acceso bajo previa autorización).</p>
            <a href="{{ route('institucion-arbitral.repositorio') }}" class="btn-card">Acceder al Portal</a>
        </div>

        <div class="info-card card-highlight">
            <h3>Solicitar el Servicio</h3>
            <p>Inicie formalmente su proceso de resolución de disputas descargando los formularios oficiales de solicitud de arbitraje.</p>
            <a href="{{ route('institucion-arbitral.solicitar') }}" class="btn-card">Entrar</a>
        </div>
    </div>
</div>
@endsection