@extends('Admin.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-dark fw-bold">
                <i class="fas fa-history text-primary me-2"></i>Historial de Actividad (Logs)
            </h2>
            <p class="text-muted">Registro de las acciones realizadas por los usuarios en el sistema.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label fw-semibold">Fecha Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label fw-semibold">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-3">
                    <label for="rol" class="form-label fw-semibold">Filtrar por Rol</label>
                    <select name="rol" id="rol" class="form-select">
                        <option value="">Todos los Roles</option>
                        @if(isset($roles))
                            @foreach($roles as $r)
                                <option value="{{ $r }}" {{ request('rol') == $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-5 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> Limpiar
                    </a>
                    <button type="submit" formaction="{{ route('admin.logs.export') }}" class="btn btn-success">
                        <i class="fas fa-file-export me-1"></i> Exportar a TXT
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Fecha y Hora</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Módulo / Vista</th>
                            <th scope="col">Acción Realizada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $log->user_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $log->user_role }}</span>
                                </td>
                                <td>{{ $log->view_accessed ?? 'N/A' }}</td>
                                <td>{{ $log->action }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                    No hay registros de actividad para mostrar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
