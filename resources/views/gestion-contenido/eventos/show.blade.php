@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Detalle del Evento</h2>
            <span class="text-muted small">Visualizando información registrada</span>
        </div>
        <div>
            <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('eventos.edit', $evento->id) }}" class="btn btn-warning text-white btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold border-bottom pb-2 mb-3 text-primary">Información General</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="small text-muted fw-bold text-uppercase">Título del Evento</label>
                            <p class="fs-5 fw-bold text-dark">{{ $evento->titulo }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Fecha</label>
                            <p class="fs-5">
                                <i class="far fa-calendar-alt text-danger me-2"></i>
                                {{ \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Lugar</label>
                            <p class="fs-5">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                {{ $evento->lugar ?? 'No especificado' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="small text-muted fw-bold text-uppercase mb-2">Descripción del Evento</label>
                        <div class="p-3 bg-light rounded border">
                            {!! $evento->descripcion !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-success">
                    <i class="fas fa-images me-2"></i> Galería de Fotos
                </div>
                <div class="card-body">
                    @if($galeria->count() > 0)
                        <div class="row g-2">
                            @foreach($galeria as $img)
                                <div class="col-md-3 col-6">
                                    <div class="border rounded overflow-hidden position-relative group-hover" style="height: 120px;">
                                        <img src="{{ asset('storage/' . $img->ruta_imagen) }}" 
                                             class="w-100 h-100 object-fit-cover pointer-cursor"
                                             style="object-fit: cover; cursor: pointer; transition: transform 0.3s;"
                                             onmouseover="this.style.transform='scale(1.1)'"
                                             onmouseout="this.style.transform='scale(1)'"
                                             onclick="openShowModal('{{ asset('storage/' . $img->ruta_imagen) }}')">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="far fa-image fa-2x mb-2"></i>
                            <p class="m-0 small">No hay imágenes adicionales en la galería.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-danger">
                    <i class="fas fa-star me-2"></i> Imagen Principal
                </div>
                <div class="card-body p-0">
                    @if($imagenPrincipal)
                        <div class="bg-light d-flex align-items-center justify-content-center overflow-hidden" style="height: 300px;">
                            <img src="{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}" 
                                 class="w-100 h-100" 
                                 style="object-fit: contain; cursor: pointer;"
                                 onclick="openShowModal('{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}')">
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-ban fa-3x mb-3"></i>
                            <p>Sin imagen principal asignada</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-center small text-muted">
                    Clic en la imagen para ampliar
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="small text-muted fw-bold">Fecha de Registro</label>
                    <p class="mb-2">{{ $evento->created_at->format('d/m/Y H:i A') }}</p>

                    <label class="small text-muted fw-bold">Última Actualización</label>
                    <p class="mb-0">{{ $evento->updated_at->format('d/m/Y H:i A') }}</p>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="showImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
        <div class="text-end">
            <button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2 shadow" data-bs-dismiss="modal"></button>
        </div>
        <div class="text-center mt-2">
            <img src="" id="modalShowImg" class="img-fluid rounded shadow-lg" style="max-height: 85vh;">
        </div>
    </div>
  </div>
</div>

<script>
    // Función simple para abrir el modal
    function openShowModal(src) {
        const modalEl = document.getElementById('showImageModal');
        const imgEl = document.getElementById('modalShowImg');
        const modal = new bootstrap.Modal(modalEl);
        
        imgEl.src = src;
        modal.show();
    }
</script>
@endsection