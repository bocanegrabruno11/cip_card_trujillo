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

<!-- Modal de error -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">&#9888; Error</h5>
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
<div id="loading-spinner" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div class="spinner-border text-light" role="status" style="width:3rem; height:3rem;">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<div class="container mt-4">

    {{-- novalidate: desactiva validacion nativa del browser para evitar el error
         "invalid form control is not focusable" en inputs ocultos. Toda la
         validacion la maneja el JS del submit. --}}
    <form id="arbitrajeForm" novalidate>
        @csrf

        <h4 class="mb-4">Registrar Arbitraje</h4>

        <!-- ====== DATOS DEL ARBITRAJE ====== -->
        <h5 class="mb-3">Datos del Arbitraje</h5>

        <div class="card p-3 mb-4" style="background:#f8f9fa; border:1.5px solid #dee2e6; border-radius:8px;">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Materia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nombre_materia"
                           placeholder="Ej: Incumplimiento de contrato">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cuantia</label>
                    <input type="text" class="form-control" name="cuantia">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tasa de Solicitud</label>
                    <input type="text" class="form-control" name="tasa_solicitud">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Designacion Arbitral</label>
                    <input type="text" class="form-control" name="designacion_arbitral"
                           placeholder="Ej: Arbitro unico, Tribunal arbitral, etc.">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tipo de Arbitraje <span class="text-danger">*</span></label>
                    <select class="form-select" name="tipo_arbitraje">
                        <option value="normal" selected>Normal</option>
                        <option value="emergencia">Emergencia</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Pretensiones <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="pretenciones" rows="3"
                              placeholder="Describa las pretensiones del arbitraje..."></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Controversia <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="controversia" rows="3"
                              placeholder="Describa la controversia del arbitraje..."></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Fundamentos de hecho <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="fundamentos_hecho" rows="3"
                              placeholder="Describa los fundamentos de hecho del arbitraje..."></textarea>
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
                    <input type="text" class="form-control" id="demandante-nombres" placeholder="Nombres">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="demandante-apellidos" placeholder="Apellidos">
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
                    <label class="form-label">Telefono <span class="text-muted small">(opcional)</span></label>
                    <input type="text" class="form-control" id="demandante-telefono"
                           placeholder="987654321" maxlength="9" oninput="soloNumeros(this, 9)">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Domicilio</label>
                    <input type="text" class="form-control" id="demandante-direccion" placeholder="Av Mansiche 250">
                </div>

            </div>
        </div>

        <!-- ====== DEMANDADOS ====== -->
        <h5 class="mb-3">Demandados</h5>

        <div id="demandados-container"></div>

        <button type="button" class="btn btn-secondary mb-4" onclick="agregarDemandado()">
            <i class="fas fa-user-plus me-1"></i> Agregar Demandado
        </button>

        <!-- ====== DOCUMENTACION ====== -->
        <h5 class="mt-2 mb-3">Documentacion</h5>

        <!-- MEDIOS DE PAGO -->
        <div class="card mb-4 border-0" style="background:#fffbeb; border-left:4px solid #f59e0b !important; border-radius:8px;">
            <div class="card-body py-3">
                <h6 class="fw-bold mb-3" style="color:#92400e;">
                    <i class="fas fa-university me-2" style="color:#f59e0b;"></i>MEDIOS DE PAGO
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="pago-item p-2 rounded" style="background:#fff; border:1px solid #fde68a;">
                            <div class="text-muted small fw-semibold mb-1">CAJA TRUJILLO</div>
                            <div class="fw-bold">000137172-001</div>
                            <div class="text-muted small">CCI: 80200100013717200180</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pago-item p-2 rounded" style="background:#fff; border:1px solid #fde68a;">
                            <div class="text-muted small fw-semibold mb-1">INTERBANK - CUENTA EN SOLES</div>
                            <div class="fw-bold">616-300756418-7</div>
                            <div class="text-muted small">CCI: 00361600300756418705</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pago-item p-2 rounded" style="background:#fff; border:1px solid #fde68a;">
                            <div class="text-muted small fw-semibold mb-1">BANCO DE COMERCIO - CUENTA EN SOLES</div>
                            <div class="fw-bold">110-010476413</div>
                            <div class="text-muted small">CCI: 02304511001047641386</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voucher de pago (obligatorio) -->
        <!-- SIN required en el input: evita "invalid form control not focusable" -->
        <!-- La validacion obligatoria la maneja el JS -->
        <div class="mb-4">
            <label class="form-label">Voucher de Pago <span class="text-danger">*</span></label>
            <div class="file-upload-area p-3 border rounded" id="voucher-area"
                 onclick="document.getElementById('voucher').click()" style="cursor:pointer;">
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <p class="mb-1">Haz clic para subir el voucher</p>
                    <p class="text-muted small mb-2">Formatos aceptados: JPG, JPEG, PNG, PDF (Max. 20 MB)</p>
                    <div id="voucherFileName" class="text-primary fw-bold">Ningun archivo seleccionado</div>
                </div>
            </div>
            <input type="file" class="form-control d-none" id="voucher" name="voucher"
                   accept=".jpg,.jpeg,.png,.pdf">
        </div>

        <!-- Escrito PDF (opcional) -->
        <div class="mb-4">
            <label class="form-label">
                Archivo de Escrito
                <span class="text-muted small">(opcional)</span>
            </label>
            <div class="file-upload-area file-upload-escrito p-3 border rounded"
                 onclick="document.getElementById('escrito').click()" style="cursor:pointer;">
                <div class="text-center">
                    <i class="fas fa-file-pdf fa-2x mb-2" style="color:#dc3545;"></i>
                    <p class="mb-1">Haz clic para subir el escrito</p>
                    <p class="text-muted small mb-2">Solo PDF (Max. 20 MB)</p>
                    <div id="escritoFileName" class="text-primary fw-bold">Ningun archivo seleccionado</div>
                </div>
            </div>
            <input type="file" class="form-control d-none" id="escrito" name="escrito" accept=".pdf">
        </div>

        <!-- Link de Google Drive (opcional) -->
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
                    <small class="text-muted">Nombre que identificara el documento</small>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                <strong>Nota:</strong> El voucher de pago es obligatorio y sera verificado por el personal del centro de arbitraje.
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

let dniDemandante      = '';
let contadorDemandados = 0;

// ── Utilidades UI ─────────────────────────────────────────────────────────────

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

function soloNumeros(input, maxLen) {
    input.value = input.value.replace(/[^0-9]/g, '').substring(0, maxLen);
}

function isDNIDuplicado(dni, lista) {
    return lista.some(p => p.dni === dni);
}

// ── Modal de resumen post-registro ────────────────────────────────────────────

function showResumenRegistro(data, resumen) {
    const hayAlertas = resumen.alertas.length > 0;

    const itemsOk = resumen.ok.map(txt =>
        `<li class="list-group-item list-group-item-success py-2">
            <i class="fas fa-check-circle me-2 text-success"></i>${txt}
        </li>`
    ).join('');

    const itemsAlerta = resumen.alertas.map(txt =>
        `<li class="list-group-item list-group-item-warning py-2">
            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>${txt}
        </li>`
    ).join('');

    const modalHtml = `
        <div class="modal fade" id="resumenModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header ${hayAlertas ? 'bg-warning' : 'bg-success'} text-white">
                        <h5 class="modal-title">
                            <i class="fas ${hayAlertas ? 'fa-exclamation-circle' : 'fa-check-circle'} me-2"></i>
                            ${hayAlertas ? 'Registrado con observaciones' : 'Arbitraje Registrado Correctamente'}
                        </h5>
                    </div>
                    <div class="modal-body">
                        ${hayAlertas ? `
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            El arbitraje fue guardado, pero algunos campos opcionales estan incompletos.
                        </div>` : ''}
                        <p class="text-muted small mb-2">
                            <strong>Expediente #${data.arbitraje}</strong> &mdash; Resumen de lo registrado:
                        </p>
                        <ul class="list-group list-group-flush mb-0">
                            ${itemsOk}
                            ${itemsAlerta}
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn ${hayAlertas ? 'btn-warning' : 'btn-success'}" id="btn-resumen-ok">
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const viejo = document.getElementById('resumenModal');
    if (viejo) viejo.remove();

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('resumenModal'));
    modal.show();

    document.getElementById('btn-resumen-ok').addEventListener('click', () => {
        modal.hide();
        window.location.reload();
    });
}

function construirResumen() {
    const resumen = { ok: [], alertas: [] };

    const materia = document.querySelector('[name="nombre_materia"]').value.trim();
    materia
        ? resumen.ok.push(`Materia registrada: <strong>${materia}</strong>`)
        : resumen.alertas.push('La materia no fue registrada.');

    const pretenciones = document.querySelector('[name="pretenciones"]').value.trim();
    pretenciones
        ? resumen.ok.push('Pretensiones registradas.')
        : resumen.alertas.push('Las pretensiones estan vacias.');

    const controversia = document.querySelector('[name="controversia"]').value.trim();
    controversia
        ? resumen.ok.push('Controversia registrada.')
        : resumen.alertas.push('La controversia esta vacia.');

    const fundamentos = document.querySelector('[name="fundamentos_hecho"]').value.trim();
    fundamentos
        ? resumen.ok.push('Fundamentos de hecho registrados.')
        : resumen.alertas.push('Los fundamentos de hecho estan vacios.');

    const tipoArbitraje = document.querySelector('[name="tipo_arbitraje"]').value;
    resumen.ok.push(`Tipo de arbitraje: <strong>${tipoArbitraje === 'emergencia' ? 'Emergencia' : 'Normal'}</strong>`);

    const cuantia = document.querySelector('[name="cuantia"]').value.trim();
    cuantia
        ? resumen.ok.push(`Cuantia registrada: <strong>${cuantia}</strong>`)
        : resumen.alertas.push('No se ingreso cuantia (campo opcional).');

    const designacion = document.querySelector('[name="designacion_arbitral"]').value.trim();
    designacion
        ? resumen.ok.push(`Designacion arbitral: <strong>${designacion}</strong>`)
        : resumen.alertas.push('No se ingreso designacion arbitral (campo opcional).');

    const nombreDemandante   = document.getElementById('demandante-nombres').value.trim();
    const apellidoDemandante = document.getElementById('demandante-apellidos').value.trim();
    resumen.ok.push(`Demandante registrado: <strong>${nombreDemandante} ${apellidoDemandante}</strong>`);

    const totalDemandados = document.querySelectorAll('.demandado-item').length;
    totalDemandados > 0
        ? resumen.ok.push(`${totalDemandados} demandado(s) registrado(s).`)
        : resumen.alertas.push('No se registraron demandados.');

    const voucher = document.getElementById('voucher').files[0];
    voucher
        ? resumen.ok.push(`Voucher de pago subido: <strong>${voucher.name}</strong>`)
        : resumen.alertas.push('No se subio el voucher de pago.');

    const escrito = document.getElementById('escrito').files[0];
    escrito
        ? resumen.ok.push(`Escrito subido: <strong>${escrito.name}</strong>`)
        : resumen.alertas.push('No se adjunto escrito PDF (campo opcional).');

    const driveLink = document.getElementById('drive_link').value.trim();
    const driveName = document.getElementById('nombre_documento_link').value.trim();
    driveLink
        ? resumen.ok.push(`Enlace de Drive adjuntado: <strong>${driveName}</strong>`)
        : resumen.alertas.push('No se adjunto enlace de Google Drive (campo opcional).');

    return resumen;
}

// ── Init ──────────────────────────────────────────────────────────────────────

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
            document.getElementById('demandante-dni').value       = dniDemandante;
            document.getElementById('demandante-direccion').value  = data.persona.direccion       || '';
            document.getElementById('demandante-telefono').value   = data.persona.celular         || '';
            document.getElementById('demandante-correo').value     = data.persona.correo_contacto || '';

            if (data.persona.nombres)   document.getElementById('demandante-nombres').value   = data.persona.nombres;
            if (data.persona.apellidos) document.getElementById('demandante-apellidos').value  = data.persona.apellidos;
            if (data.persona.email)     document.getElementById('demandante-correo').value     = data.persona.email;
            if (data.persona.telefono)  document.getElementById('demandante-telefono').value   = data.persona.telefono;
        })
        .catch(() => {
            toggleSpinner(false);
            showModal(
                'Se requiere actualizar sus datos para poder iniciar un proceso.',
                '{{ route("persona.actualizar") }}'
            );
        });

    // Listener voucher — feedback visual en el area de carga
    document.getElementById('voucher').addEventListener('change', function () {
        const file = this.files[0];
        const area = document.getElementById('voucher-area');
        document.getElementById('voucherFileName').textContent = file ? file.name : 'Ningun archivo seleccionado';
        if (file && file.size > 20 * 1024 * 1024) {
            showError('El archivo supera los 20 MB permitidos.');
            this.value = '';
            document.getElementById('voucherFileName').textContent = 'Ningun archivo seleccionado';
            area.style.borderColor = '#dc3545';
            return;
        }
        // Verde si OK, normal si vacio
        area.style.borderColor = file ? '#198754' : '';
    });

    // Listener escrito
    document.getElementById('escrito').addEventListener('change', function () {
        const file = this.files[0];
        document.getElementById('escritoFileName').textContent = file ? file.name : 'Ningun archivo seleccionado';
        if (file && file.size > 20 * 1024 * 1024) {
            showError('El archivo de escrito supera los 20 MB permitidos.');
            this.value = '';
            document.getElementById('escritoFileName').textContent = 'Ningun archivo seleccionado';
        }
    });
});

// ── Demandados ────────────────────────────────────────────────────────────────

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
                <label class="form-label">Telefono <span class="text-muted small">(opcional)</span></label>
                <input type="text" class="form-control campo-telefono" placeholder="987654321"
                       maxlength="9" data-id="${id}" oninput="soloNumeros(this, 9)">
            </div>
            <div class="col-md-3">
                <label class="form-label">Domicilio <span class="text-muted small">(opcional)</span></label>
                <input type="text" class="form-control campo-direccion" placeholder="Av Mansiche 230" data-id="${id}">
            </div>
        </div>
    `;

    document.getElementById('demandados-container').appendChild(div);
}

function eliminarDemandado(id) {
    const item = document.querySelector(`.demandado-item[data-id="${id}"]`);
    if (item) item.remove();
}

// ── Submit ────────────────────────────────────────────────────────────────────

document.getElementById('arbitrajeForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // ── Validacion manual de campos obligatorios (novalidate activo) ──────────
    const materia = document.querySelector('[name="nombre_materia"]').value.trim();
    if (!materia) { showError('La materia del arbitraje es obligatoria.'); return; }

    const pretenciones = document.querySelector('[name="pretenciones"]').value.trim();
    if (!pretenciones) { showError('Las pretensiones son obligatorias.'); return; }

    const controversia = document.querySelector('[name="controversia"]').value.trim();
    if (!controversia) { showError('La controversia es obligatoria.'); return; }

    const fundamentos = document.querySelector('[name="fundamentos_hecho"]').value.trim();
    if (!fundamentos) { showError('Los fundamentos de hecho son obligatorios.'); return; }

    // ── Demandante ────────────────────────────────────────────────────────────
    const personas = [];

    const nombresD   = document.getElementById('demandante-nombres').value.trim();
    const apellidosD = document.getElementById('demandante-apellidos').value.trim();
    const correoD    = document.getElementById('demandante-correo').value.trim();
    const telefonoD  = document.getElementById('demandante-telefono').value.trim();
    const rucD       = document.getElementById('demandante-ruc').value.trim();
    const domicilioD = document.getElementById('demandante-direccion').value.trim();

    if (!nombresD || !apellidosD) {
        showError('Los nombres y apellidos del demandante son obligatorios.');
        return;
    }

    personas.push({
        dni:       dniDemandante,
        nombres:   nombresD,
        apellidos: apellidosD,
        correo:    correoD,
        telefono:  telefonoD,
        ruc:       rucD,
        direccion: domicilioD,
        tipo:      'Demandante'
    });

    // ── Demandados ────────────────────────────────────────────────────────────
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
        const correo    = correoInput    ? correoInput.value.trim()    : '';
        const telefono  = telefonoInput  ? telefonoInput.value.trim()  : '';
        const ruc       = rucInput       ? rucInput.value.trim()       : '';
        const direccion = direccionInput ? direccionInput.value.trim() : '';

        if (dni.length !== 8) {
            errorEl.textContent   = 'El DNI debe tener exactamente 8 digitos.';
            errorEl.style.display = 'block';
            dniInput.classList.add('is-invalid');
            hayError = true;
            errores.push('Hay DNI invalidos en los demandados.');
            return;
        }

        if (isDNIDuplicado(dni, personas)) {
            errorEl.textContent   = 'Este DNI ya esta registrado en el arbitraje.';
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

        personas.push({ dni, nombres, apellidos, correo, telefono, ruc, tipo: 'Demandado', direccion });
    });

    if (hayError) {
        showError([...new Set(errores)].join('\n'));
        return;
    }

    if (personas.length < 2) {
        showError('Debe agregar al menos un demandado.');
        return;
    }

    // ── Voucher obligatorio (sin required en el HTML, validado aqui) ──────────
    const file = document.getElementById('voucher').files[0];
    if (!file) {
        document.getElementById('voucher-area').style.borderColor = '#dc3545';
        showError('Debe cargar el voucher de pago.');
        return;
    }

    // ── Drive ─────────────────────────────────────────────────────────────────
    const driveLink = document.getElementById('drive_link').value.trim();
    const driveName = document.getElementById('nombre_documento_link').value.trim();

    if (driveLink && !driveLink.includes('drive.google.com')) {
        showError('Por favor, ingresa un enlace valido de Google Drive.');
        return;
    }

    if ((driveLink && !driveName) || (!driveLink && driveName)) {
        showError('Si ingresas un enlace de Drive, debes ponerle un nombre, y viceversa.');
        return;
    }

    // ── Capturar resumen ANTES del fetch (DOM aun tiene los datos) ────────────
    const resumenPrevio = construirResumen();

    toggleSpinner(true);

    const formData = new FormData();
    formData.append('nombre_materia',       document.querySelector('[name="nombre_materia"]').value);
    formData.append('pretenciones',         document.querySelector('[name="pretenciones"]').value);
    formData.append('controversia',         document.querySelector('[name="controversia"]').value);
    formData.append('fundamentos_hecho',    document.querySelector('[name="fundamentos_hecho"]').value);
    formData.append('cuantia',              document.querySelector('[name="cuantia"]').value);
    formData.append('tasa_solicitud',       document.querySelector('[name="tasa_solicitud"]').value);
    formData.append('designacion_arbitral', document.querySelector('[name="designacion_arbitral"]').value);
    formData.append('tipo_arbitraje',       document.querySelector('[name="tipo_arbitraje"]').value);

    personas.forEach((p, i) => {
        formData.append(`personas[${i}][dni]`,       p.dni);
        formData.append(`personas[${i}][nombres]`,   p.nombres);
        formData.append(`personas[${i}][apellidos]`, p.apellidos);
        formData.append(`personas[${i}][correo]`,    p.correo    || '');
        formData.append(`personas[${i}][telefono]`,  p.telefono  || '');
        formData.append(`personas[${i}][ruc]`,       p.ruc       || '');
        formData.append(`personas[${i}][tipo]`,      p.tipo);
        formData.append(`personas[${i}][direccion]`, p.direccion || '');
    });

    formData.append('voucher', file);

    const escritoFile = document.getElementById('escrito').files[0];
    if (escritoFile) formData.append('escrito', escritoFile);

    if (driveLink) {
        formData.append('drive_link',            driveLink);
        formData.append('nombre_documento_link', driveName);
    }

    fetch('{{ route("arbitraje.store") }}', {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body:    formData
    })
    .then(r => r.json())
    .then(data => {
        toggleSpinner(false);
        if (data.error) {
            showError(data.detalle || data.message || 'Error al registrar');
        } else if (data.success) {
            showResumenRegistro(data, resumenPrevio);
        } else {
            showError(data.message || 'Error desconocido');
        }
    })
    .catch(err => {
        toggleSpinner(false);
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

.file-upload-escrito:hover {
    border-color: #dc3545 !important;
}

.demandado-item {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.pago-item {
    transition: box-shadow 0.2s;
}

.pago-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
</style>
@endpush