@extends('inicio')

@section('title', 'Organización del CARD - CIP La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR === */
    .org-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
        text-align: center;
    }

    .page-header {
        text-align: left;
        margin-bottom: 40px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }
    .page-header h1 { color: #333; font-size: 28px; font-weight: 700; margin: 0; }

    /* === TÍTULOS DE SECCIÓN (ROJOS) === */
    .section-title {
        color: #E31E24; /* Rojo Institucional */
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
        margin-top: 50px;
        margin-bottom: 30px;
        position: relative;
        display: inline-block;
    }
    
    .section-title::after {
        content: ''; display: block; width: 60%; height: 1px; 
        background: #eee; margin: 10px auto 0;
    }

    .sub-title {
        color: #883E5D; 
        font-size: 20px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 30px 0 20px;
    }

    /* === GRID DE PERSONAS === */
    .team-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 40px;
        margin-bottom: 20px;
    }

    .person-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 200px;
    }

    .person-img {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        margin-bottom: 15px;
        transition: transform 0.3s;
        background-color: #eee;
    }

    .person-card:hover .person-img {
        transform: scale(1.05);
    }

    .person-name {
        font-size: 15px;
        font-weight: bold;
        color: #222;
        margin-bottom: 5px;
        line-height: 1.3;
    }

    .person-role {
        font-size: 13px;
        color: #666;
    }

    /* === BOTÓN DE RESOLUCIÓN === */
    .btn-resolution {
        background-color: #FF6B6B;
        color: white;
        padding: 15px 30px;
        text-decoration: none;
        text-transform: uppercase;
        font-size: 14px;
        border-radius: 5px;
        display: inline-block;
        margin: 40px 0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: background 0.3s;
    }
    .btn-resolution:hover {
        background-color: #ff5252;
        color: white;
    }

    /* === RESPONSIVIDAD === */
    @media (max-width: 768px) {
        .team-row { gap: 20px; }
        .person-card { width: 150px; }
        .person-img { width: 120px; height: 120px; }
    }
</style>
@endsection

@section('content')

<div class="org-container">
    
    <div class="page-header">
        <h1>Organización del CARD</h1>
    </div>

    <div class="section-title">ÓRGANO DIRECTIVO - CIP LA LIBERTAD</div>

    <div class="team-row">
        @forelse($directivos as $persona)
            <div class="person-card">
                @if($persona->ruta_imagen)
                    <img src="{{ asset('storage/' . $persona->ruta_imagen) }}" class="person-img" alt="{{ $persona->nombres }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($persona->nombres) }}&background=eee&color=333&size=160" class="person-img" alt="Sin foto">
                @endif
                
                <div class="person-name">{{ $persona->nombres }}</div>
                <div class="person-role">{{ $persona->cargo }}</div>
            </div>
        @empty
            <p class="text-muted">No hay miembros directivos registrados.</p>
        @endforelse
    </div>


    <div class="section-title">ÓRGANO DECISORIO - DIRECTORIO</div>

    @if($decisorioPresidente)
        <div class="sub-title">PRESIDENTE DEL DIRECTORIO</div>
        <div class="team-row">
            <div class="person-card">
                @if($decisorioPresidente->ruta_imagen)
                    <img src="{{ asset('storage/' . $decisorioPresidente->ruta_imagen) }}" class="person-img" alt="{{ $decisorioPresidente->nombres }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($decisorioPresidente->nombres) }}&background=eee&color=333&size=160" class="person-img" alt="Sin foto">
                @endif
                
                <div class="person-name">{{ $decisorioPresidente->nombres }}</div>
                <div class="person-role">{{ $decisorioPresidente->cargo }}</div>
            </div>
        </div>
    @endif

    @if($decisorioMiembros->count() > 0)
        <div class="sub-title">MIEMBROS DEL DIRECTORIO</div>
        <div class="team-row">
            @foreach($decisorioMiembros as $miembro)
                <div class="person-card">
                    @if($miembro->ruta_imagen)
                        <img src="{{ asset('storage/' . $miembro->ruta_imagen) }}" class="person-img" alt="{{ $miembro->nombres }}">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($miembro->nombres) }}&background=eee&color=333&size=160" class="person-img" alt="Sin foto">
                    @endif
                    
                    <div class="person-name">{{ $miembro->nombres }}</div>
                    <div class="person-role">{{ $miembro->cargo }}</div>
                </div>
            @endforeach
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
            <a href="#" target="_blank" class="btn-resolution">
                RESOLUCIÓN DE LA CONFORMACIÓN DEL DIRECTORIO
            </a>
        </div>
    </div>

    @if($secretaria)
        <div class="section-title">ORGANO DE GESTION - SECRETARIA GENERAL</div>

        <div class="team-row">
            <div class="person-card" style="width: 400px;">
                @if($secretaria->ruta_imagen)
                    <img src="{{ asset('storage/' . $secretaria->ruta_imagen) }}" class="person-img" alt="{{ $secretaria->nombres }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($secretaria->nombres) }}&background=eee&color=333&size=160" class="person-img" alt="Sin foto">
                @endif
                
                <div class="person-name" style="font-size: 18px;">{{ $secretaria->nombres }}</div>
                <div class="person-role">{{ $secretaria->cargo }}</div>
            </div>
        </div>
    @endif

</div>

@endsection