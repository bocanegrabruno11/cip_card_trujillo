@php
    $contacto = \App\Models\SoporteTecnicoContacto::where('estado', 1)->first();
@endphp

@if($contacto)
<style>
    .floating-support-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background-color: var(--cip-red, #dc3545);
        color: white;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        cursor: pointer;
        z-index: 1050;
        transition: all 0.3s ease;
    }
    .floating-support-btn:hover {
        transform: scale(1.1);
        color: white;
    }
    .floating-support-btn i {
        transition: transform 0.3s ease;
    }
    .support-menu {
        position: fixed;
        bottom: 100px;
        right: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        width: 300px;
        z-index: 1050;
        transform: scale(0);
        transform-origin: bottom right;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        opacity: 0;
        visibility: hidden;
    }
    .support-menu.show {
        transform: scale(1);
        opacity: 1;
        visibility: visible;
    }
    .support-menu-header {
        background: var(--cip-red, #dc3545);
        color: white;
        padding: 15px;
        font-weight: bold;
        text-align: center;
        border-radius: 12px 12px 0 0;
    }
    .support-contact-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    .support-contact-item:last-child {
        border-bottom: none;
    }
    .support-contact-name {
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
        font-size: 0.95rem;
    }
    .support-contact-actions {
        display: flex;
        gap: 8px;
    }
    .btn-whatsapp-small {
        background: #25D366;
        color: white;
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 6px;
        text-decoration: none;
        flex: 1;
        text-align: center;
        transition: background 0.2s;
    }
    .btn-whatsapp-small:hover {
        background: #1ebc5a;
        color: white;
    }
    .btn-email-small {
        background: #007bff;
        color: white;
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 6px;
        text-decoration: none;
        flex: 1;
        text-align: center;
        transition: background 0.2s;
    }
    .btn-email-small:hover {
        background: #0056b3;
        color: white;
    }
</style>

<div class="floating-support-btn" id="floatingSupportBtn" onclick="toggleSupportMenu()">
    <i class="fas fa-headset"></i>
</div>

<div class="support-menu" id="supportMenu">
    <div class="support-menu-header">
        <i class="fas fa-life-ring me-2"></i> Soporte Técnico
    </div>
    <div class="support-menu-body" style="max-height: 350px; overflow-y: auto;">
            <div class="support-contact-item">
                <div class="support-contact-name">
                    <!-- <i class="fas fa-user-circle text-muted me-1"></i> {{ $contacto->nombres }} -->
                </div>
                <div class="support-contact-actions">
                    @php
                        $numeroLimpio = null;
                        if (!empty($contacto->numero_contacto)) {
                            $numeroLimpio = preg_replace('/[^0-9]/', '', $contacto->numero_contacto);
                            if (strlen($numeroLimpio) == 9 && substr($numeroLimpio, 0, 1) == '9') {
                                $numeroLimpio = '51' . $numeroLimpio;
                            }
                        }
                    @endphp
                    @if($numeroLimpio)
                    <a href="https://wa.me/{{ $numeroLimpio }}?text=Hola,%20necesito%20ayuda%20con%20el%20m%C3%B3dulo%20de%20mesa%20de%20partes%20del%20CARD%20CIP%20CDLL" target="_blank" class="btn-whatsapp-small">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    @endif
                    
                    @if(!empty($contacto->correo_electronico))
                    <a href="mailto:{{ $contacto->correo_electronico }}" class="btn-email-small">
                        <i class="fas fa-envelope"></i> Correo
                    </a>
                    @endif
                </div>
            </div>
    </div>
</div>

<script>
    function toggleSupportMenu() {
        const menu = document.getElementById('supportMenu');
        const btnIcon = document.querySelector('#floatingSupportBtn i');
        
        menu.classList.toggle('show');
        
        if (menu.classList.contains('show')) {
            btnIcon.classList.remove('fa-headset');
            btnIcon.classList.add('fa-times');
        } else {
            btnIcon.classList.remove('fa-times');
            btnIcon.classList.add('fa-headset');
        }
    }

    document.addEventListener('click', function(event) {
        const btn = document.getElementById('floatingSupportBtn');
        const menu = document.getElementById('supportMenu');
        const btnIcon = document.querySelector('#floatingSupportBtn i');
        
        if (menu && btn) {
            if (!menu.contains(event.target) && !btn.contains(event.target)) {
                menu.classList.remove('show');
                if(btnIcon) {
                    btnIcon.classList.remove('fa-times');
                    btnIcon.classList.add('fa-headset');
                }
            }
        }
    });
</script>
@endif
