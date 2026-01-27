@extends('adminlte::page')

@section('title', 'Editar destinatario')

@section('content_header')
<h1>Editar destinatario</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Actualizar destinatario</span>
                        <a href="{{ route('payees.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
                    </div>
                    <div class="card-body bg-white">
                        <form action="{{ route('payees.update', $payee) }}" method="POST">
                            @method('PUT')
                            @include('payees.form', ['submitText' => 'Actualizar'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
