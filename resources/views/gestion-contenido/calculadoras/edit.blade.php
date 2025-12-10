@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Editar Escala Tarifaria</h2>
        <a href="{{ route('calculadoras-gestion.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    {{-- Alerta general de errores (opcional pero recomendada) --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>¡Atención!</strong> Por favor corrige los errores del formulario.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('calculadoras-gestion.update', $tarifa->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                
                {{-- TIPO DE CALCULADORA --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-bold">Pertenece a la Calculadora:</label>
                        <select name="tipo_calculadora" class="form-select @error('tipo_calculadora') is-invalid @enderror" required>
                            <option value="servicio_arbitral" {{ $tarifa->tipo_calculadora == 'servicio_arbitral' ? 'selected' : '' }}>Servicio Arbitral (Arbitraje)</option>
                            <option value="junta_prevencion" {{ $tarifa->tipo_calculadora == 'junta_prevencion' ? 'selected' : '' }}>Junta de Prevención (JRD)</option>
                        </select>
                        @error('tipo_calculadora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- TIPO Y LETRA --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de Tabla</label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="arbitro_unico" {{ $tarifa->tipo == 'arbitro_unico' ? 'selected' : '' }}>Honorarios Árbitro Único</option>
                            <option value="tribunal_arbitral" {{ $tarifa->tipo == 'tribunal_arbitral' ? 'selected' : '' }}>Honorarios Tribunal Arbitral</option>
                            <option value="gastos_administrativos" {{ $tarifa->tipo == 'gastos_administrativos' ? 'selected' : '' }}>Gastos Administrativos</option>
                        </select>
                        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Letra del Rango</label>
                        <input type="text" name="rango_letra" class="form-control @error('rango_letra') is-invalid @enderror" value="{{ old('rango_letra', $tarifa->rango_letra) }}" 
                        oninput="this.value = this.value.replace(/[^A-Za-zñÑ]/g, '').toUpperCase();" required>
                        @error('rango_letra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="text-muted">

                {{-- DEFINICIÓN DE RANGO --}}
                <h6 class="fw-bold text-primary mb-3">Definición del Rango</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Monto Mínimo</label>
                        <input type="number" step="0.01" name="monto_min" class="form-control @error('monto_min') is-invalid @enderror" value="{{ old('monto_min', $tarifa->monto_min) }}" required>
                        @error('monto_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monto Máximo</label>
                        <input type="number" step="0.01" name="monto_max" class="form-control @error('monto_max') is-invalid @enderror" value="{{ old('monto_max', $tarifa->monto_max) }}" placeholder="Dejar vacío para 'A más'">
                        @error('monto_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="text-muted">

                {{-- COSTOS Y CÁLCULOS --}}
                <h6 class="fw-bold text-success mb-3">Costos y Cálculos</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Monto Fijo</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">S/.</span>
                            <input type="number" step="0.01" name="monto_fijo" class="form-control @error('monto_fijo') is-invalid @enderror" value="{{ old('monto_fijo', $tarifa->monto_fijo) }}" required>
                            @error('monto_fijo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">% Exceso</label>
                        <div class="input-group has-validation">
                            <input type="number" step="0.001" name="porcentaje_exceso" class="form-control @error('porcentaje_exceso') is-invalid @enderror" value="{{ old('porcentaje_exceso', $tarifa->porcentaje_exceso) }}">
                            <span class="input-group-text">%</span>
                            @error('porcentaje_exceso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Base del Exceso</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">S/.</span>
                            <input type="number" step="0.01" name="base_exceso" class="form-control @error('base_exceso') is-invalid @enderror" value="{{ old('base_exceso', $tarifa->base_exceso) }}" required>
                            @error('base_exceso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" id="btnSubmit" class="btn btn-warning text-white px-4">Actualizar Escala</button>
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