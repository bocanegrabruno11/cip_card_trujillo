@extends('inicio')

@section('title', 'Misión y Visión - CARD CD La Libertad')

@section('styles')
<style>
    /* Contenedor principal centrado */
    .mv-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
    }

    /* Título de la página */
    .mv-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .mv-header h1 {
        color: #AD2B2E; /* Rojo CIP */
        font-size: 36px;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }

    /* Línea decorativa bajo el título */
    .mv-header h1::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background-color: #D7B56D; /* Dorado */
        margin: 10px auto 0;
    }

    /* Grid para las tarjetas */
    .mv-grid {
        display: flex;
        gap: 40px;
        justify-content: center;
        flex-wrap: wrap; /* Para que se adapte a móviles */
    }

    /* Tarjetas de Misión y Visión */
    .mv-card {
        background: white;
        flex: 1;
        min-width: 300px;
        max-width: 500px;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 5px solid #AD2B2E;
        position: relative;
        overflow: hidden;
    }

    .mv-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    /* Iconos */
    .mv-icon {
        font-size: 50px;
        color: #AD2B2E;
        margin-bottom: 20px;
        background-color: #FFF0F0;
        width: 100px;
        height: 100px;
        line-height: 100px;
        border-radius: 50%;
        margin: 0 auto 25px;
    }

    .mv-card h2 {
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .mv-card p {
        color: #666;
        line-height: 1.8;
        font-size: 16px;
        text-align: justify; /* Texto justificado para mayor formalidad */
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .mv-grid {
            flex-direction: column;
            align-items: center;
        }
        .mv-card {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')

<div class="mv-container">
    
    <div class="mv-header">
        <h1>Nuestra Identidad Institucional</h1>
        <p style="color: #777; margin-top: 15px;">Consejo Departamental de La Libertad</p>
    </div>

    <div class="mv-grid">
        
        <div class="mv-card">
            <div class="mv-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h2>Misión</h2>
            <p>
                Administrar servicios especializados de arbitraje y resolución de disputas con imparcialidad, transparencia y la correcta aplicación de los reglamentos, garantizando procesos eficientes y confiables que contribuyan a la solución oportuna de controversias en los sectores público y privado, promoviendo la integridad, la ética profesional y el desarrollo sostenible en beneficio de la sociedad. 

            </p>
        </div>

        <div class="mv-card">
            <div class="mv-icon">
                <i class="fas fa-eye"></i>
            </div>
            <h2>Visión</h2>
            <p>
                Consolidarnos como un centro líder en arbitraje y resolución de disputas, reconocido por la calidad, transparencia y eficiencia en la solución de controversias, contribuyendo al fortalecimiento de la gestión pública y/o privada.
            </p>
        </div>

    </div>

</div>

@endsection