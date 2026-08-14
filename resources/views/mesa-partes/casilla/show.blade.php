@extends('mesa-partes.app')
@section('title', 'Detalle de Notificación')

@section('content')
<div class="container-fluid">
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

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('casilla.index') }}" class="btn btn-sm btn-light border shadow-sm text-secondary hover-dark">
            <i class="fas fa-arrow-left me-1"></i> Volver a la bandeja
        </a>
        <form action="{{ route('casilla.destroy', $notificacion->id_casilla) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este mensaje?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">
                <i class="fas fa-trash-alt me-1"></i> Eliminar mensaje
            </button>
        </form>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4 p-md-5">
            <!-- Encabezado del Mensaje -->
            <div class="border-bottom pb-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold text-dark mb-0">{{ $notificacion->asunto }}</h3>
                    
                    @if($notificacion->arbitraje_id || $notificacion->jrd_id)
                        <a href="{{ $notificacion->arbitraje_id ? route('RegistrosArbitraje') : route('registros.jrd') }}" 
                           class="btn {{ $notificacion->arbitraje_id ? 'btn-primary' : 'btn-success' }} btn-sm rounded-pill px-3 shadow-sm btn-ir-expediente"
                           data-tipo="{{ $tipoExpediente }}"
                           data-id="{{ $expedienteId }}">
                            <i class="fas {{ $notificacion->arbitraje_id ? 'fa-scale-balanced' : 'fa-gavel' }} me-1"></i> Ver {{ $tituloExpediente }}
                        </a>
                    @endif
                </div>

                <div class="d-flex align-items-center text-muted">
                    <div class="bg-light border rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-building text-secondary fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">{{ $notificacion->emisor->name ?? 'Administración CIP' }} <span class="badge bg-light text-secondary border ms-2 fw-normal">De: {{ $notificacion->emisor->email ?? 'admin@cip.org.pe' }}</span></div>
                        <div class="small mt-1">
                            <i class="far fa-calendar-alt me-1"></i> Recibido: <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($notificacion->fecha_registro)->format('d/m/Y h:i A') }}</span>
                            <span class="mx-2 text-light-gray">|</span>
                            <i class="far fa-eye me-1"></i> Leído: <span class="fw-semibold text-dark">{{ $notificacion->fecha_lectura ? \Carbon\Carbon::parse($notificacion->fecha_lectura)->format('d/m/Y h:i A') : \Carbon\Carbon::now()->format('d/m/Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuerpo del Mensaje -->
            <div class="message-content" style="font-size: 1.05rem; line-height: 1.7; color: #333;">
                {!! nl2br(e($notificacion->comentario)) !!}
            </div>
            
            @if(!$notificacion->arbitraje_id && !$notificacion->jrd_id)
                <div class="mt-4 pt-3 border-top">
                    <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> Esta notificación es de carácter general informativo.</p>
                </div>
            @endif
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