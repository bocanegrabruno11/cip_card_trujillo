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
            {{-- COLUMNA DATOS --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Nombres y Apellidos <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="nombres" 
                                    class="form-control" 
                                    required 
                                    value="{{ old('nombres') }}"
                                    oninput="this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, '').toUpperCase();"
                                >
                            </div>
                             <div class="col-6">
                                <label class="form-label fw-bold">Código</label>
                                <input type="text" name="codigo" class="form-control" value="{{ old('codigo') }}">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Grupo / Sección <span class="text-danger">*</span></label>
                                <select name="grupo" class="form-select" required>
                                    <option value="" disabled {{ old('grupo') == '' ? 'selected' : '' }}>Seleccione...</option>
                                    
                                    <option value="directivo" {{ old('grupo') == 'directivo' ? 'selected' : '' }}>Órgano Directivo</option>
                                    <option value="decisorio_presidente" {{ old('grupo') == 'decisorio_presidente' ? 'selected' : '' }}>Órgano Decisorio (Presidente)</option>
                                    <option value="decisorio_miembros" {{ old('grupo') == 'decisorio_miembros' ? 'selected' : '' }}>Órgano Decisorio (Miembros)</option>
                                    <option value="secretaria" {{ old('grupo') == 'secretaria' ? 'selected' : '' }}>Secretaría General</option>
                                    <option value="secretarios_arbitrales" {{ old('grupo') == 'secretarios_arbitrales' ? 'selected' : '' }}>Secretarios Arbitrales</option>
                                    <option value="apoyo" {{ old('grupo') == 'apoyo' ? 'selected' : '' }}>Personal de Apoyo</option>
                                    <option value="administrativo" {{ old('grupo') == 'administrativo' ? 'selected' : '' }}>Soporte Administrativo</option>
                                    <option value="arbitros-nomina" {{ old('grupo') == 'arbitros-nomina' ? 'selected' : '' }}>Nómina de Arbitros</option>
                                    <option value="adjudicadores-nomina" {{ old('grupo') == 'adjudicadores-nomina' ? 'selected' : '' }}>Nómina de Adjudicadores</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cargo</label>
                                <input type="text" name="cargo" class="form-control" placeholder="Ej: Decano..." value="{{ old('cargo') }}">
                            </div>
                        </div>

                        {{-- NUEVO CAMPO: ESPECIALIDAD --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Especialidad / Profesión</label>
                                <input type="text" name="especialidad" class="form-control" placeholder="Ej: Ingeniero Civil, Abogado..." value="{{ old('especialidad') }}">
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

                        {{-- NUEVO CAMPO: CV --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Hoja de Vida (CV)</label>
                                <input type="file" name="cv" class="form-control" accept=".pdf">
                                <div class="form-text">Formato PDF. Máximo 10MB.</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- COLUMNA FOTO --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Foto del Miembro</div>
                    <div class="card-body text-center">
                        <input type="file" name="imagen" class="form-control mb-3" id="inputImg" accept=".jpg,.jpeg,.png">
                        
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto overflow-hidden" style="width: 200px; height: 200px; border: 5px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                            <img id="previewImg" src="" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            <span id="textInfo" class="text-muted small">Sin Foto</span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg">GUARDAR REGISTRO</button>
            </div>
        </div>
    </form>
</div>

<script>
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
</script>
@endsection