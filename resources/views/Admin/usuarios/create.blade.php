@extends('layouts.admin')
@section('title', 'Crear Usuario')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 max-w-800 mx-auto">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registrar Nuevo Usuario</h6>
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

            <form action="{{ route('admin.usuarios.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nombre Completo</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>DNI</label>
                        <input type="text" name="dni" class="form-control" value="{{ old('dni') }}" maxlength="15" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label>Asignar Rol</label>
                        <select name="rol" class="form-select" required>
                            <option value="">-- Seleccione un rol --</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}">{{ strtoupper(str_replace('_', ' ', $rol->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Usuario</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection