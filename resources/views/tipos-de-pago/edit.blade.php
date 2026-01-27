@extends('adminlte::page')

@section('title', 'Tipos de Pago')

@section('content_header')
<h1>Editar</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Tipo de Pago') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('tipos-de-pagos.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('tipos-de-pagos.update', $tiposDePago->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('tipos-de-pago.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
