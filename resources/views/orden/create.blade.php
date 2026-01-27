@extends('adminlte::page')

@section('title', 'Órdenes')

@section('content_header')
<h1>Crear</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Orden') }}</span>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('ordens.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('orden.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

