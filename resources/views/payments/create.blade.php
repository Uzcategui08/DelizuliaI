@extends('adminlte::page')

@section('title', 'Registrar pago')

@section('content_header')
<h1>Registrar pago</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-header">Nuevo pago</div>
                    <div class="card-body bg-white">
                        <form action="{{ route('payments.store') }}" method="POST">
                            @include('payments.form', ['submitText' => 'Guardar pago'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
