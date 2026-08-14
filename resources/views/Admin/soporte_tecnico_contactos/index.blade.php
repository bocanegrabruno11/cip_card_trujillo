@extends('Admin.app')

@section('title', 'Soporte Técnico - Contactos')
@section('page-title', 'Contactos de Soporte Técnico')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="mb-0">Contactos de Soporte Técnico</h3>
            <p class="text-muted">Administra los contactos de soporte técnico del sistema</p>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i> Nuevo Contacto
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> Por favor corrige los siguientes errores:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Nombres</th>
                            <th>N° Contacto</th>
                            <th>Correo Electrónico</th>
                            <th>Estado</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contactos as $contacto)
                        <tr>
                            <td class="ps-3 fw-semibold text-dark">{{ $contacto->nombres }}</td>
                            <td>{{ $contacto->numero_contacto }}</td>
                            <td>{{ $contacto->correo_electronico }}</td>
                            <td>
                                @if($contacto->estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $contacto->id }}" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.soporte_contactos.destroy', $contacto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este contacto? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $contacto->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.soporte_contactos.update', $contacto->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>Editar Contacto</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark">Nombres</label>
                                                <input type="text" name="nombres" class="form-control" value="{{ $contacto->nombres }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark">Número de Contacto <span class="text-muted fw-normal small">(Opcional si hay correo)</span></label>
                                                <input type="text" name="numero_contacto" class="form-control" value="{{ $contacto->numero_contacto }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark">Correo Electrónico <span class="text-muted fw-normal small">(Opcional si hay número)</span></label>
                                                <input type="email" name="correo_electronico" class="form-control" value="{{ $contacto->correo_electronico }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark">Estado</label>
                                                <select name="estado" class="form-select" required>
                                                    <option value="1" {{ $contacto->estado == 1 ? 'selected' : '' }}>Activo</option>
                                                    <option value="0" {{ $contacto->estado == 0 ? 'selected' : '' }}>Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3 d-block opacity-50"></i>
                                No hay contactos registrados en el sistema.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.soporte_contactos.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Nuevo Contacto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nombres</label>
                        <input type="text" name="nombres" class="form-control" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Número de Contacto <span class="text-muted fw-normal small">(Opcional si hay correo)</span></label>
                        <input type="text" name="numero_contacto" class="form-control" placeholder="Ej. 987654321">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Correo Electrónico <span class="text-muted fw-normal small">(Opcional si hay número)</span></label>
                        <input type="email" name="correo_electronico" class="form-control" placeholder="Ej. soporte@empresa.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Estado</label>
                        <select name="estado" class="form-select" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
