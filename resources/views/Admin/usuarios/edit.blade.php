@extends('Admin.app')
@section('title', 'Editar Usuario')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 max-w-800 mx-auto">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Editar Usuario</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="editForm" action="{{ route('admin-usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="text-muted mb-3 border-bottom pb-2">Datos de la Persona</h5>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label>Nombre Registrado</label>
                        <input type="text" class="form-control" value="{{ $usuario->name }}" readonly disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>DNI Registrado</label>
                        <input type="text" class="form-control" value="{{ $usuario->persona->dni ?? 'Sin DNI vinculado' }}" readonly disabled>
                    </div>
                </div>

                <h5 class="text-muted mb-3 border-bottom pb-2">Credenciales y Accesos</h5>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Correo Electrónico (Acceso)</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Rol del Sistema</label>
                        <select name="rol" class="form-select" required>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}" {{ $usuario->hasRole($rol->name) ? 'selected' : '' }}>
                                    {{ strtoupper(str_replace('_', ' ', $rol->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label>Nueva Contraseña <small class="text-danger">(Dejar en blanco si no desea cambiarla)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Escriba la nueva contraseña" minlength="8">
                    </div>
                </div>

                <button type="submit" id="btnSubmit" class="btn btn-warning">
                    <i class="fas fa-sync"></i> <span>Actualizar Usuario</span>
                </button>
                <a href="{{ route('admin-usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editForm');
        const btnSubmit = document.getElementById('btnSubmit');

        if (form) {
            form.addEventListener('submit', function () {
                // Deshabilitamos el botón
                btnSubmit.disabled = true;
                
                // Cambiamos el texto y el ícono para mostrar que está cargando
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Actualizando...</span>';
            });
        }
    });
</script>
@endsection