@extends('Admin.app')

@section('title', 'Gestión de Etapas Arbitrales')
@section('page-title', 'Administración de Etapas Arbitrales')

@section('content')

<div class="container-fluid px-4">

    <!-- Tarjeta para crear nueva etapa -->
    <div class="card shadow-lg mb-4 border-0 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                    <i class="fas fa-plus-circle fa-lg text-danger"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Crear Nueva Etapa Arbitral</h5>
                    <p class="mb-0 small text-muted">Registra las etapas del proceso de Arbitraje</p>
                </div>
            </div>
        </div>
        <div class="card-body pt-4">
            <form action="{{ route('Admin.etapas.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-dark">
                        <i class="fas fa-tag me-1 text-danger"></i>Nombre de la etapa <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-layer-group text-danger"></i>
                        </span>
                        <input type="text" name="nombre" class="form-control border-start-0 text-dark" 
                               placeholder="Ej: Conciliación, Evaluación de Pruebas, Laudo Arbitral..." required>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>Ingrese un nombre descriptivo para la etapa del proceso de Arbitraje
                    </small>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-danger w-100 btn-lg fw-semibold shadow-sm">
                        <i class="fas fa-save me-2"></i>Guardar Etapa
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjeta para listado de etapas -->
    <div class="card shadow-lg border-0 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                        <i class="fas fa-list-alt text-danger fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Listado de Etapas Arbitrales</h5>
                        <p class="text-muted small mb-0">Gestiona las etapas activas e inactivas</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Activas: {{ $etapas->where('estado', 1)->count() }}
                    </span>
                    <span class="badge bg-secondary rounded-pill px-3 py-2 shadow-sm">
                        <i class="fas fa-tasks me-1"></i> Total: {{ $etapas->count() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="border-0">
                            <th width="8%" class="text-center text-dark">#</th>
                            <th width="52%" class="text-dark">Nombre de la Etapa</th>
                            <th width="20%" class="text-dark">Estado</th>
                            <th width="20%" class="text-center text-dark">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($etapas as $etapa)
                        <tr class="border-bottom">
                            <td class="text-center fw-bold">
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">
                                    #{{ $etapa->id }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('Admin.etapas.update', $etapa->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group input-group-sm" style="max-width: 350px;">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-edit text-warning"></i>
                                        </span>
                                        <input type="text" name="nombre" value="{{ $etapa->nombre }}" 
                                               class="form-control border-start-0 text-dark" required>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-sm shadow-sm" title="Actualizar">
                                        <i class="fas fa-save me-1"></i> Guardar
                                    </button>
                                </form>
                            </td>
                            <td>
                                @if($etapa->estado == 1)
                                    <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('Admin.etapas.toggle', $etapa->id) }}" 
                                       class="btn btn-sm {{ $etapa->estado == 1 ? 'btn-outline-warning' : 'btn-outline-success' }} shadow-sm" 
                                       title="{{ $etapa->estado == 1 ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas {{ $etapa->estado == 1 ? 'fa-pause-circle' : 'fa-play-circle' }} me-1"></i>
                                        {{ $etapa->estado == 1 ? 'Desactivar' : 'Activar' }}
                                    </a>

                                    <form action="{{ route('Admin.etapas.destroy', $etapa->id) }}" method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta etapa?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm" title="Eliminar">
                                            <i class="fas fa-trash-alt me-1"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                                        <i class="fas fa-inbox fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="text-muted">No hay etapas registradas</h6>
                                    <p class="small text-muted">¡Comienza creando tu primera etapa!</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tarjeta de información -->
    <div class="card mt-4 border-0 bg-light shadow-sm">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb fa-2x text-warning me-3"></i>
                        <div>
                            <small class="text-dark fw-semibold">
                                <i class="fas fa-info-circle me-1 text-info"></i> Información importante
                            </small>
                            <p class="small text-muted mb-0">
                                Las etapas activas son las que estarán disponibles en el flujo del proceso de Arbitraje.
                                Puedes desactivar una etapa sin eliminarla para mantener el historial.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-inline-block bg-white rounded-3 px-3 py-2 shadow-sm">
                        <i class="fas fa-clock me-1 text-muted"></i>
                        <small class="text-muted">Última actualización: {{ now()->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
/* Estilos de tarjetas */
.card {
    border-radius: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15) !important;
}

/* Botones */
.btn-danger {
    background-color: #AD2B2E;
    border-color: #AD2B2E;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #8B2326;
    border-color: #8B2326;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(173, 43, 46, 0.4);
}

.btn-outline-warning {
    border-color: #ffc107;
    color: #ffc107;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #2c3e50;
}

.btn-outline-success {
    border-color: #28a745;
    color: #28a745;
}

.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

/* Tabla */
.table-hover tbody tr:hover {
    background: linear-gradient(90deg, #fef9f9 0%, #fff 100%);
    cursor: pointer;
}

.table tbody td {
    vertical-align: middle;
    padding: 14px 8px;
}

.table thead th {
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.5px;
    color: #1a1a1a;
}

/* Input group */
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-color: #AD2B2E;
    box-shadow: none;
}

/* Textos */
.text-dark {
    color: #1a1a1a !important;
}

.text-muted {
    color: #6c757d !important;
}

/* Empty state */
.empty-state {
    padding: 40px 0;
}

/* Badges */
.badge {
    transition: all 0.3s ease;
    font-size: 12px;
    font-weight: 500;
}

.badge:hover {
    transform: scale(1.05);
}

/* Responsive */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 8px !important;
    }
    
    .btn-sm {
        padding: 4px 10px;
        font-size: 11px;
    }
    
    .table-responsive {
        font-size: 13px;
    }
    
    .btn-danger {
        font-size: 14px;
        padding: 10px;
    }
}
</style>
@endpush