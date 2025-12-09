@extends('inicio')

@section('title', 'Organización del CARD - CARD CD La Libertad')

@section('styles')
<style>
    /* === ESTILOS GENERALES === */
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

    /* === TÍTULOS === */
    .section-title {
        color: #E31E24;
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

    /* === TARJETA DE PERSONA === */
    .person-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 200px;
        cursor: pointer;
        transition: transform 0.2s;
        position: relative;
    }
    
    .person-card:hover {
        transform: translateY(-5px);
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
        border-color: #AD2B2E;
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
        min-height: 20px; /* Evita saltos si está vacío en la tarjeta */
    }

    .click-hint {
        opacity: 0;
        position: absolute;
        top: 130px;
        background: rgba(0,0,0,0.7);
        color: white;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        transition: opacity 0.3s;
        pointer-events: none;
    }
    .person-card:hover .click-hint { opacity: 1; }


    /* === BOTÓN RESOLUCIÓN === */
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
    .btn-resolution:hover { background-color: #ff5252; color: white; }

    /* === ESTILOS DEL MODAL === */
    /* === MODAL PERFIL PROFESIONAL (NUEVO DISEÑO) === */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .custom-modal.show { opacity: 1; }

    .modal-content {
        background-color: #fff;
        border-radius: 8px;
        width: 95%;
        max-width: 800px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        display: flex;
        flex-direction: row;
        overflow: hidden;
        position: relative;
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .custom-modal.show .modal-content { transform: scale(1); }

    /* LADO IZQUIERDO (FOTO) */
    .modal-left {
        width: 35%;
        background-color: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        border-right: 1px solid #e0e0e0;
    }
    
    .modal-img-large {
        width: 100%; max-width: 200px; aspect-ratio: 1/1;
        object-fit: cover; border-radius: 50%;
        border: 5px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .modal-initials-large {
        width: 180px; height: 180px; background-color: #AD2B2E;
        color: white; border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        font-size: 60px; font-weight: 800; border: 5px solid white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    /* LADO DERECHO (DATOS) */
    .modal-right {
        width: 65%;
        padding: 40px 30px;
        display: flex; flex-direction: column;
        justify-content: center; text-align: left;
    }

    /* BOTÓN CERRAR FLOTANTE */
    .close-btn-float {
        position: absolute; top: 15px; right: 15px;
        width: 35px; height: 35px; background: #f1f1f1;
        color: #333; border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        cursor: pointer; font-size: 20px; transition: all 0.2s;
        border: none; z-index: 10;
    }
    .close-btn-float:hover { background: #AD2B2E; color: white; }

    /* TIPOGRAFÍA */
    .modal-name-title {
        font-size: 26px; font-weight: 800; color: #333;
        line-height: 1.2; margin-bottom: 5px;
    }

    .modal-cargo-badge {
        display: inline-block; background-color: #AD2B2E;
        color: white; padding: 5px 12px; border-radius: 20px;
        font-size: 12px; font-weight: bold; text-transform: uppercase;
        margin-bottom: 15px; align-self: flex-start;
    }

    /* LISTA DE DATOS */
    .modal-data-list { margin-top: 15px; width: 100%; }

    .data-row {
        display: flex; align-items: flex-start;
        margin-bottom: 12px; font-size: 14px; color: #555;
    }
    .data-icon {
        width: 25px; color: #AD2B2E; margin-right: 10px;
        text-align: center; font-size: 16px; margin-top: 2px;
    }
    .data-content strong {
        display: block; font-size: 11px; color: #999;
        text-transform: uppercase; margin-bottom: 2px;
    }
    .data-text { font-weight: 500; color: #333; word-break: break-word; }

    /* BOTÓN CV */
    .btn-download-cv {
        margin-top: 25px; display: inline-flex; align-items: center;
        justify-content: center; background-color: #333;
        color: white !important; padding: 12px 25px; border-radius: 6px;
        text-decoration: none; font-weight: bold; transition: background 0.3s;
        align-self: flex-start;
    }
    .btn-download-cv:hover { background-color: #AD2B2E; }
    .btn-download-cv i { margin-right: 10px; }

    /* RESPONSIVE */
    @media (max-width: 700px) {
        .modal-content { flex-direction: column; overflow-y: auto; max-height: 90vh; }
        .modal-left { width: 100%; padding: 30px 20px 20px; border-right: none; border-bottom: 1px solid #eee; }
        .modal-right { width: 100%; padding: 25px 20px; align-items: center; text-align: center; }
        .modal-img-large, .modal-initials-large { width: 140px; height: 140px; font-size: 50px; }
        .modal-name-title { font-size: 22px; }
        .modal-cargo-badge { align-self: center; }
        .data-row { justify-content: center; text-align: left; }
        .btn-download-cv { align-self: center; }
    }

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

    {{-- ==========================================
         1. ÓRGANO DIRECTIVO
         ========================================== --}}
    <div class="section-title">ÓRGANO DIRECTIVO - CARD CD LA LIBERTAD</div>
    <div class="team-row">
        @forelse($directivos as $persona)
            <div class="person-card" onclick="openModal(this)"
                 data-name="{{ $persona->nombres }}"
                 data-role="{{ $persona->cargo ?? '' }}"
                 data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($persona->nombres).'&background=eee&color=333&size=200' }}"
                 data-code="{{ $persona->codigo ?? '' }}"
                 data-specialty="{{ $persona->especialidad ?? '' }}"
                 data-email="{{ $persona->email ?? '' }}"
                 data-phone="{{ $persona->telefono ?? '' }}" {{-- NUEVO ATRIBUTO --}}
                 data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                
                <img src="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($persona->nombres).'&background=eee&color=333&size=160' }}" 
                     class="person-img" alt="Foto">
                
                <span class="click-hint">+ Detalles</span>
                <div class="person-name">{{ $persona->nombres }}</div>
                <div class="person-role">{{ $persona->cargo }}</div>
            </div>
        @empty
            <p class="text-muted">No hay miembros directivos registrados.</p>
        @endforelse
    </div>


    {{-- ==========================================
         2. ÓRGANO DECISORIO
         ========================================== --}}
    <div class="section-title">ÓRGANO DECISORIO - DIRECTORIO</div>

    @if($decisorioPresidente)
        <div class="sub-title">PRESIDENTE DEL DIRECTORIO</div>
        <div class="team-row">
            <div class="person-card" onclick="openModal(this)"
                 data-name="{{ $decisorioPresidente->nombres }}"
                 data-cargo="{{ $decisorioPresidente->cargo ?? '' }}"
                 data-img="{{ $decisorioPresidente->ruta_imagen ? asset('storage/' . $decisorioPresidente->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($decisorioPresidente->nombres).'&background=eee&color=333&size=200' }}"
                 data-code="{{ $decisorioPresidente->codigo ?? '' }}"
                 data-specialty="{{ $decisorioPresidente->especialidad ?? '' }}"
                 data-email="{{ $decisorioPresidente->email ?? '' }}"
                 data-phone="{{ $decisorioPresidente->telefono ?? '' }}"
                 data-cv="{{ $decisorioPresidente->ruta_cv ? asset('storage/' . $decisorioPresidente->ruta_cv) : '' }}">
                
                <img src="{{ $decisorioPresidente->ruta_imagen ? asset('storage/' . $decisorioPresidente->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($decisorioPresidente->nombres).'&background=eee&color=333&size=160' }}" 
                     class="person-img" alt="Foto">
                
                <span class="click-hint">+ Detalles</span>
                <div class="person-name">{{ $decisorioPresidente->nombres }}</div>
                <div class="person-role">{{ $decisorioPresidente->cargo }}</div>
            </div>
        </div>
    @endif

    @if($decisorioMiembros->count() > 0)
        <div class="sub-title">MIEMBROS DEL DIRECTORIO</div>
        <div class="team-row">
            @foreach($decisorioMiembros as $miembro)
                <div class="person-card" onclick="openModal(this)"
                     data-name="{{ $miembro->nombres }}"
                     data-cargo="{{ $miembro->cargo ?? '' }}"
                     data-img="{{ $miembro->ruta_imagen ? asset('storage/' . $miembro->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($miembro->nombres).'&background=eee&color=333&size=200' }}"
                     data-code="{{ $miembro->codigo ?? '' }}"
                     data-specialty="{{ $miembro->especialidad ?? '' }}"
                     data-email="{{ $miembro->email ?? '' }}"
                     data-phone="{{ $miembro->telefono ?? '' }}"
                     data-cv="{{ $miembro->ruta_cv ? asset('storage/' . $miembro->ruta_cv) : '' }}">
                    
                    <img src="{{ $miembro->ruta_imagen ? asset('storage/' . $miembro->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($miembro->nombres).'&background=eee&color=333&size=160' }}" 
                         class="person-img" alt="Foto">
                    
                    <span class="click-hint">+ Detalles</span>
                    <div class="person-name">{{ $miembro->nombres }}</div>
                    <div class="person-role">{{ $miembro->cargo }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            {{-- LÓGICA DINÁMICA PARA LA RESOLUCIÓN --}}
            @if(isset($documentoResolucion) && $documentoResolucion)
                <a href="{{ asset('storage/' . $documentoResolucion->ruta_archivo) }}" target="_blank" class="btn-resolution">
                    {{ $documentoResolucion->titulo ?? 'RESOLUCIÓN DE LA CONFORMACIÓN DEL DIRECTORIO' }}
                </a>
            @else
                {{-- Botón oculto o mensaje de pendiente --}}
                <span class="btn-resolution" style="background-color: #ccc; cursor: default;">
                    RESOLUCIÓN PENDIENTE DE PUBLICACIÓN
                </span>
            @endif
        </div>
    </div>

    {{-- ==========================================
         3. SECRETARÍA GENERAL
         ========================================== --}}
    @if($secretaria)
        <div class="section-title">ORGANO DE GESTION - SECRETARIA GENERAL</div>
        <div class="team-row">
            <div class="person-card" style="width: 400px; max-width: 100%;" onclick="openModal(this)"
                 data-name="{{ $secretaria->nombres }}"
                 data-cargo="{{ $secretaria->cargo ?? '' }}"
                 data-img="{{ $secretaria->ruta_imagen ? asset('storage/' . $secretaria->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($secretaria->nombres).'&background=eee&color=333&size=200' }}"
                 data-code="{{ $secretaria->codigo ?? '' }}"
                 data-specialty="{{ $secretaria->especialidad ?? '' }}"
                 data-email="{{ $secretaria->email ?? '' }}"
                 data-phone="{{ $secretaria->telefono ?? '' }}"
                 data-cv="{{ $secretaria->ruta_cv ? asset('storage/' . $secretaria->ruta_cv) : '' }}">
                
                <img src="{{ $secretaria->ruta_imagen ? asset('storage/' . $secretaria->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($secretaria->nombres).'&background=eee&color=333&size=160' }}" 
                     class="person-img" alt="Foto">
                
                <span class="click-hint">+ Detalles</span>
                <div class="person-name" style="font-size: 18px;">{{ $secretaria->nombres }}</div>
                <div class="person-role">{{ $secretaria->cargo }}</div>
            </div>
        </div>
    @endif

</div>

{{-- =========================================================
     MODAL DE DETALLE
     ========================================================= --}}
{{-- MODAL PERFIL PROFESIONAL --}}
<div id="personModal" class="custom-modal">
    <div class="modal-content">
        <button class="close-btn-float" onclick="closeModal()">&times;</button>
        
        <div class="modal-left">
            <img id="mImg" src="" class="modal-img-large" style="display: none;">
            <div id="mInitials" class="modal-initials-large" style="display: none;"></div>
        </div>

        <div class="modal-right">
            <h2 id="mName" class="modal-name-title"></h2>
            
            <div id="mCargoGroup" style="display: none;">
                <span id="mCargo" class="modal-cargo-badge"></span>
            </div>

            <div class="modal-data-list">
                <div id="mCodeGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-id-card"></i></div>
                    <div class="data-content">
                        <strong>Registro / Código</strong>
                        <span id="mCode" class="data-text"></span>
                    </div>
                </div>

                <div id="mSpecialtyGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="data-content">
                        <strong>Especialidad</strong>
                        <span id="mSpecialty" class="data-text"></span>
                    </div>
                </div>

                <div id="mEmailGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-envelope"></i></div>
                    <div class="data-content">
                        <strong>Correo Electrónico</strong>
                        <span id="mEmail" class="data-text"></span>
                    </div>
                </div>

                <div id="mPhoneGroup" class="data-row" style="display: none;">
                    <div class="data-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="data-content">
                        <strong>Teléfono / Contacto</strong>
                        <span id="mPhone" class="data-text"></span>
                    </div>
                </div>
            </div>

            <a id="mCvBtn" href="#" target="_blank" class="btn-download-cv" style="display: none;">
                <i class="fas fa-file-pdf"></i> Descargar Hoja de Vida
            </a>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    // --- LÓGICA DEL MODAL ---
    function toggleDisplay(elementId, value) {
        const element = document.getElementById(elementId);
        if (value && value.trim() !== "") {
            element.style.display = 'flex';
            return true;
        } else {
            element.style.display = 'none';
            return false;
        }
    }

    function openModal(element) {
        // 1. Obtener datos
        const name = element.getAttribute('data-name');
        const cargo = element.getAttribute('data-cargo'); // Ahora leemos data-cargo
        const img = element.getAttribute('data-img');
        const code = element.getAttribute('data-code');
        const specialty = element.getAttribute('data-specialty');
        const email = element.getAttribute('data-email');
        const phone = element.getAttribute('data-phone');
        const cv = element.getAttribute('data-cv');

        // 2. Llenar Nombre
        document.getElementById('mName').textContent = name;
        
        // 3. Imagen vs Iniciales
        const imgEl = document.getElementById('mImg');
        const initEl = document.getElementById('mInitials');
        
        // Verificamos si img tiene contenido real y no es la URL por defecto de avatars si prefieres controlarlo aqui
        // En este caso, asumimos que si viene data-img, lo mostramos.
        // Si quieres forzar iniciales cuando no hay foto real subida (evitando ui-avatars en el modal grande):
        const hasRealImage = img && !img.includes('ui-avatars.com') && img.trim() !== "";

        if (hasRealImage) {
            imgEl.src = img;
            imgEl.style.display = 'block';
            initEl.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            initEl.textContent = name.charAt(0).toUpperCase();
            initEl.style.display = 'flex';
        }

        // 4. Llenar campos
        const cargoGroup = document.getElementById('mCargoGroup');
        if (cargo && cargo.trim() !== "") {
            document.getElementById('mCargo').textContent = cargo;
            cargoGroup.style.display = 'block';
        } else {
            cargoGroup.style.display = 'none';
        }

        const codeGroup = document.getElementById('mCodeGroup');
        if (code && code.trim() !== "") {
            document.getElementById('mCode').textContent = code;
            codeGroup.style.display = 'flex';
        } else {
            codeGroup.style.display = 'none';
        }

        if(toggleDisplay('mSpecialtyGroup', specialty)) document.getElementById('mSpecialty').textContent = specialty;
        if(toggleDisplay('mEmailGroup', email)) document.getElementById('mEmail').textContent = email;
        if(toggleDisplay('mPhoneGroup', phone)) document.getElementById('mPhone').textContent = phone;

        // Botón CV
        const cvBtn = document.getElementById('mCvBtn');
        if (cv && cv.trim() !== "") {
            cvBtn.href = cv;
            cvBtn.style.display = 'inline-flex';
        } else {
            cvBtn.style.display = 'none';
        }

        // 5. Mostrar
        const modal = document.getElementById('personModal');
        modal.style.display = 'flex';
        setTimeout(() => { modal.classList.add('show'); }, 10);
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