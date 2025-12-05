@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Nueva Escala Tarifaria</h2>
        <a href="{{ route('calculadoras-gestion.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    {{-- Alerta general de errores (opcional) --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>¡Atención!</strong> Por favor corrige los errores del formulario.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('calculadoras-gestion.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de Tabla <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            <option value="arbitro_unico" {{ old('tipo') == 'arbitro_unico' ? 'selected' : '' }}>Honorarios Árbitro Único</option>
                            <option value="tribunal_arbitral" {{ old('tipo') == 'tribunal_arbitral' ? 'selected' : '' }}>Honorarios Tribunal Arbitral</option>
                            <option value="gastos_administrativos" {{ old('tipo') == 'gastos_administrativos' ? 'selected' : '' }}>Gastos Administrativos</option>
                        </select>
                        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Letra del Rango <span class="text-danger">*</span></label>
                        <input type="text" name="rango_letra" class="form-control @error('rango_letra') is-invalid @enderror" placeholder="Ej: A, B, C..." value="{{ old('rango_letra') }}" required maxlength="5">
                        @error('rango_letra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="text-muted">

                <h6 class="fw-bold text-primary mb-3">Definición del Rango (Soles)</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Monto Mínimo <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">S/.</span>
                            <input type="number" step="0.01" name="monto_min" class="form-control @error('monto_min') is-invalid @enderror" required placeholder="0.00" value="{{ old('monto_min') }}">
                            @error('monto_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monto Máximo</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">S/.</span>
                            <input type="number" step="0.01" name="monto_max" class="form-control @error('monto_max') is-invalid @enderror" placeholder="Dejar vacío para 'A más'" value="{{ old('monto_max') }}">
                            @error('monto_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Si es el último rango, dejar en blanco.</div>
                    </div>
                </div>

                <hr class="text-muted">

                <h6 class="fw-bold text-success mb-3">Costos y Cálculos</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Monto Fijo (Base) <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">S/.</span>
                            <input type="number" step="0.01" name="monto_fijo" class="form-control @error('monto_fijo') is-invalid @enderror" required value="{{ old('monto_fijo', '0.00') }}">
                            @error('monto_fijo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">% Exceso (Opcional)</label>
                        <div class="input-group has-validation">
                            <input type="number" step="0.001" name="porcentaje_exceso" class="form-control @error('porcentaje_exceso') is-invalid @enderror" value="{{ old('porcentaje_exceso', '0.00') }}">
                            <span class="input-group-text">%</span>
                            @error('porcentaje_exceso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Ej: 0.70 para 0.70%</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Base del Exceso</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">S/.</span>
                            <input type="number" step="0.01" name="base_exceso" class="form-control @error('base_exceso') is-invalid @enderror" required value="{{ old('base_exceso', '0.00') }}">
                            @error('base_exceso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Monto sobre el que se aplica el %</div>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-primary px-4">Guardar Escala</button>
            </div>
        </div>
    </form>
</div>
@endsection