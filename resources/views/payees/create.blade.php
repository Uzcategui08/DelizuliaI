@extends('adminlte::page')

@section('title', 'Nuevo destinatario')

@section('content_header')
<h1>Agregar destinatario</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-header">Registrar destinatario</div>
                    <div class="card-body bg-white">
                        <form action="{{ route('payees.store') }}" method="POST">
                            @include('payees.form', ['submitText' => 'Crear destinatario'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
