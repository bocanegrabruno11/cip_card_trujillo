@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Eventos</h2>
        <a href="{{ route('eventos.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus"></i> Nuevo Evento
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="autoDismissAlert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('eventos.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Mes del Evento</label>
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
                    <div class="col-md-3 text-end">
                        <a href="{{ route('eventos.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                        <button type="submit" class="btn btn-sm btn-dark px-3">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Imagen</th>
                        <th>Título</th>
                        <th>Fecha Creación</th>
                        <th>Fecha Evento</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventos as $ev)
                    <tr>
                        <td class="ps-4">
                            @php $main = $ev->detalles->where('tipo', 'principal')->first(); @endphp
                            @if($main)
                                <img src="{{ asset('storage/' . $main->ruta_imagen) }}" 
                                     class="rounded border" 
                                     style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal" 
                                     data-full-src="{{ asset('storage/' . $main->ruta_imagen) }}">
                            @else
                                <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $ev->titulo }}</div>
                            <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $ev->lugar ?? 'Virtual' }}</small>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ev->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ \Carbon\Carbon::parse($ev->fecha_evento)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($ev->activo)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('eventos.toggle', $ev->id) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-icon {{ $ev->activo ? 'btn-outline-success' : 'btn-outline-secondary' }}" title="Cambiar Estado">
                                    <i class="fas {{ $ev->activo ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                </button>
                            </form>
                            <a href="{{ route('eventos.edit', $ev->id) }}" class="btn btn-sm btn-warning text-white" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('eventos.destroy', $ev->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este evento?')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No hay eventos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        @if($eventos->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $eventos->links('pagination::bootstrap-4') }}
            </div>
        @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. CERRAR ALERTAS AUTOMÁTICAMENTE
        const alerts = document.querySelectorAll('#autoDismissAlert');
        if (alerts.length > 0) {
            setTimeout(() => {
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000);
        }

        // 2. MODAL IMAGEN
        const modal = document.getElementById('imageModal');
        if(modal) {
            modal.addEventListener('show.bs.modal', e => {
                const src = e.relatedTarget.getAttribute('data-full-src');
                modal.querySelector('#modalImagePreview').src = src;
            });
        }
    });
</script>

<style>
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>
@endsection