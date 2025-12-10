@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>¡Atención!</strong> Revisa los errores.
            <ul class="mb-0 mt-1">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold text-dark">Editar Comunicado</h2>
        <a href="{{ route('comunicados.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <form action="{{ route('comunicados.update', $comunicado->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-7 border-end">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título</label>
                            <input type="text" name="titulo" class="form-control" value="{{ $comunicado->titulo }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enlace URL</label>
                            <input type="url" name="url_enlace" class="form-control" value="{{ $comunicado->url_enlace }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="5">{{ $comunicado->descripcion }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Reemplazar Imagen</label>
                        <input type="file" name="imagen" class="form-control mb-3" id="inputImg" accept=".jpg,.jpeg,.png">
                        
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center position-relative" style="height: 250px; overflow: hidden;">
                            <img id="previewImg" 
                                 src="{{ asset('storage/' . $comunicado->ruta_imagen) }}" 
                                 style="width: 100%; height: 100%; object-fit: contain; cursor: pointer;"
                                 data-bs-toggle="modal" data-bs-target="#imageModal" title="Clic para ampliar">
                        </div>
                        <div class="form-text text-end mt-1">Imagen actual mostrada arriba.</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" id="btnSubmit" class="btn btn-warning text-white px-5">ACTUALIZAR TODO</button>
            </div>
        </div>
    </form>
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
    // Preview al cambiar archivo
    document.getElementById('inputImg').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('previewImg').src = ev.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Modal Logic
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', event => {
            const src = document.getElementById('previewImg').src;
            imageModal.querySelector('#modalImagePreview').src = src;
        });
    }
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