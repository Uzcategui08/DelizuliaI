@extends('adminlte::page')

@section('title', 'Costos')

@section('content_header')
    <h1>Mostrar</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Detalle del Costo') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary" href="{{ route('costos.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Información del Costo</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="f_costos" class="form-label">{{ __('Fecha') }}</label>
                                            <input type="date" class="form-control" value="{{ $costo->f_costos }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="id_tecnico" class="form-label">{{ __('ID Técnico') }}</label>
                                            <input type="text" class="form-control" value="{{ $costo->empleado->nombre }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="valor" class="form-label">{{ __('Valor Total') }}</label>
                                            <input type="number" step="0.01" class="form-control" value="{{ $costo->valor }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $costo->descripcion }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="subcategoria" class="form-label">{{ __('Subcategoría') }}</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                value="@php
                                                    $categoriaEncontrada = collect($categorias)->firstWhere('id_categoria', $costo->subcategoria);
                                                    echo $categoriaEncontrada->nombre ?? 'N/A';
                                                @endphp" 
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="estatus" class="form-label">{{ __('Estatus') }}</label>
                                            <input type="text" class="form-control" value="{{ ['pendiente' => 'Pendiente', 'parcialmente_pagado' => 'Parcial', 'pagado' => 'Pagado'][$costo->estatus] ?? 'N/A' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Registro de Pagos Parciales</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Valor Total:</strong>
                                            <span>${{ number_format($costo->valor ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <strong>Total Pagado:</strong>
                                            <span>${{ number_format($total_pagado ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <strong>Saldo Pendiente:</strong>
                                            <span>${{ number_format($saldo_pendiente ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4" id="lista-pagos">
                                    @if(!empty($costo->pagos) && is_array($costo->pagos))
                                        @foreach($costo->pagos as $index => $pago)
                                            <div class="pago-item card mb-2">
                                                <div class="card-body py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="fw-bold">${{ number_format($pago['monto'], 2) }}</span>
                                                            <span class="text-muted ms-2">
                                                                ({{ $metodos[$pago['metodo_pago']] ?? 'Método desconocido' }})
                                                            </span>
                                                            <small class="text-muted ms-2">{{ $pago['fecha'] }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning">No hay pagos registrados</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .card-body {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f0f0f0;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        #card_title {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
@stop