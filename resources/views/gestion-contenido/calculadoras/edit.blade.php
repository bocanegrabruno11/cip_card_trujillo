@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Editar Escala Tarifaria</h2>
        <a href="{{ route('calculadoras-gestion.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <form action="{{ route('calculadoras-gestion.update', $tarifa->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de Tabla</label>
                        <select name="tipo" class="form-select" required>
                            <option value="arbitro_unico" {{ $tarifa->tipo == 'arbitro_unico' ? 'selected' : '' }}>Honorarios Árbitro Único</option>
                            <option value="tribunal_arbitral" {{ $tarifa->tipo == 'tribunal_arbitral' ? 'selected' : '' }}>Honorarios Tribunal Arbitral</option>
                            <option value="gastos_administrativos" {{ $tarifa->tipo == 'gastos_administrativos' ? 'selected' : '' }}>Gastos Administrativos</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Letra del Rango</label>
                        <input type="text" name="rango_letra" class="form-control" value="{{ $tarifa->rango_letra }}" required>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold text-primary mb-3">Definición del Rango</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Monto Mínimo</label>
                        <input type="number" step="0.01" name="monto_min" class="form-control" value="{{ $tarifa->monto_min }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monto Máximo</label>
                        <input type="number" step="0.01" name="monto_max" class="form-control" value="{{ $tarifa->monto_max }}">
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold text-success mb-3">Costos y Cálculos</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Monto Fijo</label>
                        <input type="number" step="0.01" name="monto_fijo" class="form-control" value="{{ $tarifa->monto_fijo }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">% Exceso</label>
                        <div class="input-group">
                            <input type="number" step="0.001" name="porcentaje_exceso" class="form-control" value="{{ $tarifa->porcentaje_exceso }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Base del Exceso</label>
                        <input type="number" step="0.01" name="base_exceso" class="form-control" value="{{ $tarifa->base_exceso }}" required>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-warning text-white px-4">Actualizar Escala</button>
            </div>
        </div>
    </form>
</div>
@endsection