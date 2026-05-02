@extends('mesa-partes.app')
@section('title', 'Detalle de Notificación')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('casilla.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver a la bandeja
        </a>
    </div>

    @php
        // Obtener el número de expediente según el tipo
        $numeroExpediente = null;
        $tipoExpediente = null;
        $expedienteId = null;
        
        if($notificacion->arbitraje_id && $notificacion->arbitraje) {
            $numeroExpediente = $notificacion->arbitraje->numero_expediente;
            $tipoExpediente = 'arbitraje';
            $expedienteId = $notificacion->arbitraje_id;
        } elseif($notificacion->jrd_id && $notificacion->jrd) {
            $numeroExpediente = $notificacion->jrd->numero_expediente;
            $tipoExpediente = 'jrd';
            $expedienteId = $notificacion->jrd_id;
        }
        
        $tituloExpediente = $numeroExpediente 
            ? "Expediente N° {$numeroExpediente}"
            : ($tipoExpediente === 'arbitraje' ? "Arbitraje #{$notificacion->arbitraje_id}" : "JRD #{$notificacion->jrd_id}");
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 overflow-hidden">
                <div class="card-header bg-white py-4 px-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="fw-bold text-dark mb-1">{{ $notificacion->asunto }}</h4>
                            <p class="text-muted mb-0">
                                <i class="far fa-calendar-alt me-1"></i> Recibido el: {{ \Carbon\Carbon::parse($notificacion->fecha_registro)->format('d/m/Y h:i A') }}
                            </p>
                        </div>
                        <span class="badge {{ $notificacion->estado == 'leido' ? 'bg-success' : 'bg-primary' }} px-3 py-2">
                            {{ ucfirst($notificacion->estado) }}
                        </span>
                    </div>
                </div>

                <div class="card-body bg-light border-bottom px-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-bold">{{ $notificacion->emisor->name ?? 'Administración CIP' }}</h6>
                            <small class="text-muted">{{ $notificacion->emisor->email ?? 'admin@cip.org.pe' }}</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <h6 class="text-uppercase text-muted fw-bold small mb-3">Mensaje:</h6>
                    <div class="p-4 bg-white border rounded" style="min-height: 150px; line-height: 1.6; color: #333;">
                        {!! nl2br(e($notificacion->comentario)) !!}
                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-column align-items-center p-3 bg-light rounded border border-dashed">
                        <p class="mb-2 fw-bold text-muted small"><i class="fas fa-link me-1"></i> EXPEDIENTE RELACIONADO</p>
                        
                        @if($notificacion->arbitraje_id)
                            <h5 class="mb-3 text-dark">
                                <i class="fas fa-scale-balanced me-2 text-primary"></i>
                                {{ $tituloExpediente }}
                            </h5>
                            <a href="{{ route('RegistrosArbitraje') }}" 
                               class="btn btn-primary px-4 shadow-sm btn-ir-expediente"
                               data-tipo="arbitraje"
                               data-id="{{ $expedienteId }}">
                                <i class="fas fa-external-link-alt me-2"></i> Ir al Expediente de Arbitraje
                            </a>

                        @elseif($notificacion->jrd_id)
                            <h5 class="mb-3 text-dark">
                                <i class="fas fa-gavel me-2 text-success"></i>
                                {{ $tituloExpediente }}
                            </h5>
                            <a href="{{ route('registros.jrd') }}" 
                               class="btn btn-success px-4 shadow-sm btn-ir-expediente"
                               data-tipo="jrd"
                               data-id="{{ $expedienteId }}">
                                <i class="fas fa-external-link-alt me-2"></i> Ir al Expediente JRD
                            </a>

                        @else
                            <p class="text-muted italic mb-0">Esta notificación es de carácter general informativo.</p>
                        @endif
                    </div>
                </div>

                <div class="card-footer bg-white text-end py-3">
                    <form action="{{ route('casilla.destroy', $notificacion->id_casilla) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este mensaje?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash-alt me-1"></i> Eliminar de mi casilla
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Capturar clic en los botones "Ir al Expediente"
    document.querySelectorAll('.btn-ir-expediente').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tipo = this.dataset.tipo;
            const id = this.dataset.id;
            const href = this.getAttribute('href');
            
            // ✅ SIMPLE: Limpiar cualquier dato anterior y guardar SOLO el que vamos a buscar
            sessionStorage.removeItem('expediente_buscar');
            sessionStorage.setItem('expediente_buscar', JSON.stringify({
                tipo: tipo,
                id: parseInt(id)
            }));
            
            // Redirigir a la página correspondiente
            window.location.href = href;
        });
    });
});
</script>
@endsection