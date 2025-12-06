@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Detalle del Comunicado</h2>
        <div>
            <a href="{{ route('comunicados.edit', $comunicado->id) }}" class="btn btn-warning text-white btn-sm me-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('comunicados.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header bg-white fw-bold text-primary">Información General</div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="small text-muted fw-bold text-uppercase">Título</label>
                        <p class="fs-5 fw-bold text-dark mb-0">{{ $comunicado->titulo }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted fw-bold text-uppercase">Enlace URL</label>
                        @if($comunicado->url_enlace)
                            <div>
                                <a href="{{ $comunicado->url_enlace }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-external-link-alt me-1"></i> {{ $comunicado->url_enlace }}
                                </a>
                            </div>
                        @else
                            <p class="text-muted fst-italic">Sin enlace asignado.</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted fw-bold text-uppercase">Descripción</label>
                        <div class="p-3 bg-light rounded border text-secondary">
                            @if($comunicado->descripcion)
                                {!! nl2br(e($comunicado->descripcion)) !!}
                            @else
                                <em class="text-muted">Sin descripción registrada.</em>
                            @endif
                        </div>
                    </div>

                    <div class="row border-top pt-3 mt-auto">
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Estado</label>
                            <div>
                                @if($comunicado->activo)
                                    <span class="badge bg-success">Activo / Visible</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo / Oculto</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-muted fw-bold">Fecha Registro</label>
                            <p class="mb-0">{{ $comunicado->created_at->format('d/m/Y h:i A') }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header bg-white fw-bold text-danger">Imagen del Comunicado</div>
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center bg-light">
                    
                    @if($comunicado->ruta_imagen)
                        <div class="img-thumbnail border-0 bg-transparent p-2" style="cursor: pointer;" 
                             data-bs-toggle="modal" data-bs-target="#imageModal">
                            <img src="{{ asset('storage/' . $comunicado->ruta_imagen) }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 400px; object-fit: contain;"
                                 alt="{{ $comunicado->titulo }}">
                            <div class="mt-2 text-muted small"><i class="fas fa-search-plus"></i> Clic para ampliar</div>
                        </div>
                    @else
                        <div class="text-muted py-5">
                            <i class="fas fa-image fa-4x mb-3 opacity-25"></i>
                            <p>Sin imagen disponible</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl"> {{-- Modal Extra Grande (XL) para mejor vista --}}
    <div class="modal-content bg-transparent border-0">
      <div class="modal-header border-0 p-0 justify-content-end mb-2">
         <button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2 shadow" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-0">
        @if($comunicado->ruta_imagen)
            <img src="{{ asset('storage/' . $comunicado->ruta_imagen) }}" 
                 class="img-fluid rounded shadow-lg" 
                 style="max-height: 90vh; object-fit: contain;">
        @endif
      </div>
    </div>
  </div>
</div>

@endsection