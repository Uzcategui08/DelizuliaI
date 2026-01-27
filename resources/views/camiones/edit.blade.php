@extends('adminlte::page')

@section('title', 'Editar Camión')

@section('content_header')
    <h1>Editar Camión</h1>
@stop

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('camiones.update', $camion) }}">
                @csrf
                @method('PUT')
                @include('camiones.partials.form', ['camion' => $camion])
                <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Guardar</button>
                <a href="{{ route('camiones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@stop
