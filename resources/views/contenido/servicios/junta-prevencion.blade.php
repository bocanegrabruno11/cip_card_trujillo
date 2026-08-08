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
        padding-bottom: 80px;
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
        background-color: var(--rojo-primario);
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

    .card-highlight {
        background-color: #333;
    }
    .card-highlight:hover {
        background-color: #000;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

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
                <h2><i class="fas fa-hard-hat"></i> Presentación JPRD</h2>
                <p>
                    La <strong>Junta de Prevención y Resolución de Disputas (JPRD)</strong> del Colegio de Ingenieros del Perú - CD La Libertad, es un mecanismo moderno de solución de controversias diseñado para acompañar la ejecución de proyectos de construcción. Nuestro objetivo es prevenir que las discrepancias paralicen las obras, emitiendo decisiones técnicas vinculantes y oportunas que garanticen la continuidad de la inversión pública y privada.
                </p>
            </div>
            <div class="presentacion-image-container text-center">
                <img src="{{ asset('img/main-site/2.jpg') }}" alt="JRD CD La Libertad" class="presentacion-img">
            </div>
        </div>
    </section>

    <div class="card-grid">
        <div class="info-card">
            <h3>Normativa JPRD</h3>
            <p>Consulte la Ley de Contrataciones del Estado, su Reglamento y las directivas de OECE que regulan la obligatoriedad y el funcionamiento de la JPRD.</p>
            <a href="{{ route('junta-prevencion.normativa') }}" class="btn-card">Ver Documentos</a>
        </div>

        <div class="info-card">
            <h3>Tarifarios y Calculadora</h3>
            <p>Acceda al cuadro de honorarios de los miembros de la Junta y los gastos administrativos del Centro según los montos de obra.</p>
            <a href="{{ route('junta-prevencion.tarifario') }}" class="btn-card">Ver Tarifario</a>
        </div>

        <div class="info-card">
            <h3>Nómina de Adjudicadores y Secretarios Técnicos</h3>
            <p>Consulte nuestra lista de expertos calificados con amplia experiencia técnica para integrar Juntas de Disputas.</p>
            <a href="{{ route('junta-prevencion.nomina') }}" class="btn-card">Ver Expertos</a>
        </div>

        <div class="info-card">
            <h3>Requisitos</h3>
            <p>Descargue los modelos de cláusulas de solución de controversias para ser incorporados en sus contratos de ejecución de obra.</p>
            <a href="{{ route('junta-prevencion.requisitos-incorporacion') }}" class="btn-card">Entrar</a>
        </div>

        <div class="info-card">
            <h3>Repositorio de Decisiones, Opiniones y Consultas</h3>
            <p>Acceda a las decisiones emitidas por las Juntas, clasificadas para fines académicos y de transparencia (sujeto a confidencialidad).</p>
            <a href="{{ route('junta-prevencion.repositorio') }}" class="btn-card">Acceder al Portal</a>
        </div>

        <div class="info-card card-highlight">
            <h3>Solicitar el servicio</h3>
            <p>Inicie el trámite para la designación de miembros o el registro de una JPRD ante nuestro centro para su obra en curso.</p>
            <a href="{{ route('junta-prevencion.solicitar') }}" class="btn-card">Continuar</a>
        </div>
    </div>
</div>
@endsection