@extends('adminlte::page')

@section('title', 'Subcategorías')

@section('content_header')
<h1>Mostrar</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Subcategoría') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('categorias.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="form-group mb-2 mb20">
                            <strong>Nombre:</strong>
                            {{ $categoria->nombre }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
