@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Gestión de Publicaciones</h2>
        <a href="{{ route('publicaciones.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus"></i> Nueva Publicación
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold py-3">
            <i class="fas fa-filter me-1 text-secondary"></i> Filtros de Búsqueda
        </div>
        <div class="card-body bg-light">
            <form action="{{ route('publicaciones.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Sección</label>
                        <select name="seccion" class="form-select form-select-sm">
                            <option value="">Todas las secciones</option>
                            <option value="presentacion" {{ request('seccion') == 'presentacion' ? 'selected' : '' }}>Presentación</option>
                            <option value="inicio_popup" {{ request('seccion') == 'inicio_popup' ? 'selected' : '' }}>Pop Up Inicio</option>
                            <option value="inicio_slider" {{ request('seccion') == 'inicio_slider' ? 'selected' : '' }}>Slider Principal</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Mes</label>
                        <select name="mes" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Año</label>
                        <select name="anio" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 text-end d-flex gap-2">
                        <a href="{{ route('publicaciones.index') }}" class="btn btn-sm btn-outline-secondary w-50" title="Limpiar">
                            <i class="fas fa-eraser"></i>
                        </a>
                        <button type="submit" class="btn btn-sm btn-dark w-100">
                            <i class="fas fa-search"></i> Buscar
                        </button>
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
                        <th class="ps-4">Imagen Principal</th>
                        <th>Título</th>
                        <th>Sección</th>
                        <th>Fecha</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($publicaciones as $pub)
                    <tr>
                        <td class="ps-4">
                            @php
                                $imgPrincipal = $pub->detalles->where('grupo', 'principal')->first();
                            @endphp

                            @if($imgPrincipal)
                                <img src="{{ asset('storage/' . $imgPrincipal->ruta_imagen) }}" 
                                     alt="Img" 
                                     class="rounded border shadow-sm" 
                                     style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal"
                                     data-full-src="{{ asset('storage/' . $imgPrincipal->ruta_imagen) }}">
                            @else
                                <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        
                        <td class="fw-bold">{{ $pub->titulo }}</td>
                        
                        <td>
                            <span class="badge bg-secondary text-uppercase" style="font-size: 0.75rem;">
                                {{ str_replace('_', ' ', $pub->seccion) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $pub->created_at->format('d/m/Y') }}</td>

                        {{-- ESTADO INTERACTIVO --}}
                        <td class="text-center">
                            <button type="button" 
                                    class="badge border-0 {{ $pub->activo ? 'bg-success' : 'bg-secondary' }}" 
                                    style="cursor: pointer;"
                                    onclick="confirmAction('{{ route('publicaciones.toggle', $pub->id) }}', 'toggle')">
                                {{ $pub->activo ? 'Publicado' : 'Oculto' }}
                            </button>
                        </td>
                        
                        {{-- DROPDOWN DE ACCIONES --}}
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm border shadow-sm" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('publicaciones.show', $pub->id) }}">
                                            <i class="fas fa-eye text-primary me-2"></i> Ver Detalle
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('publicaciones.edit', $pub->id) }}">
                                            <i class="fas fa-edit text-warning me-2"></i> Editar
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item py-2 text-danger" 
                                                onclick="confirmAction('{{ route('publicaciones.destroy', $pub->id) }}', 'delete')">
                                            <i class="fas fa-trash me-2"></i> Eliminar
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            
            @if($publicaciones->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $publicaciones->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-header border-0 p-0 justify-content-end mb-2">
         <button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2 shadow" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img src="" id="modalImagePreview" class="img-fluid rounded shadow-lg" style="max-height: 85vh;">
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
        // Auto-cerrar alertas
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            setTimeout(() => new bootstrap.Alert(alert).close(), 3000);
        });

        // Lógica Modal Imagen
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', e => {
                const src = e.relatedTarget.getAttribute('data-full-src');
                imageModal.querySelector('#modalImagePreview').src = src;
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
            iconContainer.innerHTML = '<i class="fas fa-trash-alt fa-3x text-danger"></i>';
            headerText.innerText = '¿Eliminar Publicación?';
            messageText.innerText = 'El registro y sus imágenes se borrarán permanentemente.';
            confirmBtn.className = 'btn btn-danger px-4 fw-bold';
            confirmBtn.innerText = 'Sí, eliminar';
        } 
        else if (type === 'toggle') {
            methodInput.value = 'PUT';
            headerBg.className = 'modal-header bg-primary text-white border-0';
            iconContainer.innerHTML = '<i class="fas fa-eye fa-3x text-primary"></i>';
            headerText.innerText = '¿Cambiar Visibilidad?';
            messageText.innerText = 'El estado público del contenido cambiará.';
            confirmBtn.className = 'btn btn-primary px-4 fw-bold';
            confirmBtn.innerText = 'Sí, cambiar';
        }

        modal.show();
    }
</script>
@endsection