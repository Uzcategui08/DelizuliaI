@extends('adminlte::page')

@section('title', 'Productos')

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
                            <span class="card-title">{{ __('Información del Producto') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('productos.index') }}">
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
                                        <strong class="d-block">ID Producto:</strong>
                                        <span class="text-muted">{{ $producto->id_producto }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-tag text-info mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Item:</strong>
                                        <span class="text-muted">{{ $producto->item }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-industry text-warning mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Marca:</strong>
                                        <span class="text-muted">{{ $producto->marca }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-key text-secondary mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Tipo de Llave:</strong>
                                        <span class="text-muted">{{ $producto->t_llave ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-hashtag text-success mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">SKU:</strong>
                                        <span class="text-muted">{{ $producto->sku }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-dollar-sign text-danger mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Precio:</strong>
                                        <span class="text-muted">${{ number_format($producto->precio, 2) }}</span>
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