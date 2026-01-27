@extends('adminlte::page')

@section('title', 'Abono')

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
                            <span class="card-title">{{ __('Abono') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('abonos.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('abonos.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('abono.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            if (typeof $().select2 === 'function') {
                $('.select2').select2();
            }
        });
    </script>
@stop
