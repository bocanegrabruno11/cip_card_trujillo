@extends('inicio')

@section('title', 'Eventos - CIP La Libertad')

@section('styles')
<style>
    .events-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
    }

    .page-title {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }
    .page-title h1 { color: #333; margin: 0; font-weight: 700; }

    /* === LAYOUT GRID === */
    .events-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr; /* Izquierda más ancha */
        gap: 40px;
    }

    /* === TARJETA DESTACADA (IZQUIERDA) === */
    .featured-card {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        background: #fff;
        display: flex;
        flex-direction: column;
        height: 100%; /* Llenar altura */
    }
    .featured-card:hover { transform: translateY(-5px); }

    .featured-img-wrapper {
        width: 100%;
        height: 350px; /* Altura fija para la imagen grande */
        overflow: hidden;
    }
    .featured-img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.5s;
    }
    .featured-card:hover .featured-img { transform: scale(1.05); }

    .featured-content { padding: 25px; flex: 1; display: flex; flex-direction: column; }
    .featured-date {
        color: #E31E24; font-weight: bold; font-size: 14px; margin-bottom: 10px;
        text-transform: uppercase;
    }
    .featured-title {
        font-size: 24px; font-weight: 800; color: #222; margin-bottom: 15px;
        line-height: 1.3;
    }
    .featured-desc { color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 20px; flex: 1; }
    
    /* === LISTA LATERAL (DERECHA) === */
    .side-list {
        display: flex; flex-direction: column; gap: 20px;
    }
    
    .side-card {
        display: flex;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        height: 120px; /* Altura fija compacta */
    }
    .side-card:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

    .side-img-wrapper {
        width: 140px; flex-shrink: 0; overflow: hidden;
    }
    .side-img { width: 100%; height: 100%; object-fit: cover; }
    
    .side-content { padding: 15px; display: flex; flex-direction: column; justify-content: center; }
    .side-date { font-size: 11px; color: #999; margin-bottom: 5px; font-weight: 600; }
    .side-title { 
        font-size: 14px; font-weight: 700; color: #333; margin: 0; 
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }

    .btn-read-more {
        display: inline-block;
        padding: 10px 25px;
        background-color: #333;
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-size: 13px;
        font-weight: bold;
        align-self: flex-start;
        transition: background 0.3s;
    }
    .btn-read-more:hover { background-color: #E31E24; }

    /* === RESPONSIVE === */
    @media (max-width: 900px) {
        .events-grid { grid-template-columns: 1fr; } /* Una sola columna */
        .featured-img-wrapper { height: 250px; }
    }
</style>
@endsection

@section('content')
<div class="events-container">
    <div class="page-title">
        <h1>Próximos Eventos</h1>
    </div>

    <div class="events-grid">
        
        @if($destacado)
            @php 
                $mainImg = $destacado->detalles->where('tipo', 'principal')->first();
                $imgUrl = $mainImg ? asset('storage/' . $mainImg->ruta_imagen) : asset('img/appmovil.jpg');
            @endphp
            <div class="featured-card">
                <div class="featured-img-wrapper">
                    <a href="{{ route('detalle-evento', $destacado->id) }}">
                        <img src="{{ $imgUrl }}" alt="{{ $destacado->titulo }}" class="featured-img">
                    </a>
                </div>
                <div class="featured-content">
                    <div class="featured-date">
                        <i class="far fa-calendar-alt me-1"></i> 
                        {{ \Carbon\Carbon::parse($destacado->fecha_evento)->isoFormat('D [de] MMMM, YYYY') }}
                    </div>
                    <h2 class="featured-title">
                        <a href="{{ route('detalle-evento', $destacado->id) }}" style="text-decoration: none; color: inherit;">
                            {{ $destacado->titulo }}
                        </a>
                    </h2>
                    <div class="featured-desc">
                        {{ Str::limit(strip_tags($destacado->descripcion), 150) }}
                    </div>
                    <a href="{{ route('detalle-evento', $destacado->id) }}" class="btn-read-more">LEER MÁS</a>
                </div>
            </div>
        @else
            <p class="text-muted">No hay eventos destacados.</p>
        @endif

        <div class="side-list">
            @foreach($listaEventos as $ev)
                @php 
                    $sideImg = $ev->detalles->where('tipo', 'principal')->first();
                    $sideUrl = $sideImg ? asset('storage/' . $sideImg->ruta_imagen) : asset('img/appmovil.jpg');
                @endphp
                <a href="{{ route('detalle-evento', $ev->id) }}" style="text-decoration: none;">
                    <div class="side-card">
                        <div class="side-img-wrapper">
                            <img src="{{ $sideUrl }}" alt="{{ $ev->titulo }}" class="side-img">
                        </div>
                        <div class="side-content">
                            <div class="side-date">
                                {{ \Carbon\Carbon::parse($ev->fecha_evento)->format('d/m/Y') }}
                            </div>
                            <h3 class="side-title">{{ $ev->titulo }}</h3>
                        </div>
                    </div>
                </a>
            @endforeach
            
            <div class="mt-4">
                {{ $listaEventos->links('pagination::bootstrap-4') }}
            </div>
        </div>

    </div>
</div>
@endsection