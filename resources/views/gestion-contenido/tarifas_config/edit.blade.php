@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Editar Variable Global</h2>
        <a href="{{ route('tarifas_config.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <form action="{{ route('tarifas_config.update', $config->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card border-0 shadow-sm" style="max-width: 600px; margin: 0 auto;">
            <div class="card-body p-4">
                
                <div class="alert alert-info small border-0 bg-light text-muted">
                    <i class="fas fa-info-circle me-1"></i> 
                    La <b>Clave</b> no se puede editar para mantener la integridad del sistema.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Variable (Clave)</label>
                    <input type="text" class="form-control font-monospace bg-light" value="{{ $config->clave }}" disabled>
                    <input type="hidden" name="clave" value="{{ $config->clave }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Valor Numérico</label>
                    <input type="number" step="0.01" name="valor" class="form-control fw-bold text-success fs-5" value="{{ $config->valor }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ $config->descripcion }}</textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" id="btnSubmit" class="btn btn-warning text-white fw-bold">Actualizar Variable</button>
                </div>

            </div>
        </div>
    </form>
</div>

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