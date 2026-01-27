@extends('adminlte::page')

@section('title', 'Inventario')

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
                            <span class="card-title">{{ __('Información del Inventario') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('inventarios.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body bg-white p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-barcode text-primary mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">ID Inventario:</strong>
                                        <span class="text-muted">{{ $inventario->id_inventario }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-box-open text-info mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Producto:</strong>
                                        <span class="text-muted">{{ $inventario->producto->item }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-warehouse text-warning mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Almacén:</strong>
                                        <span class="text-muted">{{ $inventario->almacene->nombre }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-cubes text-success mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Cantidad:</strong>
                                        <span class="text-muted">{{ $inventario->cantidad }}</span>
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