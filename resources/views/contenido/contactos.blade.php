@extends('inicio')

@section('title', 'Contactos - CARD CD La Libertad')

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
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d698.2323527845317!2d-79.03755024624849!3d-8.121972155743038!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91ad3d73f5f841bb%3A0x695f9a8cf95bb611!2sMart%C3%ADnez%20de%20Compa%C3%B1%C3%B3n%20901%2C%20V%C3%ADctor%20Larco%20Herrera%2013008!5e0!3m2!1ses!2spe!4v1764254842091!5m2!1ses!2spe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <div class="info-column">
            <h2>Contactos</h2>

            <div class="info-block">
                <span class="info-label">Horario de atención presencial:</span>
                <div class="info-text">
                    Lunes a viernes, de 9:00am a 01:00pm y de 4:00pm a 8:00pm.
                </div>
                <div class="info-text">
                    Sábado, de 9:00am a 01:00pm.
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Correos electrónicos:</span>
                <div class="info-text">
                    <div><a href="arbitrajecdll@cip.org.pe" class="info-link">arbitrajecdll@cip.org.pe</a></div>
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Teléfono:</span>
                <div class="info-text">
                    <div> 044-340010 - Anexo: 229</div>
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Dirección:</span>
                <div class="info-text">
                    Martínez de Compañon #901 - Urb. San Andrés - Trujillo
                </div>
            </div>

        </div>

    </div>

</div>

@endsection