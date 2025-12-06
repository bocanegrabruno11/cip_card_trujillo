@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Tarifas y Escalas</h2>
        <div>
            <a href="{{ route('tarifas_config.index') }}" class="btn btn-dark shadow-sm me-2">
                <i class="fas fa-cogs"></i> Variables
            </a>
            <a href="{{ route('calculadoras-gestion.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle"></i> Nueva Escala
            </a>
        </div>
    </div>

    {{-- ALERTAS (Se mantienen igual) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-ban me-1"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORMULARIO DE FILTROS (Se mantiene igual) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('calculadoras-gestion.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Filtrar por Tipo</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">Todas las tablas</option>
                            <option value="arbitro_unico" {{ request('tipo') == 'arbitro_unico' ? 'selected' : '' }}>Honorarios Árbitro Único</option>
                            <option value="tribunal_arbitral" {{ request('tipo') == 'tribunal_arbitral' ? 'selected' : '' }}>Honorarios Tribunal Arbitral</option>
                            <option value="gastos_administrativos" {{ request('tipo') == 'gastos_administrativos' ? 'selected' : '' }}>Gastos Administrativos</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <a href="{{ route('calculadoras-gestion.index') }}" class="btn btn-sm btn-outline-secondary w-50" title="Limpiar"><i class="fas fa-eraser"></i></a>
                        <button type="submit" class="btn btn-sm btn-dark w-100"><i class="fas fa-search"></i> Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tipo</th>
                        <th class="text-center">Rango</th>
                        <th class="text-end">Desde (S/.)</th>
                        <th class="text-end">Hasta (S/.)</th>
                        <th class="text-end">Monto Fijo</th>
                        <th class="text-center">% Exceso</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifas as $item)
                    <tr>
                        <td class="ps-4">
                            @if($item->tipo == 'arbitro_unico') <span class="badge bg-primary">Árbitro Único</span>
                            @elseif($item->tipo == 'tribunal_arbitral') <span class="badge bg-success">Tribunal</span>
                            @else <span class="badge bg-secondary">Gastos Admin</span> @endif
                        </td>
                        <td class="text-center fw-bold">{{ $item->rango_letra }}</td>
                        <td class="text-end">{{ number_format($item->monto_min, 2) }}</td>
                        <td class="text-end">{{ $item->monto_max ? number_format($item->monto_max, 2) : 'A más' }}</td>
                        <td class="text-end fw-bold">S/. {{ number_format($item->monto_fijo, 2) }}</td>
                        <td class="text-center">
                            @if($item->porcentaje_exceso > 0) <span class="badge bg-warning text-dark">{{ $item->porcentaje_exceso }}%</span>
                            @else <span class="text-muted">-</span> @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('calculadoras-gestion.show', $item->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('calculadoras-gestion.edit', $item->id) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                                
                                {{-- BOTÓN ELIMINAR CON MODAL --}}
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmAction('{{ route('calculadoras-gestion.destroy', $item->id) }}', 'delete')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay tarifas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $tarifas->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold" id="modalTitle">Confirmar Acción</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="mb-3">
            <i class="fas fa-exclamation-circle fa-3x text-warning"></i>
        </div>
        <h5 class="fw-bold mb-2" id="modalHeader">¿Estás seguro?</h5>
        <p class="text-muted mb-0" id="modalMessage">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        
        {{-- Formulario oculto que se envía --}}
        <form id="confirmForm" action="" method="POST">
            @csrf 
            <input type="hidden" name="_method" id="formMethod" value="DELETE">
            <button type="submit" class="btn btn-danger px-4 fw-bold" id="confirmBtnText">Sí, eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Alertas auto-dismiss
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alertNode) {
            setTimeout(function() {
                if (alertNode) {
                    const bsAlert = new bootstrap.Alert(alertNode);
                    bsAlert.close();
                }
            }, 4000); 
        });
    });

    // === FUNCIÓN PARA ABRIR MODAL DINÁMICO ===
    function confirmAction(url, type) {
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const form = document.getElementById('confirmForm');
        const methodInput = document.getElementById('formMethod');
        const header = document.getElementById('modalHeader');
        const msg = document.getElementById('modalMessage');
        const btn = document.getElementById('confirmBtnText');
        const titleContainer = document.querySelector('.modal-header');

        // Configurar ruta
        form.action = url;

        // Configurar textos y colores según tipo
        if (type === 'delete') {
            methodInput.value = 'DELETE';
            titleContainer.className = 'modal-header bg-danger text-white border-0';
            header.innerText = '¿Eliminar este registro?';
            msg.innerText = 'Si lo eliminas, la calculadora podría verse afectada si no hay otros rangos.';
            btn.className = 'btn btn-danger px-4 fw-bold';
            btn.innerText = 'Sí, eliminar';
        } 
        /* Si en el futuro quieres usarlo para cambiar estado (PUT):
        else if (type === 'toggle') {
            methodInput.value = 'PUT';
            titleContainer.className = 'modal-header bg-primary text-white border-0';
            header.innerText = '¿Cambiar visibilidad?';
            msg.innerText = 'El estado del registro cambiará públicamente.';
            btn.className = 'btn btn-primary px-4 fw-bold';
            btn.innerText = 'Sí, cambiar';
        } */

        modal.show();
    }
</script>
@endsection