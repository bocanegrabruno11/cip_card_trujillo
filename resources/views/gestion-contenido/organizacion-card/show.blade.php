@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Detalle del Miembro</h2>
        <div>
            <a href="{{ route('organizacion-gestion.edit', $miembro->id) }}" class="btn btn-warning text-white btn-sm me-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('organizacion-gestion.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        
        {{-- COLUMNA IZQUIERDA: INFORMACIÓN --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold text-primary py-3">
                    <i class="fas fa-id-card-alt me-2"></i> Datos Personales y Profesionales
                </div>
                <div class="card-body p-4">
                    
                    {{-- 1. NOMBRE Y CÓDIGO --}}
                    <div class="mb-4 border-bottom pb-3">
                        <label class="small text-muted fw-bold text-uppercase">Nombres y Apellidos</label>
                        <h3 class="fw-bold text-dark mb-1">{{ $miembro->nombres }}</h3>
                        
                        {{-- CÓDIGO (Después del nombre) --}}
                        @if($miembro->codigo)
                            <span class="badge bg-dark text-white mt-1">
                                <i class="fas fa-hashtag me-1 text-warning"></i> CÓDIGO: {{ $miembro->codigo }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted border mt-1">Sin Código</span>
                        @endif
                    </div>

                    {{-- 2. CARGO Y ESPECIALIDAD --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Cargo</label>
                            <div class="fs-5">
                                @if($miembro->cargo)
                                    {{ $miembro->cargo }}
                                @else
                                    <em class="text-muted small">No especificado</em>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Especialidad / Profesión</label>
                            <div class="fs-5 text-secondary">
                                @if($miembro->especialidad)
                                    <i class="fas fa-graduation-cap me-1"></i> {{ $miembro->especialidad }}
                                @else
                                    <em class="text-muted small">No especificado</em>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. GRUPO Y ESTADO --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Grupo Asignado</label>
                            <div>
                                <span class="badge bg-info text-dark border border-info-subtle p-2">
                                    {{ ucfirst(str_replace('_', ' ', $miembro->grupo)) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Estado Actual</label>
                            <div>
                                @if($miembro->activo)
                                    <span class="badge bg-success p-2"><i class="fas fa-check-circle me-1"></i> Activo</span>
                                @else
                                    <span class="badge bg-secondary p-2"><i class="fas fa-ban me-1"></i> Inactivo</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 4. CONTACTO --}}
                    <div class="row mb-4 bg-light p-3 rounded mx-0">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold">Correo Electrónico</label>
                            <div class="text-dark fw-bold">
                                @if($miembro->email)
                                    <a href="mailto:{{ $miembro->email }}" class="text-decoration-none">{{ $miembro->email }}</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold">Teléfono / Anexo</label>
                            <div class="text-dark fw-bold">{{ $miembro->telefono ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- 5. ARCHIVO CV --}}
                    <div class="mb-2">
                        <label class="small text-muted fw-bold text-uppercase d-block mb-2">Documentación</label>
                        @if($miembro->ruta_cv)
                            <a href="{{ asset('storage/' . $miembro->ruta_cv) }}" target="_blank" class="btn btn-outline-danger w-100 py-3 text-start">
                                <i class="fas fa-file-pdf fa-2x float-start me-3"></i>
                                <span class="fw-bold d-block">Ver Hoja de Vida (CV)</span>
                                <span class="small">Clic para abrir el documento PDF</span>
                            </a>
                        @else
                            <div class="alert alert-light border text-center text-muted">
                                <i class="fas fa-folder-open me-2"></i> No se ha cargado hoja de vida.
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: FOTO --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold text-center py-3">
                    Foto de Perfil
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center bg-light">
                    
                    <div class="position-relative" style="width: 250px; height: 250px;">
                        @if($miembro->ruta_imagen)
                            <img src="{{ asset('storage/' . $miembro->ruta_imagen) }}" 
                                 class="rounded-circle border border-5 border-white shadow-sm" 
                                 style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#imageModal"
                                 title="Clic para ampliar">
                            
                            <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 shadow-sm text-primary">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        @else
                            <div class="rounded-circle bg-white border d-flex align-items-center justify-content-center shadow-sm" 
                                 style="width: 100%; height: 100%;">
                                <i class="fas fa-user fa-5x text-muted opacity-25"></i>
                            </div>
                            <p class="text-muted mt-3 mb-0">Sin foto asignada</p>
                        @endif
                    </div>

                </div>
              
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="text-end mb-2">
         <button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2 shadow" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="text-center">
        @if($miembro->ruta_imagen)
            <img src="{{ asset('storage/' . $miembro->ruta_imagen) }}" 
                 class="img-fluid rounded border border-4 border-white shadow-lg" 
                 style="max-height: 80vh; max-width: 100%; object-fit: contain;">
        @endif
      </div>
    </div>
  </div>
</div>

@endsection