@extends('inicio')

@section('title', 'Licencia de Funcionamiento - CARD CD La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .license-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
        min-height: 60vh; /* Asegurar que ocupe buen espacio vertical */
    }

    /* === TÍTULO === */
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

    /* === CONTENEDOR DE LAS IMÁGENES === */
    .license-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap; /* Permite que bajen si la pantalla es pequeña */
        gap: 40px; /* Separación entre los documentos */
        padding: 40px 20px;
        background-color: #f9f9f9; 
        border-radius: 8px;
        border: 1px solid #eee;
    }

    /* === ESTILOS INDIVIDUALES DE DOCUMENTOS === */
    .document-item {
        position: relative;
        cursor: zoom-in;
    }

    .license-img {
        max-width: 100%; 
        width: auto;     
        height: 500px; /* Altura fija para que ambos botones/imágenes tengan exactamente el mismo tamaño */
        object-fit: contain;
        background-color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15); /* Sombra tipo papel */
        border-radius: 4px;
        transition: transform 0.3s ease;
        display: block;
    }

    .document-item:hover .license-img {
        transform: scale(1.02);
    }

    /* Pequeña etiqueta para indicar que tiene 2 páginas */
    .doc-badge {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background-color: #AD2B2E;
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        pointer-events: none; /* Evita que estorbe al hacer clic */
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    /* === LIGHTBOX MODIFICADO PARA MÚLTIPLES IMÁGENES === */
    .lightbox-overlay { 
        display: none; 
        position: fixed; 
        top: 0; left: 0; 
        width: 100%; height: 100%; 
        background: rgba(0,0,0,0.9); 
        z-index: 10000; 
        justify-content: center; 
        align-items: flex-start; /* Alineado arriba para permitir scroll */
    }
    .lightbox-overlay.active { display: flex; }
    
    .lightbox-close { 
        position: fixed; /* Fijo para que no se pierda al bajar */
        top: 20px; right: 30px; 
        color: white; font-size: 40px; 
        cursor: pointer; background: none; border: none; 
        z-index: 10001;
    }

    /* Contenedor que permite hacer scroll si hay varias páginas */
    .lightbox-gallery {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 30px; /* Espacio entre las páginas del PDF */
        width: 100%;
        height: 100%;
        overflow-y: auto;
        padding: 50px 20px;
    }

    .lightbox-gallery img { 
        max-width: 95%; 
        max-height: 95vh; 
        object-fit: contain; 
        border-radius: 4px; 
        background: white; 
        flex-shrink: 0; /* Evita que se aplasten */
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .license-img { height: 400px; }
    }
</style>
@endsection

@section('content')

<div class="license-container">
    
    <div class="page-title">
        <h1>Licencias de Funcionamiento</h1>
    </div>

    <div class="license-wrapper">
        
        <!-- DOCUMENTO 1: Licencia anterior (1 página) -->
        <div class="document-item" onclick="openLightbox(['{{ asset('img/licencias/2025.png') }}'])">
            <img src="{{ asset('img/licencias/2025.png') }}" 
                 alt="Licencia de Funcionamiento 2025" 
                 class="license-img">
        </div>

        <!-- DOCUMENTO 2: Licencia nueva (2 páginas) -->
        <!-- DOCUMENTO 2: Licencia nueva (7 páginas) -->
        <div class="document-item" onclick="openLightbox([
                '{{ asset('img/licencias/LICENCIA 13-07-2026_page-0001.jpg') }}', 
                '{{ asset('img/licencias/LICENCIA 13-07-2026_page-0002.jpg') }}',
                '{{ asset('img/licencias/LICENCIA 13-07-2026_page-0003.jpg') }}'
            ])">
            <!-- Solo se muestra la primera como miniatura -->
            <img src="{{ asset('img/licencias/LICENCIA 13-07-2026_page-0001.jpg') }}" 
                 alt="Licencia de Funcionamiento 2026" 
                 class="license-img">
            <div class="doc-badge">Ver 7 páginas</div>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<!-- Lightbox actualizado para contener la galería -->
<div class="lightbox-overlay" id="lightboxOverlay">
    <button class="lightbox-close" id="lightboxClose">×</button>
    <div class="lightbox-gallery" id="lightboxGallery">
        <!-- Las imágenes se insertan aquí vía JS -->
    </div>
</div>

<script>
    const lightbox = document.getElementById('lightboxOverlay');
    const lightboxGallery = document.getElementById('lightboxGallery');
    const closeBtn = document.getElementById('lightboxClose');

    // Ahora la función recibe un array de rutas de imágenes
    function openLightbox(imagesArray) {
        // Limpiamos el lightbox por si había imágenes anteriores
        lightboxGallery.innerHTML = ''; 

        // Creamos una etiqueta <img> por cada ruta en el array
        imagesArray.forEach(src => {
            let img = document.createElement('img');
            img.src = src;
            lightboxGallery.appendChild(img);
        });

        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; // Bloquear scroll del fondo
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        lightboxGallery.innerHTML = ''; // Limpiar al cerrar
        document.body.style.overflow = 'auto'; // Activar scroll del fondo
    }

    if(closeBtn) closeBtn.addEventListener('click', closeLightbox);
    
    lightbox.addEventListener('click', (e) => {
        // Solo cerrar si se hace clic en el fondo oscuro, no en las imágenes
        if(e.target === lightbox || e.target === lightboxGallery) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') closeLightbox();
    });
</script>
@endsection