@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Atención!</strong> Revisa los errores.
            <ul class="mb-0 mt-1">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold text-dark">Nuevo Comunicado</h2>
        <a href="{{ route('comunicados.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <form action="{{ route('comunicados.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-7 border-end">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control" required value="{{ old('titulo') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enlace URL (Opcional)</label>
                            <input type="url" name="url_enlace" class="form-control" placeholder="https://..." value="{{ old('url_enlace') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="5">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Imagen del Comunicado <span class="text-danger">*</span></label>
                        <input type="file" name="imagen" class="form-control mb-3" id="inputImg" accept=".jpg,.jpeg,.png" required>
                        
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center position-relative" style="height: 250px; overflow: hidden;">
                            
                            <img id="previewImg" src="" 
                                 style="width: 100%; height: 100%; object-fit: contain; display: none; cursor: pointer;"
                                 data-bs-toggle="modal" data-bs-target="#imageModal" title="Clic para ampliar">
                                 
                            <span id="textInfo" class="text-muted small">Vista Previa</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-primary px-5">GUARDAR COMUNICADO</button>
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
    // Preview
    document.getElementById('inputImg').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = document.getElementById('previewImg');
                img.src = ev.target.result;
                img.style.display = 'block';
                document.getElementById('textInfo').style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });

    // Modal Logic
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', event => {
            // Usamos la fuente de la imagen de previsualización
            const src = document.getElementById('previewImg').src;
            imageModal.querySelector('#modalImagePreview').src = src;
        });
    }
</script>
@endsection