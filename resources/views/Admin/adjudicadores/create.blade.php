@extends('admin.app')

@section('title', 'Registrar Nuevo Adjudicador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-plus me-2"></i> Registrar Nuevo Adjudicador
                        </h3>
                        <div>
                            <a href="{{ route('adjudicadores.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i> Por favor corrige los siguientes errores:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('adjudicadores.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <!-- DATOS DEL ADJUDICADOR -->
                        <div class="card card-outline card-primary mb-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-user-tie me-2"></i> Datos del Adjudicador
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre">Nombre <span class="text-danger">*</span></label>
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
                                            <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
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
                        <div class="card card-outline card-success mb-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-user me-2"></i> Datos del Usuario
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre de Usuario <span class="text-danger">*</span></label>
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
                                            <label for="email">Email de Usuario <span class="text-danger">*</span></label>
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
                                            <label for="password">Contraseña <span class="text-danger">*</span></label>
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
                                            <label for="password_confirmation">Confirmar Contraseña <span class="text-danger">*</span></label>
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
                                    <i class="fas fa-info-circle me-2"></i> 
                                    <small>El usuario será creado automáticamente con privilegios de Adjudicador</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('adjudicadores.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-1"></i> Registrar Adjudicador
                            </button>
                        </div>
                    </form>
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
    .shadow-lg {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
    }
    .text-danger {
        color: #AD2B2E !important;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dniInput = document.getElementById('dni');
        if (dniInput) {
            dniInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });
        }

        const rucInput = document.getElementById('ruc');
        if (rucInput) {
            rucInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 11);
            });
        }

        const telefonoInput = document.getElementById('telefono');
        if (telefonoInput) {
            telefonoInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '');
            });
        }
    });
</script>
@endpush
@endsection