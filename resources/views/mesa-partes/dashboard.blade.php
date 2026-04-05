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
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- BIENVENIDA -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">¡Bienvenido, {{ Auth::user()->name }}!</h1>
                <p class="mb-0">Sistema de Mesa de Partes del CIP CDLL</p>
                <small>
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('d/m/Y H:i') }}
                </small>
            </div>
            <div class="col-md-4 text-end">
                <div class="bg-white text-danger d-inline-block px-3 py-2 rounded">
                    <i class="fas fa-circle me-2"></i>
                    <strong>Sistema Activo</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- ESTADÍSTICAS ARBITRAJE -->
    <h5 class="mb-3"><i class="fas fa-scale-balanced me-2"></i>Arbitraje</h5>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(173, 43, 46, 0.1); color: var(--cip-red);">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="stat-number">{{ $arbitrajesRecibidos }}</div>
            <div class="stat-title">Recibidos</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $arbitrajesPendientes }}</div>
            <div class="stat-title">Pendientes</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(0, 123, 255, 0.1); color: #007bff;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $arbitrajesRevision }}</div>
            <div class="stat-title">En Revisión</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $arbitrajesConcluidos }}</div>
            <div class="stat-title">Concluidos</div>
        </div>
    </div>

    <!-- ESTADÍSTICAS JRD -->
    <h5 class="mb-3 mt-4"><i class="fas fa-gavel me-2"></i>JPRD</h5>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $jrdPendientes }}</div>
            <div class="stat-title">Pendientes</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(0, 123, 255, 0.1); color: #007bff;">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="stat-number">{{ $jrdRevision }}</div>
            <div class="stat-title">En Revisión</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-number">{{ $jrdConcluidos }}</div>
            <div class="stat-title">Concluidos</div>
        </div>
    </div>

    <!-- ACCIONES RÁPIDAS -->
    <div class="quick-actions mt-4">
        <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>

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
                <small class="d-block text-muted">Iniciar proceso de arbitraje</small>
            </div>
        </a>

        <a href="{{ route('jrd') }}" class="action-btn">
            <i class="fas fa-gavel"></i>
            <div>
                <strong>JPRD</strong>
                <small class="d-block text-muted">Iniciar procesos JPRD</small>
            </div>
        </a>
    </div>

</div>
@endsection
