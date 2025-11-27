@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">

    {{-- 1. LÓGICA DE SEPARACIÓN DE IMÁGENES --}}
    @php
        // Buscamos la imagen marcada como 'principal' dentro de la relación detalles
        $imagenPrincipal = $publicacion->detalles->where('grupo', 'principal')->first();
        
        // Buscamos el resto de imágenes marcadas como 'galeria'
        $galeria = $publicacion->detalles->where('grupo', 'galeria');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Detalle de la Publicación</h2>
        <div>
            <a href="{{ route('publicaciones.edit', $publicacion->id) }}" class="btn btn-warning text-white btn-sm me-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('publicaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-primary">1. Información General</div>
                <div class="card-body p-4">
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label class="small text-muted fw-bold text-uppercase">Título</label>
                            <p class="fs-5 fw-bold text-dark mb-0">{{ $publicacion->titulo }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold text-uppercase">Sección</label>
                            <div>
                                @if($publicacion->seccion == 'presentacion')
                                    <span class="badge bg-info text-dark">Presentación</span>
                                @elseif($publicacion->seccion == 'inicio_popup')
                                    <span class="badge bg-warning text-dark">Pop Up Inicio</span>
                                @elseif($publicacion->seccion == 'inicio_slider')
                                    <span class="badge bg-primary">Slider Principal</span>
                                @else
                                    <span class="badge bg-secondary">{{ $publicacion->seccion }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted fw-bold text-uppercase">Descripción</label>
                        <div class="p-3 bg-light rounded border text-secondary">
                            @if($publicacion->descripcion)
                                {!! nl2br(e($publicacion->descripcion)) !!}
                            @else
                                <em class="text-muted">Sin descripción registrada.</em>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold">Fecha de Creación</label>
                            <p>{{ $publicacion->created_at->format('d/m/Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold">Última Actualización</label>
                            <p>{{ $publicacion->updated_at->format('d/m/Y H:i A') }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-success">3. Galería de Imágenes</div>
                <div class="card-body p-4">
                    @if($galeria->count() > 0)
                        <div class="row g-3">
                            @foreach($galeria as $img)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 border shadow-sm">
                                        <div class="ratio ratio-4x3 bg-light">
                                            <img src="{{ asset('storage/' . $img->ruta_imagen) }}" 
                                                 class="object-fit-contain p-2" 
                                                 alt="Imagen galería"
                                                 style="cursor: pointer;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal"
                                                 title="Clic para ampliar">
                                        </div>
                                        <div class="card-body p-2 small">
                                            @if($img->descripcion)
                                                <p class="mb-1 fw-bold text-truncate">{{ $img->descripcion }}</p>
                                            @endif
                                            @if($img->url_enlace)
                                                <a href="{{ $img->url_enlace }}" target="_blank" class="text-primary text-decoration-none">
                                                    <i class="fas fa-link"></i> Ver enlace
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="far fa-images fa-2x mb-2"></i>
                            <p class="mb-0">No hay imágenes en la galería.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-danger">2. Imagen Principal</div>
                <div class="card-body text-center">
                    
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center mb-3 position-relative" style="height: 250px; overflow: hidden;">
                        @if($imagenPrincipal)
                            <img src="{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}" 
                                 class="img-fluid" 
                                 style="max-height: 100%; cursor: pointer;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#imageModal"
                                 title="Clic para ampliar">
                        @else
                            <div class="text-muted d-flex flex-column align-items-center">
                                <i class="fas fa-image fa-3x mb-2 opacity-50"></i>
                                <span>Sin imagen principal</span>
                            </div>
                        @endif
                    </div>

                    {{-- El enlace también viene del detalle principal --}}
                    @if($imagenPrincipal && $imagenPrincipal->url_enlace)
                        <div class="d-grid">
                            <a href="{{ $imagenPrincipal->url_enlace }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-2"></i> Visitar URL Principal
                            </a>
                        </div>
                    @else
                        <p class="text-muted small mb-0">Sin enlace URL asignado.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-header border-0 p-0 justify-content-end mb-2">
         <button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2 shadow" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img src="" id="modalImagePreview" class="img-fluid rounded shadow-lg" style="max-height: 85vh; object-fit: contain;">
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImagePreview');

    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', function (event) {
            const triggerElement = event.relatedTarget;
            const src = triggerElement.src;
            modalImage.src = src;
        });
    }
});
</script>
@endsection