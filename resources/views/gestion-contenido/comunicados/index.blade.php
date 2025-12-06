@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Comunicados</h2>
        <a href="{{ route('comunicados.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus"></i> Nuevo Comunicado
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
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
            <form action="{{ route('comunicados.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Visibles</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Ocultos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Mes</label>
                        <select name="mes" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Año</label>
                        <select name="anio" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @for($y = date('Y') + 1; $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 text-end d-flex gap-2">
                        <a href="{{ route('comunicados.index') }}" class="btn btn-sm btn-outline-secondary w-50" title="Limpiar">
                            <i class="fas fa-eraser"></i>
                        </a>
                        <button type="submit" class="btn btn-sm btn-dark w-100">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Imagen</th>
                        <th>Título</th>
                        <th>Fecha Publicación</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comunicados as $item)
                    <tr>
                        <td class="ps-4">
                            <img src="{{ asset('storage/' . $item->ruta_imagen) }}" 
                                 class="rounded border shadow-sm" 
                                 style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                 data-bs-toggle="modal" data-bs-target="#imageModal"
                                 data-full-src="{{ asset('storage/' . $item->ruta_imagen) }}">
                        </td>
                        <td class="fw-bold">{{ $item->titulo }}</td>
                        <td class="text-muted small">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        
                        {{-- ESTADO INTERACTIVO --}}
                        <td class="text-center">
                            <form action="{{ route('comunicados.toggle', $item->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" 
                                        class="badge border-0 {{ $item->activo ? 'bg-success' : 'bg-secondary' }}" 
                                        style="cursor: pointer;" 
                                        title="Clic para cambiar visibilidad">
                                    {{ $item->activo ? 'Visible' : 'Oculto' }}
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
                                        <a class="dropdown-item py-2" href="{{ route('comunicados.edit', $item->id) }}">
                                            <i class="fas fa-edit text-warning me-2"></i> Editar
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('comunicados.show', $item->id) }}">
                                            <i class="fas fa-eye text-info me-2"></i> Ver
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        {{-- Usamos el mismo modal de confirmación genérico si ya lo tienes implementado, o el confirm simple --}}
                                        <button class="dropdown-item py-2 text-danger" 
                                                onclick="confirmAction('{{ route('comunicados.destroy', $item->id) }}', 'delete')">
                                            <i class="fas fa-trash me-2"></i> Eliminar
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No hay comunicados registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $comunicados->links('pagination::bootstrap-4') }}
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
      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold">Confirmar Eliminación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <h5 class="fw-bold mb-2">¿Estás seguro?</h5>
        <p class="text-muted mb-0">El comunicado será eliminado permanentemente.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        <form id="confirmForm" action="" method="POST">
            @csrf <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger px-4 fw-bold">Sí, eliminar</button>
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

        // Modal Imagen
        const imageModal = document.getElementById('imageModal');
        if(imageModal) {
            imageModal.addEventListener('show.bs.modal', e => {
                const src = e.relatedTarget.getAttribute('data-full-src');
                imageModal.querySelector('#modalImagePreview').src = src;
            });
        }
    });

    // Función para modal de confirmación
    function confirmAction(url, type) {
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const form = document.getElementById('confirmForm');
        form.action = url;
        modal.show();
    }
</script>
@endsection