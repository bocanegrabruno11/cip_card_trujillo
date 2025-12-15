@extends('mesa-partes.app')

@section('title', 'Actualizar Información Personal - CIP')

@section('page-title', 'Actualizar Información')

@push('styles')
<style>
    /* Estilos del formulario */
    .form-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        max-width: 700px;
        width: 100%;
        padding: 40px;
        animation: slideIn 0.5s ease-out;
        margin: 0 auto;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-header h1 {
        color: var(--cip-red);
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .form-header p {
        color: #666;
        font-size: 14px;
    }

    .info-badge {
        display: inline-block;
        padding: 8px 16px;
        background: linear-gradient(135deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
        color: white;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background-color: #d4edda;
        border-left: 4px solid #28a745;
        color: #155724;
    }

    .alert-error {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    .alert-icon {
        font-size: 18px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 600;
        font-size: 14px;
    }

    .form-group label .required {
        color: var(--cip-red);
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--cip-red);
        box-shadow: 0 0 0 3px rgba(173, 43, 46, 0.1);
    }

    .form-control.error {
        border-color: #dc3545;
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    .btn-submit {
        width: 100%;
        padding: 14px 20px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
        color: white;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(173, 43, 46, 0.3);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .main-content { 
        padding: 30px; 
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media(max-width: 991px) {
        .main-content {
            padding: 20px;
        }
        .form-container {
            padding: 25px;
        }
    }

    @media(max-width: 576px) {
        .form-header h1 {
            font-size: 24px;
        }
        .form-container {
            padding: 20px;
        }
    }
</style>
@endpush

@section('sidebar-menu')
    <!-- Aquí podrías agregar items de menú específicos para esta vista si fuera necesario -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h1>{{ $persona ? 'Actualizar' : 'Registrar' }} Información Personal</h1>
            <p>{{ $persona ? 'Modifica tus datos personales' : 'Completa tu perfil con tus datos' }}</p>
        </div>

        @if($persona)
            <span class="info-badge"><i class="fas fa-check-circle me-1"></i> Registro encontrado</span>
        @else
            <span class="info-badge"><i class="fas fa-edit me-1"></i> Nuevo registro</span>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ $persona ? route('persona.update') : route('persona.store') }}" method="POST" id="personaForm">
            @csrf
            @if($persona)
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="dni">DNI <span class="required">*</span></label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="dni" 
                    name="dni" 
                    value="{{ old('dni', $persona->dni ?? '') }}"
                    maxlength="8"
                    required
                    placeholder="Ingrese su DNI de 8 dígitos"
                >
                <span class="error-message" id="dni-error">El DNI debe tener 8 dígitos numéricos</span>
            </div>

            <div class="form-group">
                <label for="correo_contacto">Correo de Contacto <span class="required">*</span></label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="correo_contacto" 
                    name="correo_contacto" 
                    value="{{ old('correo_contacto', $persona->correo_contacto ?? '') }}"
                    required
                    placeholder="ejemplo@correo.com"
                >
                <span class="error-message" id="correo-error">Ingrese un correo válido</span>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="direccion" 
                    name="direccion" 
                    value="{{ old('direccion', $persona->direccion ?? '') }}"
                    maxlength="200"
                    placeholder="Ingrese su dirección"
                >
            </div>

            <div class="form-group">
                <label for="celular">Celular</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="celular" 
                    name="celular" 
                    value="{{ old('celular', $persona->celular ?? '') }}"
                    maxlength="9"
                    placeholder="Ingrese su número de celular (9 dígitos)"
                >
                <span class="error-message" id="celular-error">El celular debe tener 9 dígitos</span>
            </div>

            <button type="submit" class="btn-submit">
                <i class="{{ $persona ? 'fas fa-sync-alt' : 'fas fa-save' }} me-2"></i>
                {{ $persona ? 'Actualizar Información' : 'Guardar Información' }}
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validación del formulario
    document.getElementById('personaForm').addEventListener('submit', function(e) {
        let isValid = true;

        // Validar DNI
        const dni = document.getElementById('dni');
        const dniError = document.getElementById('dni-error');
        if (!/^\d{8}$/.test(dni.value)) {
            dni.classList.add('error');
            dniError.classList.add('show');
            isValid = false;
        } else {
            dni.classList.remove('error');
            dniError.classList.remove('show');
        }

        // Validar Correo
        const correo = document.getElementById('correo_contacto');
        const correoError = document.getElementById('correo-error');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo.value)) {
            correo.classList.add('error');
            correoError.classList.add('show');
            isValid = false;
        } else {
            correo.classList.remove('error');
            correoError.classList.remove('show');
        }

        // Validar Celular (solo si tiene valor)
        const celular = document.getElementById('celular');
        const celularError = document.getElementById('celular-error');
        if (celular.value && !/^\d{9}$/.test(celular.value)) {
            celular.classList.add('error');
            celularError.classList.add('show');
            isValid = false;
        } else {
            celular.classList.remove('error');
            celularError.classList.remove('show');
        }

        if (!isValid) {
            e.preventDefault();
            
            // Mostrar alerta general de error
            if (!document.querySelector('.alert-error.general')) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-error general';
                alertDiv.innerHTML = `
                    <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <span>Por favor, corrija los errores en el formulario antes de enviar.</span>
                `;
                const formHeader = document.querySelector('.form-header');
                formHeader.parentNode.insertBefore(alertDiv, formHeader.nextSibling);
            }
        }
    });

    // Permitir solo números en DNI
    document.getElementById('dni').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
    });

    // Permitir solo números en Celular
    document.getElementById('celular').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
    });

    // Remover mensaje de error cuando el usuario empiece a escribir
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error');
            const errorId = this.id + '-error';
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.remove('show');
            }
            
            // Remover alerta general si existe
            const generalAlert = document.querySelector('.alert-error.general');
            if (generalAlert) {
                generalAlert.remove();
            }
        });
    });

    // Verificar si hay campos con errores cuando se carga la página
    document.addEventListener('DOMContentLoaded', function() {
        // Si hay errores del servidor, marcamos los campos
        @if($errors->any())
            @error('dni')
                document.getElementById('dni').classList.add('error');
                document.getElementById('dni-error').classList.add('show');
            @enderror
            
            @error('correo_contacto')
                document.getElementById('correo_contacto').classList.add('error');
                document.getElementById('correo-error').classList.add('show');
            @enderror
            
            @error('celular')
                document.getElementById('celular').classList.add('error');
                document.getElementById('celular-error').classList.add('show');
            @enderror
        @endif
    });
</script>
@endpush