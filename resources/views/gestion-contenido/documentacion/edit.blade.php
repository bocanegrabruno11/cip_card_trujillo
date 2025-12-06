@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Editar Documento</h2>
        <a href="{{ route('documentos-gestion.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <form action="{{ route('documentos-gestion.update', $documento->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título</label>
                            <input type="text" name="titulo" class="form-control" required value="{{ $documento->titulo }}">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Publicación</label>
                                <input type="date" name="fecha_publicacion" class="form-control" required value="{{ $documento->fecha_publicacion->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Sección</label>
                                <select name="seccion" id="selectSeccion" class="form-select" required>
                                    <option value="institucion" {{ $documento->seccion == 'institucion' ? 'selected' : '' }}>Institución Arbitral</option>
                                    <option value="junta" {{ $documento->seccion == 'junta' ? 'selected' : '' }}>Junta de Prevención</option>
                                    <option value="convocatorias" {{ $documento->seccion == 'convocatorias' ? 'selected' : '' }}>Convocatorias</option>
                                    <option value="certificaciones" {{ $documento->seccion == 'certificaciones' ? 'selected' : '' }}>Certificaciones</option>
                                    <option value="politicas" {{ $documento->seccion == 'politicas' ? 'selected' : '' }}>Políticas</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="divCategoria" style="display:none;">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="normativa" {{ $documento->categoria == 'normativa' ? 'selected' : '' }}>Normativa</option>
                                <option value="tarifario" {{ $documento->categoria == 'tarifario' ? 'selected' : '' }}>Tarifario y Calculadora</option>
                                <option value="incorporacion" {{ $documento->categoria == 'incorporacion' ? 'selected' : '' }}>Incorporación y Nómina</option>
                                <option value="requisitos" {{ $documento->categoria == 'requisitos' ? 'selected' : '' }}>Requisitos</option>
                                <option value="solicitar" {{ $documento->categoria == 'solicitar' ? 'selected' : '' }}>Solicitar el Servicio</option>
                                <option value="repositorio" {{ $documento->categoria == 'repositorio' ? 'selected' : '' }}>Repositorio</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reemplazar Archivo (Opcional)</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <div class="mt-2 small">
                                Archivo actual: 
                                <a href="{{ asset('storage/' . $documento->ruta_archivo) }}" target="_blank" class="text-danger fw-bold">
                                    <i class="fas fa-file-alt"></i> Ver documento actual
                                </a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="4">{{ $documento->descripcion }}</textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-warning text-white px-4">Actualizar Documento</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectSeccion = document.getElementById('selectSeccion');
        const divCategoria = document.getElementById('divCategoria');

        function toggleCategoria() {
            const val = selectSeccion.value;
            if (val === 'institucion' || val === 'junta') {
                divCategoria.style.display = 'block';
            } else {
                divCategoria.style.display = 'none';
            }
        }
        selectSeccion.addEventListener('change', toggleCategoria);
        toggleCategoria(); // Ejecutar al cargar para mostrar estado actual
    });
</script>
@endsection