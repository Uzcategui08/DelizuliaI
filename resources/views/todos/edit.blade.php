@extends('adminlte::page')

@section('title', 'Editar tarea')

@section('content_header')
<h1>Editar tarea</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Actualizar tarea</span>
                        <a href="{{ route('todos.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
                    </div>
                    <div class="card-body bg-white">
                        <form action="{{ route('todos.update', $todo) }}" method="POST">
                            @method('PUT')
                            @include('todos.form', ['submitText' => 'Actualizar'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
