@extends('inicio')

@section('styles')
<style>
    .custom-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .back-btn { display: inline-flex; align-items: center; text-decoration: none; color: #AD2B2E; font-weight: bold; margin-bottom: 25px; transition: 0.3s; }
    .back-btn:hover { transform: translateX(-5px); }
    .section-title { color: #AD2B2E; border-left: 5px solid #AD2B2E; padding-left: 15px; font-weight: 800; text-transform: uppercase; margin-bottom: 40px; }
    
    .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
    .doc-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 25px; display: flex; flex-direction: column; transition: 0.3s; border: 1px solid #eee; }
    .doc-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .doc-title { color: #0d2a5e; font-size: 1.2rem; font-weight: 700; margin-bottom: 12px; line-height: 1.3; }
    .doc-desc { color: #666; font-size: 0.95rem; margin-bottom: 25px; flex-grow: 1; }
    
    .btn-download { display: inline-flex; align-items: center; background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); width: fit-content; transition: 0.3s; }
    .btn-download:hover { box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    .icon-box { background: #c9e4dd; color: #0d2a5e; padding: 12px 18px; font-size: 1.1rem; }
    .text-box { padding: 0 20px; color: #0d2a5e; font-weight: 700; font-size: 0.9rem; }
</style>
@endsection

@section('content')
<div class="custom-container" style="margin-bottom: 80px;">
    <a href="{{ route('institucion-arbitral') }}" class="back-btn">
        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Volver al Menú
    </a>

    <h2 class="section-title">NORMATIVA</h2>

    <div class="doc-grid">
        @forelse($documentos as $doc)
            <div class="doc-card">
                <h5 class="doc-title">{{ $doc->titulo }}</h5>
                <p class="doc-desc">{{ $doc->descripcion }}</p>
                <p>Publicado el: {{ $doc->fecha_publicacion?->format('d/m/Y') ?? 'Fecha no disponible' }}</p>
                <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="btn-download">
                    <div class="icon-box"><i class="fas fa-download"></i></div>
                    <div class="text-box">Descargar</div>
                </a>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; color: #888; padding: 40px;">No hay documentos disponibles.</div>
        @endforelse
    </div>
</div>
@endsection