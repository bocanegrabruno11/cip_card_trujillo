@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Gestión de Publicaciones</h2>
        <a href="{{ route('publicaciones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Publicación
        </a>
    </div>

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

                    <div class="col-md-3 text-end">
                        <a href="{{ route('publicaciones.index') }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                        <button type="submit" class="btn btn-sm btn-dark px-3">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Imagen Principal</th>
                        <th>Título</th>
                        <th>Sección</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($publicaciones as $pub)
                    <tr>
                        <td>
                            @php
                                // Buscamos en la relación 'detalles' la imagen marcada como 'principal'
                                $imgPrincipal = $pub->detalles->where('grupo', 'principal')->first();
                            @endphp

                            @if($imgPrincipal)
                                <img src="{{ asset('storage/' . $imgPrincipal->ruta_imagen) }}" 
                                     alt="Img" 
                                     class="rounded border img-clickable" 
                                     style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal"
                                     data-full-src="{{ asset('storage/' . $imgPrincipal->ruta_imagen) }}"
                                     title="Clic para ampliar">
                            @else
                                <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        
                        <td>{{ $pub->titulo }}</td>
                        
                        
                        <td>
                            <span class="badge bg-secondary">
                                {{ strtoupper(str_replace('_', ' ', $pub->seccion)) }}
                            </span>
                        </td>
                        <td>{{  Carbon\Carbon::parse($pub->fecha)->format('d/m/Y') }}</td>

                        <td>
                            <span class="badge {{ $pub->activo ? 'bg-success' : 'bg-danger' }}">
                                {{ $pub->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        
                        <td>
                            <div class="d-flex gap-1">
                                
                                {{-- BOTÓN TOGGLE ESTADO --}}
                                <form action="{{ route('publicaciones.toggle', $pub->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    @if($pub->activo)
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('¿Estás seguro de ocultar esta publicación?')" title="Click para Ocultar">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('¿Estás seguro de mostrar esta publicación?')" title="Click para Mostrar">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    @endif
                                </form>

                                <a href="{{ route('publicaciones.edit', $pub->id) }}" class="btn btn-sm btn-warning text-white" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('publicaciones.destroy', $pub->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar todo este registro?')" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            
            <div class="mt-3">
                {{ $publicaciones->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      {{-- Botón de cierre blanco y flotante --}}
      <div class="modal-header border-0 p-0 justify-content-end mb-2">
         <button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-0">
        {{-- La imagen src se llenará con JS --}}
        <img src="" id="modalImagePreview" class="img-fluid rounded shadow-lg" style="max-height: 85vh; object-fit: contain;">
      </div>
    </div>
  </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Cerrar alertas automáticamente (tu código anterior)
        const alerts = document.querySelectorAll('#autoDismissAlert');
        if (alerts.length > 0) {
            setTimeout(() => {
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000);
        }

        // 2. Lógica para el Modal de Imagen Ampliada
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', event => {
                // Elemento que disparó el modal (la imagen pequeña)
                const triggerImg = event.relatedTarget;
                // Extraer la URL completa del atributo data-full-src
                const fullSrc = triggerImg.getAttribute('data-full-src');
                // Actualizar la fuente de la imagen dentro del modal
                const modalImage = imageModal.querySelector('#modalImagePreview');
                modalImage.src = fullSrc;
            });
            
            // Limpiar la imagen al cerrar para evitar parpadeos en la próxima apertura
            imageModal.addEventListener('hidden.bs.modal', event => {
                 const modalImage = imageModal.querySelector('#modalImagePreview');
                 modalImage.src = '';
            });
        }
    });
</script>
@endsection