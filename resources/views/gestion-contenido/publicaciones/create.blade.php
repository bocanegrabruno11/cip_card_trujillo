@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Atención!</strong> Revisa los siguientes errores:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Nuevo Registro de Contenido</h2>
        <a href="{{ route('publicaciones.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ route('publicaciones.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold text-primary">1. Datos Generales</div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Título de la Publicación <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" required value="{{ old('titulo') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Sección Destino <span class="text-danger">*</span></label>
                        <select name="seccion" class="form-select" required>
                            <option value="" selected disabled>Seleccione...</option>
                            <option value="presentacion" {{ old('seccion') == 'presentacion' ? 'selected' : '' }}>Presentación</option>
                            <option value="inicio_popup" {{ old('seccion') == 'inicio_popup' ? 'selected' : '' }}>Pop Up Inicio</option>
                            <option value="inicio_slider" {{ old('seccion') == 'inicio_slider' ? 'selected' : '' }}>Slider Principal</option>
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
                        <label class="form-label fw-bold">Descripción General (Opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="8" placeholder="Escribe aquí una descripción simple...">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Imagen Principal (Primera a mostrar)</label>
                        <div class="input-group mb-3">
                            <input type="file" name="imagen_principal" class="form-control" id="inputMainImg" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                             <label class="form-label small text-muted">Enlace URL (Opcional)</label>
                             <input type="url" name="url_enlace_principal" class="form-control form-control-sm" placeholder="https://...">
                        </div>
                        
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center position-relative" style="height: 200px; overflow: hidden;">
                            <img id="previewMain" src="" 
                                 style="width: 100%; height: 100%; object-fit: contain; display: none; cursor: pointer;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#imageModal"
                                 title="Clic para ampliar">
                            <span id="textMain" class="text-muted small text-center">
                                <i class="fas fa-image fa-3x mb-2"></i><br>Vista Previa
                            </span>
                        </div>
                        <div class="form-text mt-2">Formatos: JPG, PNG. Máx 10MB.</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-white fw-bold text-success d-flex justify-content-between align-items-center">
                <span>3. Galería de Imágenes (Opcional - Múltiples)</span>
                <button type="button" class="btn btn-sm btn-success" id="btnAddGallery">
                    <i class="fas fa-plus-circle me-1"></i> Agregar Imagen
                </button>
            </div>
            <div class="card-body p-4 bg-light" id="galleryContainer">
                <div class="text-center text-muted py-3" id="emptyGalleryMsg">
                    <small>No has agregado imágenes a la galería todavía.</small>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5 pb-5">
            <button type="submit" class="btn btn-primary px-5 btn-lg shadow">
                <i class="fas fa-save me-2"></i> GUARDAR REGISTRO
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
                <h6 class="fw-bold text-secondary">Imagen de Galería</h6>
                <button type="button" class="btn-close btn-remove-item" aria-label="Close"></button>
            </div>
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Archivo de Imagen <span class="text-danger">*</span></label>
                    <input type="file" name="galeria[INDEX][imagen]" class="form-control form-control-sm" accept="image/*" required>
                    
                    <div class="mt-2" style="height: 100px; background: #f8f9fa; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img src="" class="img-preview d-none" 
                             style="max-height: 100%; max-width: 100%; cursor: pointer;"
                             data-bs-toggle="modal" 
                             data-bs-target="#imageModal"
                             title="Clic para ampliar">
                        <span class="text-muted x-small preview-placeholder">Sin imagen</span>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Enlace URL (Opcional)</label>
                        <input type="url" name="galeria[INDEX][url_enlace]" class="form-control form-control-sm" placeholder="https://...">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Descripción Corta (Opcional)</label>
                        <textarea name="galeria[INDEX][descripcion]" class="form-control form-control-sm" rows="2" placeholder="Detalle de esta imagen..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // === 1. FUNCIÓN REUTILIZABLE PARA ABRIR MODAL ===
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImagePreview');

    // Delegación de eventos para manejar clics en imágenes dinámicas (y estáticas)
    document.body.addEventListener('click', function(e) {
        if (e.target.matches('img[data-bs-toggle="modal"]')) {
            modalImage.src = e.target.src;
        }
    });

    // === 2. PREVIEW IMAGEN PRINCIPAL ===
    const inputMain = document.getElementById('inputMainImg');
    const previewMain = document.getElementById('previewMain');
    const textMain = document.getElementById('textMain');

    inputMain.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewMain.src = e.target.result;
                previewMain.style.display = 'block';
                textMain.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            previewMain.style.display = 'none';
            textMain.style.display = 'block';
        }
    });

    // === 3. GALERÍA DINÁMICA ===
    let galleryIndex = 0;
    const container = document.getElementById('galleryContainer');
    const template = document.getElementById('galleryItemTemplate');
    const emptyMsg = document.getElementById('emptyGalleryMsg');

    document.getElementById('btnAddGallery').addEventListener('click', function() {
        if(emptyMsg) emptyMsg.style.display = 'none';

        const clone = template.content.cloneNode(true);
        
        // Actualizar índices
        clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
            el.name = el.name.replace('INDEX', galleryIndex);
        });

        // Configurar Preview
        const fileInput = clone.querySelector('input[type="file"]');
        const imgPreview = clone.querySelector('.img-preview');
        const placeholder = clone.querySelector('.preview-placeholder');
        
        fileInput.addEventListener('change', function() {
            if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    imgPreview.src = e.target.result;
                    imgPreview.classList.remove('d-none');
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Eliminar ítem
        clone.querySelector('.btn-remove-item').addEventListener('click', function() {
            this.closest('.gallery-item').remove();
            if(container.children.length <= 1) { 
                if(emptyMsg) emptyMsg.style.display = 'block';
            }
        });

        container.appendChild(clone);
        galleryIndex++;
    });
});
</script>
@endsection