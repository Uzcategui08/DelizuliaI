@extends('adminlte::page')

@section('title', 'Editar pago')

@section('content_header')
<h1>Editar pago</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Actualizar pago</span>
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
                    </div>
                    <div class="card-body bg-white">
                        <form action="{{ route('payments.update', $payment) }}" method="POST">
                            @method('PUT')
                            @include('payments.form', ['submitText' => 'Actualizar pago'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
