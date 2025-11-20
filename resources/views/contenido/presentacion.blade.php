@extends('inicio')

@section('title', 'Presentación - CIP La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .presentation-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
    }

    /* === TÍTULO DE PÁGINA === */
    .page-title {
        margin-bottom: 40px;
        border-bottom: 1px solid #eee; /* Línea suave separadora */
        padding-bottom: 20px;
    }

    .page-title h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    /* === LAYOUT DE CONTENIDO (Flexbox) === */
    .content-grid {
        display: flex;
        gap: 50px;
        margin-bottom: 60px;
        align-items: flex-start; /* Alinear al inicio */
    }

    /* Columna Izquierda (Botón e Imagen) */
    .left-column {
        flex: 1; /* Ocupa aprox 40% */
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    /* Columna Derecha (Texto y Logos) */
    .right-column {
        flex: 1.5; /* Ocupa aprox 60% */
    }

    /* === BOTÓN DE DESCARGA === */
    .btn-download {
        background-color: #FF6B6B; /* Color coral/rojo similar a la imagen */
        color: white;
        text-decoration: none;
        padding: 15px 20px;
        border-radius: 4px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background 0.3s;
        width: 100%;
        max-width: 300px; /* Que no sea gigante */
        margin: 0 auto; /* Centrado en su columna */
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .btn-download:hover {
        background-color: #ff4c4c;
    }

    /* === IMAGEN PRINCIPAL === */
    .main-building-img {
        width: 100%;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        object-fit: cover;
    }

    /* === TEXTO === */
    .text-content p {
        color: #555;
        line-height: 1.8;
        font-size: 15px;
        margin-bottom: 25px;
        text-align: justify;
    }

    /* === LOGOS DE CERTIFICACIÓN === */
    .certification-logos {
        display: flex;
        justify-content: flex-end; /* Alineados a la derecha como en la foto */
        gap: 20px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .cert-logo {
        height: 60px; /* Altura fija para uniformidad */
        width: auto;
        filter: grayscale(0%); /* Opcional: grayscale(100%) si los quieres grises */
    }

    /* === GALERÍA INFERIOR (4 IMÁGENES) === */
    .bottom-gallery {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* 4 columnas iguales */
        gap: 20px;
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #eee;
    }

    .gallery-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        transition: transform 0.3s;
        background-color: #eee; /* Fondo mientras carga */
    }

    .gallery-img:hover {
        transform: scale(1.05);
    }

    /* === RESPONSIVIDAD === */
    @media (max-width: 900px) {
        .content-grid {
            flex-direction: column; /* Uno debajo del otro en tablets/móviles */
            gap: 30px;
        }

        .left-column, .right-column {
            width: 100%;
            flex: none;
        }

        .btn-download {
            max-width: 100%; /* Botón ancho completo en móvil */
        }

        .certification-logos {
            justify-content: center; /* Logos centrados en móvil */
        }

        .bottom-gallery {
            grid-template-columns: repeat(2, 1fr); /* 2 columnas en tablet */
        }
    }

    @media (max-width: 600px) {
        .bottom-gallery {
            grid-template-columns: 1fr; /* 1 columna en celular */
        }
    }
</style>
@endsection

@section('content')

<div class="presentation-container">
    
    <div class="page-title">
        <h1>Presentación</h1>
    </div>

    <div class="content-grid">
        
        <div class="left-column">
            <a href="#" class="btn-download">
                <i class="fas fa-file-pdf"></i> CREACION DEL CARD
            </a>
            
            <img src="{{ asset('img/appmovil.jpg') }}" alt="Edificio CIP" class="main-building-img">
        </div>

        <div class="right-column">
            <div class="text-content">
                <p>
                    El Centro de Arbitraje y Resolución de Disputas del Consejo Departamental de La Libertad del Colegio de Ingenieros del Perú, en adelante el CARD, es un ente encargado de la dirección y administración de unidades de solución de conflicto orientadas a brindar a la sociedad alternativas eficientes para la solución de controversias.
                </p>
                <p>
                    El Centro de Arbitraje y Resolución de Disputas tiene por finalidad coadyuvar a la solución de controversias por medio de la administración de arbitrajes y juntas de resolución de disputas ("dispute boards"), entre otros. Tiene como objeto prestar los servicios de organización y administración en los procesos de Arbitraje, contribuyendo de esta manera a la solución de las controversias que surjan entre las Partes. Asimismo, dentro de sus fines se encuentra fomentar la institucionalización de los citados métodos alternativos de solución de conflictos, promoviendo su investigación y difusión a nivel nacional e internacional.
                </p>
            </div>

            <div class="certification-logos">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/ISO_9001-2015.svg/1200px-ISO_9001-2015.svg.png" class="cert-logo" alt="ISO 9001">
                <img src="https://seeklogo.com/images/S/sgs-logo-0449458015-seeklogo.com.png" class="cert-logo" alt="SGS">
                <img src="https://seeklogo.com/images/I/icontec-internacional-logo-2D0D470768-seeklogo.com.png" class="cert-logo" alt="Icontec">
            </div>
        </div>

    </div>

    <div class="bottom-gallery">
        <img src="{{ asset('img/pop-up1.png') }}" alt="Galeria 1" class="gallery-img">
        <img src="{{ asset('img/pop-up2.png') }}" alt="Galeria 2" class="gallery-img">
        <img src="{{ asset('img/gestion.png') }}" alt="Galeria 3" class="gallery-img">
        <img src="{{ asset('img/denuncia.png') }}" alt="Galeria 4" class="gallery-img">
    </div>

</div>

@endsection