@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
<h1>Crear</h1>
@stop

@section('content')

    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Cliente</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('clientes.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('cliente.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop