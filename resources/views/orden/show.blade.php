@extends('adminlte::page')

@section('title', 'Órdenes')

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
                            <span class="card-title">{{ __('Información de la Órden') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary" href="{{ route('ordens.index') }}">
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
                                        <strong class="d-block">ID Órden:</strong>
                                        <span class="text-muted">{{ $orden->id_orden }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-user text-info mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Cliente:</strong>
                                        <span class="text-muted">{{ $orden->cliente->nombre ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-calendar-day text-warning mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Fecha de Órden:</strong>
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($orden->f_orden)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-user-cog text-secondary mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Técnico:</strong>
                                        <span class="text-muted">{{ $orden->empleado->nombre }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-success mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Dirección:</strong>
                                        <span class="text-muted">{{ $orden->direccion }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4 d-flex align-items-center">
                                    <i class="fas fa-receipt text-danger mr-3 fa-lg"></i>
                                    <div>
                                        <strong class="d-block">Estado:</strong>
                                        <span class="text-muted">
                                            <span class="badge 
                                                @if($orden->estado == 'completado') badge-success 
                                                @elseif($orden->estado == 'pendiente') badge-warning 
                                                @elseif($orden->estado == 'cancelado') badge-danger 
                                                @else badge-secondary 
                                                @endif">
                                                {{ ucfirst($orden->estado) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h4 class="mb-4">
                                <i class="fas fa-list-check mr-2"></i>Trabajos
                            </h4>
                            
                            @if (is_array($orden->items) && count($orden->items) > 0)
                                @php
                                    $totalItems = 0;
                                @endphp
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="15%">#</th>
                                                <th width="70%">Descripción</th>
                                                <th width="15%" class="text-right">Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orden->items as $index => $item)
                                                @php
                                                    $totalItems += $item['cantidad'] ?? 0;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item['descripcion'] ?? 'Descripción no disponible' }}</td>
                                                    <td class="text-right">${{ number_format($item['cantidad'] ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <th colspan="2" class="text-right">Total</th>
                                                <th class="text-right">${{ number_format($totalItems ?? 0,2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>No hay trabajos registrados en esta orden.
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