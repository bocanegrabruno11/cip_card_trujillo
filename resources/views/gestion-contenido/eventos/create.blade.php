@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>
    @endif

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Nuevo Evento</h2>
        <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <form action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Título del Evento <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" class="form-control" required value="{{ old('titulo') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha del Evento <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_evento" class="form-control" required value="{{ old('fecha_evento') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lugar / Dirección (Opcional)</label>
                            <input type="text" name="lugar" class="form-control" placeholder="Ej: Auditorio Principal" value="{{ old('lugar') }}">
                        </div>
                       <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" id="editor" class="form-control">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold text-success d-flex justify-content-between">
                        <span>Galería de Fotos (Opcional)</span>
                        <button type="button" class="btn btn-sm btn-success" id="btnAddGallery"><i class="fas fa-plus"></i> Agregar</button>
                    </div>
                    <div class="card-body" id="galleryContainer"></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold text-danger">Imagen Principal <span class="text-danger">*</span></div>
                    <div class="card-body text-center">
                        <input type="file" name="imagen_principal" class="form-control mb-3" id="inputMain" accept=".jpg,.jpeg,.png" required>
                        
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center overflow-hidden" style="height: 200px;">
                            <img id="previewMain" src="" 
                                 style="width: 100%; height: 100%; object-fit: contain; display: none; cursor: pointer;" 
                                 data-bs-toggle="modal" data-bs-target="#imageModal" title="Clic para ampliar">
                            
                            <span id="textMain" class="text-muted small">Vista Previa</span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg">GUARDAR EVENTO</button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
        <div class="text-end"><button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2" data-bs-dismiss="modal"></button></div>
        <div class="text-center"><img src="" id="modalImg" class="img-fluid rounded shadow-lg" style="max-height: 85vh;"></div>
    </div>
  </div>
</div>

<template id="galleryTemplate">
    <div class="gallery-item border rounded p-3 mb-3 bg-light position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-item"></button>
        
        <div class="row align-items-center">
            <div class="col-md-8">
                <label class="form-label small fw-bold">Archivo de Imagen</label>
                <input type="file" name="galeria[]" class="form-control form-control-sm gallery-input" accept=".jpg,.jpeg,.png" required>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="bg-white border rounded d-flex align-items-center justify-content-center" style="height: 80px; overflow: hidden;">
                    <img src="" class="gallery-preview d-none" 
                         style="max-height: 100%; max-width: 100%; cursor: pointer;"
                         data-bs-toggle="modal" data-bs-target="#imageModal" title="Clic para ampliar">
                    <span class="text-muted x-small gallery-placeholder">Sin imagen</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // 1. Preview Main
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
        })
        .catch(error => {
            console.error(error);
        });
    document.getElementById('inputMain').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = ev => {
                const img = document.getElementById('previewMain');
                img.src = ev.target.result;
                img.style.display = 'block';
                document.getElementById('textMain').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    // 2. Modal Logic (Delegación de eventos para imágenes dinámicas)
    // Cuando se abre el modal, buscamos qué imagen lo disparó y copiamos su src
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', function (event) {
            // Botón/Imagen que disparó el modal
            const triggerElement = event.relatedTarget; 
            // Extraer la fuente
            const src = triggerElement.src;
            // Actualizar la imagen del modal
            const modalImg = imageModal.querySelector('#modalImg');
            modalImg.src = src;
        });
    }

    // 3. Gallery Logic
    document.getElementById('btnAddGallery').addEventListener('click', () => {
        const tpl = document.getElementById('galleryTemplate');
        const clone = tpl.content.cloneNode(true);
        
        // Botón eliminar
        clone.querySelector('.remove-item').addEventListener('click', function() { 
            this.closest('.gallery-item').remove(); 
        });
        
        // Lógica de Preview para este ítem específico
        const input = clone.querySelector('.gallery-input');
        const preview = clone.querySelector('.gallery-preview');
        const placeholder = clone.querySelector('.gallery-placeholder');
        
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    placeholder.style.display = 'none';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        document.getElementById('galleryContainer').appendChild(clone);
    });
});
</script>
@endsection