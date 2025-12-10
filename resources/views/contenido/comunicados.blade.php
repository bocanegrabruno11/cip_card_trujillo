@extends('inicio')

@section('title', 'Comunicados - CARD CD La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .comunicados-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
    }

    /* === TÍTULO === */
    .page-title {
        margin-bottom: 40px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .page-title h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    /* === GRID DE COMUNICADOS === */
    .comunicados-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }

    /* === TARJETA DE COMUNICADO === */
    .comunicado-card {
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        background: white;
        display: flex;
        flex-direction: column;
    }

    .comunicado-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }

    .img-wrapper {
        position: relative;
        width: 100%;
        padding-top: 140%; /* Relación de aspecto vertical (tipo A4) */
        overflow: hidden;
        cursor: pointer;
        background-color: #f9f9f9;
    }

    .comunicado-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; /* Cubre el espacio */
        transition: transform 0.5s ease;
    }

    .img-wrapper:hover .comunicado-img {
        transform: scale(1.05);
    }

    /* Overlay al pasar el mouse (Icono lupa) */
    .hover-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .img-wrapper:hover .hover-overlay {
        opacity: 1;
    }

    .hover-overlay i {
        color: white;
        font-size: 30px;
        background: rgba(0,0,0,0.5);
        padding: 15px;
        border-radius: 50%;
    }

    .comunicado-info {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .comunicado-title {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .comunicado-date {
        font-size: 12px;
        color: #999;
        margin-bottom: 10px;
    }

    .btn-ver {
        display: inline-block;
        padding: 8px 0;
        color: #AD2B2E;
        font-weight: bold;
        font-size: 13px;
        text-decoration: none;
        text-transform: uppercase;
        transition: color 0.2s;
    }
    .btn-ver:hover { color: #801a1d; text-decoration: underline; }

    /* === PAGINACIÓN === */
    .pagination-wrapper {
        margin-top: 40px;
        display: flex;
        justify-content: center;
    }
    
    /* === MODAL LIGHTBOX (PURO CSS) === */
    .lightbox-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.9); z-index: 10000;
        justify-content: center; align-items: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .lightbox-overlay.active { display: flex; opacity: 1; }
    
    .lightbox-content {
        position: relative; max-width: 90%; max-height: 95%;
        display: flex; flex-direction: column; align-items: center;
    }
    
    .lightbox-img {
        display: block; max-width: 100%; max-height: 85vh;
        object-fit: contain; border-radius: 4px;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
    }
    
    .lightbox-close {
        position: absolute; top: -40px; right: -40px;
        color: white; font-size: 30px; cursor: pointer;
        background: none; border: none; padding: 10px;
        transition: transform 0.2s;
    }
    .lightbox-close:hover { transform: scale(1.2); color: #FF6B6B; }
    
    /* Botón de enlace dentro del modal */
    .lightbox-link-btn {
        margin-top: 15px;
        background-color: #AD2B2E; color: white;
        padding: 10px 25px; border-radius: 30px;
        text-decoration: none; font-weight: bold; font-size: 14px;
        display: inline-flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: background 0.3s;
    }
    .lightbox-link-btn:hover { background-color: #cc0000; transform: scale(1.05); }

    @media (max-width: 600px) {
        .lightbox-close { top: -40px; right: 0; }
    }
</style>
@endsection

@section('content')

<div class="comunicados-container">
    
    <div class="page-title">
        <h1>Comunicados</h1>
    </div>

    <div class="comunicados-grid">
        @forelse($comunicados as $item)
            @php 
                $imgUrl = asset('storage/' . $item->ruta_imagen); 
                $link = $item->url_enlace;
            @endphp
            
            <div class="comunicado-card">
                <div class="img-wrapper" onclick="openLightbox('{{ $imgUrl }}', '{{ $link }}')">
                    <img src="{{ $imgUrl }}" alt="{{ $item->titulo }}" class="comunicado-img">
                    <div class="hover-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>

                <div class="comunicado-info">
                    <div>
                        <div class="comunicado-date">
                            <i class="far fa-calendar-alt me-1"></i> {{ $item->created_at->format('d/m/Y') }}
                        </div>
                        <h3 class="comunicado-title">{{ $item->titulo }}</h3>
                    </div>
                    
                    @if($item->url_enlace)
                        <a href="{{ $item->url_enlace }}" target="_blank" class="btn-ver">
                            Leer Más <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No hay comunicados publicados recientemente.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrapper">
        {{ $comunicados->links('pagination::bootstrap-4') }} {{-- Usa estilos de Bootstrap por defecto si están cargados, sino se verán simples --}}
    </div>

</div>

@endsection

@section('scripts')
<div class="lightbox-overlay" id="lightboxOverlay">
    <div class="lightbox-content">
        <button class="lightbox-close" id="lightboxClose">✕</button>
        <img src="" id="lightboxImage" class="lightbox-img">
        
        <a href="#" target="_blank" id="lightboxLink" class="lightbox-link-btn" style="display: none;">
            Ver Documento / Enlace <i class="fas fa-external-link-alt"></i>
        </a>
    </div>
</div>

<script>
    const lightboxOverlay = document.getElementById('lightboxOverlay');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxLink = document.getElementById('lightboxLink');

    function openLightbox(src, link) {
        lightboxImage.src = src;
        
        // Configurar botón si hay enlace
        if (link && link !== '') {
            lightboxLink.href = link;
            lightboxLink.style.display = 'inline-flex';
        } else {
            lightboxLink.style.display = 'none';
        }

        lightboxOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightboxOverlay.classList.remove('active');
        setTimeout(() => {
            lightboxImage.src = '';
            lightboxLink.href = '#';
        }, 300); // Limpiar después de la transición
        document.body.style.overflow = 'auto';
    }

    if(lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    
    if(lightboxOverlay) {
        lightboxOverlay.addEventListener('click', (e) => {
            if(e.target === lightboxOverlay) closeLightbox();
        });
    }

    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape' && lightboxOverlay.classList.contains('active')) {
            closeLightbox();
        }
    });
</script>
@endsection