@extends('inicio')

@section('title', $evento->titulo)

@section('styles')
<style>
    .detail-container {
        max-width: 1000px; /* Un poco más angosto para lectura */
        margin: 0 auto;
        padding: 50px 20px;
        background: #fff;
    }

    /* Header del evento */
    .event-header { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    .event-title { font-size: 36px; font-weight: 800; color: #222; margin-bottom: 15px; line-height: 1.2; }
    .event-meta { display: flex; gap: 20px; color: #666; font-size: 14px; align-items: center; flex-wrap: wrap; }
    .meta-item i { color: #AD2B2E; margin-right: 5px; }

    /* Contenido Principal */
    .main-image-wrapper {
        width: 100%; height: 400px; overflow: hidden; border-radius: 8px; margin-bottom: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .main-image { width: 100%; height: 100%; object-fit: cover; }

    .event-body {
        font-size: 16px; line-height: 1.8; color: #444; text-align: justify; margin-bottom: 50px;
    }

    /* Galería (Estilos reutilizados de Presentación pero simplificados) */
    .gallery-title { font-size: 20px; font-weight: bold; margin-bottom: 20px; border-left: 4px solid #AD2B2E; padding-left: 10px; }
    
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
    .gallery-item {
        height: 150px; overflow: hidden; border-radius: 6px; cursor: pointer; position: relative;
    }
    .gallery-thumb { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .gallery-item:hover .gallery-thumb { transform: scale(1.1); }

    /* LIGHTBOX (Pega aquí el CSS del lightbox que te di antes o ponlo global en inicio) */
    .lightbox-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; justify-content: center; align-items: center; }
    .lightbox-overlay.active { display: flex; }
    .lightbox-img { max-width: 95%; max-height: 90vh; object-fit: contain; border-radius: 4px; }
    .lightbox-close { position: absolute; top: 20px; right: 20px; color: white; font-size: 30px; cursor: pointer; background: none; border: none; }

    @media (max-width: 768px) {
        .event-title { font-size: 28px; }
        .main-image-wrapper { height: 250px; }
    }
</style>
@endsection

@section('content')
<div class="detail-container">
    
    <div class="event-header">
        <h1 class="event-title">{{ $evento->titulo }}</h1>
        <div class="event-meta">
            <span class="meta-item">
                <i class="far fa-calendar-alt"></i> 
                {{ \Carbon\Carbon::parse($evento->fecha_evento)->isoFormat('D [de] MMMM, YYYY') }}
            </span>
            @if($evento->lugar)
                <span class="meta-item">
                    <i class="fas fa-map-marker-alt"></i> {{ $evento->lugar }}
                </span>
            @endif
        </div>
    </div>

    @if($imagenPrincipal)
        <div class="main-image-wrapper">
            <img src="{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}" 
                 alt="{{ $evento->titulo }}" 
                 class="main-image"
                 onclick="openLightbox('{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}')">
        </div>
    @endif

    <div class="event-body">
        {!! nl2br(e($evento->descripcion)) !!}
    </div>

    @if($galeria->count() > 0)
        <div class="gallery-section">
            <h3 class="gallery-title">Galería de Fotos</h3>
            <div class="gallery-grid">
                @foreach($galeria as $img)
                    <div class="gallery-item" onclick="openLightbox('{{ asset('storage/' . $img->ruta_imagen) }}')">
                        <img src="{{ asset('storage/' . $img->ruta_imagen) }}" class="gallery-thumb">
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<div class="text-center mb-5">
    <a href="{{ route('eventos') }}" class="btn-read-more" style="background: #eee; color: #333;">
        <i class="fas fa-arrow-left me-2"></i> Volver a Eventos
    </a>
</div>
@endsection

@section('scripts')
<div class="lightbox-overlay" id="lightboxOverlay">
    <button class="lightbox-close" id="lightboxClose">✕</button>
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

    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => {
        if(e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') closeLightbox();
    });
</script>
@endsection