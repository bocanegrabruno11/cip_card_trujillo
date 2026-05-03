@extends('mesa-partes.app')
@section('title', 'Mi Casilla Electrónica')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 text-dark fw-bold"><i class="fas fa-inbox me-2"></i>Bandeja de Entrada</h3>
    
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('casilla.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Desde:</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Hasta:</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tipo de Expediente:</label>
                    <select name="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="arbitraje" {{ request('tipo') == 'arbitraje' ? 'selected' : '' }}>Arbitraje</option>
                        <option value="jrd" {{ request('tipo') == 'jrd' ? 'selected' : '' }}>JPRD</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-filter me-1"></i> Filtrar</button>
                    <a href="{{ route('casilla.index') }}" class="btn btn-light btn-sm px-3 border"><i class="fas fa-eraser me-1"></i> Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Estado</th>
                            <th>Fecha</th>
                            <th>Expediente</th>
                            <th>Remitente</th>
                            <th>Asunto</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notificaciones as $n)
                        @php
                            // Obtener el número de expediente según el tipo
                            $numeroExpediente = null;
                            $tipoExpediente = null;
                            
                            if($n->arbitraje_id && $n->arbitraje) {
                                $numeroExpediente = $n->arbitraje->numero_expediente;
                                $tipoExpediente = 'arbitraje';
                            } elseif($n->jrd_id && $n->jrd) {
                                $numeroExpediente = $n->jrd->numero_expediente;
                                $tipoExpediente = 'jrd';
                            }
                            
                            $tituloExpediente = $numeroExpediente 
                                ? "Expediente N° {$numeroExpediente}"
                                : ($tipoExpediente === 'arbitraje' ? "Arbitraje #{$n->arbitraje_id}" : "JRD #{$n->jrd_id}");
                        @endphp
                        <tr class="{{ $n->estado == 'no leido' ? 'bg-light fw-bold' : '' }}" style="cursor: pointer;" onclick="window.location='{{ route('casilla.show', $n->id_casilla) }}'">
                            <td class="ps-4">
                                @if($n->estado == 'no leido')
                                    <span class="text-primary"><i class="fas fa-envelope fa-lg"></i></span>
                                @else
                                    <span class="text-muted"><i class="fas fa-envelope-open fa-lg"></i></span>
                                @endif
                            </td>
                            
                            <td class="text-muted" style="font-size: 0.9rem;">
                                {{ \Carbon\Carbon::parse($n->fecha_registro)->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                @if($n->arbitraje_id)
                                    @if($numeroExpediente)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">
                                            <i class="fas fa-scale-balanced me-1"></i> {{ $tituloExpediente }}
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">
                                            <i class="fas fa-scale-balanced me-1"></i> ARB-{{ $n->arbitraje_id }}
                                        </span>
                                    @endif
                                @elseif($n->jrd_id)
                                    @if($numeroExpediente)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2">
                                            <i class="fas fa-gavel me-1"></i> {{ $tituloExpediente }}
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2">
                                            <i class="fas fa-gavel me-1"></i> JRD-{{ $n->jrd_id }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>

                            <td>{{ $n->emisor->name ?? 'Sistema' }}</td>
                            <td>{{ $n->asunto }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group" onclick="event.stopPropagation();">
                                    <a href="{{ route('casilla.show', $n->id_casilla) }}" class="btn btn-sm btn-outline-info" title="Leer">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                                No se encontraron notificaciones con los filtros aplicados.
                             </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notificaciones->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $notificaciones->links() }}
        </div>
        @endif
    </div>
</div>
@endsection