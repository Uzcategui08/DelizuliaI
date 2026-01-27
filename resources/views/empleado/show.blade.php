@extends('adminlte::page')

@section('title', 'Empleado')

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
                            <span class="card-title">{{ __('Información del Empleado') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('empleados.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body bg-white p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-id-card text-primary mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">ID Empleado:</strong>
                                        <span class="text-muted">{{ $empleado->id_empleado }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-user text-info mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Nombre:</strong>
                                        <span class="text-muted">{{ $empleado->nombre }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-address-card text-warning mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Cédula:</strong>
                                        <span class="text-muted">{{ $empleado->cedula }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-briefcase text-secondary mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Cargo:</strong>
                                        <span class="text-muted">
                                            @php
                                                $cargos = [
                                                    1 => 'Técnico',
                                                    2 => 'Administrativo',
                                                    3 => 'Supervisor',
                                                    4 => 'Gerente'
                                                ];
                                                echo $cargos[$empleado->cargo] ?? 'Desconocido';
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave text-success mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Salario Base:</strong>
                                        <span class="text-muted">${{ number_format($empleado->salario_base, 2) }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-tags text-danger mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Tipo:</strong>
                                        <span class="text-muted">
                                            @php
                                                $tipos = [
                                                    1 => 'Costo',
                                                    2 => 'Gasto'
                                                ];
                                                echo $tipos[$empleado->tipo] ?? 'Desconocido';
                                            @endphp
                                        </span>
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
        .fa-lg {
            font-size: 1.5rem;
            min-width: 30px;
        }
        .text-muted {
            font-size: 1rem;
        }
        strong.d-block {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
@endsection