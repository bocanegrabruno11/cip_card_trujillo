@extends('Admin.app')

@section('title', 'Página Principal')
@section('page-title', 'Mesa de Partes Virtual')

@section('content')
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Bienvenida -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="mb-3">
                        Bienvenido al Sistema de Mesa de Partes Virtual
                    </h3>

                    <p class="text-muted mb-0">
                        El Sistema de Mesa de Partes Virtual del
                        <strong>Colegio de Ingenieros del Perú – Consejo Departamental de La Libertad (CIPCDLL)</strong>
                        permite a los usuarios registrar y presentar de manera digital
                        sus procesos correspondientes a las áreas de
                        <strong>JPRD</strong> y <strong>Arbitraje</strong>.
                    </p>
                </div>
            </div>

            <!-- Funcionalidad para los usuarios -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Registro de procesos</h5>

                    <p class="mb-0">
                        A través de esta plataforma, los usuarios pueden
                        <strong>registrar sus procesos</strong>,
                        adjuntar la documentación correspondiente y realizar
                        el seguimiento del estado de su trámite, sin necesidad
                        de acudir de manera presencial.
                    </p>
                </div>
            </div>

            <!-- Funcionalidad administrativa -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Gestión y validación administrativa</h5>

                    <p class="mb-0">
                        Desde este apartado, los <strong>administradores del sistema</strong>
                        pueden revisar, validar y dar seguimiento a los procesos
                        registrados, así como
                        <strong>subir información, observaciones y documentos</strong>
                        asociados a cada expediente, garantizando un control
                        ordenado y transparente.
                    </p>
                </div>
            </div>

            <!-- Objetivo -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Objetivo del sistema</h5>

                    <p class="mb-0">
                        Optimizar la gestión de los procesos de
                        <strong>JPRD</strong> y <strong>Arbitraje</strong>
                        mediante una Mesa de Partes Virtual que asegure
                        eficiencia, trazabilidad y una comunicación clara
                        entre los usuarios y la administración del CIPCDLL.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('styles')
<!-- Estilos específicos -->
@endpush

@push('scripts')
<!-- Scripts específicos -->
@endpush
