@extends('Admin.app')

@section('title', 'Vincular Árbitros a Casos')
@section('page-title', 'Vincular Árbitros a Casos')

@section('content')

<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i>Vincular Árbitro a Caso de Arbitraje
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formVincular" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Caso <span class="text-danger">*</span></label>
                                <select class="form-select" id="arbitraje_id" name="arbitraje_id" required>
                                    <option value="">Seleccione un caso...</option>
                                    @foreach($arbitrajes as $arbitraje)
                                        <option value="{{ $arbitraje->id_arbitraje }}">
                                            {{ $arbitraje->numero_expediente ?? 'Sin expediente' }} 
                                            - {{ $arbitraje->nombre_materia ?? 'Sin materia' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Árbitro <span class="text-danger">*</span></label>
                                <select class="form-select" id="arbitro_id" name="arbitro_id" required>
                                    <option value="">Seleccione un árbitro...</option>
                                    @foreach($arbitros as $arbitro)
                                        <option value="{{ $arbitro->id }}" data-dni="{{ $arbitro->dni }}">
                                            {{ $arbitro->nombre }} {{ $arbitro->apellidos }}
                                            @if($arbitro->dni) ({{ $arbitro->dni }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-link me-2"></i>Vincular
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Árbitros Vinculados al Caso
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Expediente</th>
                                    <th>Nombre</th>
                                    <th>DNI</th>
                                    <th>Contacto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVinculados">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Seleccione un caso para ver los árbitros vinculados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const csrfToken = '{{ csrf_token() }}';

document.addEventListener('DOMContentLoaded', function() {

    // Cuando cambia el caso, cargar los árbitros vinculados
    document.getElementById('arbitraje_id').addEventListener('change', function() {
        const arbitrajeId = this.value;
        
        if (arbitrajeId) {
            cargarVinculados(arbitrajeId);
        } else {
            document.getElementById('tbodyVinculados').innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Seleccione un caso para ver los árbitros vinculados
                    </td>
                </tr>
            `;
        }
    });

    // Cargar árbitros vinculados
    function cargarVinculados(arbitrajeId) {
        const tbody = document.getElementById('tbodyVinculados');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-3">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch(`/admin/arbitros/vinculados/${arbitrajeId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.vinculados.length > 0) {
                    tbody.innerHTML = data.vinculados.map(v => `
                        <tr>
                            <td>
                                <span class="badge bg-dark">${data.expediente || 'Sin expediente'}</span>
                            </td>
                            <td>
                                <strong>${v.nombre}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">${v.dni || 'N/A'}</span>
                            </td>
                            <td>
                                ${v.correo ? `<small><i class="fas fa-envelope me-1"></i>${v.correo}</small><br>` : ''}
                                ${v.telefono ? `<small><i class="fas fa-phone me-1"></i>${v.telefono}</small>` : ''}
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-desvincular"
                                        data-persona-id="${v.id}"
                                        data-nombre="${v.nombre}">
                                    <i class="fas fa-unlink me-1"></i> Desvincular
                                </button>
                            </td>
                        </tr>
                    `).join('');

                    // Eventos para desvincular
                    document.querySelectorAll('.btn-desvincular').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const personaId = this.dataset.personaId;
                            const nombre = this.dataset.nombre;

                            Swal.fire({
                                title: '¿Desvincular árbitro?',
                                text: `Eliminar a ${nombre} de este caso`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Sí, desvincular',
                                cancelButtonText: 'Cancelar'
                            }).then(result => {
                                if (!result.isConfirmed) return;

                                Swal.fire({
                                    title: 'Procesando...',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => Swal.showLoading()
                                });

                                fetch('{{ route("admin.arbitros.desvincular") }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        persona_id: personaId
                                    })
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Éxito', data.message, 'success');
                                        cargarVinculados(document.getElementById('arbitraje_id').value);
                                    } else {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                })
                                .catch(err => {
                                    Swal.fire('Error', 'Error de conexión: ' + err.message, 'error');
                                });
                            });
                        });
                    });

                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                No hay árbitros vinculados a este caso
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(err => {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger py-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error al cargar: ${err.message}
                        </td>
                    </tr>
                `;
            });
    }

    // Vincular árbitro
    document.getElementById('formVincular').addEventListener('submit', function(e) {
        e.preventDefault();

        const arbitrajeId = document.getElementById('arbitraje_id').value;
        const arbitroId = document.getElementById('arbitro_id').value;

        if (!arbitrajeId || !arbitroId) {
            Swal.fire('Error', 'Seleccione un caso y un árbitro', 'error');
            return;
        }

        Swal.fire({
            title: '¿Vincular árbitro?',
            text: 'El árbitro será asignado a este caso',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sí, vincular',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('{{ route("admin.arbitros.vincular.post") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    arbitraje_id: arbitrajeId,
                    arbitro_id: arbitroId
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success');
                    cargarVinculados(arbitrajeId);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Error de conexión: ' + err.message, 'error');
            });
        });
    });

});
</script>

@endsection