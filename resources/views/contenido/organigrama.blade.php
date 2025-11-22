@extends('inicio')

@section('title', 'Organigrama - CIP La Libertad')

@section('styles')
<style>
    .org-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
        text-align: center; /* Centrar todo */
    }

    .page-title {
        margin-bottom: 40px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
        text-align: left; /* El título se ve mejor a la izquierda */
    }

    .page-title h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    /* Contenedor de la imagen */
    .chart-wrapper {
        display: inline-block;
        max-width: 100%;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .chart-wrapper:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .organigrama-img {
        max-width: 100%;
        height: auto;
        display: block;
        cursor: zoom-in; /* Icono de lupa */
    }

    /* Estilos del Modal (Lightbox) para reutilizar */
    .lightbox-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; justify-content: center; align-items: center; }
    .lightbox-overlay.active { display: flex; }
    .lightbox-img { max-width: 95%; max-height: 95vh; object-fit: contain; border-radius: 4px; background: white; }
    .lightbox-close { position: absolute; top: 20px; right: 20px; color: white; font-size: 40px; cursor: pointer; background: none; border: none; }
</style>
@endsection

@section('content')

<div class="org-container">
    
    <div class="page-title">
        <h1>Organigrama Estructural</h1>
    </div>

    <div class="chart-wrapper">
        <img src="{{ asset('img/organigrama.png') }}" 
             alt="Organigrama CIP" 
             class="organigrama-img" 
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
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        lightboxImg.src = '';
        document.body.style.overflow = 'auto';
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