@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Documentos</h2>
        <a href="{{ route('documentos-gestion.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-file-upload"></i> Subir Documento
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="autoDismissAlert">
            {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('documentos-gestion.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Sección</label>
                        <select name="seccion" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="institucion" {{ request('seccion') == 'institucion' ? 'selected' : '' }}>Institución Arbitral</option>
                            <option value="junta" {{ request('seccion') == 'junta' ? 'selected' : '' }}>Junta de Prevención</option>
                            <option value="convocatorias" {{ request('seccion') == 'convocatorias' ? 'selected' : '' }}>Convocatorias</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Buscar</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Título..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Mes</label>
                        <select name="mes" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Año</label>
                        <select name="anio" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(range(date('Y'), 2020) as $y)
                                <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <a href="{{ route('documentos-gestion.index') }}" class="btn btn-sm btn-outline-secondary w-50" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </a>
                        <button type="submit" class="btn btn-sm btn-dark w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Sección / Categoría</th>
                        <th>Documento</th>
                         <th>Fecha Registro</th>

                        <th>Fecha Publicación</th>
                        <th>Archivo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $doc)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-primary">{{ ucfirst($doc->seccion) }}</span>
                            @if($doc->categoria)
                                <div class="small text-muted mt-1">{{ ucfirst($doc->categoria) }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $doc->titulo }}</div>
                            @if($doc->descripcion)
                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">{{ strip_tags($doc->descripcion) }}</small>
                            @endif
                        </td>
                        <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                        <td>{{ $doc->fecha_publicacion->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> Ver
                            </a>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('documentos-gestion.toggle', $doc->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit"  class="badge border-0 {{ $doc->activo ? 'bg-success' : 'bg-secondary' }}" style="cursor: pointer;" onclick="return confirm('¿Deseas cambiar el estado de visibilidad?')">
                                    {{ $doc->activo ? 'Visible' : 'Oculto' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm border shadow-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                    <li><a class="dropdown-item" href="{{ route('documentos-gestion.edit', $doc->id) }}"><i class="fas fa-edit text-warning me-2"></i> Editar</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('documentos-gestion.destroy', $doc->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('¿Eliminar archivo?')"><i class="fas fa-trash me-2"></i> Eliminar</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay documentos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $documentos->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alert = document.getElementById('autoDismissAlert');
        if (alert) setTimeout(() => new bootstrap.Alert(alert).close(), 3000);
    });
</script>
@endsection