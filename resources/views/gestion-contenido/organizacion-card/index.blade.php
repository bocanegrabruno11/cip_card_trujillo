@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Organización</h2>
        <a href="{{ route('organizacion-gestion.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-user-plus"></i> Nuevo Miembro
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="autoDismissAlert">
            {{ session('success') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('organizacion-gestion.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Grupo</label>
                        <select name="grupo" class="form-select form-select-sm">
                            <option value="">Todos los grupos</option>
                            <option value="directivo" {{ request('grupo') == 'directivo' ? 'selected' : '' }}>Órgano Directivo</option>
                            <option value="decisorio_presidente" {{ request('grupo') == 'decisorio_presidente' ? 'selected' : '' }}>Decisorio - Presidente</option>
                            <option value="decisorio_miembros" {{ request('grupo') == 'decisorio_miembros' ? 'selected' : '' }}>Decisorio - Miembros</option>
                            <option value="secretaria" {{ request('grupo') == 'secretaria' ? 'selected' : '' }}>Secretaría General</option>
                            <option value="secretarios_arbitrales" {{ request('grupo') == 'secretarios_arbitrales' ? 'selected' : '' }}>Secretarios Arbitrales</option>
                            <option value="apoyo" {{ request('grupo') == 'apoyo' ? 'selected' : '' }}>Personal de Apoyo</option>
                            <option value="administrativo" {{ request('grupo') == 'administrativo' ? 'selected' : '' }}>Soporte Administrativo</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Cargo</label>
                        <input type="text" name="cargo" class="form-control form-control-sm" placeholder="Ej: Decano" value="{{ request('cargo') }}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Mes</label>
                        <select name="mes" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Año</label>
                        <select name="anio" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(range(2020, date('Y')) as $a)
                                <option value="{{ $a }}" {{ request('anio') == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 text-end">
                        <a href="{{ route('organizacion-gestion.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Foto</th>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Grupo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($miembros as $item)
                    <tr>
                        <td class="ps-4">
                            @if($item->ruta_imagen)
                                <img src="{{ asset('storage/' . $item->ruta_imagen) }}" class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;"><i class="fas fa-user"></i></div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $item->nombres }}</td>
                        <td>{{ $item->cargo }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->grupo }}</span></td>
                        <td class="text-center">
                            @if($item->activo)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('organizacion-gestion.toggle', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-icon {{ $item->activo ? 'btn-outline-success' : 'btn-outline-secondary' }}" onclick="return confirm('¿Confirmar cambio de visibilidad?')"><i class="fas {{ $item->activo ? 'fa-eye' : 'fa-eye-slash' }}"></i></button>
                            </form>
                            <a href="{{ route('organizacion-gestion.edit', $item->id) }}" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('organizacion-gestion.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay miembros registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
           
        </div>
        <div class="card-footer bg-white d-flex justify-content-end py-3">
    {{ $miembros->links('pagination::bootstrap-4') }}
</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('autoDismissAlert');
        if (alert) setTimeout(() => new bootstrap.Alert(alert).close(), 3000);
    });
</script>
<style>
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>
@endsection