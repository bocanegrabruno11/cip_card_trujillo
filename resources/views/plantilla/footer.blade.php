<style>
    /* Estilos para el footer */
    .cip-footer {
        background-color: #2a2a29; /* Fondo oscuro */
        color: #f8f9fa; /* Texto claro */
        padding: 40px 0 20px 0; /* Espaciado superior, inferior y lateral */
        font-family: Arial, sans-serif;
        font-size: 0.9em;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px; /* Padding para evitar que el contenido toque los bordes en pantallas pequeñas */
        flex-wrap: wrap; /* Permitir que los elementos se envuelvan en pantallas pequeñas */
    }

    .footer-section {
        flex: 1;
        min-width: 280px; /* Ancho mínimo para cada sección antes de envolverse */
        margin-bottom: 20px; /* Espacio entre secciones en pantallas pequeñas */
        padding-right: 20px; /* Espacio entre columnas */
    }

    .footer-section:last-child {
        padding-right: 0; /* Eliminar padding de la última sección */
    }

    .footer-logo-container {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .footer-logo {
        height: 60px; /* Ajusta el tamaño del logo según sea necesario */
        margin-right: 15px;
    }

    .footer-section h3 {
        font-size: 1.3em;
        margin-bottom: 20px;
        color: #D7B56D; /* Color dorado para los títulos */
        font-weight: bold;
    }
    
    .footer-section p {
        line-height: 1.6;
        margin-bottom: 10px;
        color: #cccccc; /* Un gris más claro para el texto general */
    }

    .footer-section .contact-info {
        margin-bottom: 15px;
    }

    .footer-section .contact-info strong {
        display: block;
        margin-bottom: 5px;
        color: #f8f9fa;
    }

    .footer-section .contact-info a {
        color: #D7B56D; /* Enlaces de correo dorado */
        text-decoration: none;
        display: block;
        margin-bottom: 5px;
    }

    .footer-section .contact-info a:hover {
        text-decoration: underline;
    }

    .social-icons a {
        display: inline-block;
        color: #f8f9fa;
        background-color: #4a4a4a; /* Fondo para los iconos de redes sociales */
        width: 35px;
        height: 35px;
        line-height: 35px;
        text-align: center;
        border-radius: 50%;
        margin-right: 10px;
        transition: background-color 0.3s ease;
    }

    .social-icons a:hover {
        background-color: #D7B56D; /* Dorado en hover */
        color: #2a2a29;
    }

    .footer-bottom {
    background-color: #1a1a19; 
    color: #cccccc;
    padding: 15px 20px;
    text-align: center;
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    align-items: center;
    /* --- Asegurar que se centre correctamente --- */
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
    /* ------------------------------------------ */
}

    .footer-bottom-links a {
        color: #cccccc;
        text-decoration: none;
        margin: 0 10px;
        transition: color 0.3s ease;
    }

    .footer-bottom-links a:hover {
        color: #D7B56D;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .footer-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .footer-section {
            padding-right: 0;
            margin-bottom: 30px;
        }
        .footer-section h3 {
            margin-top: 20px; /* Espacio superior para títulos en móvil */
        }
        .footer-logo-container {
            justify-content: center;
        }
        .footer-section:nth-child(2), .footer-section:nth-child(3) {
            border-top: 1px solid #4a4a4a; /* Separador entre secciones en móvil */
            padding-top: 20px;
        }
        .footer-bottom {
            flex-direction: column;
            gap: 10px;
        }
        .footer-bottom-links {
            margin-top: 15px;
        }
    }

    @media (max-width: 576px) {
        .footer-bottom-links {
            flex-direction: column;
            gap: 5px;
        }
        .footer-bottom-links a {
            margin: 5px 0;
        }
    }

    /* Asumo que Font Awesome está incluido en el proyecto */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

    /* Botón flotante de WhatsApp */
    .whatsapp-float-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: #25D366; /* Color de WhatsApp */
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 60px;
        font-size: 2.2em;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        transition: transform 0.3s ease, background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none; /* Asegurar que no tenga subrayado */
    }

    .whatsapp-float-btn:hover {
        transform: scale(1.1);
        background-color: #1DA851;
    }
</style>

<footer class="cip-footer">
    <div class="footer-content">
        {{-- Sección 1: Logo y Descripción del CARD --}}
        <div class="footer-section">
            <div class="footer-logo-container">
                <img src="{{ asset('img/logo.png') }}" alt="Logo CIP" class="footer-logo">
                {{-- CAMBIO: TRUJILLO --}}
                <h3>CONSEJO DEPARTAMENTAL <br> LA LIBERTAD</h3>
            </div>
            <p>
                El Centro de Arbitraje y Resolución de Disputas del Consejo Departamental de La Libertad del Colegio de Ingenieros del Perú, en adelante el CARD, es un ente encargado de la dirección y administración de unidades de solución de conflicto, orientadas a brindar a la sociedad alternativas eficientes para la solución de controversias.
            </p>
        </div>

        {{-- Sección 2: Contáctenos --}}
        <div class="footer-section">
            <h3>Contáctenos</h3>
            <div class="contact-info">
                {{-- CAMBIO: HORARIO DE TRABAJO --}}
                <p>
                    Lunes a viernes: 9:00 a 13:00 horas y 16:00 a 20:00 horas.<br>
                    Sábados: 9:00 a 13:00 horas.
                </p>
                <strong>Correos electrónicos:</strong>
                <a href="mailto:arbitrajecdll@cip.org.pe">arbitrajecdll@cip.org.pe</a>
            </div>
        </div>

        {{-- Sección 3: Síguenos en --}}
        <div class="footer-section">
            <h3>Síguenos en:</h3>
            <div class="social-icons">
                <a href="https://www.facebook.com/CIPLaLibertad?locale=es_LA" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <!-- <a href="https://www.instagram.com/ciptrujillo" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://www.linkedin.com/company/ciptrujillo" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://www.youtube.com/channel/UC-CIP_Trujillo" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a> -->
            </div>
        </div>
    </div>

    {{-- Footer inferior con Copyright y Enlaces --}}
    <div class="footer-bottom">
        {{-- CAMBIO: TRUJILLO --}}
        <span>Copyright {{ date('Y') }} - CENTRO DE ARBITRAJE Y RESOLUCIÓN DE DISPUTAS CD LL</span>
        <div class="footer-bottom-links">
            {{-- Mantenemos los enlaces sin ruta (#) según la solicitud --}}
            <a href="{{ route('presentacion') }}">Presentación</a>
            <a href="{{ route('eventos') }}">Eventos</a>
            <a href="{{ route('junta-prevencion') }}">Junta de Prevención y Resolución de Disputas (CAJPRD)</a>
            <a href="{{ route('institucion-arbitral') }}">Institución Arbitral</a>
        </div>
    </div>


</footer>