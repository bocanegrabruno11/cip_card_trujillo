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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="autoDismissAlert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ALERTA DE ERROR (Por si acaso) --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" id="autoDismissError">
            <i class="fas fa-exclamation-triangle me-1"></i> Por favor revisa los errores.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                            <td class="text-end pe-4">
                                <a href="{{ route('tarifas_config.edit', $item->id) }}" class="btn btn-sm btn-warning text-white me-1"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('tarifas_config.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta variable? Esto podría afectar los cálculos.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No hay configuraciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecciona todas las alertas que quieras cerrar (por clase o ID)
        const alerts = document.querySelectorAll('.alert-dismissible');
        
        alerts.forEach(function(alertNode) {
            // Esperar 3 segundos (3000 ms)
            setTimeout(function() {
                // Usar la API de Bootstrap para cerrar la alerta suavemente
                const bsAlert = new bootstrap.Alert(alertNode);
                bsAlert.close();
            }, 3000);
        });
    });
</script>
@endsection