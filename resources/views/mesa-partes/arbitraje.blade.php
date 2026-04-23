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
                <button type="button" class="btn btn-success" id="btn-success-ok">Aceptar</button>
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

        <h4 class="mb-4">Registrar Arbitraje</h4>

        <!-- ====== DATOS DEL ARBITRAJE ====== -->
        <h5 class="mb-3">Datos del Arbitraje</h5>

        <div class="card p-3 mb-4" style="background:#f8f9fa; border:1.5px solid #dee2e6; border-radius:8px;">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Materia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nombre_materia" placeholder="Ej: Incumplimiento de contrato" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Cuantía
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="cuantia">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Tasa de Solicitud
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="tasa_solicitud">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Designación Arbitral <span class="text-muted small">(opcional)</span>
                    </label>
                    <input type="text" class="form-control" name="designacion_arbitral"
                           placeholder="Ej: Árbitro único, Tribunal arbitral, etc.">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Pretensiones <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="pretenciones" rows="3"
                              placeholder="Describa las pretensiones del arbitraje..." required></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Controversia <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="controversia" rows="3"
                              placeholder="Describa la controversia del arbitraje..." required></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Fundamentos de hecho <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="fundamentos_hecho" rows="3"
                              placeholder="Describa los fundamentos de hecho del arbitraje..." required></textarea>
                </div>
            </div>
        </div>

        <!-- ====== DEMANDANTE ====== -->
        <h5 class="mb-3">Demandante</h5>

        <div class="card card-demandante mb-4 p-3">
            <div class="row g-2">

                <div class="col-md-3">
                    <label class="form-label">DNI</label>
                    <input type="text" class="form-control" id="demandante-dni" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Nombres <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="demandante-nombres" placeholder="Nombres" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="demandante-apellidos" placeholder="Apellidos" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">RUC <span class="text-muted small">(opcional)</span></label>
                    <input type="text" class="form-control" id="demandante-ruc"
                           placeholder="20123456789" maxlength="11" oninput="soloNumeros(this, 11)">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo <span class="text-muted small">(opcional)</span></label>
                    <input type="email" class="form-control" id="demandante-correo" placeholder="correo@ejemplo.com">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Teléfono <span class="text-muted small">(opcional)</span></label>
                    <input type="text" class="form-control" id="demandante-telefono"
                           placeholder="987654321" maxlength="9" oninput="soloNumeros(this, 9)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Domicilio</label>
                    <input type="text" class="form-control" id="demandante-direccion"
                           placeholder="Av Mansiche 250">
                </div>
            </div>
        </div>

        <!-- ====== DEMANDADOS ====== -->
        <h5 class="mb-3">Demandados</h5>

        <div id="demandados-container"></div>

        <button type="button" class="btn btn-secondary mb-4" onclick="agregarDemandado()">
            <i class="fas fa-user-plus me-1"></i> Agregar Demandado
        </button>

        <!-- ====== DOCUMENTACIÓN ====== -->
        <h5 class="mt-2 mb-3">Documentación</h5>

        <!-- Voucher (obligatorio) -->
        <div class="mb-4">
            <label for="voucher" class="form-label">Voucher de Pago <span class="text-danger">*</span></label>
            <div class="file-upload-area p-3 border rounded" onclick="document.getElementById('voucher').click()" style="cursor: pointer;">
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <p class="mb-1">Haz clic para subir el voucher</p>
                    <p class="text-muted small mb-2">Formatos aceptados: JPG, JPEG, PNG, PDF (Máx. 20MB)</p>
                    <div id="voucherFileName" class="text-primary fw-bold">Ningún archivo seleccionado</div>
                </div>
            </div>
            <input type="file" class="form-control d-none" id="voucher" name="voucher" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>

        <!-- Nombre descriptivo para el voucher -->
        <div class="mb-3">
            <label for="nombre_documento" class="form-label">Nombre del Voucher <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre_documento" name="nombre_documento"
                   placeholder="Ej: Voucher de pago - Tasa de solicitud" value="Voucher de Pago" required>
            <small class="text-muted">Nombre que identificará este voucher en el sistema</small>
        </div>

        <!-- Link de Google Drive (Opcional) -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="drive_link" class="form-label">Google Drive Para Anexos</label>
                    <input type="url" class="form-control" id="drive_link" name="drive_link"
                           placeholder="https://drive.google.com/...">
                    <small class="text-muted">Enlace a documento adicional en Drive para anexos</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nombre_documento_link" class="form-label">Nombre del Documento en Drive</label>
                    <input type="text" class="form-control" id="nombre_documento_link" name="nombre_documento_link"
                           placeholder="Ej: Contrato firmado, Acuerdo, etc.">
                    <small class="text-muted">Nombre que identificará el documento</small>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                <strong>Nota:</strong> El voucher de pago es obligatorio y se guardará como tipo "voucher" para su posterior verificación.
            </small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i> Registrar Arbitraje
        </button>
    </form>

</div>

@endsection

@push('scripts')
<script>

let dniDemandante = '';
let contadorDemandados = 0;

function toggleSpinner(show) {
    document.getElementById('loading-spinner').style.display = show ? 'flex' : 'none';
}

function showModal(message, redirectUrl) {
    document.getElementById('modal-body').innerText = message;
    const modal = new bootstrap.Modal(document.getElementById('infoModal'));
    modal.show();
    document.getElementById('btn-actualizar').addEventListener('click', () => {
        window.location.href = redirectUrl;
    });
}

function showError(message) {
    document.getElementById('errorModalBody').innerText = message;
    new bootstrap.Modal(document.getElementById('errorModal')).show();
}

function showSuccess() {
    new bootstrap.Modal(document.getElementById('successModal')).show();
    document.getElementById('btn-success-ok').addEventListener('click', () => {
        window.location.reload();
    });
}

function soloNumeros(input, maxLen) {
    input.value = input.value.replace(/[^0-9]/g, '').substring(0, maxLen);
}

function isDNIDuplicado(dni, lista) {
    return lista.some(p => p.dni === dni);
}

document.addEventListener('DOMContentLoaded', () => {
    toggleSpinner(true);

    fetch('{{ route("persona.buscar") }}')
        .then(r => {
            if (!r.ok) throw new Error('Error en el servidor');
            return r.json();
        })
        .then(data => {
            toggleSpinner(false);
            if (!data.success) {
                showModal(data.message, data.redirect_url);
                return;
            }
            dniDemandante = data.persona.dni;
            document.getElementById('demandante-dni').value = dniDemandante;

            if (data.persona.nombres) {
                document.getElementById('demandante-nombres').value = data.persona.nombres;
            }
            if (data.persona.apellidos) {
                document.getElementById('demandante-apellidos').value = data.persona.apellidos;
            }
            if (data.persona.email) {
                document.getElementById('demandante-correo').value = data.persona.email;
            }
            if (data.persona.telefono) {
                document.getElementById('demandante-telefono').value = data.persona.telefono;
            }
        })
        .catch(() => {
            toggleSpinner(false);
            showModal(
                'Se requiere actualizar sus datos para poder iniciar un proceso.',
                '{{ route("persona.actualizar") }}'
            );
        });

    document.getElementById('voucher').addEventListener('change', function () {
        const file = this.files[0];
        document.getElementById('voucherFileName').textContent = file ? file.name : 'Ningún archivo seleccionado';

        if (file && file.size > 20 * 1024 * 1024) {
            showError('El archivo supera los 20MB permitidos.');
            this.value = '';
            document.getElementById('voucherFileName').textContent = 'Ningún archivo seleccionado';
        }
    });
});

function agregarDemandado() {
    contadorDemandados++;
    const id = contadorDemandados;

    const div = document.createElement('div');
    div.classList.add('card', 'card-demandado', 'mb-3', 'p-3', 'demandado-item');
    div.setAttribute('data-id', id);

    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-secondary">Demandado #${id}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDemandado(${id})">
                <i class="fas fa-trash me-1"></i> Eliminar
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">DNI <span class="text-danger">*</span></label>
                <input type="text" class="form-control campo-dni" placeholder="12345678"
                       maxlength="8" data-id="${id}" oninput="soloNumeros(this, 8)">
                <small class="text-danger campo-error" id="error-${id}" style="display:none;"></small>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nombres <span class="text-danger">*</span></label>
                <input type="text" class="form-control campo-nombres" placeholder="Nombres" data-id="${id}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                <input type="text" class="form-control campo-apellidos" placeholder="Apellidos" data-id="${id}">
            </div>
            <div class="col-md-3">
                <label class="form-label">RUC <span class="text-muted small">(opcional)</span></label>
                <input type="text" class="form-control campo-ruc" placeholder="20123456789"
                       maxlength="11" data-id="${id}" oninput="soloNumeros(this, 11)">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo <span class="text-muted small">(opcional)</span></label>
                <input type="email" class="form-control campo-correo" placeholder="correo@ejemplo.com" data-id="${id}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Teléfono <span class="text-muted small">(opcional)</span></label>
                <input type="text" class="form-control campo-telefono" placeholder="987654321"
                       maxlength="9" data-id="${id}" oninput="soloNumeros(this, 9)">
            </div>
            <div class="col-md-3">
                <label class="form-label">Domicilio <span class="text-muted small">(opcional)</span></label>
                <input type="text" class="form-control campo-direccion" placeholder="Av Maansiche 230"
                     data-id="${id}"">
            </div>
        </div>
    `;

    document.getElementById('demandados-container').appendChild(div);
}

function eliminarDemandado(id) {
    const item = document.querySelector(`.demandado-item[data-id="${id}"]`);
    if (item) item.remove();
}

document.getElementById('arbitrajeForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const personas = [];

    const nombresD   = document.getElementById('demandante-nombres').value.trim();
    const apellidosD = document.getElementById('demandante-apellidos').value.trim();
    const correoD    = document.getElementById('demandante-correo').value.trim();
    const telefonoD  = document.getElementById('demandante-telefono').value.trim();
    const rucD       = document.getElementById('demandante-ruc').value.trim();
    const DomicilioD       = document.getElementById('demandante-direccion').value.trim();

    if (!nombresD || !apellidosD) {
        showError('Los nombres y apellidos del demandante son obligatorios.');
        return;
    }

    personas.push({
        dni: dniDemandante,
        nombres: nombresD,
        apellidos: apellidosD,
        correo: correoD,
        telefono: telefonoD,
        ruc: rucD,
        direccion: DomicilioD,
        tipo: 'Demandante'
    });

    let hayError = false;
    const errores = [];

    document.querySelectorAll('.demandado-item').forEach(div => {
        const id             = div.getAttribute('data-id');
        const dniInput       = div.querySelector('.campo-dni');
        const nombresInput   = div.querySelector('.campo-nombres');
        const apellidosInput = div.querySelector('.campo-apellidos');
        const correoInput    = div.querySelector('.campo-correo');
        const telefonoInput  = div.querySelector('.campo-telefono');
        const rucInput       = div.querySelector('.campo-ruc');
        const direccionInput = div.querySelector('.campo-direccion');
        const errorEl        = document.getElementById(`error-${id}`);

        errorEl.style.display = 'none';
        dniInput.classList.remove('is-invalid');

        const dni       = dniInput.value.trim();
        const nombres   = nombresInput.value.trim();
        const apellidos = apellidosInput.value.trim();
        const correo    = correoInput    ? correoInput.value.trim()   : '';
        const telefono  = telefonoInput  ? telefonoInput.value.trim() : '';
        const ruc       = rucInput       ? rucInput.value.trim()      : '';
        const direccion = direccionInput ? direccionInput.value.trim() : ''; // 👈 Obtener el VALOR

        if (dni.length !== 8) {
            errorEl.textContent = 'El DNI debe tener exactamente 8 dígitos.';
            errorEl.style.display = 'block';
            dniInput.classList.add('is-invalid');
            hayError = true;
            errores.push('Hay DNI inválidos en los demandados.');
            return;
        }

        if (isDNIDuplicado(dni, personas)) {
            errorEl.textContent = 'Este DNI ya está registrado en el arbitraje.';
            errorEl.style.display = 'block';
            dniInput.classList.add('is-invalid');
            hayError = true;
            errores.push('Hay DNI duplicados.');
            return;
        }

        if (!nombres || !apellidos) {
            hayError = true;
            errores.push('Nombres y Apellidos son obligatorios en todos los demandados.');
            return;
        }

        personas.push({ dni, nombres, apellidos, correo, telefono, ruc, tipo: 'Demandado',direccion });
    });

    if (hayError) {
        showError([...new Set(errores)].join('\n'));
        return;
    }

    if (personas.length < 2) {
        showError('Debe agregar al menos un demandado.');
        return;
    }

    const file = document.getElementById('voucher').files[0];
    if (!file) {
        showError('Debe cargar el voucher de pago.');
        return;
    }

    const nombreDocumento = document.getElementById('nombre_documento').value.trim();
    if (!nombreDocumento) {
        showError('Debe ingresar un nombre para el voucher.');
        return;
    }

    const driveLink = document.getElementById('drive_link').value.trim();
    const driveName = document.getElementById('nombre_documento_link').value.trim();

    if (driveLink && !driveLink.includes('drive.google.com')) {
        showError('Por favor, ingresa un enlace válido de Google Drive.');
        return;
    }

    if ((driveLink && !driveName) || (!driveLink && driveName)) {
        showError('Si ingresas un enlace de Drive, debes ponerle un nombre, y viceversa.');
        return;
    }

    // ============================================================
    // 🔍 LOG DE DIAGNÓSTICO — datos antes de armar el FormData
    // ============================================================
    console.group('%c📋 DIAGNÓSTICO — Datos a enviar', 'color: #0d6efd; font-weight: bold; font-size: 14px;');

    console.group('%c👤 Demandante', 'color: #198754; font-weight: bold;');
    console.table([personas[0]]);
    console.groupEnd();

    const demandados = personas.filter(p => p.tipo === 'Demandado');
    console.group(`%c⚖️ Demandados (${demandados.length})`, 'color: #dc3545; font-weight: bold;');
    if (demandados.length > 0) {
        console.table(demandados);
    } else {
        console.warn('⚠️ No hay demandados en el array');
    }
    console.groupEnd();

    console.group('%c📦 Todas las personas (array completo)', 'color: #6610f2; font-weight: bold;');
    console.table(personas);
    console.groupEnd();

    console.group('%c📄 Metadatos del formulario', 'color: #0dcaf0; font-weight: bold;');
    console.log('nombre_materia:',        document.querySelector('[name="nombre_materia"]').value);
    console.log('pretenciones:',          document.querySelector('[name="pretenciones"]').value);
    console.log('controversia:',          document.querySelector('[name="controversia"]').value);
    console.log('fundamentos_hecho:',          document.querySelector('[name="fundamentos_hecho"]').value);
    console.log('cuantia:',               document.querySelector('[name="cuantia"]').value);
    console.log('tasa_solicitud:',        document.querySelector('[name="tasa_solicitud"]').value);
    console.log('designacion_arbitral:',  document.querySelector('[name="designacion_arbitral"]').value);
    console.log('nombre_documento:',      nombreDocumento);
    console.log('drive_link:',            driveLink || '(vacío)');
    console.log('nombre_documento_link:', driveName || '(vacío)');
    console.log('voucher archivo:',       file ? `${file.name} (${(file.size / 1024).toFixed(1)} KB)` : '⚠️ SIN ARCHIVO');
    console.groupEnd();

    console.groupEnd(); // cierra grupo principal
    // ============================================================

    toggleSpinner(true);

    const formData = new FormData();

    formData.append('nombre_materia',       document.querySelector('[name="nombre_materia"]').value);
    formData.append('pretenciones',         document.querySelector('[name="pretenciones"]').value);
    formData.append('controversia',         document.querySelector('[name="controversia"]').value);
    formData.append('fundamentos_hecho',         document.querySelector('[name="fundamentos_hecho"]').value);
    formData.append('cuantia',              document.querySelector('[name="cuantia"]').value);
    formData.append('tasa_solicitud',       document.querySelector('[name="tasa_solicitud"]').value);
    formData.append('designacion_arbitral', document.querySelector('[name="designacion_arbitral"]').value);

    personas.forEach((p, i) => {
        formData.append(`personas[${i}][dni]`,       p.dni);
        formData.append(`personas[${i}][nombres]`,   p.nombres);
        formData.append(`personas[${i}][apellidos]`, p.apellidos);
        formData.append(`personas[${i}][correo]`,    p.correo    || '');
        formData.append(`personas[${i}][telefono]`,  p.telefono  || '');
        formData.append(`personas[${i}][ruc]`,       p.ruc       || '');
        formData.append(`personas[${i}][tipo]`,      p.tipo);
        formData.append(`personas[${i}][direccion]`, p.direccion || ''); // 👈 AGREGAR ESTA LÍNEA

    });

    // Log del FormData ya armado
    console.group('%c📤 FormData armado (personas)', 'color: #fd7e14; font-weight: bold;');
    personas.forEach((p, i) => {
        console.log(`personas[${i}]:`, {
            dni:       formData.get(`personas[${i}][dni]`),
            nombres:   formData.get(`personas[${i}][nombres]`),
            apellidos: formData.get(`personas[${i}][apellidos]`),
            correo:    formData.get(`personas[${i}][correo]`),
            telefono:  formData.get(`personas[${i}][telefono]`),
            ruc:       formData.get(`personas[${i}][ruc]`),
            tipo:      formData.get(`personas[${i}][tipo]`),
        });
    });
    console.groupEnd();

    formData.append('voucher', file);
    formData.append('nombre_documento', nombreDocumento);

    if (driveLink) {
        formData.append('drive_link', driveLink);
        formData.append('nombre_documento_link', driveName);
    }

    fetch('{{ route("arbitraje.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        toggleSpinner(false);
        console.group('%c📥 Respuesta del servidor', 'color: #6c757d; font-weight: bold;');
        console.log(data);
        console.groupEnd();

        if (data.error) {
            showError(data.detalle || data.message || 'Error al registrar');
        } else if (data.success) {
            showSuccess();
        } else {
            showError(data.message || 'Error desconocido');
        }
    })
    .catch(err => {
        toggleSpinner(false);
        console.error('❌ Error de red:', err);
        showError('Error de red: ' + err.message);
    });
});

</script>
@endpush

@push('styles')
<style>
.is-invalid {
    border-color: #dc3545 !important;
}

.card-demandante {
    background-color: #eef4ff;
    border: 1.5px solid #9ec5fe;
    border-radius: 8px;
}

.card-demandado {
    background-color: #fff8f8;
    border: 1.5px solid #f5c2c7;
    border-radius: 8px;
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

.demandado-item {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush