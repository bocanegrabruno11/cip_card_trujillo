@extends('inicio')

@section('title', 'Licencia de Funcionamiento - CIP La Libertad')

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

    /* === CONTENEDOR DE LA IMAGEN === */
    .license-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background-color: #f9f9f9; /* Fondo gris suave para resaltar el documento */
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .license-img {
        max-width: 100%; /* Responsivo: nunca excederá el ancho de pantalla */
        width: auto;     /* Ancho natural hasta el max-width */
        height: auto;
        max-height: 800px; /* Límite de altura para que no sea eterno en PC */
        box-shadow: 0 10px 30px rgba(0,0,0,0.15); /* Sombra tipo papel */
        border-radius: 4px;
        cursor: zoom-in;
        transition: transform 0.3s ease;
    }

    .license-img:hover {
        transform: scale(1.02);
    }

    /* === LIGHTBOX (Estilos Reutilizables) === */
    .lightbox-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; justify-content: center; align-items: center; }
    .lightbox-overlay.active { display: flex; }
    .lightbox-img { max-width: 95%; max-height: 95vh; object-fit: contain; border-radius: 4px; background: white; }
    .lightbox-close { position: absolute; top: 20px; right: 20px; color: white; font-size: 40px; cursor: pointer; background: none; border: none; }
</style>
@endsection

@section('content')

<div class="license-container">
    
    <div class="page-title">
        <h1>Licencia de Funcionamiento</h1>
    </div>

    <div class="license-wrapper">
        <img src="{{ asset('img/licencias/2025.png') }}" 
             alt="Licencia de Funcionamiento CARD" 
             class="license-img"
             onclick="openLightbox(this.src)">
    </div>

</div>

@endsection

@section('scripts')
<div class="lightbox-overlay" id="lightboxOverlay">
    <button class="lightbox-close" id="lightboxClose">×</button>
    <img src="" id="lightboxImage" class="lightbox-img">
</div>

<script>
    const lightbox = document.getElementById('lightboxOverlay');
    const lightboxImg = document.getElementById('lightboxImage');
    const closeBtn = document.getElementById('lightboxClose');

    function openLightbox(src) {
        lightboxImg.src = src;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; // Bloquear scroll
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        lightboxImg.src = '';
        document.body.style.overflow = 'auto'; // Activar scroll
    }

    if(closeBtn) closeBtn.addEventListener('click', closeLightbox);
    
    lightbox.addEventListener('click', (e) => {
        if(e.target === lightbox) closeLightbox();
    });

    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') closeLightbox();
    });
</script>
@endsection