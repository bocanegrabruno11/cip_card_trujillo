@extends('inicio')

@section('title', 'Modelo de Cláusula Arbitral - CIP La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .clause-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
        min-height: 60vh; /* Altura mínima para que no se vea vacío */
    }

    /* === HEADER DE PÁGINA === */
    .page-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
        margin-bottom: 60px;
    }
    .page-header h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    /* === CONTENIDO DE LA CLÁUSULA === */
    .clause-box {
        max-width: 900px; /* Ancho controlado para lectura */
        margin: 0 auto;   /* Centrado horizontal */
        text-align: center;
        padding: 0 20px;
    }

    .clause-text {
        color: #555;
        font-size: 18px; /* Letra grande y legible */
        line-height: 2;  /* Espaciado generoso */
        font-style: normal;
        position: relative;
    }

    /* Estilo para las comillas francesas si deseas resaltarlas */
    .quote-mark {
        font-weight: bold;
        color: #888;
    }

    /* === RESPONSIVIDAD === */
    @media (max-width: 768px) {
        .page-header h1 { font-size: 24px; }
        .clause-text { font-size: 15px; line-height: 1.8; text-align: justify; }
    }
</style>
@endsection

@section('content')

<div class="clause-container">
    
    <div class="page-header">
        <h1>Modelo de Cláusula Arbitral</h1>
    </div>

    <div class="clause-box">
        <p class="clause-text">
            <span class="quote-mark">«</span>Las partes acuerdan que todo litigio o controversia resultante de este contrato 
            o relativo a este, se resolverá mediante el arbitraje organizado y administrado por el Centro de Arbitraje 
            del Colegio de Ingenieros del Perú – Consejo Departamental de La Libertad, de conformidad con sus reglamentos 
            vigentes, a los cuales las partes se someten libremente, señalando que el laudo que se emita en el proceso 
            será inapelable y definitivo.<span class="quote-mark">”.</span>
        </p>
    </div>

</div>

@endsection