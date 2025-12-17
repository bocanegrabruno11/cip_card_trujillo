@extends('Admin.app')

@section('title', 'Solicitudes de Acceso al Repositorio')
@section('page-title', 'Gestión de Solicitudes')

@section('content')
<div class="container-fluid">

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TARJETA PRINCIPAL --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold text-secondary">
                        <i class="fas fa-inbox me-2"></i> Bandeja de Solicitudes
                    </h5>
                </div>
                
                {{-- FILTROS --}}
                <div class="col-md-6">
                    <form action="{{ route('admin.solicitudes.index') }}" method="GET" class="d-flex gap-2 justify-content-md-end">
                        <select name="estado" class="form-select form-select-sm" style="max-width: 150px;" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                            <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobados</option>
                            <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazados</option>
                        </select>

                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>

                        @if(request()->anyFilled(['search', 'estado']))
                            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-sm btn-light" title="Limpiar"><i class="fas fa-times"></i></a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 70vh; overflow: auto;">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Solicitante</th>
                            <th>Correo / DNI</th>
                            <th class="text-center">Evidencia</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td class="ps-4 text-muted small">
                                    {{ $solicitud->created_at->format('d/m/Y') }} <br>
                                    {{ $solicitud->created_at->format('H:i A') }}
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $solicitud->nombres }}</div>
                                    @if($solicitud->usuarioRegistrado)
                                        <span class="badge bg-info text-dark" style="font-size: 10px;">
                                            <i class="fas fa-user-check"></i> Registrado
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size: 10px;">Externo</span>
                                    @endif
                                </td>
                                <td>
                                    <div><i class="far fa-envelope text-muted me-1"></i> {{ $solicitud->email }}</div>
                                    <div class="small text-muted"><i class="far fa-id-card text-muted me-1"></i> {{ $solicitud->dni }}</div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="verFoto('{{ asset('storage/' . $solicitud->foto_dni_path) }}', '{{ $solicitud->dni }}')">
                                        <i class="fas fa-image"></i> Ver DNI
                                    </button>
                                </td>
                                <td class="text-center">
                                    @if($solicitud->estado == 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($solicitud->estado == 'aprobado')
                                        <span class="badge bg-success">Aprobado</span>
                                    @else
                                        <span class="badge bg-danger">Rechazado</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        
                                        {{-- 1. Ver Detalle (Siempre) --}}
                                        <a href="{{ route('admin.solicitudes.show', $solicitud->id) }}" 
                                           class="btn btn-sm btn-info text-white" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- 2. Acciones Dinámicas --}}
                                        @if($solicitud->estado == 'pendiente')
                                            {{-- Aprobar --}}
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="abrirModalEstado('{{ route('admin.solicitudes.update', $solicitud->id) }}', 'aprobado', 'pendiente')" 
                                                    title="Aprobar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            {{-- Rechazar --}}
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="abrirModalEstado('{{ route('admin.solicitudes.update', $solicitud->id) }}', 'rechazado', 'pendiente')" 
                                                    title="Rechazar">
                                                <i class="fas fa-times"></i>
                                            </button>

                                        @elseif($solicitud->estado == 'aprobado')
                                            {{-- Revocar (Cambia a rechazado) --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="abrirModalEstado('{{ route('admin.solicitudes.update', $solicitud->id) }}', 'rechazado', 'aprobado')" 
                                                    title="Revocar Permiso">
                                                <i class="fas fa-ban"></i>
                                            </button>

                                        @elseif($solicitud->estado == 'rechazado')
                                            {{-- Reconsiderar (Cambia a aprobado) --}}
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="abrirModalEstado('{{ route('admin.solicitudes.update', $solicitud->id) }}', 'aprobado', 'rechazado')" 
                                                    title="Reconsiderar y Aprobar">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        @endif
                                        
                                        {{-- 3. Eliminar (Siempre) --}}
                                        <button type="button" class="btn btn-sm btn-link text-secondary p-0 ms-1" 
                                                title="Eliminar permanentemente"
                                                onclick="abrirModalEstado('{{ route('admin.solicitudes.destroy', $solicitud->id) }}', 'eliminar', '{{ $solicitud->estado }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <p class="mb-0">No se encontraron solicitudes.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $solicitudes->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- MODAL FOTO --}}
<div class="modal fade" id="modalFotoDni" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DNI: <span id="lblDniFoto"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center bg-light">
                <img src="" id="imgDniPreview" class="img-fluid rounded shadow-sm" style="max-height: 400px;">
            </div>
            <div class="modal-footer">
                <a href="" id="linkDescargaFoto" class="btn btn-primary btn-sm" download>Descargar</a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE CONFIRMACIÓN DE ESTADO (NUEVO) --}}
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalConfirmTitle">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="modalIcon" class="mb-3"></div>
                <h5 class="fw-bold mb-2" id="modalMainText">¿Estás seguro?</h5>
                <p class="text-muted mb-0 px-3" id="modalSubText">Acción irreversible.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                
                {{-- Formulario dentro del modal --}}
                {{-- Formulario Dinámico dentro del modal --}}
                <form id="formModalAccion" method="POST">
                    @csrf
                    {{-- Este input cambiará entre PUT y DELETE según JS --}}
                    <input type="hidden" name="_method" id="inputMethod" value="PUT">
                    
                    {{-- Input solo necesario para cambios de estado, no para eliminar --}}
                    <input type="hidden" name="estado" id="inputEstado">
                    
                    <button type="submit" class="btn px-4 fw-bold rounded-pill" id="modalConfirmBtn">
                        Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Modal Foto (Sin cambios)
    function verFoto(url, dni) {
        document.getElementById('imgDniPreview').src = url;
        document.getElementById('linkDescargaFoto').href = url;
        document.getElementById('lblDniFoto').textContent = dni;
        var myModal = new bootstrap.Modal(document.getElementById('modalFotoDni'));
        myModal.show();
    }

    // Modal Confirmación Universal (Estado + Eliminar)
    function abrirModalEstado(url, accion, estadoActual) {
        const modalEl = document.getElementById('modalConfirmacion');
        const form = document.getElementById('formModalAccion');
        const inputMethod = document.getElementById('inputMethod');
        const inputEstado = document.getElementById('inputEstado');
        
        // Elementos visuales
        const titleEl = document.getElementById('modalConfirmTitle');
        const iconEl = document.getElementById('modalIcon');
        const mainTextEl = document.getElementById('modalMainText');
        const subTextEl = document.getElementById('modalSubText');
        const btnConfirm = document.getElementById('modalConfirmBtn');

        // Configuración Base
        form.action = url;

        // --- LÓGICA SEGÚN LA ACCIÓN ---
        
        if (accion === 'eliminar') {
            // CASO ELIMINAR (DELETE)
            inputMethod.value = 'DELETE'; // Cambiamos método a DELETE
            inputEstado.value = '';       // No se necesita estado
            
            titleEl.textContent = 'Eliminar Registro';
            titleEl.className = 'modal-title fw-bold text-dark';
            iconEl.innerHTML = '<i class="fas fa-trash-alt fa-4x text-secondary"></i>';
            
            mainTextEl.textContent = '¿Eliminar permanentemente?';
            subTextEl.innerHTML = 'Se borrará la solicitud y la foto del servidor.<br><span class="text-danger fw-bold">Esta acción no se puede deshacer.</span>';
            
            btnConfirm.className = 'btn btn-dark px-4 fw-bold rounded-pill';
            btnConfirm.textContent = 'Sí, Eliminar';

        } else if (accion === 'aprobado') {
            // CASO APROBAR (PUT)
            inputMethod.value = 'PUT';
            inputEstado.value = 'aprobado';

            titleEl.className = 'modal-title fw-bold text-success';
            iconEl.innerHTML = '<i class="fas fa-check-circle fa-4x text-success"></i>';
            btnConfirm.className = 'btn btn-success px-4 fw-bold rounded-pill';
            btnConfirm.textContent = 'Sí, Aprobar';

            if(estadoActual === 'rechazado') {
                titleEl.textContent = 'Reconsiderar Solicitud';
                mainTextEl.textContent = '¿Cambiar estado a APROBADO?';
                subTextEl.innerHTML = 'El usuario tendrá acceso. Recuerda <b>agregar manualmente</b> el correo en Drive.';
            } else {
                titleEl.textContent = 'Aprobar Solicitud';
                mainTextEl.textContent = '¿Conceder acceso?';
                subTextEl.innerHTML = 'Recuerda que debes <b>agregar manualmente</b> el correo a la carpeta de Drive.';
            }

        } else if (accion === 'rechazado') {
            // CASO RECHAZAR (PUT)
            inputMethod.value = 'PUT';
            inputEstado.value = 'rechazado';

            titleEl.className = 'modal-title fw-bold text-danger';
            iconEl.innerHTML = '<i class="fas fa-times-circle fa-4x text-danger"></i>';
            btnConfirm.className = 'btn btn-danger px-4 fw-bold rounded-pill';

            if(estadoActual === 'aprobado') {
                titleEl.textContent = 'Revocar Acceso';
                mainTextEl.textContent = '¿Quitar permiso al usuario?';
                subTextEl.innerHTML = 'El sistema lo marcará como rechazado. <b>Elimina manualmente</b> el correo de Drive.';
                btnConfirm.textContent = 'Sí, Revocar';
            } else {
                titleEl.textContent = 'Rechazar Solicitud';
                mainTextEl.textContent = '¿Denegar solicitud?';
                subTextEl.textContent = 'El usuario no tendrá acceso.';
                btnConfirm.textContent = 'Sí, Rechazar';
            }
        }

        var myModal = new bootstrap.Modal(modalEl);
        myModal.show();
    }
</script>
@endpush