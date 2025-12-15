@extends('mesa-partes.app')

@section('title', 'Arbitraje')
@section('page-title', 'Registrar Arbitraje')

@section('content')

<!-- Modal obligatorio para info requerida -->
<div class="modal fade" id="infoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información Requerida</h5>
            </div>
            <div class="modal-body">
                <p id="modal-body"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-actualizar">
                    Actualizar Información
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal genérico para mensajes -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="messageModalBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de éxito -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">✓ Registro Exitoso</h5>
            </div>
            <div class="modal-body">
                <p>El arbitraje se ha registrado correctamente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-success-ok">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de error -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">⚠ Error</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="errorModalBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Spinner de carga -->
<div id="loading-spinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<div class="container mt-4">

    <form id="arbitrajeForm">
        @csrf

        <h4>Registrar Arbitraje</h4>

        <!-- DATOS DEL ARBITRAJE -->
        <div class="mb-3">
            <label>Materia</label>
            <input type="text" class="form-control" name="nombre_materia" required>
        </div>

        <div class="mb-3">
            <label>Descripción</label>
            <textarea class="form-control" name="descripcion" required></textarea>
        </div>

        <!-- PERSONAS -->
        <h5>Personas</h5>

        <!-- DEMANDANTE (AUTOMÁTICO) -->
        <div class="mb-2">
            <label>DNI Demandante</label>
            <input type="text" class="form-control" id="dni-demandante" readonly>
        </div>

        <!-- DEMANDADOS -->
        <div id="demandados-container"></div>

        <button type="button" class="btn btn-secondary mb-3" onclick="agregarDemandado()">
            Agregar Demandado
        </button>

        <!-- DOCUMENTO -->
        <div class="mb-3">
            <label>Voucher (JPG)</label>
            <input type="file" class="form-control" id="voucher" accept=".jpg,.jpeg" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Registrar Arbitraje
        </button>
    </form>

</div>

@endsection

@push('scripts')
<script>

let personas = [];
let contadorDemandados = 0;

// Función para mostrar/ocultar spinner
function toggleSpinner(show) {
    const spinner = document.getElementById('loading-spinner');
    spinner.style.display = show ? 'flex' : 'none';
}

// Modal obligatorio con redirección
function showModal(message, redirectUrl) {
    document.getElementById('modal-body').innerText = message;
    const modalElement = document.getElementById('infoModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    document.getElementById('btn-actualizar').addEventListener('click', function() {
        window.location.href = redirectUrl;
    });
}

// Modal genérico para mensajes
function showMessage(title, message) {
    document.getElementById('messageModalTitle').innerText = title;
    document.getElementById('messageModalBody').innerText = message;
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();
}

// Modal de error
function showError(message) {
    document.getElementById('errorModalBody').innerText = message;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

// Modal de éxito
function showSuccess() {
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
    
    document.getElementById('btn-success-ok').addEventListener('click', function() {
        window.location.reload();
    });
}

// Validar DNI: solo números y exactamente 8 dígitos
function validarDNI(input) {
    input.value = input.value.replace(/[^0-9]/g, '').substring(0, 8);
}

// Verificar DNI duplicado
function isDNIDuplicado(dni) {
    return personas.some(persona => persona.dni === dni);
}

// CARGAR PERSONA LOGUEADA
document.addEventListener('DOMContentLoaded', () => {
    toggleSpinner(true);

    fetch('{{ route("persona.buscar") }}')
        .then(response => {
            if (!response.ok) throw new Error('Error en el servidor');
            return response.json();
        })
        .then(data => {
            toggleSpinner(false);

            if (!data.success) {
                showModal(data.message, data.redirect_url);
                return;
            }

            // DEMANDANTE AUTOMÁTICO
            document.getElementById('dni-demandante').value = data.persona.dni;

            personas.push({
                dni: data.persona.dni,
                tipo: 'Demandante'
            });
        })
        .catch(err => {
            toggleSpinner(false);
            showModal("Se requiere actualizar sus datos para poder iniciar un proceso.", "{{ route('persona.actualizar') }}");
        });
});

// AGREGAR DEMANDADO
function agregarDemandado() {
    const container = document.getElementById('demandados-container');
    contadorDemandados++;
    
    const div = document.createElement('div');
    div.classList.add('mb-2', 'demandado-item');
    div.setAttribute('data-id', contadorDemandados);

    div.innerHTML = `
        <div class="input-group">
            <input type="text" 
                   class="form-control dni-demandado" 
                   placeholder="DNI Demandado (8 dígitos)" 
                   maxlength="8"
                   data-id="${contadorDemandados}"
                   oninput="validarDNI(this)">
            <button type="button" class="btn btn-danger" onclick="eliminarDemandado(${contadorDemandados})">
                Eliminar
            </button>
        </div>
        <small class="text-danger" id="error-${contadorDemandados}" style="display: none;"></small>
    `;

    container.appendChild(div);
}

// ELIMINAR DEMANDADO
function eliminarDemandado(id) {
    const item = document.querySelector(`.demandado-item[data-id="${id}"]`);
    if (item) {
        item.remove();
    }
}

// ENVÍO DEL FORMULARIO
document.getElementById('arbitrajeForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Limpiar demandados previos del array (mantener solo demandante)
    personas = personas.filter(p => p.tipo === 'Demandante');

    let hayError = false;
    let mensajesError = [];

    // VALIDAR Y AGREGAR DEMANDADOS
    document.querySelectorAll('.dni-demandado').forEach(input => {
        const dni = input.value.trim();
        const id = input.getAttribute('data-id');
        const errorElement = document.getElementById(`error-${id}`);

        // Limpiar error previo
        errorElement.style.display = 'none';
        errorElement.textContent = '';
        input.classList.remove('is-invalid');

        if (dni) {
            // Validar longitud
            if (dni.length !== 8) {
                errorElement.textContent = 'El DNI debe tener exactamente 8 dígitos';
                errorElement.style.display = 'block';
                input.classList.add('is-invalid');
                hayError = true;
                mensajesError.push('Algunos DNI no tienen 8 dígitos');
                return;
            }

            // Validar duplicado
            if (isDNIDuplicado(dni)) {
                errorElement.textContent = 'Este DNI ya está registrado en el arbitraje';
                errorElement.style.display = 'block';
                input.classList.add('is-invalid');
                hayError = true;
                mensajesError.push('Hay DNI duplicados');
                return;
            }

            personas.push({
                dni: dni,
                tipo: 'Demandado'
            });
        }
    });

    if (hayError) {
        const mensajeUnico = [...new Set(mensajesError)].join('. ');
        showError('Por favor corrija los errores antes de continuar: ' + mensajeUnico);
        return;
    }

    // Validar que haya al menos un demandado
    if (personas.length < 2) {
        showError('Debe agregar al menos un demandado');
        return;
    }

    // Validar archivo
    const file = document.getElementById('voucher').files[0];
    if (!file) {
        showError('Debe cargar el voucher');
        return;
    }

    toggleSpinner(true);

    const formData = new FormData();

    formData.append('nombre_materia', document.querySelector('[name="nombre_materia"]').value);
    formData.append('descripcion', document.querySelector('[name="descripcion"]').value);

    // PROCESO FIJO
    formData.append('nombre_proceso', 'Validacion de Voucher');
    formData.append('descripcion_proceso', 'Se verificara la autenticidad del pago');

    // PERSONAS - Enviar como array con índices
    personas.forEach((persona, index) => {
        formData.append(`personas[${index}][dni]`, persona.dni);
        formData.append(`personas[${index}][tipo]`, persona.tipo);
    });

    // DOCUMENTO
    formData.append('voucher', file);

    fetch('{{ route("arbitraje.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        toggleSpinner(false);
        
        if (data.error) {
            console.error('Error:', data);
            showError(data.detalle || 'Error al registrar el arbitraje');
        } else {
            showSuccess();
        }
    })
    .catch(err => {
        toggleSpinner(false);
        console.error('Error:', err);
        showError('Error de conexión al registrar el arbitraje');
    });
});

</script>
@endpush

@push('styles')
<style>
.is-invalid {
    border-color: #dc3545 !important;
}
</style>
@endpush