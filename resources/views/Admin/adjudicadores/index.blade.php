@extends('admin.app')

@section('title', 'Lista de Adjudicadores')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Tarjeta de estadísticas -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total</h6>
                                    <h3 class="mb-0">{{ $adjudicadores->total() }}</h3>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Activos</h6>
                                    <h3 class="mb-0">
                                        {{ $adjudicadores->filter(function($a) { 
                                            return $a->users->contains(function($u) { return $u->activo == 1; }); 
                                        })->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-user-check fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Con DNI</h6>
                                    <h3 class="mb-0">
                                        {{ $adjudicadores->filter(function($a) { return $a->dni; })->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-id-card fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i> Lista de Adjudicadores
                        </h3>
                        <div>
                            <a href="{{ route('adjudicadores.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i> Nuevo Adjudicador
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
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th width="160" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjudicadores as $adjudicador)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $adjudicador->id }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $adjudicador->nombre }}</strong>
                                        </td>
                                        <td>{{ $adjudicador->apellidos }}</td>
                                        <td>
                                            @if($adjudicador->dni)
                                                <span class="badge bg-dark">{{ $adjudicador->dni }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($adjudicador->telefono)
                                                <a href="tel:{{ $adjudicador->telefono }}" class="text-decoration-none text-success">
                                                    <i class="fas fa-phone me-1"></i> {{ $adjudicador->telefono }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($adjudicador->users as $user)
                                                <span class="badge {{ $user->activo == 1 ? 'bg-success' : 'bg-danger' }}">
                                                    <i class="fas {{ $user->activo == 1 ? 'fa-user-check' : 'fa-user-times' }} me-1"></i>
                                                    {{ $user->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($adjudicador->users as $user)
                                                <a href="mailto:{{ $user->email }}" class="text-decoration-none text-primary">
                                                    <i class="fas fa-envelope me-1"></i> {{ $user->email }}
                                                </a>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                <a href="{{ route('adjudicadores.show', $adjudicador) }}" 
                                                   class="btn btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('adjudicadores.edit', $adjudicador) }}" 
                                                   class="btn btn-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger" 
                                                        title="Eliminar"
                                                        onclick="confirmDelete({{ $adjudicador->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $adjudicador->id }}" 
                                                      action="{{ route('adjudicadores.destroy', $adjudicador) }}" 
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
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-user-slash fa-4x text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">No hay adjudicadores registrados</h5>
                                            <p class="text-muted">Comienza creando tu primer adjudicador.</p>
                                            <a href="{{ route('adjudicadores.create') }}" class="btn btn-danger mt-2">
                                                <i class="fas fa-plus me-1"></i> Crear Adjudicador
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
                                Mostrando {{ $adjudicadores->firstItem() ?? 0 }} - {{ $adjudicadores->lastItem() ?? 0 }} 
                                de {{ $adjudicadores->total() }} registros
                            </span>
                        </div>
                        <div>
                            {{ $adjudicadores->links() }}
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
    .table-hover tbody tr:hover {
        background-color: rgba(173, 43, 46, 0.05);
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
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

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
</script>
@endsection