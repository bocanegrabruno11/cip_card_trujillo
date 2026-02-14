@extends('inicio')

@section('styles')
<style>
    /* Contenedor Principal con margen para el footer */
    .laudos-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        margin-bottom: 100px;
    }

    /* Botón de retroceso */
    .back-btn {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        color: #AD2B2E;
        font-weight: bold;
        transition: 0.3s;
        margin-bottom: 25px;
    }
    .back-btn:hover { transform: translateX(-5px); }

    /* Título con borde institucional */
    .section-title {
        color: #AD2B2E;
        border-left: 5px solid #AD2B2E;
        padding-left: 15px;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 40px;
    }

    /* Grid Responsivo Manual */
    .laudos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    /* Card Estilo Horizontal */
    .laudo-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: flex;
        overflow: hidden;
        border: 1px solid #eee;
        transition: 0.3s;
        min-height: 180px;
    }
    .laudo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Icono Lateral */
    .card-icon-side {
        background: #AD2B2E;
        color: #fff;
        width: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Contenido de la Card */
    .card-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .card-content h5 {
        color: #0d2a5e;
        font-weight: 800;
        margin: 0 0 10px 0;
        font-size: 1.15rem;
    }
    .card-content p {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 20px;
        line-height: 1.4;
    }

    /* Botón de Descarga Interno */
    .btn-download {
        background: #AD2B2E;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        width: fit-content;
        transition: 0.3s;
        text-transform: uppercase;
    }
    .btn-download:hover { background: #333; }

    /* SECCIÓN DEL BOTÓN CENTRAL DE ENLACE */
    .link-center-container {
        text-align: center;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 15px;
        border: 2px dashed #ccc;
    }
    .btn-external-link {
        display: inline-block;
        background: #0d2a5e;
        color: white;
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 800;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 10px 20px rgba(13, 42, 94, 0.2);
        transition: 0.3s;
    }
    .btn-external-link:hover {
        background: #AD2B2E;
        transform: scale(1.05);
        box-shadow: 0 15px 30px rgba(173, 43, 46, 0.3);
    }

    /* Ajustes para Móviles */
    @media (max-width: 600px) {
        .laudos-grid { grid-template-columns: 1fr; }
        .laudo-card { flex-direction: column; }
        .card-icon-side { width: 100%; padding: 20px 0; }
        .btn-external-link { width: 100%; padding: 15px 20px; }
    }
</style>
@endsection

@section('content')
<div class="laudos-container">
    
    <div class="mb-4">
        <a href="{{ route('junta-prevencion') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Volver al Menú
        </a>
    </div>

    <h2 class="section-title">REPOSITORIO DE DECISIONES</h2>

    <div class="laudos-grid">
        @foreach($docsInformativos as $doc)
            <div class="laudo-card">
                <div class="card-icon-side">
                    <i class="fas fa-file-pdf fa-2x"></i>
                </div>
                <div class="card-content">
                    <h5>{{ $doc->titulo }}</h5>
                    <p>{{ $doc->descripcion }}</p>
                    <p>Publicado el: {{ $doc->fecha_publicacion?->format('d/m/Y') ?? 'Fecha no disponible' }}</p>
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="btn-download">
                        DESCARGAR DOCUMENTO
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="link-center-container">
        <p class="text-muted mb-4 fw-bold">Si desea acceder al portal externo del repositorio, haga clic a continuación:</p>
        <a href="https://tu-enlace-aqui.com" target="_blank" class="btn-external-link">
            <i class="fas fa-external-link-alt me-2"></i> ACCEDER AL PORTAL DE DECISIONES
        </a>
    </div>

</div>
@endsection