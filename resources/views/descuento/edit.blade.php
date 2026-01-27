@extends('adminlte::page')

@section('title', 'Descuento')

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
                            <span class="card-title">{{ __('Descuento') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('descuentos.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('descuentos.update', $descuento->id_descuentos) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('descuento.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
