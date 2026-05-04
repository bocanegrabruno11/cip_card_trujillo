@extends('eventoscipcdll.layout')

@section('title', 'Dashboard')

@section('content')

<h2>Bienvenido {{ session('usuario') }}</h2>
<p>Este es el panel principal.</p>

@endsection