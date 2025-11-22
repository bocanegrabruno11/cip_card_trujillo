@extends('inicio')

@section('title', 'Nuestro Equipo - CIP La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .team-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
    }

    /* Header de la página */
    .page-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
        margin-bottom: 50px;
    }
    .page-header h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    /* === TÍTULOS DE SECCIÓN === */
    .section-title {
        color: #E31E24;
        font-size: 26px;
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
        margin-top: 60px;
        margin-bottom: 40px;
        letter-spacing: 0.5px;
    }

    /* === GRID DE PERSONAS === */
    .team-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 40px;
        margin-bottom: 20px;
    }

    /* Tarjeta Individual */
    .person-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 260px;
    }

    /* Imagen Circular */
    .person-img-wrapper {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border: 5px solid #fff;
        background-color: #eee;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .person-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
    }

    .person-card:hover .person-img-wrapper {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    /* Tipografía */
    .person-name {
        color: #D4AF37;
        font-size: 17px;
        font-weight: bold;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .person-role {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
        font-style: italic;
    }

    /* Datos de Contacto */
    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
        font-size: 14px;
        color: #455A64;
    }

    .contact-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        color: #455A64;
        transition: color 0.2s;
    }

    .contact-item:hover { color: #E31E24; }
    .contact-item i { font-size: 13px; color: #607D8B; }

    /* Separador especial Secretaria */
    .secretary-layout {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
        max-width: 600px;
        margin: 0 auto;
    }
    .secretary-info { text-align: left; }

    /* === RESPONSIVIDAD === */
    @media (max-width: 768px) {
        .secretary-layout { flex-direction: column; text-align: center; }
        .secretary-info { text-align: center; }
        .section-title { font-size: 22px; margin-top: 40px; }
        .team-row { gap: 30px; }
    }
</style>
@endsection

@section('content')

<div class="team-container">
    
    <div class="page-header">
        <h1>Nuestro equipo</h1>
    </div>

    {{-- 1. SECRETARÍA GENERAL --}}
    @if($secretaria)
        <div class="section-title">SECRETARÍA GENERAL</div>
        
        <div class="secretary-layout">
            <div class="person-img-wrapper" style="width: 200px; height: 200px;">
                @if($secretaria->ruta_imagen)
                    <img src="{{ asset('storage/' . $secretaria->ruta_imagen) }}" class="person-img" alt="{{ $secretaria->nombres }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($secretaria->nombres) }}&background=eee&color=333&size=200" class="person-img">
                @endif
            </div>
            <div class="secretary-info">
                <div class="person-name" style="font-size: 20px;">{{ $secretaria->nombres }}</div>
                
                @if($secretaria->cargo)
                    <div class="person-role" style="max-width: 250px; margin-bottom: 15px;">
                        {{ $secretaria->cargo }}
                    </div>
                @endif

                <div class="contact-info" style="align-items: flex-start;">
                    @if($secretaria->email)
                        <a href="mailto:{{ $secretaria->email }}" class="contact-item">
                            <i class="fas fa-envelope"></i> {{ $secretaria->email }}
                        </a>
                    @endif
                    @if($secretaria->telefono)
                        <span class="contact-item">
                            <i class="fas fa-phone-alt"></i> {{ $secretaria->telefono }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif


    {{-- 2. SECRETARIOS ARBITRALES --}}
    @if($secretariosArbitrales->count() > 0)
        <div class="section-title">SECRETARIOS ARBITRALES</div>

        <div class="team-row">
            @foreach($secretariosArbitrales as $persona)
                <div class="person-card">
                    <div class="person-img-wrapper">
                        @if($persona->ruta_imagen)
                            <img src="{{ asset('storage/' . $persona->ruta_imagen) }}" class="person-img" alt="{{ $persona->nombres }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($persona->nombres) }}&background=eee&color=333&size=180" class="person-img">
                        @endif
                    </div>
                    <div class="person-name">{{ $persona->nombres }}</div>
                    
                    <div class="contact-info">
                        @if($persona->email)
                            <a href="mailto:{{ $persona->email }}" class="contact-item">
                                <i class="fas fa-envelope"></i> {{ $persona->email }}
                            </a>
                        @endif
                        @if($persona->telefono)
                            <span class="contact-item">
                                <i class="fas fa-phone-alt"></i> {{ $persona->telefono }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif


    {{-- 3. PERSONAL DE APOYO --}}
    @if($personalApoyo->count() > 0)
        <div class="section-title">PERSONAL PROFESIONAL DE APOYO PARA JRD/JPRD</div>

        <div class="team-row">
            @foreach($personalApoyo as $persona)
                <div class="person-card">
                    <div class="person-img-wrapper">
                        @if($persona->ruta_imagen)
                            <img src="{{ asset('storage/' . $persona->ruta_imagen) }}" class="person-img" alt="{{ $persona->nombres }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($persona->nombres) }}&background=eee&color=333&size=180" class="person-img">
                        @endif
                    </div>
                    <div class="person-name">{{ $persona->nombres }}</div>
                    
                    <div class="contact-info">
                        @if($persona->email)
                            <a href="mailto:{{ $persona->email }}" class="contact-item">
                                <i class="fas fa-envelope"></i> {{ $persona->email }}
                            </a>
                        @endif
                        @if($persona->telefono)
                            <span class="contact-item">
                                <i class="fas fa-phone-alt"></i> {{ $persona->telefono }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif


    {{-- 4. SOPORTE ADMINISTRATIVO --}}
    @if($soporteAdmin->count() > 0)
        <div class="section-title">SOPORTE ADMINISTRATIVO</div>

        <div class="team-row">
            @foreach($soporteAdmin as $persona)
                <div class="person-card">
                    <div class="person-img-wrapper">
                        @if($persona->ruta_imagen)
                            <img src="{{ asset('storage/' . $persona->ruta_imagen) }}" class="person-img" alt="{{ $persona->nombres }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($persona->nombres) }}&background=eee&color=333&size=180" class="person-img">
                        @endif
                    </div>
                    <div class="person-name">{{ $persona->nombres }}</div>
                    
                    <div class="contact-info">
                        @if($persona->email)
                            <a href="mailto:{{ $persona->email }}" class="contact-item">
                                <i class="fas fa-envelope"></i> {{ $persona->email }}
                            </a>
                        @endif
                        @if($persona->telefono)
                            <span class="contact-item">
                                <i class="fas fa-phone-alt"></i> {{ $persona->telefono }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection