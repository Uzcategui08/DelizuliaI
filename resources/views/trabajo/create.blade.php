@extends('adminlte::page')

@section('title', 'Trabajos')

@section('content_header')
<h1>Crear</h1>
@stop
@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Trabajo') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('trabajos.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('trabajos.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('trabajo.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
