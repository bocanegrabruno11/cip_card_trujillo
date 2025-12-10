@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Nueva Variable Global</h2>
        <a href="{{ route('tarifas_config.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    {{-- Bloque general de errores (Opcional, pero útil para ver resumen) --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Atención!</strong> Por favor revisa los errores en el formulario.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tarifas_config.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm" style="max-width: 600px; margin: 0 auto;">
            <div class="card-body p-4">
                
                {{-- CAMPO: CLAVE --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Clave Única (Sin espacios) <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="clave" 
                           class="form-control font-monospace @error('clave') is-invalid @enderror" 
                           placeholder="ej: igv, tasa_solicitud" 
                           value="{{ old('clave') }}" 
                           required>
                    
                    {{-- Mensaje de Error Específico --}}
                    @error('clave')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    
                    <div class="form-text text-muted">Este nombre se usará internamente en la calculadora.</div>
                </div>

                {{-- CAMPO: VALOR --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Valor Numérico <span class="text-danger">*</span></label>
                    <input type="number" 
                           step="0.01" 
                           name="valor" 
                           class="form-control @error('valor') is-invalid @enderror" 
                           placeholder="0.00" 
                           value="{{ old('valor') }}"
                           required>

                    @error('valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- CAMPO: DESCRIPCIÓN --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" 
                              class="form-control @error('descripcion') is-invalid @enderror" 
                              rows="3" 
                              placeholder="¿Para qué sirve este valor?">{{ old('descripcion') }}</textarea>
                    
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" id="btnSubmit" class="btn btn-primary">Guardar Variable</button>
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