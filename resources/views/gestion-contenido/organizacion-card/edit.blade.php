@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Editar Miembro</h2>
        <a href="{{ route('organizacion-gestion.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <form action="{{ route('organizacion-gestion.update', $miembro->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        
                        {{-- FILA 1: NOMBRE Y CÓDIGO --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombres y Apellidos</label>
                                <input type="text" name="nombres" class="form-control" oninput="this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, '').toUpperCase();" required value="{{ $miembro->nombres }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Código (CIP u otro)</label>
                                <input type="text" name="codigo" class="form-control" value="{{ $miembro->codigo }}">
                            </div>
                        </div>
                        
                        {{-- FILA 2: GRUPO Y CARGO --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Grupo / Sección</label>
                                <select name="grupo" class="form-select" required>
                                    <option value="" disabled>Seleccione...</option>
                                    
                                    {{-- Definimos el array completo de opciones --}}
                                    @php
                                        $opciones = [
                                            'directivo' => 'Órgano Directivo',
                                            'decisorio_presidente' => 'Órgano Decisorio (Presidente)',
                                            'decisorio_miembros' => 'Órgano Decisorio (Miembros)',
                                            'secretaria' => 'Secretaría General',
                                            'secretarios_arbitrales' => 'Secretarios Arbitrales',
                                            'apoyo' => 'Personal de Apoyo',
                                            'administrativo' => 'Soporte Administrativo',
                                            'arbitros-nomina' => 'Nómina de Arbitros',
                                            'adjudicadores-nomina' => 'Nómina de Adjudicadores'
                                        ];
                                    @endphp

                                    @foreach($opciones as $key => $label)
                                        <option value="{{ $key }}" {{ $miembro->grupo == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cargo</label>
                                <input type="text" name="cargo" class="form-control" value="{{ $miembro->cargo }}">
                            </div>
                        </div>

                        {{-- FILA 3: ESPECIALIDAD --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Especialidad / Profesión</label>
                                <input type="text" name="especialidad" class="form-control" value="{{ $miembro->especialidad }}">
                            </div>
                        </div>

                        {{-- FILA 4: CONTACTO --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Corporativo</label>
                                <input type="email" name="email" class="form-control" value="{{ $miembro->email }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono / Anexo</label>
                                <input type="text" name="telefono" class="form-control" value="{{ $miembro->telefono }}">
                            </div>
                        </div>

                        {{-- FILA 5: CV --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Hoja de Vida (CV)</label>
                                <input type="file" name="cv" class="form-control" accept=".pdf">
                                <div class="form-text">Subir nuevo para reemplazar. (PDF Máx 10MB)</div>
                                
                                @if($miembro->ruta_cv)
                                    <div class="mt-2 p-2 bg-light border rounded d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2 fs-4"></i>
                                        <span class="text-muted small me-auto">CV Actual Cargado</span>
                                        <a href="{{ asset('storage/' . $miembro->ruta_cv) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver PDF
                                        </a>
                                    </div>
                                @endif
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
                            @if($miembro->ruta_imagen)
                                <img id="previewImg" src="{{ asset('storage/' . $miembro->ruta_imagen) }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span id="textInfo" class="text-muted small">Sin Foto</span>
                                <img id="previewImg" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            @endif
                        </div>
                        <div class="form-text mt-2">Foto actual mostrada arriba</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning text-white w-100 btn-lg">ACTUALIZAR DATOS</button>
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
                const txt = document.getElementById('textInfo');
                if(txt) txt.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection