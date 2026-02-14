@extends('inicio')

@section('styles')
<style>
    .custom-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
    .section-title { color: #AD2B2E; border-left: 5px solid #AD2B2E; padding-left: 15px; font-weight: 800; margin-bottom: 40px; }
    .calc-card { background: #fff; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 40px; text-align: center; margin-bottom: 50px; border: 1px solid #eee; }
    .calc-title { font-weight: 800; margin-bottom: 30px; color: #333; font-size: 1.5rem; }
    .back-btn { display: inline-flex; align-items: center; text-decoration: none; color: #AD2B2E; font-weight: bold; margin-bottom: 25px; transition: 0.3s; }
    .back-btn:hover { transform: translateX(-5px); }
    .btn-red { display: block; width: 100%; max-width: 500px; margin: 0 auto 15px; background: #AD2B2E; color: #fff; padding: 18px; border: none; border-radius: 8px; font-weight: 800; text-transform: uppercase; cursor: pointer; transition: 0.3s; text-decoration: none; }
    .btn-red:hover { background: #8f2225; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(173, 43, 46, 0.3); }
    
    .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
    .doc-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 25px; border: 1px solid #eee; display: flex; flex-direction: column; }
    .doc-title { color: #0d2a5e; font-weight: 700; margin-bottom: 10px; }
    .badge-pdf { background: #AD2B2E; color: white; padding: 8px 15px; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.8rem; text-decoration: none; width: fit-content; margin-top: auto; }
</style>
@endsection

@section('content')
<div class="custom-container" style="margin-bottom: 80px;">
    <a href="{{ route('junta-prevencion') }}" class="back-btn">
        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Volver al Menú
    </a>
    <h2 class="section-title">TARIFARIO Y CALCULADORA</h2>

    <div class="calc-card">
        <h5 class="calc-title">Calculadoras del Centro de Arbitraje</h5>
        <button onclick="window.open('{{ route('calc.junta') }}','calc','width=550,height=750')" class="btn-red">
            ACCEDER A LA CALCULADORA
        </button>
   
    </div>

    <div class="doc-grid">
        @foreach($tarifas as $doc)
            <div class="doc-card">
                <h5 class="doc-title">{{ $doc->titulo }}</h5>
                <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">{{ $doc->descripcion }}</p>
                <p>Publicado el: {{ $doc->fecha_publicacion?->format('d/m/Y') ?? 'Fecha no disponible' }}</p>
                <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="badge-pdf">
                    <i class="fas fa-file-pdf"></i> VER TARIFARIO
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection