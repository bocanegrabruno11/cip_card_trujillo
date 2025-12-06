@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Organización</h2>
        <a href="{{ route('organizacion-gestion.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-user-plus"></i> Nuevo Miembro
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="autoDismissAlert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('organizacion-gestion.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Grupo</label>
                        <select name="grupo" class="form-select form-select-sm">
                            <option value="">Todos los grupos</option>
                            <option value="directivo" {{ request('grupo') == 'directivo' ? 'selected' : '' }}>Órgano Directivo</option>
                            <option value="decisorio_presidente" {{ request('grupo') == 'decisorio_presidente' ? 'selected' : '' }}>Decisorio - Presidente</option>
                            <option value="decisorio_miembros" {{ request('grupo') == 'decisorio_miembros' ? 'selected' : '' }}>Decisorio - Miembros</option>
                            <option value="secretaria" {{ request('grupo') == 'secretaria' ? 'selected' : '' }}>Secretaría General</option>
                            <option value="secretarios_arbitrales" {{ request('grupo') == 'secretarios_arbitrales' ? 'selected' : '' }}>Secretarios Arbitrales</option>
                            <option value="apoyo" {{ request('grupo') == 'apoyo' ? 'selected' : '' }}>Personal de Apoyo</option>
                            <option value="administrativo" {{ request('grupo') == 'administrativo' ? 'selected' : '' }}>Soporte Administrativo</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Cargo</label>
                        <input type="text" name="cargo" class="form-control form-control-sm" placeholder="Ej: Decano" value="{{ request('cargo') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Nombres</label>
                        <input type="text" name="nombres" class="form-control form-control-sm" placeholder="Buscar ..." value="{{ request('nombres') }}">
                    </div>

                    <div class="col-md-2 text-end d-flex gap-2">
                        <a href="{{ route('organizacion-gestion.index') }}" class="btn btn-sm btn-outline-secondary w-50" title="Limpiar">
                            <i class="fas fa-eraser"></i>
                        </a>
                        <button type="submit" class="btn btn-sm btn-dark w-100">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Foto</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Grupo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($miembros as $item)
                        <tr>
                            <td class="ps-4">
                                @if($item->ruta_imagen)
                                    <img src="{{ asset('storage/' . $item->ruta_imagen) }}" 
                                         class="rounded-circle border" 
                                         style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#imageModal"
                                         data-full-src="{{ asset('storage/' . $item->ruta_imagen) }}"
                                         title="Clic para ampliar">
                                @else
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;"><i class="fas fa-user"></i></div>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $item->nombres }}</td>
                            <td>{{ $item->cargo }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->grupo }}</span></td>
                            
                            {{-- ESTADO INTERACTIVO --}}
                            <td class="text-center">
                                <form action="{{ route('organizacion-gestion.toggle', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="button" 
                                            class="badge border-0 {{ $item->activo ? 'bg-success' : 'bg-secondary' }}" 
                                            style="cursor: pointer;"
                                            onclick="confirmAction('{{ route('organizacion-gestion.toggle', $item->id) }}', 'toggle')">
                                        {{ $item->activo ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>

                            {{-- ACCIONES DROPDOWN --}}
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border shadow-sm" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('organizacion-gestion.edit', $item->id) }}">
                                                <i class="fas fa-edit text-warning me-2"></i> Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('organizacion-gestion.show', $item->id) }}">
                                                <i class="fas fa-eye text-info me-2"></i> Ver
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item py-2 text-danger" 
                                                    onclick="confirmAction('{{ route('organizacion-gestion.destroy', $item->id) }}', 'delete')">
                                                <i class="fas fa-trash me-2"></i> Eliminar
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No hay miembros registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end py-3">
            {{ $miembros->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center position-relative p-0">
         <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow-lg bg-white rounded-circle p-2" data-bs-dismiss="modal"></button>
         <img src="" id="modalImagePreview" class="img-fluid rounded shadow-lg" style="max-height: 80vh;">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white border-0" id="modalHeaderBg">
        <h5 class="modal-title fw-bold" id="modalTitle">Confirmar Acción</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="mb-3" id="modalIconContainer"></div>
        <h5 class="fw-bold mb-2" id="modalHeader">¿Estás seguro?</h5>
        <p class="text-muted mb-0" id="modalMessage">...</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        <form id="confirmForm" action="" method="POST">
            @csrf 
            <input type="hidden" name="_method" id="formMethod" value="">
            <button type="submit" class="btn px-4 fw-bold text-white" id="confirmBtn"></button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('autoDismissAlert');
        if (alert) setTimeout(() => new bootstrap.Alert(alert).close(), 3000);

        // Lógica Modal Imagen
        const modal = document.getElementById('imageModal');
        if(modal) {
            modal.addEventListener('show.bs.modal', function (event) {
                const src = event.relatedTarget.getAttribute('data-full-src');
                modal.querySelector('#modalImagePreview').src = src;
            });
        }
    });

    // Lógica Modal Confirmación
    function confirmAction(url, type) {
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const form = document.getElementById('confirmForm');
        const methodInput = document.getElementById('formMethod');
        
        const headerBg = document.getElementById('modalHeaderBg');
        const iconContainer = document.getElementById('modalIconContainer');
        const headerText = document.getElementById('modalHeader');
        const messageText = document.getElementById('modalMessage');
        const confirmBtn = document.getElementById('confirmBtn');

        form.action = url;

        if (type === 'delete') {
            methodInput.value = 'DELETE';
            headerBg.className = 'modal-header bg-danger text-white border-0';
            iconContainer.innerHTML = '<i class="fas fa-user-times fa-3x text-danger"></i>';
            headerText.innerText = '¿Eliminar Miembro?';
            messageText.innerText = 'El registro se eliminará permanentemente.';
            confirmBtn.className = 'btn btn-danger px-4 fw-bold';
            confirmBtn.innerText = 'Sí, eliminar';
        } 
        else if (type === 'toggle') {
            methodInput.value = 'PUT';
            headerBg.className = 'modal-header bg-primary text-white border-0';
            iconContainer.innerHTML = '<i class="fas fa-exchange-alt fa-3x text-primary"></i>';
            headerText.innerText = '¿Cambiar Estado?';
            messageText.innerText = 'El estado activo/inactivo del miembro cambiará.';
            confirmBtn.className = 'btn btn-primary px-4 fw-bold';
            confirmBtn.innerText = 'Sí, cambiar';
        }

        modal.show();
    }
</script>
@endsection