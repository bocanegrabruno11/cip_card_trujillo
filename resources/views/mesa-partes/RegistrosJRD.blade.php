@extends('mesa-partes.app')

@section('title', 'Control de JPRD')
@section('page-title', 'Mis JPRD')

@section('content')

<div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Control de JPRD</h3>
            <p class="text-muted">Gestiona y visualiza todos tus procesos de JPRD</p>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="searchJrd" placeholder="Buscar por materia, descripción o ID...">
            </div>
        </div>
    </div>

    <div id="loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-danger" role="status"></div>
        <p class="mt-3 text-muted">Cargando JPRD...</p>
    </div>

    <div id="noResults" class="alert alert-info text-center" style="display: none;">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No se encontraron JPRD</h5>
        <p class="mb-0">No tienes JPRD registrados o no coinciden con tu búsqueda.</p>
    </div>

    <div id="jrdList" class="accordion"></div>

</div>

<!-- Modal Subir Documento -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="uploadModalTitle">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Subir Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="upload_jrd_id">
                    <input type="hidden" id="upload_proceso_id">
                    <input type="hidden" id="upload_modo">

                    <!-- Alerta voucher rechazado -->
                    <div id="alerta-voucher-rechazado" class="alert alert-danger d-none">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Voucher rechazado.</strong> Sube el nuevo comprobante de pago para continuar.
                    </div>

                    <!-- MODO NORMAL: selector de tipo -->
                    <div id="bloque-tipo-normal">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_documento" name="tipo_documento">
                                <option value="">Seleccione...</option>
                                <option value="archivo">📄 Subir Archivo (PDF, JPG, PNG)</option>
                                <option value="link">🔗 Enlace (Google Drive, Dropbox, etc.)</option>
                            </select>
                        </div>
                        <div id="campo_link" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Enlace <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" name="link" placeholder="https://drive.google.com/...">
                            </div>
                        </div>
                        <!-- Archivo en modo normal -->
                        <div id="campo_archivo_normal" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Archivo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="archivo-normal" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Formatos: PDF, JPG, PNG (Máx. 20MB)</small>
                            </div>
                        </div>
                    </div>

                    <!-- MODO VOUCHER: botón grande para seleccionar archivo -->
                    <div id="bloque-voucher" style="display:none;">
                        <div class="mb-3 text-center">
                            <label class="d-block mb-2 fw-semibold">Selecciona el comprobante de pago</label>
                            <label for="archivo-voucher" class="btn btn-outline-danger btn-lg w-100 py-4" style="cursor:pointer; border-style:dashed;">
                                <i class="fas fa-file-upload fa-2x d-block mb-2"></i>
                                <span id="voucher-filename">Haz clic para seleccionar archivo</span>
                                <br><small class="text-muted">PDF, JPG o PNG · Máx 20MB</small>
                            </label>
                            <input type="file" id="archivo-voucher" class="d-none" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <!-- Nombre del documento (solo en modo normal) -->
                    <div id="bloque-nombre-normal" class="mb-3">
                        <label class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_documento_input" name="nombre_documento">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="btn-upload-submit">
                        <i class="fas fa-upload me-2"></i>Subir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Mensajes -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><p id="messageModalBody"></p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let jrdList = [];
const STORAGE_KEY = 'jprd_abiertos';

// ── Estado de paneles ─────────────────────────────────────────────────────────
function cargarEstadoPaneles() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch(e) { return []; }
}

function estaPanelAbierto(jrdId) {
    return cargarEstadoPaneles().includes(String(jrdId));
}

function togglePanelState(jrdId, isOpen) {
    let abiertos = cargarEstadoPaneles();
    const idStr = String(jrdId);
    if (isOpen) {
        if (!abiertos.includes(idStr)) abiertos.push(idStr);
    } else {
        abiertos = abiertos.filter(id => id !== idStr);
    }
    localStorage.setItem(STORAGE_KEY, JSON.stringify(abiertos));
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function formatFecha(fecha) {
    if (!fecha) return 'No especificada';
    return new Date(fecha).toLocaleDateString('es-PE', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function formatFechaCorta(fecha) {
    if (!fecha) return 'No especificada';
    return new Date(fecha).toLocaleDateString('es-PE', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function getEstadoBadge(estado) {
    const badges = {
        'en proceso': 'bg-warning text-dark',
        'terminado':  'bg-success',
        'observado':  'bg-danger',
        'archivado':  'bg-secondary',
        'validando':  'bg-warning text-dark',
        'iniciado':   'bg-info',
        'rechazado':  'bg-danger'
    };
    return badges[estado] || 'bg-secondary';
}

function getProcesoEstadoBadge(estado) {
    const badges = {
        'finalizado': 'bg-success',
        'activo':     'bg-primary',
        'observado':  'bg-danger',
        'iniciado':   'bg-info'
    };
    return badges[estado] || 'bg-secondary';
}

function showMessage(title, msg, isError = false) {
    // Cerrar cualquier modal abierto primero para evitar el warning de aria-hidden
    const uploadModalEl = document.getElementById('uploadDocumentModal');
    const uploadModal = bootstrap.Modal.getInstance(uploadModalEl);
    if (uploadModal) uploadModal.hide();

    setTimeout(() => {
        const m = new bootstrap.Modal(document.getElementById('messageModal'));
        document.getElementById('messageModalTitle').textContent = title;
        document.getElementById('messageModalTitle').className = `modal-title text-${isError ? 'danger' : 'success'}`;
        document.getElementById('messageModalBody').textContent = msg;
        m.show();
    }, 300);
}

function badgeSubidoPor(doc) {
    if (!doc.subido_por) return '';
    const { label, color, icono, nombre } = doc.subido_por;
    const txtCls = color === 'warning' ? 'text-dark' : '';
    return `<span class="badge bg-${color} ${txtCls}" style="font-size:.68rem;" title="${nombre}">
                <i class="fas ${icono} me-1"></i>${label}
            </span>
            <small class="text-muted ms-1" style="font-size:.68rem;">${nombre}</small>`;
}

// ── Modal: abrir ──────────────────────────────────────────────────────────────
function subirDocumento(jrdId, procesoId, jrdEstado) {
    document.getElementById('upload_jrd_id').value     = jrdId;
    document.getElementById('upload_proceso_id').value = procesoId;
    document.getElementById('uploadDocumentForm').reset();
    document.getElementById('voucher-filename').textContent = 'Haz clic para seleccionar archivo';

    const esObservado = jrdEstado === 'observado';
    document.getElementById('upload_modo').value = esObservado ? 'voucher' : 'normal';

    // Mostrar/ocultar bloques según modo
    document.getElementById('alerta-voucher-rechazado').classList.toggle('d-none', !esObservado);
    document.getElementById('bloque-tipo-normal').style.display   = esObservado ? 'none' : 'block';
    document.getElementById('bloque-voucher').style.display       = esObservado ? 'block' : 'none';
    document.getElementById('bloque-nombre-normal').style.display = esObservado ? 'none' : 'block';
    document.getElementById('campo_archivo_normal').style.display = 'none';
    document.getElementById('campo_link').style.display           = 'none';
    document.getElementById('tipo_documento').value               = '';

    document.getElementById('uploadModalTitle').innerHTML =
        `<i class="fas fa-${esObservado ? 'receipt' : 'cloud-upload-alt'} me-2"></i>` +
        (esObservado ? 'Subir nuevo voucher' : 'Subir Documento');

    new bootstrap.Modal(document.getElementById('uploadDocumentModal')).show();
}

// ── Cambio de tipo en modo normal ─────────────────────────────────────────────
document.getElementById('tipo_documento').addEventListener('change', function() {
    document.getElementById('campo_archivo_normal').style.display = this.value === 'archivo' ? 'block' : 'none';
    document.getElementById('campo_link').style.display           = this.value === 'link'    ? 'block' : 'none';
    if (this.value !== 'archivo') document.getElementById('archivo-normal').value = '';
    if (this.value !== 'link')    document.querySelector('input[name="link"]').value = '';
});

// ── Preview nombre de voucher ─────────────────────────────────────────────────
document.getElementById('archivo-voucher').addEventListener('change', function() {
    document.getElementById('voucher-filename').textContent =
        this.files[0] ? this.files[0].name : 'Haz clic para seleccionar archivo';
});

// ── Submit del form (UN SOLO LISTENER) ───────────────────────────────────────
document.getElementById('uploadDocumentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const modo      = document.getElementById('upload_modo').value;
    const jrdId     = document.getElementById('upload_jrd_id').value;
    const procesoId = document.getElementById('upload_proceso_id').value;
    const formData  = new FormData();

    if (modo === 'voucher') {
        // Modo voucher: solo archivo, nombre fijo
        const archivoVoucher = document.getElementById('archivo-voucher').files[0];
        if (!archivoVoucher) {
            showMessage('Error', 'Selecciona el archivo del voucher', true); return;
        }
        if (archivoVoucher.size > 20 * 1024 * 1024) {
            showMessage('Error', 'El archivo no debe superar 20MB', true); return;
        }
        formData.append('proceso_id',       procesoId);
        formData.append('tipo_documento',   'archivo');
        formData.append('nombre_documento', 'Voucher de pago');
        formData.append('archivo',          archivoVoucher);

    } else {
        // Modo normal: validar tipo y nombre
        const tipo   = document.getElementById('tipo_documento').value;
        const nombre = document.getElementById('nombre_documento_input').value.trim();

        if (!tipo)   { showMessage('Error', 'Seleccione el tipo de documento', true); return; }
        if (!nombre) { showMessage('Error', 'Ingrese el nombre del documento',  true); return; }

        if (tipo === 'archivo') {
            const archivo = document.getElementById('archivo-normal').files[0];
            if (!archivo) { showMessage('Error', 'Seleccione un archivo', true); return; }
            if (archivo.size > 20 * 1024 * 1024) { showMessage('Error', 'El archivo no debe superar 20MB', true); return; }
            formData.append('archivo', archivo);
        } else {
            const link = document.querySelector('input[name="link"]').value.trim();
            if (!link || (!link.startsWith('http://') && !link.startsWith('https://'))) {
                showMessage('Error', 'Ingrese un enlace válido (http:// o https://)', true); return;
            }
            formData.append('link', link);
        }
        formData.append('proceso_id',       procesoId);
        formData.append('tipo_documento',   tipo);
        formData.append('nombre_documento', nombre);
    }

    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subiendo...';
    btn.disabled  = true;

    fetch(`/mesa-partes/jrd/${jrdId}/documentos/mesapartes`, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body:    formData
    })
    .then(async r => {
        const text = await r.text();
        try { return { ok: r.ok, json: JSON.parse(text) }; }
        catch(err) { throw new Error(text.substring(0, 300)); }
    })
    .then(({ ok, json }) => {
        btn.innerHTML = originalText;
        btn.disabled  = false;

        if (ok && json.success) {
            // Cerrar modal y recargar lista
            bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal')).hide();
            document.getElementById('uploadDocumentForm').reset();

            fetch('{{ route("jrd.obtener.mesapartes") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        jrdList = data.jrd || [];
                        renderJrd(jrdList);
                    }
                    showMessage('Éxito', modo === 'voucher'
                        ? 'Voucher enviado. El administrador lo revisará.'
                        : 'Documento subido correctamente');
                });
        } else {
            showMessage('Error', json.message || json.detalle || 'Error al subir', true);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled  = false;
        showMessage('Error', 'Error de conexión: ' + error.message, true);
    });
});

// ── Filtro de búsqueda ────────────────────────────────────────────────────────
function filterJrd() {
    const searchTerm = document.getElementById('searchJrd').value.toLowerCase();
    let visibleCount = 0;

    document.querySelectorAll('.jrd-card').forEach(card => {
        const matches = card.getAttribute('data-materia').includes(searchTerm)
                     || card.getAttribute('data-id').includes(searchTerm)
                     || card.textContent.toLowerCase().includes(searchTerm);
        card.style.display = matches ? 'block' : 'none';
        if (matches) visibleCount++;
    });

    document.getElementById('noResults').style.display =
        (visibleCount === 0 && searchTerm !== '') ? 'block' : 'none';
}

// ── Render ────────────────────────────────────────────────────────────────────
function renderJrd(data) {
    const container = document.getElementById('jrdList');

    if (!data || data.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        container.innerHTML = '';
        return;
    }

    document.getElementById('noResults').style.display = 'none';

    container.innerHTML = data.map((jrd) => {


// DESPUÉS:
const rolConfig = {
    'Creador':      { color: 'bg-info',                  icono: 'fa-user-tie' },
    'Solicitante':  { color: 'bg-success',               icono: 'fa-user-check' },
    'Demandado':    { color: 'bg-warning text-dark',     icono: 'fa-user-shield' },
    'Contraparte':  { color: 'bg-danger',                icono: 'fa-user-slash' },
    'Demandante':   { color: 'bg-primary',               icono: 'fa-user-plus' },
    'Tercero':      { color: 'bg-secondary',             icono: 'fa-user-friends' },
};
const rolKey = jrd.es_creador ? 'Creador' : (jrd.rol_usuario || 'Observador');
const cfg    = rolConfig[rolKey] || { color: 'bg-secondary', icono: 'fa-user' };
const rolBadge = `<span class="badge ${cfg.color} ms-2"><i class="fas ${cfg.icono} me-1"></i>${rolKey}</span>`;

        const panelId          = `collapseJrd${jrd.id_jrd}`;
        const debeEstarAbierto = estaPanelAbierto(jrd.id_jrd);
        const expandedClass    = debeEstarAbierto ? 'show' : '';
        const ariaExpanded     = debeEstarAbierto ? 'true' : 'false';

        const alertaObservado = jrd.estado === 'observado' ? `
            <div class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Voucher rechazado.</strong> Tu voucher fue rechazado por el administrador.
                Debes subir un nuevo voucher para continuar el proceso.
            </div>` : '';

        return `
        <div class="card mb-3 shadow-sm jrd-card"
             data-materia="${(jrd.nombre_materia || '').toLowerCase()}"
             data-id="${jrd.id_jrd}">

            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">
                            <i class="fas fa-gavel text-danger me-2"></i>
                            ${jrd.nombre_materia || 'Sin materia'}
                            ${rolBadge}
                        </h5>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Iniciado: ${formatFecha(jrd.fecha_inicio)}
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge ${getEstadoBadge(jrd.estado)} px-3 py-2">
                            ${(jrd.estado || 'iniciado').toUpperCase()}
                        </span>
                        <button class="btn btn-sm btn-outline-secondary ms-2"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#${panelId}"
                                aria-expanded="${ariaExpanded}">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="${panelId}" class="collapse ${expandedClass}">
                <div class="card-body">

                    ${alertaObservado}

                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Información General
                            </h6>
                            <p><strong>Materia:</strong> ${jrd.nombre_materia || 'No especificada'}</p>
                            <p><strong>Pretensiones:</strong> ${jrd.pretenciones || 'No especificadas'}</p>
                            <p><strong>ID JPRD:</strong> #${jrd.id_jrd}</p>
                            <p><strong>Tu rol:</strong>
                              <span class="badge ${cfg.color}">
                                    <i class="fas ${cfg.icono} me-1"></i>${rolKey}
                                </span>
                            </p>
                            ${jrd.fecha_finalizacion ? `<p><strong>Finalizado:</strong> ${formatFecha(jrd.fecha_finalizacion)}</p>` : ''}
                        </div>
                    </div>

                    <!-- Personas Involucradas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-users me-2"></i>Personas Involucradas
                            </h6>
                            <div class="row">
                                ${jrd.personas && jrd.personas.length > 0 ? jrd.personas.map(persona => {
                                    let badgeClass = 'bg-secondary';
                                    if (persona.tipo === 'Solicitante')  badgeClass = 'bg-success';
                                    else if (persona.tipo === 'Demandado')   badgeClass = 'bg-warning text-dark';
                                    else if (persona.tipo === 'Contraparte') badgeClass = 'bg-info';
                                    return `
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 bg-light rounded">
                                            <span class="badge ${badgeClass} me-2">${persona.tipo}</span>
                                            <strong>${persona.nombres || ''} ${persona.apellidos || ''}</strong><br>
                                            <small class="text-muted">DNI: ${persona.dni || ''}</small>
                                            ${persona.correo ? `<br><small><i class="fas fa-envelope me-1"></i>${persona.correo}</small>` : ''}
                                        </div>
                                    </div>`;
                                }).join('') : '<p class="text-muted">No hay personas registradas</p>'}
                            </div>
                        </div>
                    </div>

                    <!-- Procesos / Etapas -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-tasks me-2"></i>
                                Etapas del Proceso (${jrd.procesos ? jrd.procesos.length : 0})
                            </h6>
                            ${jrd.procesos && jrd.procesos.length > 0 ? `
                                <div class="list-group">
                                    ${jrd.procesos.map(proceso => {

                                        const nombreEtapa = proceso.etapa
                                            ? proceso.etapa.nombre
                                            : 'Sin etapa';

                                        const esActivo    = proceso.estado === 'activo' || proceso.estado === 'iniciado';
                                        const esObservado = proceso.estado === 'observado';
                                        const puedeSubir  = esActivo || (jrd.estado === 'observado' && esObservado);

                                        return `
                                        <div class="list-group-item ${esObservado ? 'border-danger' : ''}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-layer-group text-primary me-2"></i>
                                                        <strong>${nombreEtapa}</strong>
                                                        ${esActivo    ? '<span class="badge bg-success ms-2">ACTIVO</span>'    : ''}
                                                        ${esObservado ? '<span class="badge bg-danger ms-2">OBSERVADO</span>'  : ''}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Iniciado: ${formatFecha(proceso.fecha_creacion || proceso.fecha)}
                                                        ${proceso.fecha_finalizacion
                                                            ? `<br><i class="fas fa-check-circle me-1"></i>Finalizado: ${formatFecha(proceso.fecha_finalizacion)}`
                                                            : ''}
                                                    </small>
                                                </div>
                                                <span class="badge ${getProcesoEstadoBadge(proceso.estado)} mb-2">
                                                    ${(proceso.estado || 'iniciado').toUpperCase()}
                                                </span>
                                            </div>

                                            ${proceso.documentos && proceso.documentos.length > 0 ? `
                                                <div class="mt-3 pt-3 border-top">
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="fas fa-paperclip me-1"></i>
                                                        Documentos adjuntos (${proceso.documentos.length}):
                                                    </small>
                                                    <div class="d-flex flex-column gap-2">
                                                        ${proceso.documentos.map(doc => {
                                                            const fechaSubida = doc.fecha_subida
                                                                ? formatFechaCorta(doc.fecha_subida)
                                                                : 'Fecha no disponible';
                                                            let icon = 'fa-link text-warning';
                                                            if (doc.tipo_documento === 'voucher')       icon = 'fa-receipt text-success';
                                                            else if (doc.tipo_documento === 'pdf')      icon = 'fa-file-pdf text-danger';
                                                            else if (doc.tipo_documento === 'imagen')   icon = 'fa-file-image text-primary';
                                                            else if (doc.ruta_archivo && doc.ruta_archivo.includes('drive.google.com'))
                                                                icon = 'fa-brands fa-google-drive text-warning';

                                                            const uploaderBadge = badgeSubidoPor(doc);

                                                            return `
                                                            <div class="p-2 bg-light rounded documento-item">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex flex-column gap-1 flex-grow-1">
                                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                            <i class="fas ${icon}"></i>
                                                                            <span class="small fw-semibold">${doc.nombre_original}</span>
                                                                            ${uploaderBadge}
                                                                        </div>
                                                                        <small class="text-muted ms-4 fecha-subida">
                                                                            <i class="far fa-calendar-alt me-1"></i>Subido: ${fechaSubida}
                                                                        </small>
                                                                    </div>
                                                                    <a href="${doc.ruta_archivo}"
                                                                       target="_blank"
                                                                       rel="noopener noreferrer"
                                                                       class="btn btn-sm btn-outline-secondary ms-2 flex-shrink-0">
                                                                        <i class="fas fa-eye me-1"></i>Ver
                                                                    </a>
                                                                </div>
                                                                ${doc.observaciones ? `
                                                                <div class="mt-2 ms-1 p-2 rounded observacion-doc">
                                                                    <small class="text-secondary">
                                                                        <i class="fas fa-comment-dots me-1 text-warning"></i>
                                                                        <strong>Observación:</strong> ${doc.observaciones}
                                                                    </small>
                                                                </div>` : ''}
                                                            </div>`;
                                                        }).join('')}
                                                    </div>
                                                </div>
                                            ` : '<p class="text-muted mt-2 mb-0 small">Sin documentos subidos</p>'}

                                            ${puedeSubir ? `
                                                <div class="mt-3 text-end">
                                                    ${esObservado ? `
                                                        <div class="alert alert-warning py-2 px-3 mb-2 text-start">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            <small>Voucher rechazado. Sube un nuevo voucher para continuar.</small>
                                                        </div>
                                                    ` : ''}
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            onclick="subirDocumento(${jrd.id_jrd}, ${proceso.id_proceso_jrd}, '${jrd.estado}')">
                                                        <i class="fas fa-cloud-upload-alt me-1"></i>
                                                        ${esObservado ? 'Subir nuevo voucher' : 'Subir documento'}
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>`;
                                    }).join('')}
                                </div>
                            ` : '<p class="text-muted">No hay procesos registrados</p>'}
                        </div>
                    </div>

                </div>
            </div>
        </div>`;
    }).join('');

    // Event listeners para persistir estado de paneles
    document.querySelectorAll('.collapse').forEach(collapse => {
        if (!collapse.id.startsWith('collapseJrd')) return;
        const jrdId = collapse.id.replace('collapseJrd', '');
        collapse.addEventListener('shown.bs.collapse',  () => togglePanelState(jrdId, true));
        collapse.addEventListener('hidden.bs.collapse', () => togglePanelState(jrdId, false));
    });
}

// ── Carga inicial ─────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const loading = document.getElementById('loading');
    loading.style.display = 'block';

    fetch('{{ route("jrd.obtener.mesapartes") }}')
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (data.success) {
                jrdList = data.jrd || [];
                renderJrd(jrdList);
            } else {
                document.getElementById('noResults').style.display = 'block';
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            console.error('Error:', error);
            document.getElementById('noResults').style.display = 'block';
        });

    document.getElementById('searchJrd').addEventListener('input', filterJrd);
});
</script>
@endpush

@push('styles')
<style>
.jrd-card { transition: all 0.3s ease; border: 1px solid #dee2e6; }
.jrd-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
.list-group-item { border-left: 4px solid #AD2B2E; margin-bottom: 10px; border-radius: 5px; transition: all 0.3s ease; }
.list-group-item.border-danger { border-left-color: #dc3545 !important; background-color: #fff8f8; }
.list-group-item:hover { background-color: #f8f9fa; }
.badge { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; }
.btn-danger { background-color: #AD2B2E; border-color: #AD2B2E; }
.btn-danger:hover { background-color: #8B2326; border-color: #8B2326; }
.btn-outline-danger { color: #AD2B2E; border-color: #AD2B2E; }
.btn-outline-danger:hover { background-color: #AD2B2E; border-color: #AD2B2E; color: white; }
.documento-item { transition: all 0.2s ease; }
.documento-item:hover { background-color: #f0f0f0 !important; transform: translateX(5px); }
.fecha-subida { font-size: 0.7rem; color: #6c757d; }
.observacion-doc { background-color: #fff8e1; border-left: 3px solid #ffc107; font-size: 0.75rem; }
.btn[data-bs-toggle="collapse"] i { transition: transform 0.3s ease; }
</style>
@endpush