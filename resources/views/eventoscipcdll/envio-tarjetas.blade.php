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
.btn-generar:disabled{background:#666;cursor:not-allowed}
.btn-enviar{background:#97C459;color:#fff}
.btn-enviar:hover{background:#82b044;transform:translateY(-1px);box-shadow:0 4px 12px rgba(151,196,89,0.35)}
.btn-enviar:disabled{background:#b5d487;cursor:not-allowed}

#loading-overlay{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center}
#loading-overlay.active{display:flex}
.loading-card{background:#fff;border-radius:16px;padding:36px 48px;text-align:center;min-width:320px;box-shadow:0 8px 40px rgba(0,0,0,0.18)}
.loading-icon{font-size:36px;margin-bottom:12px;animation:bounce .8s infinite alternate}
@keyframes bounce{from{transform:translateY(0)}to{transform:translateY(-8px)}}
.loading-title{font-size:16px;font-weight:700;color:#1a1a1a;margin-bottom:4px}
.loading-sub{font-size:13px;color:#888;margin-bottom:20px}
.progress-wrap{background:#e8e7e0;border-radius:99px;height:8px;overflow:hidden;margin-bottom:10px}
.progress-bar{height:100%;border-radius:99px;width:0%;transition:width .4s ease;background:#1a1a1a}
.progress-bar.green{background:#97C459}
.progress-pct{font-size:12px;color:#999;text-align:right}

.resumen-wrap{display:flex;gap:12px;margin-bottom:1.5rem;flex-wrap:wrap}
.resumen-card{flex:1;min-width:130px;background:#fff;border-radius:10px;padding:14px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border:1px solid #e8e7e0;display:flex;align-items:center;gap:12px}
.resumen-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.resumen-icon.total{background:#f0f0f0}
.resumen-icon.pend{background:#FAEEDA}
.resumen-icon.ap{background:#EAF3DE}
.resumen-icon.re{background:#FCEBEB}
.resumen-num{font-size:22px;font-weight:700;color:#1a1a1a;line-height:1}
.resumen-label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-top:3px}

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
        <div class="loading-sub" id="loading-sub">Esto puede tardar unos segundos</div>
        <div class="progress-wrap">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <div class="progress-pct" id="progress-pct">0%</div>
    </div>
</div>

<div class="dash-wrap">

    <div class="dash-header">
        <div class="dash-header-text">
            <h2>📋 Lista de Asistentes</h2>
            <p>Bienvenido, <strong>{{ session('usuario') }}</strong> — Listado completo de asistentes registrados</p>
        </div>
        <div class="dash-actions">

            {{-- GENERAR TARJETAS: POST normal (como código 2, que funciona) + overlay visual --}}
            <form id="form-generar" method="POST" action="{{ route('tarjetas.generar') }}" style="display:inline">
                @csrf
                <button type="submit" id="btn-generar" class="btn-accion btn-generar">
                    🪪 Generar Tarjetas
                </button>
            </form>

            {{-- ENVIAR CORREOS: fetch AJAX --}}
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
    const overlay    = document.getElementById('loading-overlay');
    const icon       = document.getElementById('loading-icon');
    const title      = document.getElementById('loading-title');
    const sub        = document.getElementById('loading-sub');
    const bar        = document.getElementById('progress-bar');
    const pct        = document.getElementById('progress-pct');
    const jsAlert    = document.getElementById('js-alert');
    const btnGenerar = document.getElementById('btn-generar');
    const btnEnviar  = document.getElementById('btn-enviar');
    const formGen    = document.getElementById('form-generar');

    function showOverlay(cfg) {
        icon.textContent  = cfg.icon;
        title.textContent = cfg.title;
        sub.textContent   = cfg.sub;
        bar.className     = 'progress-bar ' + (cfg.color || '');
        bar.style.width   = '0%';
        pct.textContent   = '0%';
        overlay.classList.add('active');
        btnEnviar.disabled = true;
    }

    function hideOverlay() {
        overlay.classList.remove('active');
        btnEnviar.disabled = false;
    }

    function fakeProgress(durationMs, color) {
        bar.className = 'progress-bar ' + (color || '');
        const start = performance.now();
        function step(now) {
            const elapsed = now - start;
            const p = Math.min(90, (elapsed / durationMs) * 90);
            bar.style.width = p + '%';
            pct.textContent = Math.round(p) + '%';
            if (p < 90) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    function finishProgress(color) {
        bar.className = 'progress-bar ' + (color || '');
        bar.style.width = '100%';
        pct.textContent = '100%';
    }

    function showAlert(type, msg) {
        jsAlert.className = 'alert ' + type;
        jsAlert.textContent = msg;
        jsAlert.style.display = 'block';
        setTimeout(() => { jsAlert.style.display = 'none'; }, 6000);
    }

    /* ========== FUNCIÓN 1: GENERAR TARJETAS (POST normal + overlay visual) ========== */
    // No usamos fetch: el controlador hace redirect normal (session flash).
    // Solo mostramos el overlay mientras la página carga y dejamos que el form se envíe.
    formGen.addEventListener('submit', function () {
        showOverlay({ icon: '🪪', title: 'Generando tarjetas…', sub: 'Creando imagen PNG para cada colegiado', color: '' });
        fakeProgress(8000, '');
        btnGenerar.disabled = true;
        // No llamamos e.preventDefault() → el form POST se envía normalmente
        // El overlay desaparece solo cuando la página recarga con la respuesta del servidor
    });

    /* ========== FUNCIÓN 2: ENVIAR TARJETAS POR CORREO (fetch AJAX) ========== */
    btnEnviar.addEventListener('click', function () {
        if (!confirm('¿Enviar la tarjeta de entrada por correo a todos los asistentes?')) return;

        showOverlay({ icon: '📨', title: 'Enviando correos…', sub: 'Adjuntando tarjetas y enviando vía Resend', color: 'green' });
        fakeProgress(12000, 'green');

        fetch('/enviar-tarjetas', {
            method:  'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':     '{{ csrf_token() }}',
                'Accept':           'application/json',
                'Content-Type':     'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            finishProgress('green');
            sub.textContent = '¡Correos enviados!';
            setTimeout(() => {
                hideOverlay();
                const errCount = data.errores ? data.errores.length : 0;
                if (errCount > 0) {
                    showAlert('err', '⚠ Enviados: ' + data.enviados + ' | Con error: ' + errCount + '. Revisa la consola.');
                    console.table(data.errores);
                } else {
                    showAlert('ok', '✅ Correos enviados: ' + data.enviados + '. ' + (data.mensaje || ''));
                }
            }, 600);
        })
        .catch(error => {
            console.error('Error:', error);
            hideOverlay();
            showAlert('err', '❌ Error al enviar correos. Verifica la ruta /enviar-tarjetas');
        });
    });

})();
</script>

@endsection