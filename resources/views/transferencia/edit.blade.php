@extends('adminlte::page')

@section('title', 'Transferencia de Almacenes')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark">
        <i class="fas fa-exchange-alt"></i> Editar Transferencia
    </h1>
    <a href="{{ route('transferencias.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@stop

@section('content')
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('transferencias.update', $transferencia->id_transferencia) }}"  role="form" enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    @csrf

                    @include('transferencia.form')

                </form>
            </div>
        </div>
    </section>
@endsection
