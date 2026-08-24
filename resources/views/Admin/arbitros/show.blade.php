@extends('admin.app')

@section('title', 'Detalle del Árbitro')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i> Detalle del Árbitro
                        </h3>
                        <div>
                            <a href="{{ route('arbitros.index') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                            <a href="{{ route('arbitros.edit', $arbitro) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Tarjeta de resumen -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-user-circle fa-4x text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ $arbitro->nombre_completo }}</h4>
                                        <small class="text-muted">
                                            <i class="fas fa-id-card me-1"></i> DNI: {{ $arbitro->dni ?? 'No registrado' }}
                                            @if($arbitro->correo)
                                                | <i class="fas fa-envelope me-1"></i> {{ $arbitro->correo }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Datos Personales -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary shadow-sm border-0">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-id-card me-2"></i> Datos Personales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <tbody>
                                                <tr>
                                                    <th width="35%" class="text-muted">ID</th>
                                                    <td><span class="badge bg-secondary">{{ $arbitro->id }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Nombre</th>
                                                    <td>{{ $arbitro->nombre }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Apellidos</th>
                                                    <td>{{ $arbitro->apellidos }}</td>
                                                </tr>
                                                <tr class="table-active">
                                                    <th class="text-muted">Nombre Completo</th>
                                                    <td><strong>{{ $arbitro->nombre_completo }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">DNI</th>
                                                    <td>
                                                        @if($arbitro->dni)
                                                            <span class="badge bg-dark">{{ $arbitro->dni }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">RUC</th>
                                                    <td>{{ $arbitro->ruc ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Teléfono</th>
                                                    <td>
                                                        @if($arbitro->telefono)
                                                            <a href="tel:{{ $arbitro->telefono }}" class="text-decoration-none">
                                                                <i class="fas fa-phone me-1 text-success"></i> {{ $arbitro->telefono }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Correo</th>
                                                    <td>
                                                        @if($arbitro->correo)
                                                            <a href="mailto:{{ $arbitro->correo }}" class="text-decoration-none">
                                                                <i class="fas fa-envelope me-1 text-primary"></i> {{ $arbitro->correo }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Dirección</th>
                                                    <td>{{ $arbitro->direccion ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Datos de Usuario -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success shadow-sm border-0">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user me-2"></i> Datos de Usuario
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @foreach($arbitro->users as $user)
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th width="35%" class="text-muted">Usuario</th>
                                                        <td><strong>{{ $user->name }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Email</th>
                                                        <td>
                                                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                                <i class="fas fa-envelope me-1 text-primary"></i> {{ $user->email }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Estado</th>
                                                        <td>
                                                            @if($user->activo == 1)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check-circle me-1"></i> Activo
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-times-circle me-1"></i> Inactivo
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Rol</th>
                                                        <td>
                                                            @foreach($user->roles as $role)
                                                                <span class="badge bg-info me-1">
                                                                    <i class="fas fa-shield-alt me-1"></i> {{ $role->name }}
                                                                </span>
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Fecha Registro</th>
                                                        <td>
                                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                                            {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : '-' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CASOS VINCULADOS -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-outline card-danger shadow-sm border-0">
                                <div class="card-header bg-danger text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-gavel me-2"></i> Casos de Arbitraje Vinculados
                                        </h5>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-folder-open me-1"></i> {{ $casosVinculados->count() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($casosVinculados->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-striped">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="50">#</th>
                                                        <th>Expediente</th>
                                                        <th>Materia</th>
                                                        <th width="120">Estado</th>
                                                        <th width="150">Fecha Inicio</th>
                                                        <th>Partes</th>
                                                        <th width="100" class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($casosVinculados as $index => $caso)
                                                        <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td>
                                                                <span class="badge bg-dark">
                                                                    {{ $caso->arbitraje->numero_expediente ?? 'Sin expediente' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <strong>{{ $caso->arbitraje->nombre_materia ?? 'Sin materia' }}</strong>
                                                                @if(($caso->arbitraje->tipo_arbitraje ?? 'normal') === 'emergencia')
                                                                    <span class="badge bg-danger ms-1">
                                                                        <i class="fas fa-bolt me-1"></i>EMERGENCIA
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $estadoClass = match(strtolower($caso->arbitraje->estado ?? '')) {
                                                                        'validando' => 'bg-warning text-dark',
                                                                        'iniciado' => 'bg-info text-white',
                                                                        'en proceso' => 'bg-primary text-white',
                                                                        'terminado' => 'bg-success text-white',
                                                                        'observado' => 'bg-danger text-white',
                                                                        'archivado' => 'bg-secondary text-white',
                                                                        default => 'bg-secondary text-white'
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $estadoClass }} w-100 py-2">
                                                                    {{ strtoupper($caso->arbitraje->estado ?? 'SIN ESTADO') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small>
                                                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                                                    {{ $caso->arbitraje->fecha_inicio ? \Carbon\Carbon::parse($caso->arbitraje->fecha_inicio)->format('d/m/Y H:i') : '-' }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach($caso->arbitraje->personas as $persona)
                                                                        <span class="badge {{ $persona->tipo === 'Demandante' ? 'bg-success' : 'bg-warning text-dark' }} px-2 py-1">
                                                                            <i class="fas {{ $persona->tipo === 'Demandante' ? 'fa-user-check' : 'fa-user-shield' }} me-1"></i>
                                                                            {{ $persona->tipo }}: {{ $persona->nombres_apellidos ?? $persona->razon_social }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ route('admin.arbitrajes.detalle', $caso->arbitraje->id_arbitraje) }}" 
                                                                   class="btn btn-danger btn-sm"
                                                                   target="_blank"
                                                                   title="Ver detalle del caso">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay casos vinculados</h5>
                                            <p class="text-muted">Este árbitro no está vinculado a ningún caso de arbitraje.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-danger {
        background: linear-gradient(135deg, #AD2B2E 0%, #801a1d 100%);
    }
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        border-bottom: none;
    }
    .table th {
        font-weight: 600;
        color: #495057;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
        padding: 0.4em 0.8em;
    }
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .alert-info {
        background: #f0f7ff;
        border-left: 4px solid #0d6efd;
        border-radius: 10px;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,0.02);
    }
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
    }
    .shadow-lg {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
    }
</style>
@endsection

@section('scripts')
<script>
    // SweetAlert2 para mensajes de éxito/error
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif
</script>
@endsection