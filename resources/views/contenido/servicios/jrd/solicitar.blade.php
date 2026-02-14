@extends('inicio')

@section('styles')
<style>
    .custom-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
    .section-title { color: #AD2B2E; border-left: 5px solid #AD2B2E; padding-left: 15px; font-weight: 800; margin-bottom: 40px; }
    .back-btn { display: inline-flex; align-items: center; text-decoration: none; color: #AD2B2E; font-weight: bold; margin-bottom: 25px; transition: 0.3s; }
    .back-btn:hover { transform: translateX(-5px); }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); gap: 30px; }
    .form-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; overflow: hidden; border: 1px solid #eee; transition: 0.3s; }
    .form-card:hover { transform: scale(1.02); }
    
    .side-icon { background: #AD2B2E; color: #fff; padding: 40px 30px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
    .content-box { padding: 30px; flex-grow: 1; }
    .form-title { color: #0d2a5e; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; }
    .btn-action { background: #AD2B2E; color: white; padding: 10px 25px; border-radius: 6px; text-decoration: none; font-weight: 700; display: inline-block; transition: 0.3s; }
    .btn-action:hover { background: #333; }
</style>
@endsection

@section('content')
<div class="custom-container" style="margin-bottom: 80px;">
    <a href="{{ route('junta-prevencion') }}" class="back-btn">
        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Volver al Menú
    </a>
    <h2 class="section-title">SOLICITAR EL SERVICIO</h2>

    <div class="form-grid">
        @foreach($formularios as $doc)
            <div class="form-card">
                <div class="side-icon"><i class="fas fa-file-signature"></i></div>
                <div class="content-box">
                    <h5 class="form-title">{{ $doc->titulo }}</h5>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">{{ $doc->descripcion }}</p>
                    <p>Publicado el: {{ $doc->fecha_publicacion?->format('d/m/Y') ?? 'Fecha no disponible' }}</p>
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="btn-action">DESCARGAR FORMATO</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection