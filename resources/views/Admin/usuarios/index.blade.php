@extends('Admin.app')
@section('title', 'Mantenedor de Usuarios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800">Gestión de Usuarios</h2>
        <!-- <a href="{{ route('admin-usuarios.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Usuario</a> -->
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin-usuarios.index') }}" method="GET" class="row mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por Nombre o DNI..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="rol" class="form-select">
                        <option value="">-- Todos los roles --</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->name }}" {{ request('rol') == $rol->name ? 'selected' : '' }}>
                                {{ strtoupper(str_replace('_', ' ', $rol->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filtrar</button>
                    <a href="{{ route('admin-usuarios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->persona->dni ?? 'N/A' }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $role->name)) }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('admin-usuarios.edit', $user->id) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                
                                {{-- CONDICIONAL DE SEGURIDAD: Solo muestra el botón si NO es admin ni gestor --}}
                                @if(!$user->hasAnyRole(['admin', 'gestor_contenido']))
                                    
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center py-4">
                                                    <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                                                    <p class="mb-0">¿Estás seguro que deseas eliminar al usuario <br><strong>{{ $user->name }}</strong>?</p>
                                                    <p class="text-muted small mt-2">Esta acción es irreversible y desactivará su acceso al sistema.</p>
                                                </div>
                                                <div class="modal-footer justify-content-center border-top-0">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i> Cancelar
                                                    </button>
                                                    <form action="{{ route('admin-usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash me-1"></i> Sí, eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                @endif
                                {{-- FIN DEL CONDICIONAL --}}

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron usuarios.</td>
                        </tr>
                        @endforelse 
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
</div>
@endsection