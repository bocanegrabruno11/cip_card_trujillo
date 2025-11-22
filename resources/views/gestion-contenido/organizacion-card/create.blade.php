@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>
    @endif

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Nuevo Miembro del Equipo</h2>
        <a href="{{ route('organizacion-gestion.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <form action="{{ route('organizacion-gestion.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Nombres y Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="nombres" class="form-control" required value="{{ old('nombres') }}">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Grupo / Sección <span class="text-danger">*</span></label>
                                <select name="grupo" class="form-select" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="directivo">Órgano Directivo</option>
                                    <option value="decisorio_presidente">Órgano Decisorio (Presidente)</option>
                                    <option value="decisorio_miembros">Órgano Decisorio (Miembros)</option>
                                    <option value="secretaria">Secretaría General</option>
                                    <option value="secretarios_arbitrales">Secretarios Arbitrales</option>
                                    <option value="apoyo">Personal de Apoyo</option>
                                    <option value="administrativo">Soporte Administrativo</option>
                                </select>
                                <div class="form-text small">El sistema asignará el orden automáticamente al final.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cargo Exacto (Opcional)</label>
                                <input type="text" name="cargo" class="form-control" placeholder="Ej: Decano, Director..." value="{{ old('cargo') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Corporativo</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono / Anexo</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Foto del Miembro</div>
                    <div class="card-body text-center">
                        <input type="file" name="imagen" class="form-control mb-3" id="inputImg" accept="image/*">
                        
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto overflow-hidden" style="width: 200px; height: 200px; border: 5px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                            <img id="previewImg" src="" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: none; cursor: pointer;"
                                 data-bs-toggle="modal" data-bs-target="#imageModal" title="Clic para ampliar">
                                 
                            <span id="textInfo" class="text-muted small">Sin Foto</span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg">GUARDAR REGISTRO</button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="text-end"><button type="button" class="btn-close btn-close-white bg-white rounded-circle p-2" data-bs-dismiss="modal"></button></div>
      <div class="text-center">
          <img src="" id="modalImagePreview" class="img-fluid rounded-circle border border-4 border-white shadow-lg" style="max-height: 500px; max-width: 500px; object-fit: cover;">
      </div>
    </div>
  </div>
</div>

<script>
    // Preview Logic
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

    // Modal Logic (Pasa el src de la preview al modal)
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', event => {
            const src = document.getElementById('previewImg').src;
            imageModal.querySelector('#modalImagePreview').src = src;
        });
    }
</script>
@endsection