@extends('inicio')

@section('styles')
<style>
    .custom-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
    .section-title { color: #AD2B2E; border-left: 5px solid #AD2B2E; padding-left: 15px; font-weight: 800; margin-bottom: 40px; text-transform: uppercase; }
    
    /* --- ESTILOS DOCUMENTOS Y TARJETAS (Se mantienen iguales) --- */
    .docs-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 50px; }
    .doc-card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #eee; display: flex; flex-direction: column; }
    .doc-title { color: #0d2a5e; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px; }
    .btn-download-doc { background-color: #c9e4dd; color: #0d2a5e; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 10px; width: fit-content; transition: 0.3s; }
    
    .arbitro-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; }
    .arbitro-card { background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); padding: 30px 20px; text-align: center; border-bottom: 5px solid #AD2B2E; transition: 0.3s; cursor: pointer; }
    .arbitro-card:hover { transform: translateY(-10px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    
    .img-circle { width: 130px; height: 130px; border-radius: 50%; overflow: hidden; margin: 0 auto 20px; border: 4px solid #f8f9fa; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .img-circle img { width: 100%; height: 100%; object-fit: cover; }
    
    .role-badge { background: #AD2B2E; color: #fff; padding: 4px 15px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 15px; display: inline-block; }
    .arb-name { color: #0d2a5e; font-weight: 800; font-size: 1.1rem; margin-bottom: 5px; text-transform: uppercase; }
    .arb-spec { color: #AD2B2E; font-weight: 700; font-size: 0.85rem; margin-bottom: 15px; }
    
    .info-box { text-align: left; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; }
    .info-item { margin-bottom: 5px; color: #555; display: flex; align-items: center; gap: 10px; }
    
    .btn-cv { display: block; width: 100%; background: #333; color: #fff; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; transition: 0.3s; text-align: center;}
    .btn-cv:hover { background: #AD2B2E; }

    /* --- NUEVOS ESTILOS MODAL PERSONALIZADO (SIN BOOTSTRAP) --- */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7); /* Fondo oscuro con transparencia */
        display: none; /* Oculto por defecto */
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
    }

    .custom-modal-content {
        background: #fff;
        width: 90%;
        max-width: 500px;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .custom-modal-header {
        background: #AD2B2E;
        color: #fff;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-modal-header h5 { margin: 0; font-size: 1.2rem; font-weight: 800; }

    .close-modal {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
    }

    .custom-modal-body {
        padding: 30px;
        text-align: center;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-profile-img { 
        width: 150px; 
        height: 150px; 
        border-radius: 50%; 
        object-fit: cover; 
        border: 5px solid #fff; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        margin-bottom: 15px; 
    }

    .modal-detail-list {
        text-align: left;
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }

    .modal-detail-list p {
        margin-bottom: 10px;
        font-size: 0.95rem;
        color: #444;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-detail-list i { color: #AD2B2E; width: 20px; text-align: center; }

    /* Bloquear scroll de la página cuando el modal está abierto */
    body.modal-open { overflow: hidden; }
</style>
@endsection

@section('content')
<div class="custom-container" style="margin-bottom: 80px;">
    
    <div class="mb-4">
        <a href="{{ route('junta-prevencion') }}" class="text-decoration-none" style="color: #AD2B2E; font-weight: bold;">
            <i class="fas fa-arrow-left"></i> Volver al Menú
        </a>
    </div>
    <br>

    <h2 class="section-title">Documentos de la Nómina</h2>
    <div class="docs-grid">
        @forelse($docsNomina as $doc)
            <div class="doc-card">
                <h5 class="doc-title">{{ $doc->titulo }}</h5>
                <p class="text-muted small mb-3">{{ $doc->descripcion }}</p>
                <p>Publicado el: {{ $doc->fecha_publicacion?->format('d/m/Y') ?? 'Fecha no disponible' }}</p>
                <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="btn-download-doc">
                    <i class="fas fa-download"></i> Descargar
                </a>
            </div>
        @empty
            <p class="text-muted italic">No hay documentos cargados para esta sección.</p>
        @endforelse
    </div>

    <h2 class="section-title">Miembros de la nómina</h2>
    <div class="arbitro-grid">
        @foreach($adjudicadoresNomina as $per)
            <div class="arbitro-card" onclick="verDetalle({{ json_encode($per) }})">
                <div class="img-circle">
                    <img src="{{ $per->ruta_imagen ? asset('storage/'.$per->ruta_imagen) : asset('img/default-user.jpg') }}">
                </div>
                <span class="role-badge">{{ $per->cargo ?? 'Adjudicador' }}</span>
                <h6 class="arb-name">{{ $per->nombres }}</h6>
                <div class="arb-spec">{{ $per->especialidad }}</div>
                
                <div class="info-box">
                    <div class="info-item"><i class="fas fa-id-card" style="color:#AD2B2E"></i> <b>Reg:</b> {{ $per->codigo }}</div>
                    @if($per->email) <div class="info-item"><i class="fas fa-envelope" style="color:#AD2B2E"></i> {{ $per->email }}</div> @endif
                </div>

                @if($per->ruta_cv)
                    <a href="{{ asset('storage/'.$per->ruta_cv) }}" target="_blank" class="btn-cv" onclick="event.stopPropagation();">VER HOJA DE VIDA</a>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div id="customModalOverlay" class="custom-modal-overlay" onclick="cerrarModal()">
    <div class="custom-modal-content" onclick="event.stopPropagation()">
        <div class="custom-modal-header">
            <h5>PERFIL PROFESIONAL</h5>
            <button class="close-modal" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <img id="m-foto" src="" class="modal-profile-img">
            <h4 id="m-nombre" class="arb-name" style="font-size: 1.4rem; margin-bottom: 5px;"></h4>
            <span id="m-cargo" class="role-badge"></span>
            
            <div class="modal-detail-list">
                <p><i class="fas fa-graduation-cap"></i> <strong>Especialidad:</strong> <span id="m-especialidad"></span></p>
                <p><i class="fas fa-id-card"></i> <strong>Registro:</strong> <span id="m-codigo"></span></p>
                <p><i class="fas fa-envelope"></i> <strong>Correo:</strong> <span id="m-email"></span></p>
                <p><i class="fas fa-phone"></i> <strong>Teléfono:</strong> <span id="m-telefono"></span></p>
            </div>
            
            <div id="m-footer-cv" style="margin-top: 25px;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function verDetalle(persona) {
        // Llenar datos en el modal
        document.getElementById('m-nombre').innerText = persona.nombres;
        document.getElementById('m-cargo').innerText = persona.cargo || 'Árbitro';
        document.getElementById('m-especialidad').innerText = persona.especialidad || 'No especificada';
        document.getElementById('m-codigo').innerText = persona.codigo || 'S/N';
        document.getElementById('m-email').innerText = persona.email || 'No disponible';
        document.getElementById('m-telefono').innerText = persona.telefono || 'No disponible';
        
        // Imagen
        const fotoUrl = persona.ruta_imagen ? `/storage/${persona.ruta_imagen}` : '/img/default-user.jpg';
        document.getElementById('m-foto').src = fotoUrl;

        // Botón CV
        const footerCv = document.getElementById('m-footer-cv');
        footerCv.innerHTML = '';
        if (persona.ruta_cv) {
            footerCv.innerHTML = `<a href="/storage/${persona.ruta_cv}" target="_blank" class="btn-cv">DESCARGAR HOJA DE VIDA COMPLETA</a>`;
        }

        // Mostrar Modal
        const overlay = document.getElementById('customModalOverlay');
        overlay.style.display = 'flex';
        document.body.classList.add('modal-open');
    }

    function cerrarModal() {
        const overlay = document.getElementById('customModalOverlay');
        overlay.style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            cerrarModal();
        }
    });
</script>
@endsection