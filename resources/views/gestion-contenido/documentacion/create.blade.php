@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Subir Documento</h2>
        <a href="{{ route('documentos-gestion.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <form action="{{ route('documentos-gestion.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título del Documento <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="titulo" 
                                class="form-control" 
                                required 
                                value="{{ old('titulo') }}" 
                                placeholder="Ej: Bases del Concurso..."
                                oninput="this.value = this.value.toUpperCase()"
                            >
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Publicación <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_publicacion" class="form-control" required value="{{ old('fecha_publicacion', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Sección <span class="text-danger">*</span></label>
                                <select name="seccion" id="selectSeccion" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="institucion">Institución Arbitral</option>
                                    <option value="junta">Junta de Prevención (JPRD)</option>
                                    <!-- <option value="convocatorias">Convocatorias</option> -->
                                    <option value="certificaciones">Certificaciones</option>
                                    <option value="politicas">Políticas</option>
                                    <option value="presentacion">Presentación del CARD</option>
                                    <option value="organizacion">Organización del CARD</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="divCategoria" style="display:none;">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="">Seleccione categoría...</option>
                                <option value="normativa">Normativa</option>
                                <option value="tarifario">Tarifario y Calculadora</option>
                                <option value="incorporacion">Incorporación y Nómina</option>
                                <option value="requisitos">Requisitos (Árbitros/Adjudicadores)</option>
                                <option value="solicitar">Solicitar el Servicio</option>
                                <option value="repositorio">Repositorio (Laudos/Decisiones)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Archivo (PDF, Word, Excel, Imagen) <span class="text-danger">*</span></label>
                            <input type="file" name="archivo" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.webp">
                            <div class="form-text">Máximo 20MB.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" id="btnSubmit" class="btn btn-primary px-4">Guardar Documento</button>
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
            // Si es Institución o Junta, mostramos categorías. Si es Convocatorias, ocultamos.
            if (val === 'institucion' || val === 'junta') {
                divCategoria.style.display = 'block';
            } else {
                divCategoria.style.display = 'none';
                // Resetear el select interno si se oculta (opcional)
                divCategoria.querySelector('select').value = "";
            }
        }

        selectSeccion.addEventListener('change', toggleCategoria);
        toggleCategoria(); // Ejecutar al inicio por si hay old values
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