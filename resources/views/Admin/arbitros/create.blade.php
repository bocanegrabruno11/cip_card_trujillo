@extends('admin.app')

@section('title', 'Registrar Nuevo Árbitro')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus"></i> Registrar Nuevo Árbitro
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('arbitros.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Por favor corrige los siguientes errores:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('arbitros.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <!-- DATOS DEL ÁRBITRO -->
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-user-tie"></i> Datos del Árbitro
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre">Nombre *</label>
                                            <input type="text" 
                                                   class="form-control @error('nombre') is-invalid @enderror" 
                                                   id="nombre" 
                                                   name="nombre" 
                                                   value="{{ old('nombre') }}" 
                                                   placeholder="Ingrese el nombre"
                                                   required>
                                            @error('nombre')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="apellidos">Apellidos *</label>
                                            <input type="text" 
                                                   class="form-control @error('apellidos') is-invalid @enderror" 
                                                   id="apellidos" 
                                                   name="apellidos" 
                                                   value="{{ old('apellidos') }}" 
                                                   placeholder="Ingrese los apellidos"
                                                   required>
                                            @error('apellidos')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="dni">DNI</label>
                                            <input type="text" 
                                                   class="form-control @error('dni') is-invalid @enderror" 
                                                   id="dni" 
                                                   name="dni" 
                                                   value="{{ old('dni') }}" 
                                                   placeholder="Número de DNI"
                                                   maxlength="8">
                                            @error('dni')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ruc">RUC</label>
                                            <input type="text" 
                                                   class="form-control @error('ruc') is-invalid @enderror" 
                                                   id="ruc" 
                                                   name="ruc" 
                                                   value="{{ old('ruc') }}" 
                                                   placeholder="Número de RUC"
                                                   maxlength="11">
                                            @error('ruc')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono</label>
                                            <input type="text" 
                                                   class="form-control @error('telefono') is-invalid @enderror" 
                                                   id="telefono" 
                                                   name="telefono" 
                                                   value="{{ old('telefono') }}" 
                                                   placeholder="Número de teléfono">
                                            @error('telefono')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="correo">Correo Electrónico</label>
                                            <input type="email" 
                                                   class="form-control @error('correo') is-invalid @enderror" 
                                                   id="correo" 
                                                   name="correo" 
                                                   value="{{ old('correo') }}" 
                                                   placeholder="correo@ejemplo.com">
                                            @error('correo')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <input type="text" 
                                                   class="form-control @error('direccion') is-invalid @enderror" 
                                                   id="direccion" 
                                                   name="direccion" 
                                                   value="{{ old('direccion') }}" 
                                                   placeholder="Dirección completa">
                                            @error('direccion')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DATOS DEL USUARIO -->
                        <div class="card card-outline card-success mt-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-user"></i> Datos del Usuario
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre de Usuario *</label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="Usuario para login"
                                                   required>
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email de Usuario *</label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="usuario@ejemplo.com"
                                                   required>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Contraseña *</label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Mínimo 8 caracteres"
                                                   required>
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirmar Contraseña *</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   placeholder="Repite la contraseña"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> 
                                    <small>El usuario será creado automáticamente con algunos privilegios Árbitro</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('arbitros.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Registrar Árbitro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Validación en tiempo real para DNI
    document.getElementById('dni')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 8);
    });

    // Validación en tiempo real para RUC
    document.getElementById('ruc')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
    });

    // Validación en tiempo real para teléfono
    document.getElementById('telefono')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
    });

    // Mostrar/ocultar contraseña (opcional)
    document.querySelectorAll('input[type="password"]').forEach(input => {
        const wrapper = input.parentElement;
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'btn btn-outline-secondary btn-sm position-absolute';
        toggleBtn.style.right = '10px';
        toggleBtn.style.top = '50%';
        toggleBtn.style.transform = 'translateY(-50%)';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        toggleBtn.onclick = function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        };
        wrapper.style.position = 'relative';
        wrapper.appendChild(toggleBtn);
    });
</script>
@endsection