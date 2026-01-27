@extends('adminlte::page')

@section('title', 'Crear tarea')

@section('content_header')
<h1>Nueva tarea</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header">Registrar tarea</div>
                    <div class="card-body bg-white">
                        <form action="{{ route('todos.store') }}" method="POST">
                            @include('todos.form', ['submitText' => 'Crear tarea'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
