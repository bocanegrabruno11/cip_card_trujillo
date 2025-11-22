@extends('inicio')

@section('title', 'Contactos - CIP La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
        min-height: 60vh;
    }

    /* Título de Página */
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

    /* === GRID DE CONTACTO (MAPA + INFO) === */
    .contact-grid {
        display: flex;
        gap: 50px;
        align-items: flex-start;
    }

    /* Columna Izquierda: MAPA */
    .map-column {
        flex: 1.5; /* El mapa ocupa un poco más de espacio */
        width: 100%;
    }

    .map-wrapper {
        width: 100%;
        height: 450px; /* Altura fija para el mapa */
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }

    .map-wrapper iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Columna Derecha: INFORMACIÓN */
    .info-column {
        flex: 1;
        padding-top: 10px;
    }

    .info-column h2 {
        color: #333;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 25px;
    }

    /* Bloques de información */
    .info-block {
        margin-bottom: 25px;
    }

    .info-label {
        color: #555;
        font-weight: bold;
        font-size: 15px;
        margin-bottom: 5px;
        display: block;
    }

    .info-text {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Enlaces (Correos y Teléfonos) */
    .info-link {
        color: #0056b3; /* Azul corporativo */
        text-decoration: none;
        transition: color 0.2s;
    }
    .info-link:hover {
        color: #AD2B2E; /* Rojo al pasar mouse */
        text-decoration: underline;
    }

    /* Lista de teléfonos para que se vean ordenados */
    .phone-list span {
        display: inline-block;
        margin-right: 5px;
    }
    .phone-list span::after {
        content: "|";
        margin-left: 5px;
        color: #ccc;
    }
    .phone-list span:last-child::after {
        content: "";
    }

    /* === RESPONSIVIDAD === */
    @media (max-width: 900px) {
        .contact-grid {
            flex-direction: column; /* Uno debajo del otro */
            gap: 30px;
        }

        .map-wrapper {
            height: 300px; /* Mapa un poco más pequeño en móvil */
        }

        .info-column {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')

<div class="contact-container">
    
    <div class="page-title">
        <h1>Contactos</h1>
    </div>

    <div class="contact-grid">
        
        <div class="map-column">
           <div class="map-wrapper">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.936394189018!2d-79.02854282500953!3d-8.108028491920575!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91ad3d8450273c03%3A0x460c56f7d555031a!2sFrancisco%20Borja%20250%2C%20Trujillo%2013001!5e0!3m2!1ses!2spe!4v1708630000000!5m2!1ses!2spe" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

        <div class="info-column">
            <h2>Contactos</h2>

            <div class="info-block">
                <span class="info-label">Horario de atención presencial:</span>
                <div class="info-text">
                    Lunes a viernes, de 8:30 a 12:30 horas y de 13:30 a 18:00 horas.
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Correos electrónicos:</span>
                <div class="info-text">
                    <div><a href="mailto:mesadepartescard@ciplalibertad.org.pe" class="info-link">mesadepartescard@ciplalibertad.org.pe</a></div>
                    <div><a href="mailto:mesadepartesjrd@ciplalibertad.org.pe" class="info-link">mesadepartesjrd@ciplalibertad.org.pe</a></div>
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Teléfonos:</span>
                <div class="info-text phone-list">
                    <span>202-5045</span>
                    <span>202-5069</span>
                    <span>202-5064</span>
                    <span>202-5081</span>
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Móviles:</span>
                <div class="info-text">
                    <div>941 965 539 | 963 848 824 (Arbitraje)</div>
                    <div>987 818 665 (Junta de Resolución de Disputas)</div>
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Dirección:</span>
                <div class="info-text">
                    Urb. La Merced, Francisco Borja N° 250, Trujillo, La Libertad.
                </div>
            </div>

        </div>

    </div>

</div>

@endsection