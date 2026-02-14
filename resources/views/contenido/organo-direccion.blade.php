@extends('inicio')

@section('title', 'Organización del CARD - CARD CD La Libertad')

@section('styles')
<style>
    /* === VARIABLES === */
    :root {
        --rojo-institucional: #AD2B2E;
        --rojo-oscuro: #8a2225;
        --rojo-fondo-modal: rgba(138, 34, 37, 0.85); /* Tono rojizo para el fondo */
        --azul-institucional: #0d2a5e; 
        --gris-claro: #f8f9fa;
        --texto-oscuro: #2d3436;
    }

    .org-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px 20px;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    /* === ENCABEZADO === */
    .page-header {
        text-align: center;
        margin-bottom: 60px;
    }
    .page-header h3 {
        color: var(--rojo-institucional);
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    .separator {
        width: 100px;
        height: 5px;
        background-color: var(--rojo-oscuro);
        margin: 20px auto;
        border-radius: 10px;
    }

    /* === TÍTULOS DE SECCIÓN === */
    .section-title {
        color: var(--texto-oscuro);
        font-size: 1.6rem;
        font-weight: 800;
        text-transform: uppercase;
        text-align: center;
        margin-top: 80px;
        margin-bottom: 40px;
        position: relative;
    }
    .section-title::after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: var(--rojo-institucional);
        margin: 12px auto 0;
    }

    /* === GRID DE PERSONAS === */
    .team-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 35px;
        margin-bottom: 40px;
    }

    /* === TARJETA PERSONA === */
    .person-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        padding: 35px 25px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-top: 4px solid var(--rojo-institucional);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        width: 300px;
        max-width: 100%;
    }
    
    .person-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(173, 43, 46, 0.15);
        background-color: var(--gris-claro);
    }

    .person-img-wrapper {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 20px;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .person-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .person-name {
        color: var(--texto-oscuro);
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .person-role {
        color: var(--rojo-institucional);
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 15px;
    }

    .click-hint {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.75rem;
        color: var(--rojo-institucional);
        font-weight: bold;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .person-card:hover .click-hint { opacity: 1; }

    .btn-arrow {
        width: 40px;
        height: 40px;
        background-color: var(--rojo-institucional);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        margin-top: 10px; 
    }

    /* === BOTÓN RESOLUCIÓN === */
    .btn-resolution-container { text-align: center; margin: 60px 0; }
    .btn-resolution {
        background-color: var(--rojo-institucional); 
        color: white; padding: 16px 40px;
        text-decoration: none; text-transform: uppercase; font-weight: 700; font-size: 14px;
        border-radius: 8px; display: inline-block; box-shadow: 0 6px 20px rgba(173, 43, 46, 0.3);
        transition: all 0.3s;
    }
    .btn-resolution:hover { background-color: var(--rojo-oscuro); color: white; transform: translateY(-3px); }

    /* === ESTILOS DEL MODAL (ACTUALIZADOS) === */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: var(--rojo-fondo-modal); /* AHORA ES ROJIZO */
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .custom-modal.show { display: flex; }

    .modal-content {
        background-color: #fff;
        border-radius: 15px;
        width: 100%;
        max-width: 850px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.4);
        display: flex;
        flex-direction: row;
        overflow: hidden;
        position: relative;
    }

    .modal-left {
        width: 40%;
        background: linear-gradient(135deg, var(--gris-claro) 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        border-right: 1px solid #eee;
    }
    
    .modal-img-large {
        width: 100%; max-width: 220px; aspect-ratio: 1/1;
        object-fit: cover; border-radius: 50%;
        border: 8px solid white; box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .modal-right {
        width: 60%;
        padding: 50px 40px;
        display: flex; flex-direction: column;
        justify-content: center; text-align: left;
    }

    .close-btn-float {
        position: absolute; top: 20px; right: 20px;
        width: 35px; height: 35px; background: #fff;
        color: var(--rojo-institucional); border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        cursor: pointer; border: 1px solid #eee; z-index: 10;
        transition: 0.3s;
    }
    .close-btn-float:hover { background: var(--rojo-institucional); color: white; }

    .data-row {
        display: flex; align-items: center; margin-bottom: 18px;
        font-size: 15px; color: var(--texto-oscuro);
    }
    .data-icon {
        width: 35px; height: 35px; background: rgba(173, 43, 46, 0.1);
        color: var(--rojo-institucional); border-radius: 8px;
        margin-right: 15px; display: flex; align-items: center; justify-content: center;
    }
    
    .btn-download-cv {
        margin-top: 30px; display: inline-flex; align-items: center; justify-content: center;
        background-color: var(--rojo-institucional); /* CAMBIADO A ROJO */
        color: white !important;
        padding: 12px 25px; border-radius: 8px; text-decoration: none;
        font-weight: 700; font-size: 13px; transition: all 0.3s; align-self: flex-start;
    }
    .btn-download-cv:hover { background-color: var(--rojo-oscuro); transform: scale(1.05); }

    @media (max-width: 768px) {
        .modal-content { flex-direction: column; max-height: 90vh; overflow-y: auto; }
        .modal-left, .modal-right { width: 100%; padding: 30px; }
    }
</style>
@endsection

@section('content')
<div class="org-container">
    <div class="page-header">
        <h3>Organización del CARD</h3>
        <div class="separator"></div>
    </div>

    {{-- ÓRGANO DE DIRECCIÓN --}}
    <div class="section-title">ÓRGANO DE DIRECCIÓN</div>
    <div class="team-row">
        @forelse($organoDireccion as $persona)
            <div class="person-card" onclick="openModal(this)"
                 data-name="{{ $persona->nombres }}"
                 data-cargo="{{ $persona->cargo }}"
                 data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : '' }}"
                 data-code="{{ $persona->codigo }}"
                 data-specialty="{{ $persona->especialidad }}"
                 data-email="{{ $persona->email }}"
                 data-phone="{{ $persona->telefono }}"
                 data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                
                <span class="click-hint"><i class="fas fa-plus"></i> Detalles</span>
                <div class="person-img-wrapper">
                    <img src="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : asset('img/default-user.jpg') }}" class="person-img">
                </div>
                <div class="person-name">{{ $persona->nombres }}</div>
                <div class="person-role">{{ $persona->cargo }}</div>
                <div class="btn-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
        @empty
            <p class="text-muted text-center w-100">No hay miembros registrados.</p>
        @endforelse
    </div>


    <div class="btn-resolution-container">
        @if(isset($documentoResolucion) && $documentoResolucion)
            <a href="{{ asset('storage/' . $documentoResolucion->ruta_archivo) }}" target="_blank" class="btn-resolution">
                <i class="fas fa-file-pdf me-2"></i> {{ $documentoResolucion->titulo ?? 'RESOLUCIÓN DE CONFORMACIÓN' }}
            </a>
        @endif
    </div>
</div>

{{-- MODAL --}}
<div id="personModal" class="custom-modal">
    <div class="modal-content">
        <button class="close-btn-float" onclick="closeModal()"><i class="fas fa-times"></i></button>
        <div class="modal-left">
            <img id="mImg" src="" class="modal-img-large">
        </div>
        <div class="modal-right">
            <h3 id="mName" style="color: var(--texto-oscuro); font-weight: 800; margin-bottom: 8px; line-height: 1.2;"></h3>
            <span id="mCargo" style="background: var(--rojo-institucional); color: white; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; align-self: flex-start; margin-bottom: 25px;"></span>
            <div style="width: 100%;">
                <div id="mCodeGroup" class="data-row">
                    <div class="data-icon"><i class="fas fa-id-card"></i></div> 
                    <div><strong>Registro profesional</strong> <span id="mCode"></span></div>
                </div>
                <div id="mSpecialtyGroup" class="data-row">
                    <div class="data-icon"><i class="fas fa-graduation-cap"></i></div> 
                    <div><strong>Especialidad</strong> <span id="mSpecialty"></span></div>
                </div>
                <div id="mEmailGroup" class="data-row">
                    <div class="data-icon"><i class="fas fa-envelope"></i></div> 
                    <div><strong>Correo Electrónico</strong> <span id="mEmail"></span></div>
                </div>
                <div id="mPhoneGroup" class="data-row">
                    <div class="data-icon"><i class="fas fa-phone"></i></div> 
                    <div><strong>Teléfono de contacto</strong> <span id="mPhone"></span></div>
                </div>
            </div>
            <a id="mCvBtn" href="#" target="_blank" class="btn-download-cv">
                <i class="fas fa-file-pdf"></i> Descargar Hoja de Vida
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(element) {
        const name = element.getAttribute('data-name');
        const cargo = element.getAttribute('data-cargo');
        const img = element.getAttribute('data-img');
        const code = element.getAttribute('data-code');
        const specialty = element.getAttribute('data-specialty');
        const email = element.getAttribute('data-email');
        const phone = element.getAttribute('data-phone');
        const cv = element.getAttribute('data-cv');

        document.getElementById('mName').textContent = name;
        document.getElementById('mCargo').textContent = cargo;
        
        const imgEl = document.getElementById('mImg');
        imgEl.src = img || "{{ asset('img/default-user.jpg') }}";

        const setField = (id, group, val) => {
            if(val && val !== 'null') {
                document.getElementById(id).textContent = val;
                document.getElementById(group).style.display = 'flex';
            } else {
                document.getElementById(group).style.display = 'none';
            }
        };

        setField('mCode', 'mCodeGroup', code);
        setField('mSpecialty', 'mSpecialtyGroup', specialty);
        setField('mEmail', 'mEmailGroup', email);
        setField('mPhone', 'mPhoneGroup', phone);

        const btn = document.getElementById('mCvBtn');
        if(cv) {
            btn.href = cv;
            btn.style.display = 'inline-flex';
        } else {
            btn.style.display = 'none';
        }

        const modal = document.getElementById('personModal');
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('personModal');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('personModal');
        if (event.target == modal) closeModal();
    }
</script>
@endsection