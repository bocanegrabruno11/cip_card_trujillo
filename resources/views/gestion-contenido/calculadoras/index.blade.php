@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Gestión de Tarifas y Escalas</h2>
        <div>
            <a href="{{ route('tarifas_config.index') }}" class="btn btn-dark shadow-sm me-2">
                <i class="fas fa-cogs"></i> Variables Globales
            </a>
            <a href="{{ route('calculadoras-gestion.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle"></i> Nueva Escala
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-1"></i> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-1"></i> {{ session('warning') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-ban me-1"></i> {{ $errors->first() }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('calculadoras-gestion.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Sistema / Calculadora</label>
                        <select name="tipo_calculadora" class="form-select form-select-sm">
                            <option value="">Todos los sistemas</option>
                            <option value="servicio_arbitral" {{ request('tipo_calculadora') == 'servicio_arbitral' ? 'selected' : '' }}>Servicio Arbitral</option>
                            <option value="junta_prevencion" {{ request('tipo_calculadora') == 'junta_prevencion' ? 'selected' : '' }}>Junta de Prevención</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tipo de Tabla</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">Todas las tablas</option>
                            <option value="arbitro_unico" {{ request('tipo') == 'arbitro_unico' ? 'selected' : '' }}>Árbitro Único / Adjudicador</option>
                            <option value="tribunal_arbitral" {{ request('tipo') == 'tribunal_arbitral' ? 'selected' : '' }}>Tribunal Arbitral</option>
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

    {{-- CONTENIDO: LÓGICA DE VISUALIZACIÓN --}}
    
    @if(isset($tarifas))
        {{-- MODO 1: RESULTADOS FILTRADOS (Una sola tabla PAGINADA) --}}
        {{-- Aquí NO ponemos scroll fijo porque la paginación ya limita los resultados --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold">Resultados del Filtro</div>
            <div class="table-responsive">
                @include('gestion-contenido.calculadoras.partials.table', ['data' => $tarifas])
            </div>
            <div class="card-footer bg-white py-3">
                {{ $tarifas->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>

    @else
        {{-- MODO 2: VISTA AGRUPADA (Dos secciones CON SCROLL) --}}
        
        {{-- SECCIÓN ARBITRAJE --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-gavel me-2"></i> CALCULADORA INSTITUCIÓN ARBITRAL</span>
                <span class="badge bg-white text-danger">{{ $tarifasArbitraje->count() }} Registros</span>
            </div>
            
            {{-- AQUI ESTÁ EL CAMBIO: max-height + overflow-y --}}
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                {{-- Usamos una tabla con cabecera 'sticky' para que no se pierda al bajar --}}
                <style>
                    /* Pequeño hack local para que el encabezado se quede fijo al hacer scroll */
                    .sticky-header th { position: sticky; top: 0; z-index: 1; background-color: #f8f9fa; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1); }
                </style>
                
                @include('gestion-contenido.calculadoras.partials.table', ['data' => $tarifasArbitraje, 'showSystem' => false, 'sticky' => true])
            </div>
        </div>

        {{-- SECCIÓN JUNTA --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-handshake me-2"></i> CALCULADORA DE JUNTA DE PREVENCIÓN (JRD)</span>
                <span class="badge bg-white text-dark">{{ $tarifasJunta->count() }} Registros</span>
            </div>
            
            {{-- AQUI TAMBIÉN EL CAMBIO --}}
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                @include('gestion-contenido.calculadoras.partials.table', ['data' => $tarifasJunta, 'showSystem' => false, 'sticky' => true])
            </div>
        </div>
    @endif

</div>

{{-- MODAL DE CONFIRMACIÓN (Igual) --}}
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold" id="modalTitle">Confirmar Acción</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="mb-3"><i class="fas fa-exclamation-circle fa-3x text-warning"></i></div>
        <h5 class="fw-bold mb-2" id="modalHeader">¿Estás seguro?</h5>
        <p class="text-muted mb-0" id="modalMessage">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        <form id="confirmForm" action="" method="POST">
            @csrf <input type="hidden" name="_method" id="formMethod" value="DELETE">
            <button type="submit" class="btn btn-danger px-4 fw-bold" id="confirmBtnText">Sí, eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alertNode) {
            setTimeout(function() {
                if (alertNode) { const bsAlert = new bootstrap.Alert(alertNode); bsAlert.close(); }
            }, 4000); 
        });
    });

    function confirmAction(url, type) {
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        document.getElementById('confirmForm').action = url;
        modal.show();
    }
</script>
@endsection