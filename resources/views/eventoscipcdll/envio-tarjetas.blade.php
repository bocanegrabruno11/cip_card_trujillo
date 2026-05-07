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
</style>

{{-- OVERLAY --}}
<div id="loading-overlay" role="status" aria-live="polite" aria-label="Procesando...">
    <div class="loading-card">
        <div class="loading-icon" id="loading-icon">🪪</div>
        <div class="loading-title" id="loading-title">Generando tarjetas…</div>
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

<div class="dash-wrap">

    <div class="dash-header">
        <div class="dash-header-text">
            <h2>📋 Lista de Asistentes</h2>
            <p>Bienvenido, <strong>{{ session('usuario') }}</strong> — Listado completo de asistentes registrados</p>
        </div>
        <div class="dash-actions">

            {{-- GENERAR TARJETAS: AJAX por lotes --}}
            <button id="btn-generar" class="btn-accion btn-generar">
                🪪 Generar Tarjetas
            </button>

            {{-- ENVIAR CORREOS: AJAX por lotes --}}
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

    const CSRF = '{{ csrf_token() }}';

    // ── helpers ────────────────────────────────────────────────────────────

    function showOverlay(cfg) {
        icon.textContent  = cfg.icon;
        titleEl.textContent = cfg.title;
        subEl.textContent = cfg.sub || '';
        bar.className     = 'progress-bar ' + (cfg.color || '');
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
        bar.style.width   = p + '%';
        pctEl.textContent = p + '%';
        countEl.textContent = procesados + ' / ' + total;
        if (lote > 0 && total > 0) {
            const tamLote = 20;
            const totalLotes = Math.ceil(total / tamLote);
            loteEl.textContent = 'Lote ' + lote + ' de ' + totalLotes;
        }
    }

    function showAlert(type, msg) {
        jsAlert.className = 'alert ' + type;
        jsAlert.textContent = msg;
        jsAlert.style.display = 'block';
        setTimeout(() => { jsAlert.style.display = 'none'; }, 7000);
    }

    // ── función genérica por lotes ─────────────────────────────────────────
    // Llama a una ruta que acepta: { lote: N, tamano: 20 }
    // y devuelve: { procesados, total, lote_actual, finalizado, mensaje }

    async function procesarPorLotes(cfg) {
        showOverlay(cfg);

        let loteActual  = 1;
        let procesados  = 0;
        let total       = 0;
        let enviados    = 0;
        let erroresAcum = [];

        try {
            while (true) {
                subEl.textContent = cfg.subProcesando || 'Procesando lote ' + loteActual + '…';

                const body = { lote: loteActual, tamano: 20 };
                if (cfg.extraBody) Object.assign(body, cfg.extraBody);

                const resp = await fetch(cfg.ruta, {
                    method:  'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     CSRF,
                        'Accept':           'application/json',
                        'Content-Type':     'application/json',
                    },
                    body: JSON.stringify(body),
                });

                if (!resp.ok) {
                    const err = await resp.text();
                    throw new Error('HTTP ' + resp.status + ': ' + err);
                }

                const data = await resp.json();

                // Acumular
                total      = data.total      ?? total;
                procesados = data.procesados  ?? procesados;
                loteActual = data.lote_actual ?? loteActual;
                if (data.enviados  !== undefined) enviados    += data.enviados;
                if (data.errores   && Array.isArray(data.errores)) {
                    erroresAcum = erroresAcum.concat(data.errores);
                }

                setProgress(procesados, total, loteActual);

                if (data.finalizado) {
                    // Último lote terminado
                    setProgress(total, total, loteActual);
                    subEl.textContent = '¡Proceso completado!';
                    bar.className = 'progress-bar ' + (cfg.colorFin || cfg.color || '');
                    await sleep(600);
                    hideOverlay();

                    // Mensaje final
                    if (cfg.onFinish) {
                        cfg.onFinish({ procesados, total, enviados, errores: erroresAcum, data });
                    }
                    return;
                }

                // Pequeña pausa entre lotes para no saturar
                await sleep(cfg.pausaMs || 300);
                loteActual++;
            }
        } catch (e) {
            console.error(cfg.titulo + ' error:', e);
            hideOverlay();
            showAlert('err', '❌ Error: ' + e.message);
        }
    }

    function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

    // ── BOTÓN: GENERAR TARJETAS ────────────────────────────────────────────

    btnGen.addEventListener('click', function () {
        procesarPorLotes({
            ruta:          '{{ route("tarjetas.generar") }}',
            icon:          '🪪',
            title:         'Generando tarjetas…',
            sub:           'Preparando el proceso por lotes',
            subProcesando: 'Generando imágenes PNG…',
            color:         '',
            colorFin:      '',
            pausaMs:       200,
            onFinish({ procesados, total, errores, data }) {
                if (errores.length > 0) {
                    showAlert('err', '⚠ Generadas: ' + procesados + ' | Errores: ' + errores.length + '. Revisa consola.');
                    console.table(errores);
                } else {
                    showAlert('ok', '✅ ' + procesados + ' tarjetas generadas correctamente. ' + (data.mensaje || ''));
                }
            },
        });
    });

    // ── BOTÓN: ENVIAR CORREOS ──────────────────────────────────────────────

    btnEnv.addEventListener('click', function () {
        if (!confirm('¿Enviar la tarjeta de entrada por correo a todos los asistentes (en lotes de 20)?')) return;

        procesarPorLotes({
            ruta:          '/enviar-tarjetas',
            icon:          '📨',
            title:         'Enviando correos…',
            sub:           'Adjuntando tarjetas y enviando vía Resend',
            subProcesando: 'Enviando lote de correos…',
            color:         'green',
            colorFin:      'green',
            pausaMs:       500,
            onFinish({ enviados, errores }) {
                if (errores.length > 0) {
                    showAlert('err', '⚠ Enviados: ' + enviados + ' | Con error: ' + errores.length + '. Revisa consola.');
                    console.table(errores);
                } else {
                    showAlert('ok', '✅ Correos enviados: ' + enviados + '.');
                }
            },
        });
    });

})();
</script>

@endsection