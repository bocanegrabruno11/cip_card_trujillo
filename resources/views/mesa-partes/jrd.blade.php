@extends('mesa-partes.app')

@section('title', 'JPRD')
@section('page-title', 'Registrar JPRD')

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
                <p>El JPRD se ha registrado correctamente.</p>
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

    <form id="jrdForm">
        @csrf

        <h4>Registrar JPRD</h4>

        <!-- DATOS DEL JRD -->
        <div class="mb-3">
            <label>Materia <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_materia" required>
        </div>

        <div class="mb-3">
            <label>Descripción <span class="text-danger">*</span></label>
            <textarea class="form-control" name="descripcion" rows="3" required></textarea>
        </div>

        <!-- PERSONAS -->
        <h5 class="mt-4 mb-3">Personas Involucradas</h5>

        <!-- SOLICITANTE (AUTOMÁTICO) -->
        <div class="mb-2">
            <label>DNI Solicitante</label>
            <input type="text" class="form-control" id="dni-solicitante" readonly>
        </div>

        <!-- PARTES -->
        <div id="partes-container"></div>

        <button type="button" class="btn btn-secondary mb-3" onclick="agregarParte()">
            <i class="fas fa-user-plus me-1"></i> Agregar Parte
        </button>

        <!-- DOCUMENTACIÓN -->
        <h5 class="mt-4 mb-3">Documentación</h5>

        <!-- Voucher (archivo) -->
        <div class="mb-4">
            <label for="voucher" class="form-label">Voucher de Pago <span class="text-danger">*</span></label>
            <div class="file-upload-area p-3 border rounded" onclick="document.getElementById('voucher').click()" style="cursor: pointer;">
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <p class="mb-1">Haz clic para subir el voucher</p>
                    <p class="text-muted small mb-2">Formatos aceptados: JPG, JPEG (Máx. 5MB)</p>
                    <div id="voucherFileName" class="text-primary fw-bold">Ningún archivo seleccionado</div>
                </div>
            </div>
            <input type="file" class="form-control d-none" id="voucher" name="voucher" accept=".jpg,.jpeg" required>
        </div>

        <!-- Link de Google Drive (Opcional) -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="drive_link" class="form-label">Enlace de Google Drive (Opcional)</label>
                    <input type="url" class="form-control" id="drive_link" name="drive_link" placeholder="https://drive.google.com/...">
                    <small class="text-muted">Enlace a documento adicional en Drive</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nombre_documento_link" class="form-label">Nombre del Documento en Drive</label>
                    <input type="text" class="form-control" id="nombre_documento_link" name="nombre_documento_link" placeholder="Ej: Contrato firmado, Acuerdo, etc.">
                    <small class="text-muted">Nombre que identificará el documento</small>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                <strong>Nota:</strong> El voucher de pago es obligatorio. El enlace de Google Drive es opcional pero si lo proporcionas, debes darle un nombre descriptivo.
            </small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i> Registrar JRD
        </button>
    </form>

</div>

@endsection

@push('scripts')
<script>

let personas = [];
let contadorPartes = 0;

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

            // SOLICITANTE AUTOMÁTICO
            document.getElementById('dni-solicitante').value = data.persona.dni;

            personas.push({
                dni: data.persona.dni,
                tipo: 'Solicitante'
            });
        })
        .catch(err => {
            toggleSpinner(false);
            showModal("Se requiere actualizar sus datos para poder iniciar un proceso.", "{{ route('persona.actualizar') }}");
        });

    // Mostrar nombre del voucher seleccionado
    document.getElementById('voucher').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Ningún archivo seleccionado';
        document.getElementById('voucherFileName').textContent = fileName;
        
        if (e.target.files[0] && e.target.files[0].size > 5 * 1024 * 1024) {
            alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
            e.target.value = '';
            document.getElementById('voucherFileName').textContent = 'Ningún archivo seleccionado';
        }
    });
});

// AGREGAR PARTE
function agregarParte() {
    const container = document.getElementById('partes-container');
    contadorPartes++;
    
    const div = document.createElement('div');
    div.classList.add('mb-2', 'parte-item');
    div.setAttribute('data-id', contadorPartes);

    div.innerHTML = `
        <div class="input-group">
            <select class="form-select" style="max-width: 150px;" name="tipo_parte" data-id="${contadorPartes}">
                <option value="Demandado">Demandado</option>
                <option value="Contraparte">Contraparte</option>
                <option value="Tercero">Tercero</option>
            </select>
            <input type="text" 
                   class="form-control dni-parte" 
                   placeholder="DNI (8 dígitos)" 
                   maxlength="8"
                   data-id="${contadorPartes}"
                   oninput="validarDNI(this)">
            <button type="button" class="btn btn-outline-danger" onclick="eliminarParte(${contadorPartes})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <small class="text-danger" id="error-${contadorPartes}" style="display: none;"></small>
    `;

    container.appendChild(div);
}

// ELIMINAR PARTE
function eliminarParte(id) {
    const item = document.querySelector(`.parte-item[data-id="${id}"]`);
    if (item) {
        item.remove();
    }
}

// ENVÍO DEL FORMULARIO
document.getElementById('jrdForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Limpiar partes previas del array (mantener solo solicitante)
    personas = personas.filter(p => p.tipo === 'Solicitante');

    let hayError = false;
    let mensajesError = [];

    // VALIDAR Y AGREGAR PARTES
    document.querySelectorAll('.parte-item').forEach(item => {
        const id = item.getAttribute('data-id');
        const input = item.querySelector('.dni-parte');
        const select = item.querySelector('select[name="tipo_parte"]');
        const dni = input.value.trim();
        const tipo = select ? select.value : 'Demandado';
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
                errorElement.textContent = 'Este DNI ya está registrado en el JRD';
                errorElement.style.display = 'block';
                input.classList.add('is-invalid');
                hayError = true;
                mensajesError.push('Hay DNI duplicados');
                return;
            }

            personas.push({
                dni: dni,
                tipo: tipo
            });
        }
    });

    if (hayError) {
        const mensajeUnico = [...new Set(mensajesError)].join('. ');
        showError('Por favor corrija los errores antes de continuar: ' + mensajeUnico);
        return;
    }

    // Validar que haya al menos una parte
    if (personas.length < 2) {
        showError('Debe agregar al menos una parte');
        return;
    }

    // Validar archivo
    const file = document.getElementById('voucher').files[0];
    if (!file) {
        showError('Debe cargar el voucher');
        return;
    }

    // Validar link de Drive si se proporciona
    const driveLink = document.getElementById('drive_link').value;
    const driveName = document.getElementById('nombre_documento_link').value;
    
    if (driveLink && !driveLink.includes('drive.google.com')) {
        showError('Por favor, ingresa un enlace válido de Google Drive.');
        return;
    }
    
    if ((driveLink && !driveName.trim()) || (!driveLink && driveName.trim())) {
        showError('Si ingresas un enlace de Drive, debes ponerle un nombre, y viceversa.');
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

    // DOCUMENTO - Voucher
    formData.append('voucher', file);

    // DOCUMENTO - Link de Drive (si existe)
    if (driveLink) {
        formData.append('drive_link', driveLink);
        formData.append('nombre_documento_link', driveName);
    }

    fetch('{{ route("jrd.store") }}', {
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
            showError(data.detalle || 'Error al registrar el JRD');
        } else {
            showSuccess();
        }
    })
    .catch(err => {
        toggleSpinner(false);
        console.error('Error:', err);
        showError('Error de conexión al registrar el JRD');
    });
});

</script>
@endpush

@push('styles')
<style>
.is-invalid {
    border-color: #dc3545 !important;
}

.file-upload-area {
    border: 2px dashed #dee2e6 !important;
    background-color: #f8f9fa;
    transition: all 0.3s;
}

.file-upload-area:hover {
    border-color: #0d6efd !important;
    background-color: #e9ecef;
}

.parte-item {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.btn-outline-danger:hover {
    transform: scale(1.1);
    transition: transform 0.2s;
}

.form-label span {
    color: #dc3545;
}
</style>
@endpush