@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Tarifas y Escalas</h2>
        <div>
            <a href="{{ route('tarifas_config.index') }}" class="btn btn-dark shadow-sm me-2">
                <i class="fas fa-cogs"></i> Variables
            </a>
            
            <a href="{{ route('calculadoras-gestion.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle"></i> Nueva Escala
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 2. ALERTA DE ADVERTENCIA (AMARILLO) --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 3. ALERTA DE ERROR (ROJO) --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-ban me-1"></i> 
            {{-- Muestra el primer error o un mensaje genérico --}}
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('calculadoras-gestion.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Filtrar por Tipo</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">Todas las tablas</option>
                            <option value="arbitro_unico" {{ request('tipo') == 'arbitro_unico' ? 'selected' : '' }}>Honorarios Árbitro Único</option>
                            <option value="tribunal_arbitral" {{ request('tipo') == 'tribunal_arbitral' ? 'selected' : '' }}>Honorarios Tribunal Arbitral</option>
                            <option value="gastos_administrativos" {{ request('tipo') == 'gastos_administrativos' ? 'selected' : '' }}>Gastos Administrativos</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <a href="{{ route('calculadoras-gestion.index') }}" class="btn btn-sm btn-outline-secondary w-50" title="Limpiar"><i class="fas fa-eraser"></i></a>
                        <button type="submit" class="btn btn-sm btn-dark w-100"><i class="fas fa-search"></i> Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tipo</th>
                        <th class="text-center">Rango</th>
                        <th class="text-end">Desde (S/.)</th>
                        <th class="text-end">Hasta (S/.)</th>
                        <th class="text-end">Monto Fijo</th>
                        <th class="text-center">% Exceso</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifas as $item)
                    <tr>
                        <td class="ps-4">
                            @if($item->tipo == 'arbitro_unico') <span class="badge bg-primary">Árbitro Único</span>
                            @elseif($item->tipo == 'tribunal_arbitral') <span class="badge bg-success">Tribunal</span>
                            @else <span class="badge bg-secondary">Gastos Admin</span> @endif
                        </td>
                        <td class="text-center fw-bold">{{ $item->rango_letra }}</td>
                        <td class="text-end">{{ number_format($item->monto_min, 2) }}</td>
                        <td class="text-end">
                            {{ $item->monto_max ? number_format($item->monto_max, 2) : 'A más' }}
                        </td>
                        <td class="text-end fw-bold">S/. {{ number_format($item->monto_fijo, 2) }}</td>
                        <td class="text-center">
                            @if($item->porcentaje_exceso > 0)
                                <span class="badge bg-warning text-dark">{{ $item->porcentaje_exceso }}%</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('calculadoras-gestion.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('calculadoras-gestion.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('calculadoras-gestion.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta escala?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay tarifas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $tarifas->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionamos TODOS los elementos que tengan la clase 'alert-dismissible'
            const alerts = document.querySelectorAll('.alert-dismissible');
            
            alerts.forEach(function(alertNode) {
                // Esperar 4 segundos (4000 ms) para dar tiempo a leer
                setTimeout(function() {
                    // Verificar si la alerta sigue existiendo antes de intentar cerrarla
                    if (alertNode) {
                        const bsAlert = new bootstrap.Alert(alertNode);
                        bsAlert.close();
                    }
                }, 4000); 
            });
        });
    </script>
@endsection