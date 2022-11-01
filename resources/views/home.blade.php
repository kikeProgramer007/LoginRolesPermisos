@extends('adminlte::page')
{{-- //implementa la vista de adminlte --}}

@section('title', 'Dashboard')
{{-- //agregamos un titulo  --}}

@section('content_header')
    <h1>Dashboard</h1> 
@stop
{{-- //Agregamos un header a nuestra pagina  --}}

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
@stop

{{-- //Contenido de nuestra pagina --}}

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

{{-- //agregamos css --}}

@section('js')
    <script> console.log('Hi!'); </script>
@stop
