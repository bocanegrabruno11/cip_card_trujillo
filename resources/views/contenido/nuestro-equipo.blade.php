@extends('inicio')

@section('title', 'Nuestro Equipo - CARD CD La Libertad')

@section('styles')
<style>
    /* === ESTILOS GENERALES === */
    .team-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
    }

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

    /* === TARJETA PERSONA === */
    .person-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 260px;
        cursor: pointer;
        transition: transform 0.2s;
        position: relative;
    }
    
    .person-card:hover {
        transform: translateY(-5px);
    }

    .person-img-wrapper {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border: 5px solid #fff;
        background-color: #eee;
        transition: box-shadow 0.3s ease;
    }

    .person-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
    }

    .person-card:hover .person-img-wrapper {
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border-color: #f8f9fa;
    }

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

    /* Click Hint (Badge visual) */
    .click-hint {
        opacity: 0;
        position: absolute;
        top: 150px; /* Ajustado para la imagen de 180px */
        background: rgba(0,0,0,0.7);
        color: white;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 12px;
        transition: opacity 0.3s;
        pointer-events: none;
    }
    .person-card:hover .click-hint { opacity: 1; }

    /* Datos de Contacto (Visualización rápida) */
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
    }
    .contact-item i { font-size: 13px; color: #607D8B; }

    /* Layout Especial Secretaria */
    .secretary-layout {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
        max-width: 700px;
        margin: 0 auto;
        cursor: pointer;
        padding: 20px;
        border-radius: 10px;
        transition: background-color 0.2s;
    }
    .secretary-layout:hover {
        background-color: #fcfcfc;
    }
    .secretary-info { text-align: left; }

   /* === MODAL PERFIL PROFESIONAL (MODERNO) === */
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
        width: 180px; height: 180px; background-color: #D4AF37; /* Dorado */
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
    .close-btn-float:hover { background: #D4AF37; color: white; }

    /* TIPOGRAFÍA */
    .modal-name-title {
        font-size: 26px; font-weight: 800; color: #333;
        line-height: 1.2; margin-bottom: 5px;
    }

    .modal-cargo-badge {
        display: inline-block; background-color: #D4AF37; /* Dorado */
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
        width: 25px; color: #D4AF37; margin-right: 10px;
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
    .btn-download-cv:hover { background-color: #D4AF37; }
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

    /* === RESPONSIVIDAD === */
    @media (max-width: 768px) {
        .secretary-layout { flex-direction: column; text-align: center; }
        .secretary-info { text-align: center; }
        .section-title { font-size: 22px; margin-top: 40px; }
        .team-row { gap: 30px; }
        
        .contact-info { align-items: center !important; }
    }
</style>
@endsection

@section('content')

<div class="team-container">
    
    <div class="page-header">
        <h1>Nuestro equipo</h1>
    </div>

    {{-- ===========================================
         1. SECRETARÍA GENERAL (Layout Especial)
         =========================================== --}}
    @if($secretaria)
        <div class="section-title">SECRETARÍA GENERAL</div>
        
        <div class="secretary-layout" onclick="openModal(this)"
             data-name="{{ $secretaria->nombres }}"
             data-cargo="{{ $secretaria->cargo ?? '' }}"
             data-img="{{ $secretaria->ruta_imagen ? asset('storage/' . $secretaria->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($secretaria->nombres).'&background=eee&color=333&size=200' }}"
             data-code="{{ $secretaria->codigo ?? '' }}"
             data-email="{{ $secretaria->email ?? '' }}"
             data-phone="{{ $secretaria->telefono ?? '' }}"
             data-cv="{{ $secretaria->ruta_cv ? asset('storage/' . $secretaria->ruta_cv) : '' }}">
            
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

                {{-- Click Hint visible solo al hover --}}
                <div style="font-size: 12px; color: #D4AF37; font-weight: bold; margin-bottom: 10px;">
                    <i class="fas fa-plus-circle"></i> Ver perfil completo
                </div>

                <div class="contact-info" style="align-items: flex-start;">
                    @if($secretaria->email)
                        <span class="contact-item">
                            <i class="fas fa-envelope"></i> {{ $secretaria->email }}
                        </span>
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


    {{-- ===========================================
         2. SECRETARIOS ARBITRALES
         =========================================== --}}
    @if($secretariosArbitrales->count() > 0)
        <div class="section-title">SECRETARIOS ARBITRALES</div>

        <div class="team-row">
            @foreach($secretariosArbitrales as $persona)
                <div class="person-card" onclick="openModal(this)"
                     data-name="{{ $persona->nombres }}"
                     data-cargo="{{ $persona->cargo ?? '' }}"
                     data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($persona->nombres).'&background=eee&color=333&size=200' }}"
                     data-code="{{ $persona->codigo ?? '' }}"
                     data-email="{{ $persona->email ?? '' }}"
                     data-phone="{{ $persona->telefono ?? '' }}"
                     data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                    
                    <div class="person-img-wrapper">
                        @if($persona->ruta_imagen)
                            <img src="{{ asset('storage/' . $persona->ruta_imagen) }}" class="person-img" alt="{{ $persona->nombres }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($persona->nombres) }}&background=eee&color=333&size=180" class="person-img">
                        @endif
                    </div>
                    
                    <span class="click-hint">+ Detalles</span>
                    <div class="person-name">{{ $persona->nombres }}</div>
                    
                    <div class="contact-info">
                        @if($persona->email)
                            <span class="contact-item">
                                <i class="fas fa-envelope"></i> {{ $persona->email }}
                            </span>
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


    {{-- ===========================================
         3. PERSONAL DE APOYO
         =========================================== --}}
    @if($personalApoyo->count() > 0)
        <div class="section-title">PERSONAL PROFESIONAL DE APOYO PARA JRD/JPRD</div>

        <div class="team-row">
            @foreach($personalApoyo as $persona)
                <div class="person-card" onclick="openModal(this)"
                     data-name="{{ $persona->nombres }}"
                     data-cargo="{{ $persona->cargo ?? '' }}"
                     data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($persona->nombres).'&background=eee&color=333&size=200' }}"
                     data-code="{{ $persona->codigo ?? '' }}"
                     data-email="{{ $persona->email ?? '' }}"
                     data-phone="{{ $persona->telefono ?? '' }}"
                     data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                    
                    <div class="person-img-wrapper">
                        @if($persona->ruta_imagen)
                            <img src="{{ asset('storage/' . $persona->ruta_imagen) }}" class="person-img" alt="{{ $persona->nombres }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($persona->nombres) }}&background=eee&color=333&size=180" class="person-img">
                        @endif
                    </div>
                    
                    <span class="click-hint">+ Detalles</span>
                    <div class="person-name">{{ $persona->nombres }}</div>
                    
                    <div class="contact-info">
                        @if($persona->email)
                            <span class="contact-item">
                                <i class="fas fa-envelope"></i> {{ $persona->email }}
                            </span>
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


    {{-- ===========================================
         4. SOPORTE ADMINISTRATIVO
         =========================================== --}}
    @if($soporteAdmin->count() > 0)
        <div class="section-title">SOPORTE ADMINISTRATIVO</div>

        <div class="team-row">
            @foreach($soporteAdmin as $persona)
                <div class="person-card" onclick="openModal(this)"
                     data-name="{{ $persona->nombres }}"
                     data-cargo="{{ $persona->cargo ?? '' }}"
                     data-img="{{ $persona->ruta_imagen ? asset('storage/' . $persona->ruta_imagen) : 'https://ui-avatars.com/api/?name='.urlencode($persona->nombres).'&background=eee&color=333&size=200' }}"
                     data-code="{{ $persona->codigo ?? '' }}"
                     data-email="{{ $persona->email ?? '' }}"
                     data-phone="{{ $persona->telefono ?? '' }}"
                     data-cv="{{ $persona->ruta_cv ? asset('storage/' . $persona->ruta_cv) : '' }}">
                    
                    <div class="person-img-wrapper">
                        @if($persona->ruta_imagen)
                            <img src="{{ asset('storage/' . $persona->ruta_imagen) }}" class="person-img" alt="{{ $persona->nombres }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($persona->nombres) }}&background=eee&color=333&size=180" class="person-img">
                        @endif
                    </div>
                    
                    <span class="click-hint">+ Detalles</span>
                    <div class="person-name">{{ $persona->nombres }}</div>
                    
                    <div class="contact-info">
                        @if($persona->email)
                            <span class="contact-item">
                                <i class="fas fa-envelope"></i> {{ $persona->email }}
                            </span>
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
            element.style.display = 'flex'; // Usamos flex para mantener el diseño
            return true;
        } else {
            element.style.display = 'none';
            return false;
        }
    }

    function openModal(element) {
        // 1. Obtener datos
        const name = element.getAttribute('data-name');
        const cargo = element.getAttribute('data-cargo'); 
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
        
        // Validar que img tenga una URL real y no esté vacía
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

        // 4. Llenar campos validando nulos
        
        // Cargo
        const cargoGroup = document.getElementById('mCargoGroup');
        if (cargo && cargo.trim() !== "") {
            document.getElementById('mCargo').textContent = cargo;
            cargoGroup.style.display = 'block';
        } else {
            cargoGroup.style.display = 'none';
        }

        // Código
        const codeGroup = document.getElementById('mCodeGroup');
        if (code && code.trim() !== "") {
            document.getElementById('mCode').textContent = code;
            codeGroup.style.display = 'flex';
        } else {
            codeGroup.style.display = 'none';
        }

        // Especialidad, Email, Teléfono
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

        // 5. Mostrar con animación
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