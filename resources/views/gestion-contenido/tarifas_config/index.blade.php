@extends('gestion-contenido.main')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark m-0">Configuraciones Globales (Tasas e IGV)</h2>
        <div>
            <a href="{{ route('calculadoras-gestion.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Volver a Escalas
            </a>
            <a href="{{ route('tarifas_config.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus"></i> Nueva Variable
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Clave (Código)</th>
                            <th>Valor</th>
                            <th>Descripción</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($configs as $item)
                        <tr>
                            <td class="ps-4 font-monospace text-primary fw-bold">{{ $item->clave }}</td>
                            <td>
                                <span class="badge bg-light text-dark border fs-6">
                                    {{ number_format($item->valor, 2) }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $item->descripcion }}</td>
                            
                            {{-- ACCIONES --}}
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border shadow-sm" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('tarifas_config.edit', $item->id) }}">
                                                <i class="fas fa-edit text-warning me-2"></i> Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item py-2 text-danger" 
                                                    onclick="confirmAction('{{ route('tarifas_config.destroy', $item->id) }}')">
                                                <i class="fas fa-trash me-2"></i> Eliminar
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No hay configuraciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $configs->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold">Confirmar Eliminación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="mb-3">
            <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
        </div>
        <h5 class="fw-bold mb-2">¿Estás seguro?</h5>
        <p class="text-muted mb-0">Si eliminas esta variable, los cálculos de la calculadora podrían fallar.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        
        <form id="confirmForm" action="" method="POST">
            @csrf 
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger px-4 fw-bold">Sí, eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-cerrar alertas
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

    // Función para abrir modal
    function confirmAction(url) {
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const form = document.getElementById('confirmForm');
        form.action = url;
        modal.show();
    }
</script>
@endsection