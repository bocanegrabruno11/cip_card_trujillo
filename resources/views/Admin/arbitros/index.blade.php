@extends('admin.app')

@section('title', 'Lista de Árbitros')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Tarjeta de estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total</h6>
                                    <h3 class="mb-0">{{ $arbitros->total() }}</h3>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Activos</h6>
                                    <h3 class="mb-0">
                                        {{ $arbitros->filter(function($a) { 
                                            return $a->users->contains(function($u) { return $u->activo == 1; }); 
                                        })->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-user-check fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Con DNI</h6>
                                    <h3 class="mb-0">
                                        {{ $arbitros->filter(function($a) { return $a->dni; })->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-id-card fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Con Casos</h6>
                                    <h3 class="mb-0">
                                        {{ $arbitros->filter(function($a) { 
                                            return \App\Models\ProcesoArbitrajePersona::where('dni', $a->dni)
                                                ->where('tipo', 'Arbitro')
                                                ->exists();
                                        })->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-gavel fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-gavel me-2"></i> Lista de Árbitros
                        </h3>
                        <div>
                            <a href="{{ route('arbitros.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i> Nuevo Árbitro
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                <div>
                                    <strong>¡Éxito!</strong> {{ session('success') }}
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle fa-2x me-3 text-danger"></i>
                                <div>
                                    <strong>¡Error!</strong> {{ session('error') }}
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="tablaArbitros">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Casos</th>
                                    <th width="160" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($arbitros as $arbitro)
                                    @php
                                        $casosCount = \App\Models\ProcesoArbitrajePersona::where('dni', $arbitro->dni)
                                            ->where('tipo', 'Arbitro')
                                            ->count();
                                        $userActivo = $arbitro->users->contains(function($u) { return $u->activo == 1; });
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $arbitro->id }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $arbitro->nombre }}</strong>
                                        </td>
                                        <td>{{ $arbitro->apellidos }}</td>
                                        <td>
                                            @if($arbitro->dni)
                                                <span class="badge bg-dark">{{ $arbitro->dni }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($arbitro->telefono)
                                                <a href="tel:{{ $arbitro->telefono }}" class="text-decoration-none text-success">
                                                    <i class="fas fa-phone me-1"></i> {{ $arbitro->telefono }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($arbitro->users as $user)
                                                <span class="badge {{ $user->activo == 1 ? 'bg-success' : 'bg-danger' }}">
                                                    <i class="fas {{ $user->activo == 1 ? 'fa-user-check' : 'fa-user-times' }} me-1"></i>
                                                    {{ $user->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($arbitro->users as $user)
                                                <a href="mailto:{{ $user->email }}" class="text-decoration-none text-primary">
                                                    <i class="fas fa-envelope me-1"></i> {{ $user->email }}
                                                </a>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            @if($casosCount > 0)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-gavel me-1"></i> {{ $casosCount }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-folder-open me-1"></i> 0
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                <a href="{{ route('arbitros.show', $arbitro) }}" 
                                                   class="btn btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('arbitros.edit', $arbitro) }}" 
                                                   class="btn btn-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger" 
                                                        title="Eliminar"
                                                        onclick="confirmDelete({{ $arbitro->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $arbitro->id }}" 
                                                      action="{{ route('arbitros.destroy', $arbitro) }}" 
                                                      method="POST" 
                                                      style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-user-slash fa-4x text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">No hay árbitros registrados</h5>
                                            <p class="text-muted">Comienza creando tu primer árbitro.</p>
                                            <a href="{{ route('arbitros.create') }}" class="btn btn-danger mt-2">
                                                <i class="fas fa-plus me-1"></i> Crear Árbitro
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                Mostrando {{ $arbitros->firstItem() ?? 0 }} - {{ $arbitros->lastItem() ?? 0 }} 
                                de {{ $arbitros->total() }} registros
                            </span>
                        </div>
                        <div>
                            {{ $arbitros->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-danger {
        background: linear-gradient(135deg, #AD2B2E 0%, #801a1d 100%);
    }
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        border-bottom: none;
    }
    .table th {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
        border-radius: 6px;
    }
    .btn-group .btn {
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    .btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 2;
    }
    .btn-group .btn:not(:first-child) {
        border-left: 1px solid rgba(255,255,255,0.2);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(173, 43, 46, 0.05);
        cursor: pointer;
    }
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
    }
    .shadow-lg {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
    }
    .alert {
        border-radius: 10px;
        padding: 16px 20px;
    }
    .pagination {
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #AD2B2E;
        border-color: #AD2B2E;
    }
    .page-link {
        color: #AD2B2E;
    }
    .page-link:hover {
        color: #801a1d;
    }
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            gap: 10px;
        }
        .card-header .d-flex {
            flex-direction: column;
            width: 100%;
        }
        .card-header .btn {
            width: 100%;
        }
        .table-responsive {
            font-size: 13px;
        }
        .btn-group {
            flex-wrap: wrap;
            gap: 4px;
        }
        .btn-group .btn {
            flex: 1;
            min-width: 30px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // SweetAlert2 para mensajes de éxito/error
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 4000,
            showConfirmButton: true,
            confirmButtonColor: '#28a745'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            timer: 4000,
            showConfirmButton: true,
            confirmButtonColor: '#dc3545'
        });
    @endif

    // Tooltip para los botones
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection