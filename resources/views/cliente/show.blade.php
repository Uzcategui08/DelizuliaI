@extends('adminlte::page')

@section('title', 'Clientes')

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
                            <span class="card-title">{{ __('Información del Cliente') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('clientes.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body bg-white p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-id-card text-primary mr-3"></i>
                                    <div>
                                        <strong class="d-block">ID Cliente:</strong>
                                        <span class="text-muted">{{ $cliente->id_cliente }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-user text-info mr-3"></i>
                                    <div>
                                        <strong class="d-block">Nombre:</strong>
                                        <span class="text-muted">{{ $cliente->nombre }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-phone text-warning mr-3"></i>
                                    <div>
                                        <strong class="d-block">Teléfono:</strong>
                                        <span class="text-muted">{{ $cliente->telefono }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-success mr-3"></i>
                                    <div>
                                        <strong class="d-block">Dirección:</strong>
                                        <span class="text-muted">{{ $cliente->direccion }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        .form-group {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .form-group:last-child {
            border-bottom: none;
        }
    </style>
@endsection