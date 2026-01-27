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
                            <span class="card-title">{{ __('Información del Presupuesto') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-m" href="{{ route('presupuestos.index') }}">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body bg-white p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-hashtag text-primary mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">ID Presupuesto:</strong>
                                        <span class="text-muted">{{ $presupuesto->id_presupuesto }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-user text-info mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Cliente:</strong>
                                        <span class="text-muted">{{ $presupuesto->cliente->nombre ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-calendar-day text-warning mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Fecha Presupuesto:</strong>
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($presupuesto->f_presupuesto)->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-calendar-check text-secondary mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Validez:</strong>
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($presupuesto->validez)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-percent text-success mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Descuento:</strong>
                                        <span class="text-muted">{{ number_format($presupuesto->descuento, 2) }}%</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-file-invoice-dollar text-info mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Taxes:</strong>
                                        <span class="text-muted">{{ number_format($presupuesto->iva, 2) }}%</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-receipt text-danger mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Estado:</strong>
                                        <span class="text-muted">
                                            <span class="badge 
                                                @if($presupuesto->estado == 'aprobado') badge-success 
                                                @elseif($presupuesto->estado == 'pendiente') badge-warning 
                                                @elseif($presupuesto->estado == 'rechazado') badge-danger 
                                                @else badge-secondary 
                                                @endif">
                                                {{ ucfirst($presupuesto->estado) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h4 class="mb-4">
                                <i class="fas fa-tasks mr-2"></i>Trabajos Presupuestados
                            </h4>
                            
                            @if (is_array($presupuesto->items) && count($presupuesto->items) > 0)
                                @php
                                    $subtotal = 0;
                                    $ivaPorcentaje = $presupuesto->iva ?? 0; 
                                    
                                    foreach ($presupuesto->items as $item) {
                                        $subtotal += $item['precio'] ?? 0;
                                    }
                                    
                                    $descuentoMonto = $subtotal * ($presupuesto->descuento / 100);
                                    $baseImponible = $subtotal - $descuentoMonto;
                                    $ivaMonto = $baseImponible * ($ivaPorcentaje / 100);
                                    $total = $baseImponible + $ivaMonto;
                                @endphp
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="65%">Descripción</th>
                                                <th width="15%" class="text-right">Precio Unitario</th>
                                                <th width="15%" class="text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($presupuesto->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item['descripcion'] ?? 'Descripción no disponible' }}</td>
                                                    <td class="text-right">${{ number_format($item['precio'] ?? 0, 2) }}</td>
                                                    <td class="text-right">${{ number_format($item['precio'] ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <th colspan="3" class="text-right">Subtotal:</th>
                                                <th class="text-right">${{ number_format($subtotal, 2) }}</th>
                                            </tr>
                                            @if($presupuesto->descuento > 0)
                                            <tr>
                                                <th colspan="3" class="text-right">Descuento ({{ number_format($presupuesto->descuento, 2) }}%):</th>
                                                <th class="text-right">-${{ number_format($descuentoMonto, 2) }}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">Base Imponible:</th>
                                                <th class="text-right">${{ number_format($baseImponible, 2) }}</th>
                                            </tr>
                                            @endif
                                            @if($presupuesto->iva > 0)
                                            <tr>
                                                <th colspan="3" class="text-right">IVA ({{ $ivaPorcentaje }}%):</th>
                                                <th class="text-right">${{ number_format($ivaMonto, 2) }}</th>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th colspan="3" class="text-right">TOTAL:</th>
                                                <th class="text-right">${{ number_format($total, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>No hay trabajos registrados en este presupuesto.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
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
        .table th {
            white-space: nowrap;
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.35em 0.65em;
        }
        tfoot tr:last-child th {
            font-size: 1.1em;
            background-color: #e9ecef;
        }
    </style>
@stop