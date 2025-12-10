@extends('mesa-partes.app')

@section('title', 'Dashboard - Mesa de Partes')

@section('page-title', 'Dashboard')

@push('styles')
<style>
    .welcome-card {
        background: linear-gradient(135deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(173, 43, 46, 0.2);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 20px;
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: bold;
        color: var(--cip-red);
        margin-bottom: 5px;
    }
    
    .stat-title {
        color: #666;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .quick-actions {
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
        margin-bottom: 10px;
    }
    
    .action-btn:hover {
        background: var(--cip-red);
        color: white;
        border-color: var(--cip-red);
        transform: translateX(5px);
    }
    
    .action-btn i {
        font-size: 20px;
        margin-right: 15px;
        width: 30px;
    }
    
    .recent-activity {
        background: white;
        border-radius: 10px;
        padding: 25px;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-time {
        color: #888;
        font-size: 12px;
    }
    
    .badge-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .badge-approved {
        background-color: #d4edda;
        color: #155724;
    }
    
    .badge-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Tarjeta de bienvenida -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">¡Bienvenido, {{ Auth::user()->name }}!</h1>
                <p class="mb-0">Sistema de Mesa de Partes del CIP</p>
                <small><i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d/m/Y H:i') }}</small>
            </div>
            <div class="col-md-4 text-end">
                <div class="bg-white text-danger d-inline-block px-3 py-2 rounded">
                    <i class="fas fa-inbox me-2"></i>
                    <strong>Sistema Activo</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(173, 43, 46, 0.1); color: var(--cip-red);">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="stat-number">24</div>
            <div class="stat-title">Tramites Recibidos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">18</div>
            <div class="stat-title">Tramites Concluidos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">6</div>
            <div class="stat-title">Pendientes</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(0, 123, 255, 0.1); color: #007bff;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">3</div>
            <div class="stat-title">En Revisión</div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="quick-actions">
                <h5 class="mb-3"><i class="fas fa-bolt me-2"></i> Acciones Rápidas</h5>
                <a href="{{ route('persona.actualizar') }}" class="action-btn">
                    <i class="fas fa-user-edit"></i>
                    <div>
                        <strong>Actualizar Información</strong>
                        <small class="d-block text-muted">Modifica tus datos personales</small>
                    </div>
                </a>
                
                <a href="{{ route('arbitraje') }}" class="action-btn">
                    <i class="fas fa-scale-balanced"></i>
                    <div>
                        <strong>Solicitud de Arbitraje</strong>
                        <small class="d-block text-muted">Inicia un proceso de arbitraje</small>
                    </div>
                </a>
                
                <a href="{{ route('jrd') }}" class="action-btn">
                    <i class="fas fa-gavel"></i>
                    <div>
                        <strong>JRD - Junta de Resolución</strong>
                        <small class="d-block text-muted">Consulta tus casos JRD</small>
                    </div>
                </a>
                
                <a href="#" class="action-btn">
                    <i class="fas fa-file-export"></i>
                    <div>
                        <strong>Generar Reporte</strong>
                        <small class="d-block text-muted">Exporta información del sistema</small>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="recent-activity">
                <h5 class="mb-3"><i class="fas fa-history me-2"></i> Actividad Reciente</h5>
                
                <div class="activity-item">
                    <div class="activity-icon" style="background-color: var(--cip-red);">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="activity-content">
                        <div class="d-flex justify-content-between">
                            <strong>Nuevo trámite recibido</strong>
                            <span class="badge-status badge-pending">Pendiente</span>
                        </div>
                        <small class="text-muted">Solicitud de certificación #2024-0012</small>
                    </div>
                    <div class="activity-time">Hace 2 horas</div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon" style="background-color: #28a745;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="activity-content">
                        <div class="d-flex justify-content-between">
                            <strong>Trámite aprobado</strong>
                            <span class="badge-status badge-approved">Aprobado</span>
                        </div>
                        <small class="text-muted">Renovación de colegiatura #2024-0011</small>
                    </div>
                    <div class="activity-time">Hace 1 día</div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon" style="background-color: #ffc107;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="d-flex justify-content-between">
                            <strong>Documentación incompleta</strong>
                            <span class="badge-status badge-rejected">Rechazado</span>
                        </div>
                        <small class="text-muted">Solicitud de arbitraje #ARB-2024-008</small>
                    </div>
                    <div class="activity-time">Hace 2 días</div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon" style="background-color: #007bff;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="activity-content">
                        <div class="d-flex justify-content-between">
                            <strong>Información actualizada</strong>
                            <span class="badge-status badge-approved">Completado</span>
                        </div>
                        <small class="text-muted">Actualización de datos personales</small>
                    </div>
                    <div class="activity-time">Hace 3 días</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información importante -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i> Información Importante</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Horario de atención:</strong><br>
                                Lunes a Viernes<br>
                                8:00 am - 5:00 pm
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Plazos de atención:</strong><br>
                                Trámites ordinarios: 5 días hábiles<br>
                                Trámites urgentes: 48 horas
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-phone me-2"></i>
                                <strong>Contacto:</strong><br>
                                Tel: (01) 123-4567<br>
                                Email: mesa-partes@cip.org.pe
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Aquí puedes agregar scripts específicos del dashboard
        console.log('Dashboard de Mesa de Partes cargado');
        
        // Ejemplo: Actualizar contador de notificaciones
        setInterval(function() {
            // Simular actualización de datos
            console.log('Actualizando datos del dashboard...');
        }, 30000);
    });
</script>
@endpush