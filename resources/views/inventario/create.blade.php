@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
<h1>Crear</h1>
@stop

@section('content')
<section class="content container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card card-default">
                <div class="card-header">
                    <span class="card-title">Inventario</span>
                </div>
                <div class="card-body bg-white">
                    <form method="POST" action="{{ route('inventarios.store') }}" role="form" enctype="multipart/form-data">
                        @csrf

                        @include('inventario.form')

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    console.log("Hi, I'm using the Laravel-AdminLTE package!");
</script>
@stop