@extends('Admin.app')

@section('title', 'Gestión de Arbitrajes')
@section('page-title', 'Administración de Arbitrajes')

@section('content')

<h1>Etapas Arbitrales</h1>

<a href="{{ route('etapas.create') }}">Nueva Etapa</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Acciones</th>
    </tr>

    @foreach($etapas as $etapa)
    <tr>
        <td>{{ $etapa->id }}</td>
        <td>{{ $etapa->nombre }}</td>
        <td>
            <a href="{{ route('etapas.edit', $etapa->id) }}">Editar</a>

            <form action="{{ route('etapas.destroy', $etapa->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection

@push('scripts')

@endpush