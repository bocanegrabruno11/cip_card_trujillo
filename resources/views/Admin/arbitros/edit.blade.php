@extends('admin.app')

@section('title', 'Editar Árbitro')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i> Editar Árbitro
                        </h3>
                        <a href="{{ route('arbitros.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Por favor corrige los siguientes errores:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('arbitros.update', $arbitro) }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        {{-- ============ DATOS DEL ÁRBITRO ============ --}}
                        <div class="card card-outline card-primary shadow-sm border-0 mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user-tie me-2"></i> Datos del Árbitro
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nombre">Nombre *</label>
                                            <input type="text"
                                                   class="form-control @error('nombre') is-invalid @enderror"
                                                   id="nombre" name="nombre"
                                                   value="{{ old('nombre', $arbitro->nombre) }}"
                                                   placeholder="Ingrese el nombre" required>
                                            @error('nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="apellidos">Apellidos *</label>
                                            <input type="text"
                                                   class="form-control @error('apellidos') is-invalid @enderror"
                                                   id="apellidos" name="apellidos"
                                                   value="{{ old('apellidos', $arbitro->apellidos) }}"
                                                   placeholder="Ingrese los apellidos" required>
                                            @error('apellidos')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="dni">DNI</label>
                                            <input type="text"
                                                   class="form-control @error('dni') is-invalid @enderror"
                                                   id="dni" name="dni"
                                                   value="{{ old('dni', $arbitro->dni) }}"
                                                   placeholder="Número de DNI" maxlength="8">
                                            @error('dni')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="ruc">RUC</label>
                                            <input type="text"
                                                   class="form-control @error('ruc') is-invalid @enderror"
                                                   id="ruc" name="ruc"
                                                   value="{{ old('ruc', $arbitro->ruc) }}"
                                                   placeholder="Número de RUC" maxlength="11">
                                            @error('ruc')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="telefono">Teléfono</label>
                                            <input type="text"
                                                   class="form-control @error('telefono') is-invalid @enderror"
                                                   id="telefono" name="telefono"
                                                   value="{{ old('telefono', $arbitro->telefono) }}"
                                                   placeholder="Número de teléfono">
                                            @error('telefono')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="correo">Correo de Contacto del Árbitro</label>
                                            <input type="email"
                                                   class="form-control @error('correo') is-invalid @enderror"
                                                   id="correo" name="correo"
                                                   value="{{ old('correo', $arbitro->correo) }}"
                                                   placeholder="correo@ejemplo.com">
                                            @error('correo')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="direccion">Dirección</label>
                                            <input type="text"
                                                   class="form-control @error('direccion') is-invalid @enderror"
                                                   id="direccion" name="direccion"
                                                   value="{{ old('direccion', $arbitro->direccion) }}"
                                                   placeholder="Dirección completa">
                                            @error('direccion')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============ DATOS DEL USUARIO ============ --}}
                        @if($usuario)
                        <div class="card card-outline card-success shadow-sm border-0 mb-3">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i> Datos del Usuario (Acceso al sistema)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name">Nombre de Usuario</label>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name"
                                                   value="{{ old('name', $usuario->name) }}"
                                                   placeholder="Usuario para login">
                                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="email">Email de Usuario *</label>
                                            <input type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email"
                                                   value="{{ old('email', $usuario->email) }}"
                                                   placeholder="usuario@ejemplo.com" required>
                                            @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Este es el correo con el que inicia sesión.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password">Nueva Contraseña</label>
                                            <div class="input-group">
                                                <input type="password"
                                                       class="form-control @error('password') is-invalid @enderror"
                                                       id="password" name="password"
                                                       placeholder="Dejar en blanco para no cambiar">
                                                <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword('password', this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password_confirmation">Confirmar Contraseña</label>
                                            <div class="input-group">
                                                <input type="password"
                                                       class="form-control"
                                                       id="password_confirmation" name="password_confirmation"
                                                       placeholder="Repite la nueva contraseña">
                                                <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword('password_confirmation', this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <small>
                                        <strong>Opcional:</strong> Solo se actualizará la contraseña si rellenas ambos campos.
                                        Si quieres cambiar el email de acceso, modifícalo arriba y guarda.
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('arbitros.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-danger { background: linear-gradient(135deg, #AD2B2E 0%, #801a1d 100%); }
    .card { border-radius: 12px; overflow: hidden; }
    .card-header { border-bottom: none; }
    .form-group label { font-weight: 500; color: #495057; }
</style>
@endsection

@section('scripts')
<script>
    // Validaciones de solo números
    document.getElementById('dni')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 8);
    });
    document.getElementById('ruc')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
    });
    document.getElementById('telefono')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    // Mostrar/ocultar contraseñas
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection