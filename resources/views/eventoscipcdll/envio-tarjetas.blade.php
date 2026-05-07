@extends('eventoscipcdll.layout')

@section('title', 'Lista de Asistentes')

@section('content')

<style>
*{box-sizing:border-box;margin:0;padding:0}
.dash-wrap{padding:1.5rem;font-family:inherit}
.dash-header{margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px}
.dash-header-text h2{font-size:20px;font-weight:600;color:#1a1a1a;margin-bottom:5px}
.dash-header-text p{font-size:13px;color:#666}
.dash-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-start}

.btn-accion{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .2s;text-decoration:none}
.btn-generar{background:#1a1a1a;color:#fff}
.btn-generar:hover{background:#333;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,0.2)}
.btn-generar:disabled{background:#666;cursor:not-allowed;transform:none;box-shadow:none}
.btn-enviar{background:#97C459;color:#fff}
.btn-enviar:hover{background:#82b044;transform:translateY(-1px);box-shadow:0 4px 12px rgba(151,196,89,0.35)}
.btn-enviar:disabled{background:#b5d487;cursor:not-allowed;transform:none;box-shadow:none}

/* ── OVERLAY ── */
#loading-overlay{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.50);align-items:center;justify-content:center}
#loading-overlay.active{display:flex}
.loading-card{background:#fff;border-radius:16px;padding:36px 48px;text-align:center;min-width:340px;max-width:420px;box-shadow:0 8px 40px rgba(0,0,0,0.22)}
.loading-icon{font-size:38px;margin-bottom:12px;animation:bounce .8s infinite alternate}
@keyframes bounce{from{transform:translateY(0)}to{transform:translateY(-8px)}}
.loading-title{font-size:16px;font-weight:700;color:#1a1a1a;margin-bottom:4px}
.loading-sub{font-size:13px;color:#888;margin-bottom:20px;min-height:18px}
.progress-wrap{background:#e8e7e0;border-radius:99px;height:10px;overflow:hidden;margin-bottom:8px}
.progress-bar{height:100%;border-radius:99px;width:0%;transition:width .5s ease;background:#1a1a1a}
.progress-bar.green{background:#97C459}
.progress-bar.red{background:#dc2626}
.progress-info{display:flex;justify-content:space-between;font-size:11px;color:#aaa;margin-bottom:4px}
.lote-label{font-size:11px;color:#bbb;margin-top:6px}

/* ── CARDS RESUMEN ── */
.resumen-wrap{display:flex;gap:12px;margin-bottom:1.5rem;flex-wrap:wrap}
.resumen-card{flex:1;min-width:130px;background:#fff;border-radius:10px;padding:14px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border:1px solid #e8e7e0;display:flex;align-items:center;gap:12px}
.resumen-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.resumen-icon.total{background:#f0f0f0}
.resumen-icon.pend{background:#FAEEDA}
.resumen-icon.ap{background:#EAF3DE}
.resumen-icon.re{background:#FCEBEB}
.resumen-num{font-size:22px;font-weight:700;color:#1a1a1a;line-height:1}
.resumen-label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-top:3px}

/* ── TABLA ── */
.tbl-wrap{border:1px solid #d8d7d0;border-radius:10px;overflow:hidden;margin-top:1rem}
.tbl-scroll-head{overflow:hidden;background:#f2f1ec;border-bottom:1px solid #e0dfd8}
.tbl-scroll-head table{width:100%;border-collapse:collapse;table-layout:fixed}
.tbl-scroll-body{overflow-y:auto;max-height:420px}
.tbl-scroll-body table{width:100%;border-collapse:collapse;table-layout:fixed}
.tbl-scroll-head table th,
.tbl-scroll-body table td{padding:10px 11px;font-size:13px;color:#1a1a1a;border-bottom:1px solid #ebebeb;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.tbl-scroll-head table th{font-size:11px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.5px;padding:12px 11px;border-bottom:none}
.col-num{width:45px}.col-cip{width:90px}.col-nom{width:140px}
.col-ape{width:140px}.col-cap{width:110px}.col-dni{width:90px}
.col-cel{width:100px}.col-cor{width:190px}.col-est{width:100px}.col-asi{width:80px}
.tbl-scroll-body table tr:nth-child(even){background:#fafaf8}
.tbl-scroll-body table tr:hover{background:#f5f5f0}
.tbl-scroll-body table tr:last-child td{border-bottom:none}
.tbl-scroll-body::-webkit-scrollbar{width:6px}
.tbl-scroll-body::-webkit-scrollbar-track{background:#f2f1ec}
.tbl-scroll-body::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px}
.tbl-scroll-body::-webkit-scrollbar-thumb:hover{background:#aaa}

.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:600}
.badge.registrado{background:#FAEEDA;color:#854F0B}
.badge.aprobado{background:#EAF3DE;color:#3B6D11}
.badge.rechazado{background:#FCEBEB;color:#A32D2D}
.asistio-si{color:#3B6D11;font-weight:600}
.asistio-no{color:#A32D2D;font-weight:600}
.empty-state{text-align:center;padding:2rem;color:#bbb}
.tbl-footer{padding:10px 16px;background:#f2f1ec;border-top:1px solid #e0dfd8;font-size:12px;color:#888}

.alert{margin-top:10px;padding:12px 16px;border-radius:8px;font-size:13px;width:100%}
.alert.ok{background:#EAF3DE;color:#3B6D11}
.alert.err{background:#FCEBEB;color:#A32D2D}

/* ── MODAL ── */
.modal-reporte {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 10000;
    align-items: center;
    justify-content: center;
}
.modal-reporte.active {
    display: flex;
}
.modal-contenido {
    background: #fff;
    border-radius: 20px;
    padding: 30px 40px;
    max-width: 500px;
    width: 90%;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    animation: modalFade 0.3s ease;
}
@keyframes modalFade {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.modal-icono {
    font-size: 54px;
    margin-bottom: 15px;
}
.modal-titulo {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #1a1a1a;
}
.modal-resumen {
    background: #f5f5f0;
    border-radius: 12px;
    padding: 15px;
    margin: 20px 0;
    display: flex;
    justify-content: space-around;
}
.modal-resumen-item {
    text-align: center;
}
.modal-resumen-num {
    font-size: 28px;
    font-weight: 700;
}
.modal-resumen-num.success { color: #3B6D11; }
.modal-resumen-num.error { color: #A32D2D; }
.modal-resumen-label {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}
.modal-boton {
    background: #1a1a1a;
    color: #fff;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin: 5px;
}
.modal-boton:hover {
    background: #333;
    transform: translateY(-2px);
}
.modal-boton.excel {
    background: #97C459;
}
.modal-boton.excel:hover {
    background: #82b044;
}
.modal-boton.cerrar {
    background: #ccc;
    color: #333;
}
.modal-boton.cerrar:hover {
    background: #bbb;
}
</style>

{{-- OVERLAY --}}
<div id="loading-overlay" role="status" aria-live="polite" aria-label="Procesando...">
    <div class="loading-card">
        <div class="loading-icon" id="loading-icon">📨</div>
        <div class="loading-title" id="loading-title">Enviando correos…</div>
        <div class="loading-sub" id="loading-sub">Preparando el proceso por lotes</div>
        <div class="progress-wrap">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <div class="progress-info">
            <span id="progress-pct">0%</span>
            <span id="progress-count">0 / 0</span>
        </div>
        <div class="lote-label" id="lote-label">Iniciando…</div>
    </div>
</div>

{{-- MODAL REPORTE --}}
<div id="modal-reporte" class="modal-reporte">
    <div class="modal-contenido">
        <div class="modal-icono" id="modal-icono">📊</div>
        <div class="modal-titulo" id="modal-titulo">Proceso Completado</div>
        <div class="modal-resumen" id="modal-resumen">
            <div class="modal-resumen-item">
                <div class="modal-resumen-num success" id="modal-enviados">0</div>
                <div class="modal-resumen-label">Enviados</div>
            </div>
            <div class="modal-resumen-item">
                <div class="modal-resumen-num error" id="modal-fallidos">0</div>
                <div class="modal-resumen-label">Fallidos</div>
            </div>
            <div class="modal-resumen-item">
                <div class="modal-resumen-num" id="modal-total">0</div>
                <div class="modal-resumen-label">Total</div>
            </div>
        </div>
        <button class="modal-boton excel" id="btn-descargar-excel">📥 Descargar Reporte Excel</button>
        <button class="modal-boton cerrar" id="btn-cerrar-modal">Cerrar</button>
    </div>
</div>

<div class="dash-wrap">

    <div class="dash-header">
        <div class="dash-header-text">
            <h2>📋 Lista de Asistentes</h2>
            <p>Bienvenido, <strong>{{ session('usuario') }}</strong> — Listado completo de asistentes registrados</p>
        </div>
        <div class="dash-actions">

            <button id="btn-generar" class="btn-accion btn-generar">
                🪪 Generar Tarjetas
            </button>

            <button id="btn-enviar" class="btn-accion btn-enviar">
                📨 Enviar Tarjetas
            </button>

            @if(session('success'))
                <div class="alert ok">✅ {{ session('success') }}</div>
            @endif
            @if(session('errores') && count(session('errores')) > 0)
                <div class="alert err">
                    @foreach(session('errores') as $err)
                        <div>⚠ {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <div id="js-alert" class="alert" style="display:none"></div>
        </div>
    </div>

    <div class="resumen-wrap">
        <div class="resumen-card">
            <div class="resumen-icon total">👥</div>
            <div>
                <div class="resumen-num">{{ $asistentes->count() }}</div>
                <div class="resumen-label">Total</div>
            </div>
        </div>
        <div class="resumen-card">
            <div class="resumen-icon pend">📋</div>
            <div>
                <div class="resumen-num">{{ $asistentes->where('estado', 'registrado')->count() }}</div>
                <div class="resumen-label">Pendientes</div>
            </div>
        </div>
        <div class="resumen-card">
            <div class="resumen-icon ap">✅</div>
            <div>
                <div class="resumen-num">{{ $asistentes->where('estado', 'aprobado')->count() }}</div>
                <div class="resumen-label">Aprobados</div>
            </div>
        </div>
        <div class="resumen-card">
            <div class="resumen-icon re">❌</div>
            <div>
                <div class="resumen-num">{{ $asistentes->where('estado', 'rechazado')->count() }}</div>
                <div class="resumen-label">Rechazados</div>
            </div>
        </div>
    </div>

    <div class="tbl-wrap">
        <div class="tbl-scroll-head">
            <table>
                <colgroup>
                    <col class="col-num"><col class="col-cip"><col class="col-nom">
                    <col class="col-ape"><col class="col-cap"><col class="col-dni">
                    <col class="col-cel"><col class="col-cor"><col class="col-est">
                    <col class="col-asi">
                </colgroup>
                <thead>
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-cip">CIP</th>
                        <th class="col-nom">Nombres</th>
                        <th class="col-ape">Apellidos</th>
                        <th class="col-cap">Capítulo</th>
                        <th class="col-dni">DNI</th>
                        <th class="col-cel">Celular</th>
                        <th class="col-cor">Correo</th>
                        <th class="col-est">Estado</th>
                        <th class="col-asi">Asistió</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tbl-scroll-body">
            <table>
                <colgroup>
                    <col class="col-num"><col class="col-cip"><col class="col-nom">
                    <col class="col-ape"><col class="col-cap"><col class="col-dni">
                    <col class="col-cel"><col class="col-cor"><col class="col-est">
                    <col class="col-asi">
                </colgroup>
                <tbody>
                    @forelse ($asistentes as $index => $a)
                        <tr>
                            <td class="col-num">{{ $index + 1 }}</td>
                            <td class="col-cip"><strong>{{ $a->cip ?? '-' }}</strong></td>
                            <td class="col-nom">{{ $a->nombres ?? '-' }}</td>
                            <td class="col-ape">{{ $a->apellidos ?? '-' }}</td>
                            <td class="col-cap">{{ $a->capitulo ?? '-' }}</td>
                            <td class="col-dni">{{ $a->dni ?? '-' }}</td>
                            <td class="col-cel">{{ $a->celular ?? '-' }}</td>
                            <td class="col-cor">{{ $a->correo ?? '-' }}</td>
                            <td class="col-est">
                                <span class="badge {{ $a->estado ?? 'registrado' }}">
                                    {{ $a->estado === 'registrado' ? 'Pendiente' : ($a->estado === 'aprobado' ? 'Aprobado' : 'Rechazado') }}
                                </span>
                            </td>
                            <td class="col-asi">
                                @if(strtolower($a->asistio ?? '') === 'si' || $a->asistio == 1)
                                    <span class="asistio-si">✔ Sí</span>
                                @elseif(strtolower($a->asistio ?? '') === 'no' || $a->asistio == 0)
                                    <span class="asistio-no">✘ No</span>
                                @else
                                    <span style="color:#bbb">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-state">No hay asistentes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($asistentes->count() > 0)
            <div class="tbl-footer">Mostrando {{ $asistentes->count() }} registro(s)</div>
        @endif
    </div>

</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>

<script>
(function () {
    const overlay   = document.getElementById('loading-overlay');
    const icon      = document.getElementById('loading-icon');
    const titleEl   = document.getElementById('loading-title');
    const subEl     = document.getElementById('loading-sub');
    const bar       = document.getElementById('progress-bar');
    const pctEl     = document.getElementById('progress-pct');
    const countEl   = document.getElementById('progress-count');
    const loteEl    = document.getElementById('lote-label');
    const jsAlert   = document.getElementById('js-alert');
    const btnGen    = document.getElementById('btn-generar');
    const btnEnv    = document.getElementById('btn-enviar');

    // Elementos del modal
    const modal         = document.getElementById('modal-reporte');
    const modalEnviados = document.getElementById('modal-enviados');
    const modalFallidos = document.getElementById('modal-fallidos');
    const modalTotal    = document.getElementById('modal-total');
    const btnDescargar  = document.getElementById('btn-descargar-excel');
    const btnCerrarModal= document.getElementById('btn-cerrar-modal');

    const CSRF = '{{ csrf_token() }}';

    // Array para guardar el reporte de envío de correos
    let reporteCorreos = [];

    function showOverlay(cfg) {
        icon.textContent    = cfg.icon;
        titleEl.textContent = cfg.title;
        subEl.textContent   = cfg.sub || '';
        bar.className       = 'progress-bar ' + (cfg.color || '');
        setProgress(0, 0, 0);
        loteEl.textContent = 'Iniciando…';
        overlay.classList.add('active');
        btnGen.disabled = true;
        btnEnv.disabled = true;
    }

    function hideOverlay() {
        overlay.classList.remove('active');
        btnGen.disabled = false;
        btnEnv.disabled = false;
    }

    function setProgress(procesados, total, lote) {
        const p = total > 0 ? Math.round((procesados / total) * 100) : 0;
        bar.style.width     = p + '%';
        pctEl.textContent   = p + '%';
        countEl.textContent = procesados + ' / ' + total;
        if (lote > 0 && total > 0) {
            const totalLotes = Math.ceil(total / 20);
            loteEl.textContent = 'Lote ' + lote + ' de ' + totalLotes;
        }
    }

    function showAlert(type, msg) {
        jsAlert.className   = 'alert ' + type;
        jsAlert.textContent = msg;
        jsAlert.style.display = 'block';
        setTimeout(() => { jsAlert.style.display = 'none'; }, 7000);
    }

    function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

    function mostrarModal(enviados, fallidos, total) {
        modalEnviados.textContent = enviados;
        modalFallidos.textContent = fallidos;
        modalTotal.textContent    = total;
        modal.classList.add('active');
    }

    function descargarExcelReporte() {
        if (reporteCorreos.length === 0) {
            showAlert('err', '⚠ No hay datos para exportar');
            return;
        }

        const headers = ['CIP', 'Nombres', 'Apellidos', 'DNI', 'Correo', 'Estado Envío', 'Mensaje'];
        const data    = [headers];

        reporteCorreos.forEach(item => {
            data.push([
                item.cip       || '-',
                item.nombres   || '-',
                item.apellidos || '-',
                item.dni       || '-',
                item.correo    || '-',
                item.estado    || 'Pendiente',
                item.mensaje   || ''
            ]);
        });

        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Reporte Envío Correos');
        ws['!cols'] = [{wch:12},{wch:25},{wch:25},{wch:15},{wch:35},{wch:15},{wch:40}];

        const fecha = new Date().toISOString().slice(0,19).replace(/:/g, '-');
        XLSX.writeFile(wb, `reporte_envio_correos_${fecha}.xlsx`);
        showAlert('ok', '✅ Reporte Excel descargado correctamente');
    }

    // ── BOTÓN: GENERAR TARJETAS ────────────────────────────────────────────
    btnGen.addEventListener('click', function () {
        procesarPorLotes({
            ruta:          '{{ route("tarjetas.generar") }}',
            icon:          '🪪',
            title:         'Generando tarjetas…',
            sub:           'Preparando el proceso por lotes',
            subProcesando: 'Generando imágenes PNG…',
            color:         '',
            pausaMs:       200,
            onFinish({ procesados, total, errores }) {
                if (errores.length > 0) {
                    showAlert('err', '⚠ Generadas: ' + procesados + ' | Errores: ' + errores.length);
                } else {
                    showAlert('ok', '✅ ' + procesados + ' tarjetas generadas correctamente.');
                }
            },
        });
    });

    // ── BOTÓN: ENVIAR CORREOS ──────────────────────────────────────────────
    // FIX: ya no captura la tabla HTML. Manda { lote, tamano } al backend
    // igual que procesarPorLotes, para que el backend avance el offset
    // correctamente y no repita siempre los mismos 20 registros.
    btnEnv.addEventListener('click', async function () {
        if (!confirm('¿Enviar tarjetas por correo a todos los asistentes (en lotes de 20)?')) return;

        reporteCorreos  = [];
        let loteActual  = 1;
        let procesados  = 0;
        let total       = 0;
        let enviados    = 0;
        let finalizado  = false;

        showOverlay({
            icon:  '📨',
            title: 'Enviando correos...',
            sub:   'Procesando en lotes de 20',
            color: 'green',
        });

        while (!finalizado) {
            subEl.textContent = 'Enviando lote ' + loteActual + '…';

            try {
                const resp = await fetch('/enviar-tarjetas', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     CSRF,
                        'Accept':           'application/json',
                        'Content-Type':     'application/json',
                    },
                    // ← CORRECCIÓN CLAVE: mandamos lote y tamano, no el array de asistentes
                    body: JSON.stringify({ lote: loteActual, tamano: 20 }),
                });

                if (!resp.ok) throw new Error('HTTP ' + resp.status);

                const data = await resp.json();

                // Actualizar contadores con lo que devuelve el backend
                total      = data.total      ?? total;
                procesados = data.procesados ?? procesados;
                finalizado = data.finalizado ?? false;
                enviados  += data.enviados   ?? 0;

                // Acumular errores de este lote en el reporte
                (data.errores ?? []).forEach(err => {
                    reporteCorreos.push({
                        cip:       err.cip    || '-',
                        nombres:   '-',
                        apellidos: '-',
                        dni:       '-',
                        correo:    err.correo || '-',
                        estado:    'RECHAZADO',
                        mensaje:   err.error  || 'Error desconocido',
                    });
                });

                setProgress(procesados, total, loteActual);

            } catch (error) {
                console.error('Error en lote ' + loteActual + ':', error);
                // Si falla la petición completa, contamos el lote como fallido
                // y continuamos para no bloquear el proceso
                procesados += 20;
                setProgress(procesados, total, loteActual);
            }

            loteActual++;
            await sleep(500);
        }

        // Finalizar
        setProgress(total, total, loteActual - 1);
        subEl.textContent = '¡Proceso completado!';
        await sleep(800);
        hideOverlay();

        const fallidos = reporteCorreos.filter(r => r.estado === 'RECHAZADO').length;
        mostrarModal(enviados, fallidos, total);
    });

    // DESCARGAR EXCEL
    btnDescargar.addEventListener('click', descargarExcelReporte);

    // CERRAR MODAL
    btnCerrarModal.addEventListener('click', () => modal.classList.remove('active'));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });

    // ── FUNCIÓN GENÉRICA: PROCESAR POR LOTES (NO TOCAR) ───────────────────
    async function procesarPorLotes(cfg) {
        showOverlay(cfg);

        let loteActual  = 1;
        let procesados  = 0;
        let total       = 0;
        let erroresAcum = [];

        try {
            while (true) {
                subEl.textContent = cfg.subProcesando + ' (Lote ' + loteActual + ')';

                const resp = await fetch(cfg.ruta, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     CSRF,
                        'Accept':           'application/json',
                        'Content-Type':     'application/json',
                    },
                    body: JSON.stringify({ lote: loteActual, tamano: 20 }),
                });

                if (!resp.ok) throw new Error('HTTP ' + resp.status);

                const data = await resp.json();
                total      = data.total       || total;
                procesados = data.procesados  || procesados;
                loteActual = data.lote_actual || loteActual;

                if (data.errores) erroresAcum = erroresAcum.concat(data.errores);

                setProgress(procesados, total, loteActual);

                if (data.finalizado) {
                    setProgress(total, total, loteActual);
                    subEl.textContent = '¡Proceso completado!';
                    await sleep(600);
                    hideOverlay();
                    if (cfg.onFinish) cfg.onFinish({ procesados, total, errores: erroresAcum, data });
                    return;
                }

                await sleep(cfg.pausaMs || 300);
                loteActual++;
            }
        } catch (e) {
            hideOverlay();
            showAlert('err', '❌ Error: ' + e.message);
        }
    }

})();
</script>

@endsection