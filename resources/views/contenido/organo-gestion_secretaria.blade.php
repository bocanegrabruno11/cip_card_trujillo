@extends('inicio')

@section('title', 'Nuestro Equipo - CARD CD La Libertad')

@section('styles')
<style>
    /* === VARIABLES === */
    :root {
        --rojo-institucional: #AD2B2E;
        --rojo-oscuro: #8a2225;
        --rojo-fondo-modal: rgba(138, 34, 37, 0.85); /* Overlay rojizo */
        --azul-institucional: #0d2a5e; /* Mantenido solo como variable, no se usa en UI */
        --gris-claro: #f8f9fa;
        --texto-oscuro: #2d3436;
    }

    .team-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px 20px;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    /* === ENCABEZADO === */
    .team-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .team-header h2 {
        color: var(--rojo-institucional); 
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .team-header p {
        color: #555;
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
    }
    .separator {
        width: 100px;
        height: 5px;
        background-color: var(--rojo-oscuro);
        margin: 20px auto;
        border-radius: 10px;
    }

    /* === SECCIONES === */
    .section-subtitle {
        color: var(--texto-oscuro);
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 40px;
        text-align: center;
        text-transform: uppercase;
        position: relative;
        padding-bottom: 15px;
    }
    
    .section-subtitle::after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background-color: var(--rojo-institucional);
        margin: 10px auto 0;
        border-radius: 2px;
    }

    /* === GRID DE TARJETAS === */
    .cards-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px; /* Reducido para que entren más juntas */
        margin-bottom: 60px;
    }

    /* === DISPOSICIÓN EN DOS COLUMNAS PARA ARBITRAL Y TÉCNICA === */
    .dual-section {
        display: flex;
        gap: 60px; /* Mayor separación para dar espacio a la línea */
        justify-content: space-between;
        flex-wrap: wrap;
        position: relative; /* Necesario para la línea divisoria absoluta */
    }
    
    /* LÍNEA DIVISORIA VERTICAL */
    .dual-section::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 50px; /* Para que no pegue hasta el fondo */
        left: 50%;
        width: 2px;
        background-color: #ddd; /* Color de la línea gris clara */
        transform: translateX(-50%);
    }

    .dual-column {
        flex: 1;
        min-width: 320px;
        z-index: 1; /* Asegura que el contenido quede sobre la línea */
    }

    /* === TARJETA PERSONA === */
    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        padding: 25px 15px; /* Reducido para ahorrar espacio */
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-top: 4px solid var(--rojo-institucional);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        width: 230px; /* Reducido de 300px a 230px para que entren 2 por fila */
        max-width: 100%;
    }

    .profile-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(173, 43, 46, 0.15);
    }

    .profile-img-container {
        width: 100px; /* Reducido de 130px a 100px */
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 15px;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .profile-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-name {
        color: var(--texto-oscuro);
        font-size: 1.1rem; /* Reducido ligeramente */
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .profile-role {
        color: var(--rojo-institucional);
        font-size: 0.8rem; /* Reducido ligeramente */
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    .profile-inst {
        color: #888;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .btn-arrow {
        width: 35px; /* Reducido de 40px */
        height: 35px;
        background-color: var(--rojo-institucional);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        margin-top: auto; 
    }
    .profile-card:hover .btn-arrow {
        background-color: var(--rojo-oscuro);
        transform: scale(1.1);
    }

    /* === ESTILOS DEL MODAL === */
    .custom-modal-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: var(--rojo-fondo-modal);
        backdrop-filter: blur(8px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .custom-modal-overlay.show { display: flex; opacity: 1; }

    .custom-modal-content {
        background-color: #fff;
        width: 90%;
        max-width: 750px;
        border-radius: 15px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        display: flex;
        overflow: hidden;
        position: relative;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    .custom-modal-overlay.show .custom-modal-content { transform: translateY(0); }

    .modal-sidebar {
        width: 35%;
        background: linear-gradient(135deg, var(--gris-claro) 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        border-right: 1px solid #eee;
    }

    .modal-profile-pic {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .modal-main {
        width: 65%;
        padding: 40px 35px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
    }

    .modal-close-btn {
        position: absolute; top: 15px; right: 15px;
        width: 35px; height: 35px; background-color: #f0f0f0;
        border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        cursor: pointer; border: none; color: #333;
        transition: 0.3s;
    }
    .modal-close-btn:hover { background-color: var(--rojo-institucional); color: white; }

    .modal-name {
        color: var(--texto-oscuro);
        font-size: 1.7rem;
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1.2;
    }
    .modal-badge {
        background-color: var(--rojo-institucional);
        color: white;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 25px;
    }

    .modal-info-row {
        display: flex; align-items: center;
        margin-bottom: 15px; font-size: 0.95rem; color: #444;
    }
    .modal-info-icon {
        width: 35px; height: 35px;
        background: rgba(173, 43, 46, 0.1);
        color: var(--rojo-institucional);
        border-radius: 8px;
        margin-right: 15px;
        display: flex; align-items: center; justify-content: center;
    }
    .modal-info-text strong {
        display: block; font-size: 0.7rem;
        color: #888; text-transform: uppercase; letter-spacing: 0.5px;
    }

    .modal-cv-btn {
        margin-top: 25px;
        display: inline-flex;
        align-items: center;
        background-color: var(--rojo-institucional);
        color: white !important;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        transition: 0.3s;
        align-self: flex-start;
        text-transform: uppercase;
    }
    .modal-cv-btn:hover { background-color: var(--rojo-oscuro); transform: scale(1.05); }

    /* Media Queries para Responsive */
    @media (max-width: 992px) {
        .dual-section::after {
            display: none; /* Oculta la línea vertical en pantallas pequeñas (Tablet/Móvil) */
        }
        .dual-section {
            gap: 20px;
        }
    }
    @media (max-width: 768px) {
        .custom-modal-content { flex-direction: column; max-height: 90vh; overflow-y: auto; }
        .modal-sidebar, .modal-main { width: 100%; padding: 25px; text-align: center; }
        .modal-main { align-items: center; }
        .modal-info-row { justify-content: flex-start; width: 100%; }
        .modal-cv-btn { align-self: center; }
        
        .profile-card {
            width: 260px; /* Ligeramente más grande en celular al estar en una sola columna */
        }
    }
</style>
@endsection

@section('content')

<div class="team-container">
    <div class="team-header">
        <h2>Secretaría General</h2>
        
    </div>

    {{-- 1. SECRETARÍA GENERAL (CENTRO ARRIBA, COMO LÍDER) --}}
    @if($secGeneral->count() > 0)
        <div class="cards-grid">
            @foreach($secGeneral as $persona)
                <div class="profile-card" onclick="openModal(this)"
                     data-name="{{ $persona->nombres }}"
                     data-cargo="{{ $persona->cargo }}"
                     data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : '' }}"
                     data-code="{{ $persona->codigo }}"
                     data-specialty="{{ $persona->especialidad }}"
                     data-email="{{ $persona->email }}"
                     data-phone="{{ $persona->telefono }}"
                     data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                    
                    <div class="profile-img-container">
                        <img src="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : asset('img/default-user.jpg') }}" 
                             class="profile-img" alt="{{ $persona->nombres }}">
                    </div>

                    <div class="profile-name">{{ $persona->nombres }}</div>
                    <div class="profile-role">{{ $persona->cargo }}</div>
                    <div class="profile-inst">CARD CIP CDLL</div>

                    <div class="btn-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- CONTENEDOR DUAL PARA ARBITRAL Y TÉCNICA (IZQUIERDA Y DERECHA) --}}
    <div class="dual-section">
        
        {{-- 2. SECRETARÍA ARBITRAL (IZQUIERDA) --}}
        <div class="dual-column">
            @if($secArbitral->count() > 0)
                <div class="section-subtitle">Secretaría Arbitral</div>
                <div class="cards-grid">
                    @foreach($secArbitral as $persona)
                        <div class="profile-card" onclick="openModal(this)"
                             data-name="{{ $persona->nombres }}"
                             data-cargo="{{ $persona->cargo }}"
                             data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : '' }}"
                             data-code="{{ $persona->codigo }}"
                             data-specialty="{{ $persona->especialidad }}"
                             data-email="{{ $persona->email }}"
                             data-phone="{{ $persona->telefono }}"
                             data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                            
                            <div class="profile-img-container">
                                <img src="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : asset('img/default-user.jpg') }}" 
                                     class="profile-img" alt="{{ $persona->nombres }}">
                            </div>

                            <div class="profile-name">{{ $persona->nombres }}</div>
                            <div class="profile-role">{{ $persona->cargo }}</div>
                            <div class="profile-inst">CARD CIP CDLL</div>

                            <div class="btn-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 3. SECRETARÍA TÉCNICA (DERECHA) --}}
        <div class="dual-column">
            @if($secTecnica->count() > 0)
                <div class="section-subtitle">Secretaría Técnica</div>
                <div class="cards-grid">
                    @foreach($secTecnica as $persona)
                        <div class="profile-card" onclick="openModal(this)"
                             data-name="{{ $persona->nombres }}"
                             data-cargo="{{ $persona->cargo }}"
                             data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : '' }}"
                             data-code="{{ $persona->codigo }}"
                             data-specialty="{{ $persona->especialidad }}"
                             data-email="{{ $persona->email }}"
                             data-phone="{{ $persona->telefono }}"
                             data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                            
                            <div class="profile-img-container">
                                <img src="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : asset('img/default-user.jpg') }}" 
                                     class="profile-img" alt="{{ $persona->nombres }}">
                            </div>

                            <div class="profile-name">{{ $persona->nombres }}</div>
                            <div class="profile-role">{{ $persona->cargo }}</div>
                            <div class="profile-inst">CARD CIP CDLL</div>

                            <div class="btn-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
    </div> 
</div>

{{-- MODAL (INALTERADO) --}}
<div id="personModal" class="custom-modal-overlay">
    <div class="custom-modal-content">
        <button class="modal-close-btn" onclick="closeModal()"><i class="fas fa-times"></i></button>
        <div class="modal-sidebar">
            <img id="mImg" src="" class="modal-profile-pic">
        </div>
        <div class="modal-main">
            <h3 id="mName" class="modal-name"></h3>
            <span id="mCargo" class="modal-badge"></span>
            
            <div id="mCodeGroup" class="modal-info-row">
                <div class="modal-info-icon"><i class="fas fa-id-card"></i></div>
                <div class="modal-info-text"><strong>Registro profesional</strong> <span id="mCode"></span></div>
            </div>
            <div id="mSpecialtyGroup" class="modal-info-row">
                <div class="modal-info-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="modal-info-text"><strong>Especialidad técnica</strong> <span id="mSpecialty"></span></div>
            </div>
            <div id="mEmailGroup" class="modal-info-row">
                <div class="modal-info-icon"><i class="fas fa-envelope"></i></div>
                <div class="modal-info-text"><strong>Correo de contacto</strong> <span id="mEmail"></span></div>
            </div>
            <div id="mPhoneGroup" class="modal-info-row">
                <div class="modal-info-icon"><i class="fas fa-phone"></i></div>
                <div class="modal-info-text"><strong>Teléfono directo</strong> <span id="mPhone"></span></div>
            </div>

            <a id="mCvBtn" href="#" target="_blank" class="modal-cv-btn">
                <i class="fas fa-file-pdf me-2"></i> Descargar Hoja de Vida
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
            const el = document.getElementById(group);
            if(val && val !== 'null') {
                document.getElementById(id).textContent = val;
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
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
        modal.style.display = 'flex'; 
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('personModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }

    window.onclick = function(event) {
        const modal = document.getElementById('personModal');
        if (event.target == modal) closeModal();
    }
</script>
@endsection