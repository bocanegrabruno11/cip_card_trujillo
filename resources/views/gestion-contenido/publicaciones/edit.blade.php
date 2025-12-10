@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Atención!</strong> Revisa los errores.
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Editar Publicación</h2>
        <a href="{{ route('publicaciones.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ route('publicaciones.update', $publicacion->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold text-primary">1. Datos Generales</div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" value="{{ $publicacion->titulo }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Sección</label>
                        <select name="seccion" class="form-select">
                            <option value="presentacion" {{ $publicacion->seccion == 'presentacion' ? 'selected' : '' }}>Presentación</option>
                            <option value="inicio_popup" {{ $publicacion->seccion == 'inicio_popup' ? 'selected' : '' }}>Pop Up Inicio</option>
                            <option value="inicio_slider" {{ $publicacion->seccion == 'inicio_slider' ? 'selected' : '' }}>Slider Principal</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold text-dark">2. Contenido y Portada</div>
            <div class="card-body p-4">
                <div class="row">
                    
                    <div class="col-md-7 border-end">
                        <label class="form-label fw-bold">Descripción General</label>
                        <textarea name="descripcion" class="form-control" rows="8">{{ $publicacion->descripcion }}</textarea>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Imagen Principal</label>
                        <div class="input-group mb-2">
                            <input type="file" name="imagen_principal" class="form-control" id="inputMainImg" accept=".jpg,.jpeg,.png">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small text-muted">Enlace URL (Opcional)</label>
                            <input type="url" name="url_enlace_principal" 
                                class="form-control form-control-sm" 
                                placeholder="https://..."
                                value="{{ $imagenPrincipal->url_enlace ?? '' }}">
                        </div>

                        <div class="bg-light border rounded d-flex align-items-center justify-content-center overflow-hidden" style="height: 200px; position: relative;">
                            @if($imagenPrincipal)
                                <img id="previewMain" src="{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}" 
                                     style="width: 100%; height: 100%; object-fit: contain; cursor: pointer;"
                                     data-bs-toggle="modal" data-bs-target="#imageModal" title="Clic para ampliar">
                            @else
                                <img id="previewMain" src="" 
                                     style="width: 100%; height: 100%; object-fit: contain; display: none; cursor: pointer;"
                                     data-bs-toggle="modal" data-bs-target="#imageModal">
                                <span id="textMain" class="text-muted small text-center">Sin Imagen Principal</span>
                            @endif
                        </div>
                        <div class="form-text mt-2 text-end">Sube una nueva para reemplazar la actual.</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold text-secondary">3. Galería Actual (Editar y Gestionar)</div>
            <div class="card-body p-4">
                @if($galeria->count() > 0)
                    <div class="row g-3">
                        @foreach($galeria as $img)
                        <div class="col-md-6 col-lg-4"> <div class="border rounded p-2 h-100 shadow-sm bg-light">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <img src="{{ asset('storage/' . $img->ruta_imagen) }}" 
                                             class="img-fluid rounded w-100 h-100" 
                                             style="object-fit: cover; cursor: pointer; min-height: 100px;"
                                             data-bs-toggle="modal" data-bs-target="#imageModal" title="Ver">
                                    </div>
                                    
                                    <div class="col-8">
                                        <div class="mb-2">
                                            <input type="url" 
                                                   name="galeria_existente[{{ $img->id }}][url_enlace]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Enlace URL"
                                                   value="{{ $img->url_enlace }}">
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="galeria_existente[{{ $img->id }}][descripcion]" 
                                                      class="form-control form-control-sm" 
                                                      rows="2" 
                                                      placeholder="Descripción...">{{ $img->descripcion }}</textarea>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input border-danger" type="checkbox" name="eliminar_detalles[]" value="{{ $img->id }}" id="del_{{ $img->id }}">
                                            <label class="form-check-label text-danger fw-bold small w-100 cursor-pointer" for="del_{{ $img->id }}">
                                                Eliminar
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="alert alert-warning small mt-3 mb-0">
                        <i class="fas fa-info-circle"></i> Puedes editar los textos o marcar "Eliminar" para borrar. Guarda cambios al final.
                    </div>
                @else
                    <p class="text-muted text-center py-3">No hay imágenes en la galería.</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-white fw-bold text-success d-flex justify-content-between align-items-center">
                <span>4. Agregar Nuevas Imágenes</span>
                <button type="button" class="btn btn-sm btn-success" id="btnAddGallery">
                    <i class="fas fa-plus-circle me-1"></i> Agregar
                </button>
            </div>
            <div class="card-body p-4 bg-light" id="galleryContainer">
                <div class="text-center text-muted py-2" id="emptyGalleryMsg">
                    <small>Las imágenes que agregues aquí se sumarán a la galería existente.</small>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5 pb-5">
            <button type="submit" id="btnSubmit" class="btn btn-warning text-white px-5 btn-lg shadow">
                <i class="fas fa-sync-alt me-2"></i> ACTUALIZAR TODO
            </button>
        </div>
    </form>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-header border-0 p-0 justify-content-end mb-2">
         <button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img src="" id="modalImagePreview" class="img-fluid rounded shadow-lg" style="max-height: 85vh; object-fit: contain;">
      </div>
    </div>
  </div>
</div>

<template id="galleryItemTemplate">
    <div class="gallery-item card mb-3 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h6 class="fw-bold text-secondary">Nueva Imagen</h6>
                <button type="button" class="btn-close btn-remove-item" aria-label="Close"></button>
            </div>
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Archivo <span class="text-danger">*</span></label>
                    <input type="file" name="galeria_nueva[INDEX][imagen]" class="form-control form-control-sm" accept=".jpg,.jpeg,.png" required>
                    
                    <div class="mt-2 bg-white border" style="height: 100px; display:flex; align-items:center; justify-content:center;">
                        <img src="" class="img-preview d-none" 
                             style="max-height: 100%; max-width: 100%; cursor: pointer;"
                             data-bs-toggle="modal" data-bs-target="#imageModal" title="Clic para ampliar">
                        <span class="text-muted x-small preview-placeholder">Vista previa</span>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Enlace (Opcional)</label>
                        <input type="url" name="galeria_nueva[INDEX][url_enlace]" class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Descripción Corta</label>
                        <textarea name="galeria_nueva[INDEX][descripcion]" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Modal
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImagePreview');
    document.body.addEventListener('click', function(e) {
        if (e.target.matches('img[data-bs-toggle="modal"]')) {
            modalImage.src = e.target.src;
        }
    });

    // Preview Main
    const inputMain = document.getElementById('inputMainImg');
    const previewMain = document.getElementById('previewMain');
    inputMain.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewMain.src = e.target.result;
                previewMain.style.display = 'block';
                const t = document.getElementById('textMain');
                if(t) t.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });

    // Galería Dinámica
    let galleryIndex = 0;
    const container = document.getElementById('galleryContainer');
    const template = document.getElementById('galleryItemTemplate');
    const emptyMsg = document.getElementById('emptyGalleryMsg');

    document.getElementById('btnAddGallery').addEventListener('click', function() {
        if(emptyMsg) emptyMsg.style.display = 'none';

        const clone = template.content.cloneNode(true);
        clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
            el.name = el.name.replace('INDEX', galleryIndex);
        });

        const fileInput = clone.querySelector('input[type="file"]');
        const imgPreview = clone.querySelector('.img-preview');
        const placeholder = clone.querySelector('.preview-placeholder');
        
        fileInput.addEventListener('change', function() {
            if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    imgPreview.src = e.target.result;
                    imgPreview.classList.remove('d-none');
                    if(placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        clone.querySelector('.btn-remove-item').addEventListener('click', function() {
            this.closest('.gallery-item').remove();
        });

        container.appendChild(clone);
        galleryIndex++;
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const btn = document.getElementById('btnSubmit');

        if (form && btn) {
            form.addEventListener('submit', function(e) {
                // 1. Verificar validez del formulario (HTML5 validation)
                // Si el navegador detecta campos vacíos requeridos, no bloqueamos el botón
                if (!form.checkValidity()) {
                    return;
                }

                // 2. Congelar el ancho del botón para que no se deforme al cambiar el texto
                const width = btn.offsetWidth;
                btn.style.width = width + 'px';

                // 3. Deshabilitar y mostrar animación
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
            });
        }
    });
</script>
@endsection