@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Detalle de Escala</h2>
        <div>
            <a href="{{ route('calculadoras-gestion.edit', $tarifa->id) }}" class="btn btn-warning text-white btn-sm me-2"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('calculadoras-gestion.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Escala {{ $tarifa->rango_letra }} - {{ $tarifa->tipo_legible }}</span>
                        {{-- BADGE DEL TIPO DE CALCULADORA --}}
                        @if($tarifa->tipo_calculadora == 'servicio_arbitral')
                            <span class="badge bg-danger">Servicio Arbitral</span>
                        @elseif($tarifa->tipo_calculadora == 'junta_prevencion')
                            <span class="badge bg-info text-dark">Junta de Prevención</span>
                        @else
                            <span class="badge bg-secondary">Sin asignar</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6 border-end">
                            <h6 class="text-muted small text-uppercase fw-bold">Rango de Cuantía</h6>
                            <p class="fs-5">
                                De: <strong>S/. {{ number_format($tarifa->monto_min, 2) }}</strong><br>
                                Hasta: <strong>{{ $tarifa->monto_max ? 'S/. '.number_format($tarifa->monto_max, 2) : 'En adelante' }}</strong>
                            </p>
                        </div>
                        <div class="col-md-6 ps-4">
                            <h6 class="text-muted small text-uppercase fw-bold">Monto Fijo a Cobrar</h6>
                            <p class="fs-4 text-success fw-bold">S/. {{ number_format($tarifa->monto_fijo, 2) }}</p>
                        </div>
                    </div>
                    
                    @if($tarifa->porcentaje_exceso > 0)
                    <div class="alert alert-warning border-0 text-dark">
                        <i class="fas fa-calculator me-2"></i> 
                        <strong>Cálculo Adicional:</strong> 
                        Se suma el <b>{{ $tarifa->porcentaje_exceso }}%</b> 
                        sobre lo que exceda de <b>S/. {{ number_format($tarifa->base_exceso, 2) }}</b>.
                    </div>
                    @else
                    <div class="alert alert-light border text-muted">
                        <i class="fas fa-check-circle me-2"></i> Tarifa Plana (Sin cálculo de porcentaje adicional).
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection