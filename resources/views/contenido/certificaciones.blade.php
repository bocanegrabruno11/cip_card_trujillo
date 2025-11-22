@extends('inicio')

@section('title', 'Certificaciones - CIP La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .cert-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
    }

    /* HEADER DE PÁGINA */
    .page-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 40px;
    }
    .page-header h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    /* === TEXTO DESTACADO ROJO === */
    .main-headline {
        color: #E31E24; /* Rojo Institucional */
        font-size: 28px;
        font-weight: bold;
        text-align: center;
        line-height: 1.4;
        max-width: 800px;
        margin: 0 auto 60px; /* Margen inferior amplio */
    }

    /* === GRID DE CERTIFICADOS === */
    .cert-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* 4 Columnas */
        gap: 30px;
        align-items: start;
    }

    .cert-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    /* Estilo de la imagen del certificado (Documento A4 vertical) */
    .cert-img {
        width: 100%;
        height: auto;
        aspect-ratio: 210/297; /* Proporción A4 */
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        border: 1px solid #eee;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer; /* Indica que se puede ampliar */
        margin-bottom: 20px;
    }

    .cert-img:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    }

    .cert-title {
        font-size: 16px;
        font-weight: bold;
        color: #000;
        margin-bottom: 8px;
    }

    .cert-desc {
        font-size: 13px;
        color: #666;
        line-height: 1.4;
        max-width: 90%;
    }

    /* === MODAL LIGHTBOX (Estilos Reutilizados) === */
    .lightbox-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 10000; justify-content: center; align-items: center; }
    .lightbox-overlay.active { display: flex; }
    .lightbox-content { position: relative; max-width: 90%; max-height: 95%; display: flex; justify-content: center; }
    .lightbox-img { max-width: 100%; max-height: 90vh; object-fit: contain; border-radius: 4px; background: white; }
    .lightbox-close { position: absolute; top: -40px; right: -40px; color: white; font-size: 35px; cursor: pointer; background: none; border: none; transition: transform 0.2s; }
    .lightbox-close:hover { transform: scale(1.2); color: #FF6B6B; }

    /* === RESPONSIVIDAD === */
    @media (max-width: 1024px) {
        .cert-grid { grid-template-columns: repeat(2, 1fr); } /* Tablet: 2 columnas */
        .main-headline { font-size: 24px; }
    }

    @media (max-width: 600px) {
        .cert-grid { grid-template-columns: 1fr; } /* Móvil: 1 columna */
        .main-headline { font-size: 20px; text-align: left; }
        .lightbox-close { top: 10px; right: 10px; background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px; }
    }
</style>
@endsection

@section('content')

<div class="cert-container">
    
    <div class="page-header">
        <h1>Certificación</h1>
    </div>

    <div class="main-headline">
        El CARD cuenta con el respaldo y certificación de las siguientes normas en Arbitraje Institucional
    </div>

    <div class="cert-grid">
        
        <div class="cert-card">
            <img src="https://via.placeholder.com/300x420/fff/ccc?text=ISO+27001" 
                 alt="ISO/IEC 27001:2022" 
                 class="cert-img"
                 onclick="openLightbox(this.src)">
            <div class="cert-title">ISO/IEC 27001:2022</div>
            <div class="cert-desc">Estándar para la Seguridad de la Información</div>
        </div>

        <div class="cert-card">
            <img src="https://via.placeholder.com/300x420/fff/ccc?text=ISO+9001" 
                 alt="ISO 9001:2015" 
                 class="cert-img"
                 onclick="openLightbox(this.src)">
            <div class="cert-title">ISO 9001:2015</div>
            <div class="cert-desc">Sistema de Gestión de la Calidad</div>
        </div>

        <div class="cert-card">
            <img src="https://via.placeholder.com/300x420/fff/ccc?text=ISO+37001" 
                 alt="ISO 37001:2016" 
                 class="cert-img"
                 onclick="openLightbox(this.src)">
            <div class="cert-title">ISO 37001:2016</div>
            <div class="cert-desc">Sistema de Gestión Antisoborno</div>
        </div>

        <div class="cert-card">
            <img src="https://via.placeholder.com/300x420/fff/ccc?text=ISO+37001+SGSI" 
                 alt="ISO 37001:2016 SGSI" 
                 class="cert-img"
                 onclick="openLightbox(this.src)">
            <div class="cert-title">ISO 37001:2016</div>
            <div class="cert-desc">Sistemas de Gestión de la Seguridad de la Información (SGSI)</div>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<div class="lightbox-overlay" id="lightboxOverlay">
    <div class="lightbox-content">
        <button class="lightbox-close" id="lightboxClose">✕</button>
        <img src="" id="lightboxImage" class="lightbox-img">
    </div>
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