@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Editar Evento</h2>
        <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <form action="{{ route('eventos.update', $evento->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Título</label>
                                <input type="text" name="titulo" class="form-control" value="{{ $evento->titulo }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha</label>
                                <input type="date" name="fecha_evento" class="form-control" value="{{ $evento->fecha_evento }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lugar</label>
                            <input type="text" name="lugar" class="form-control" value="{{ $evento->lugar }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" id="editor" class="form-control">{{ $evento->descripcion }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Galería Actual</div>
                    <div class="card-body">
                        @if($galeria->count() > 0)
                            <div class="row g-2">
                                @foreach($galeria as $img)
                                    <div class="col-md-3 position-relative">
                                        <img src="{{ asset('storage/' . $img->ruta_imagen) }}" 
                                             class="img-fluid rounded mb-1" 
                                             style="height: 100px; width: 100%; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal"
                                             data-full-src="{{ asset('storage/' . $img->ruta_imagen) }}">
                                             
                                        <div class="form-check bg-light border rounded px-2">
                                            <input class="form-check-input border-danger" type="checkbox" name="eliminar_detalles[]" value="{{ $img->id }}">
                                            <label class="form-check-label text-danger small">Eliminar</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small m-0">No hay imágenes adicionales.</p>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span>Agregar Más Fotos</span>
                        <button type="button" class="btn btn-sm btn-success" id="btnAddGallery"><i class="fas fa-plus"></i> Agregar</button>
                    </div>
                    <div class="card-body" id="galleryContainer">
                        </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Imagen Principal</div>
                    <div class="card-body text-center">
                        <input type="file" name="imagen_principal" class="form-control mb-3" id="inputMain" accept=".jpg,.jpeg,.png">
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center overflow-hidden" style="height: 200px;">
                            @if($imagenPrincipal)
                                <img id="previewMain" 
                                     src="{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}" 
                                     style="width: 100%; height: 100%; object-fit: contain; cursor: pointer;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal"
                                     data-full-src="{{ asset('storage/' . $imagenPrincipal->ruta_imagen) }}">
                            @else
                                <span class="text-muted">Sin imagen</span>
                            @endif
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning text-white w-100 btn-lg">ACTUALIZAR EVENTO</button>
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
    <div class="gallery-item border rounded p-2 mb-2 bg-light position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-1 remove-item"></button>
        
        <div class="row align-items-center">
            <div class="col-md-8">
                <label class="form-label small fw-bold mb-1">Archivo de Imagen</label>
                <input type="file" name="galeria_nueva[]" class="form-control form-control-sm input-new-img" accept=".jpg,.jpeg,.png" required>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="bg-white border rounded d-flex align-items-center justify-content-center overflow-hidden" style="height: 80px;">
                    <img src="" class="preview-new-img d-none" 
                         style="max-height: 100%; max-width: 100%; cursor: pointer;"
                         data-bs-toggle="modal" data-bs-target="#imageModal">
                    <span class="text-muted x-small placeholder-text">Vista</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    // 1. Preview Main
document.addEventListener('DOMContentLoaded', function() {
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
                let img = document.getElementById('previewMain');
                if(!img) {
                    // Si no existía la imagen (estaba el texto "Sin imagen"), la creamos dinámicamente
                    const container = document.querySelector('.bg-light.border.rounded');
                    container.innerHTML = ''; // Limpiar texto
                    img = document.createElement('img');
                    img.id = 'previewMain';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'contain';
                    img.style.cursor = 'pointer';
                    img.setAttribute('data-bs-toggle', 'modal');
                    img.setAttribute('data-bs-target', '#imageModal');
                    container.appendChild(img);
                }
                img.src = ev.target.result;
                // Actualizar el atributo data-full-src para que el modal cargue la nueva imagen
                img.setAttribute('data-full-src', ev.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // 2. Gallery Logic (Agregar + Preview)
    document.getElementById('btnAddGallery').addEventListener('click', () => {
        const tpl = document.getElementById('galleryTemplate');
        const clone = tpl.content.cloneNode(true);
        
        // Botón Eliminar fila
        clone.querySelector('.remove-item').addEventListener('click', function() { 
            this.closest('.gallery-item').remove(); 
        });

        // Lógica de Preview para esta nueva fila
        const input = clone.querySelector('.input-new-img');
        const imgPreview = clone.querySelector('.preview-new-img');
        const placeholder = clone.querySelector('.placeholder-text');

        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.classList.remove('d-none'); // Mostrar imagen
                    placeholder.style.display = 'none';    // Ocultar texto
                    
                    // Asignar src para el modal también
                    imgPreview.setAttribute('src', e.target.result); 
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        document.getElementById('galleryContainer').appendChild(clone);
    });

    // 3. Modal Logic (General para todas las imágenes)
    const modalElement = document.getElementById('imageModal');
    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', e => {
            // Intentamos obtener data-full-src (imágenes existentes) o src directo (imágenes nuevas/previews)
            const trigger = e.relatedTarget;
            const src = trigger.getAttribute('data-full-src') || trigger.src;
            document.getElementById('modalImg').src = src;
        });
    }
    });
</script>
@endsection