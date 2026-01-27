@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
<h1>Editar</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Venta') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('registro-vs.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('registro-vs.update', $registroV->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('registro-v.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    console.log("Hi, I'm using the Laravel-AdminLTE package!");
</script>
@stop