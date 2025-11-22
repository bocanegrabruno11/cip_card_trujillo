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
        <h1>Cláusula modelo Dispute Review Board (DRB)</h1>
    </div>

    <div class="clause-box">
        <p class="clause-text">
            <span class="quote-mark">«</span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis nobis dolorem perspiciatis, amet atque molestias architecto provident sint fugit, ipsa, dolor ex. Perspiciatis consectetur ea ratione voluptatum? Blanditiis, perspiciatis? Perferendis?<span class="quote-mark">”.</span>
        </p>
    </div>

</div>

@endsection