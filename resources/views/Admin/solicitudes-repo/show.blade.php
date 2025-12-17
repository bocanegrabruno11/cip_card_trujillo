@extends('Admin.app')

@section('title', 'Detalle de Solicitud #' . $solicitud->id)
@section('page-title', 'Detalle de Solicitud')

@section('content')
<div class="container-fluid">

    {{-- Botón Volver --}}
    <div class="mb-3">
        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- COLUMNA DATOS --}}
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-secondary m-0"><i class="far fa-user me-2"></i> Datos del Solicitante</h5>
                </div>
                <div class="card-body">
                    
                    {{-- Estado --}}
                    <div class="mb-4 text-center">
                        <label class="d-block text-muted small text-uppercase fw-bold mb-2">Estado Actual</label>
                        @if($solicitud->estado == 'pendiente')
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">Pendiente de Revisión</span>
                        @elseif($solicitud->estado == 'aprobado')
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> Aprobado
                            </span>
                            <div class="small text-muted mt-2">
                                Atendido el: {{ \Carbon\Carbon::parse($solicitud->fecha_respuesta)->format('d/m/Y H:i A') }}
                            </div>
                        @else
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> Rechazado
                            </span>
                            <div class="small text-muted mt-2">
                                Rechazado el: {{ \Carbon\Carbon::parse($solicitud->fecha_respuesta)->format('d/m/Y H:i A') }}
                            </div>
                        @endif
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Nombres Completos</small>
                            <div class="fw-bold text-dark fs-5">{{ $solicitud->nombres }}</div>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">DNI</small>
                            <div class="fs-6">{{ $solicitud->dni }}</div>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Correo Electrónico (Gmail)</small>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-6 text-primary">{{ $solicitud->email }}</span>
                                <button class="btn btn-sm btn-light border" onclick="copiarCorreo('{{ $solicitud->email }}')" title="Copiar correo">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Usuario en Sistema</small>
                            <div class="mt-1">
                                @if($solicitud->usuarioRegistrado)
                                    <span class="badge bg-info text-dark"><i class="fas fa-check me-1"></i> Coincide con usuario registrado</span>
                                @else
                                    <span class="badge bg-light text-muted border">No registrado en el sistema</span>
                                @endif
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Fecha de Solicitud</small>
                            <div>{{ $solicitud->created_at->format('d/m/Y - h:i A') }}</div>
                        </li>
                    </ul>
                </div>
                
                {{-- Acciones solo si está pendiente --}}
                <div class="card-footer bg-light p-3">
                    <div class="d-grid gap-2">
                        {{-- CASO 1: PENDIENTE (Flujo normal) --}}
                        @if($solicitud->estado == 'pendiente')
                            <button type="button" class="btn btn-success fw-bold" 
                                    onclick="abrirModalConfirmacion('aprobado')">
                                <i class="fas fa-check me-2"></i> APROBAR SOLICITUD
                            </button>
                            <button type="button" class="btn btn-outline-danger fw-bold" 
                                    onclick="abrirModalConfirmacion('rechazado')">
                                <i class="fas fa-times me-2"></i> RECHAZAR
                            </button>

                        {{-- CASO 2: YA ESTÁ APROBADO (Permitir Revocar) --}}
                        @elseif($solicitud->estado == 'aprobado')
                            <div class="alert alert-success py-2 text-center small mb-2">
                                <i class="fas fa-check-circle me-1"></i> El usuario tiene acceso actualmente.
                            </div>
                            {{-- THIS BUTTON WAS MISSING OR NOT RENDERING --}}
                            <button type="button" class="btn btn-danger fw-bold" 
                                    onclick="abrirModalConfirmacion('rechazado')">
                                <i class="fas fa-ban me-2"></i> REVOCAR PERMISO
                            </button>

                        {{-- CASO 3: YA ESTÁ RECHAZADO (Permitir Reconsiderar) --}}
                        @else
                            <div class="alert alert-danger py-2 text-center small mb-2">
                                <i class="fas fa-times-circle me-1"></i> Esta solicitud fue denegada.
                            </div>
                            <button type="button" class="btn btn-success fw-bold" 
                                    onclick="abrirModalConfirmacion('aprobado')">
                                <i class="fas fa-redo me-2"></i> RECONSIDERAR Y APROBAR
                            </button>
                        @endif

                    </div>
                </div>
                
            </div>
        </div>

        {{-- COLUMNA FOTO --}}
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-secondary m-0"><i class="far fa-id-card me-2"></i> Evidencia (DNI)</h5>
                    <a href="{{ asset('storage/' . $solicitud->foto_dni_path) }}" class="btn btn-sm btn-primary" download>
                        <i class="fas fa-download me-1"></i> Descargar
                    </a>
                </div>
                <div class="card-body bg-light text-center d-flex align-items-center justify-content-center p-4">
                    <div style="max-width: 100%; overflow: hidden; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <img src="{{ asset('storage/' . $solicitud->foto_dni_path) }}" alt="Foto DNI" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- MODAL DE CONFIRMACIÓN --}}
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalConfirmTitle">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="modalIcon" class="mb-3">
                    </div>
                <h5 class="fw-bold mb-2" id="modalMainText">¿Estás seguro?</h5>
                <p class="text-muted mb-0 px-3" id="modalSubText">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                
                <form id="formAccion" action="{{ route('admin.solicitudes.update', $solicitud->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="estado" id="inputEstadoAccion">
                    <button type="submit" class="btn px-4 fw-bold rounded-pill" id="modalConfirmBtn">
                        Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- USAMOS PUSH PARA QUE SE EJECUTE CON @STACK('SCRIPTS') --}}
@push('scripts')
<script>
    // Copiar correo
    function copiarCorreo(texto) {
        navigator.clipboard.writeText(texto).then(function() {
            // Opcional: Feedback visual
        });
    }

    // Modal de Confirmación Inteligente
    function abrirModalConfirmacion(nuevoEstado) {
        const modalEl = document.getElementById('modalConfirmacion');
        const titleEl = document.getElementById('modalConfirmTitle');
        const mainTextEl = document.getElementById('modalMainText');
        const subTextEl = document.getElementById('modalSubText');
        const iconEl = document.getElementById('modalIcon');
        const btnConfirm = document.getElementById('modalConfirmBtn');
        const inputHidden = document.getElementById('inputEstadoAccion');

        // Estado actual traído desde Blade
        const estadoActual = "{{ $solicitud->estado }}";

        // Configurar valor a enviar
        inputHidden.value = nuevoEstado;

        // --- LÓGICA DE TEXTOS SEGÚN CAMBIO DE ESTADO ---
        
        if (nuevoEstado === 'aprobado') {
            // APROBAR
            titleEl.className = 'modal-title fw-bold text-success';
            iconEl.innerHTML = '<i class="fas fa-check-circle fa-4x text-success"></i>';
            btnConfirm.className = 'btn btn-success px-4 fw-bold rounded-pill';
            btnConfirm.textContent = 'Sí, Aprobar';

            if (estadoActual === 'rechazado') {
                titleEl.textContent = 'Reconsiderar Solicitud';
                mainTextEl.textContent = '¿Deseas cambiar el estado a APROBADO?';
                subTextEl.innerHTML = 'El usuario pasará a tener acceso. Recuerda <b>agregar manualmente</b> el correo en Drive.';
            } else {
                titleEl.textContent = 'Aprobar Solicitud';
                mainTextEl.textContent = '¿Conceder acceso al repositorio?';
                subTextEl.innerHTML = 'Recuerda que debes <b>agregar manualmente</b> el correo a la carpeta de Drive.';
            }

        } else {
            // RECHAZAR / REVOCAR
            titleEl.className = 'modal-title fw-bold text-danger';
            iconEl.innerHTML = '<i class="fas fa-times-circle fa-4x text-danger"></i>';
            btnConfirm.className = 'btn btn-danger px-4 fw-bold rounded-pill';
            
            if (estadoActual === 'aprobado') {
                titleEl.textContent = 'Revocar Acceso';
                mainTextEl.textContent = '¿Quitar permiso al usuario?';
                subTextEl.innerHTML = '<b>IMPORTANTE:</b> El sistema marcará la solicitud como rechazada, pero debes <b>eliminar manualmente</b> el correo de Google Drive.';
                btnConfirm.textContent = 'Sí, Revocar';
            } else {
                titleEl.textContent = 'Rechazar Solicitud';
                mainTextEl.textContent = '¿Deseas rechazar esta solicitud?';
                subTextEl.textContent = 'El usuario no tendrá acceso. Esta acción se puede revertir luego si es necesario.';
                btnConfirm.textContent = 'Sí, Rechazar';
            }
        }

        var myModal = new bootstrap.Modal(modalEl);
        myModal.show();
    }
</script>
@endpush