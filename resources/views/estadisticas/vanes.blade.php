@php
use Carbon\Carbon;
@endphp

@extends('adminlte::page')

@section('title', 'Estadísticas Vans')

@section('content_header')
    <h1>Estadísticas de Vans y Técnicos</h1>
    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <label for="start_date" class="mr-2">Desde</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', isset($startDate) ? $startDate : '') }}">
                </div>
                <div class="form-group mr-2">
                    <label for="end_date" class="mr-2">Hasta</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', isset($endDate) ? $endDate : '') }}">
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

    </div>
@stop

@section('content')
    <!-- Resumen General -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Resumen General</h3>
                    <div class="card-tools">
                        <span class="badge badge-light mr-2" 
                              data-toggle="tooltip" 
                              title="Utilidad {{ $vanGrande }}">
                            {{ $vanGrande }}: 
                            <span class="text-{{ $totales['utilidadGrande'] >= 0 ? 'success' : 'danger' }}">
                                ${{ number_format($totales['utilidadGrande'], 2) }}
                            </span>
                        </span>
                        <span class="badge badge-light mr-2" 
                              data-toggle="tooltip" 
                              title="Utilidad {{ $vanPequena }}">
                            {{ $vanPequena }}: 
                            <span class="text-{{ $totales['utilidadPequena'] >= 0 ? 'success' : 'danger' }}">
                                ${{ number_format($totales['utilidadPequena'], 2) }}
                            </span>
                        </span>
                        <span class="badge badge-light mr-2" 
                              data-toggle="tooltip" 
                              title="Suma de utilidades">
                            Total: 
                            <span class="text-{{ ($totales['utilidadGrande'] + $totales['utilidadPequena']) >= 0 ? 'success' : 'danger' }}">
                                ${{ number_format($totales['utilidadGrande'] + $totales['utilidadPequena'], 2) }}
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-truck"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $vanGrande }}</span>
                                    <span class="info-box-number">${{ number_format($totales['ventasGrande'], 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totales['ventasGrande'] ? 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $totales['itemsGrande'] }} items
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-primary">
                                <span class="info-box-icon"><i class="fas fa-truck"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $vanPequena }}</span>
                                    <span class="info-box-number">${{ number_format($totales['ventasPequena'], 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totales['ventasPequena'] ? 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $totales['itemsPequena'] }} items
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-key"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Llaves Vendidas</span>
                                    <span class="info-box-number">{{ $totales['totalLlaves'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totales['totalLlaves'] ? 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        ${{ number_format($totales['totalValorLlaves'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-danger">
                                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Porcentaje Cerrajero</span>
                                    <span class="info-box-number">{{ $totales['porcentajeCerrajeroGrande'] }}$ / {{ $totales['porcentajeCerrajeroPequena'] }}$</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Grande / Pequeña
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items por Van -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Items Vendidos - {{ $vanGrande }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">Total: ${{ number_format($totales['valorItemsGrande'], 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Valor Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventasVanGrande['items'] as $item)
                                <tr>
                                    <td>{{ $item['nombre'] }}</td>
                                    <td>{{ $item['total_cantidad'] }}</td>
                                    <td>${{ number_format($item['total_valor'], 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="collapse" 
                                                data-target="#detalleItemGrande{{ $loop->index }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="p-0">
                                        <div id="detalleItemGrande{{ $loop->index }}" class="collapse">
                                            <div class="p-3 bg-light">
                                                <h5>Detalle de ventas</h5>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Cliente</th>
                                                            <th>Técnico</th>
                                                            <th>Cantidad</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($item['ventas'] as $venta)
                                                        <tr>
                                                            <td>{{ $venta['fecha'] }}</td>
                                                            <td>{{ $venta['cliente'] }}</td>
                                                            <td>{{ $venta['tecnico'] }}</td>
                                                            <td>{{ $venta['cantidad'] }}</td>
                                                            <td>${{ number_format($venta['total'], 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay items registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Items Vendidos - {{ $vanPequena }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-success">Total: ${{ number_format($totales['valorItemsPequena'], 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Valor Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventasVanPequena['items'] as $item)
                                <tr>
                                    <td>{{ $item['nombre'] }}</td>
                                    <td>{{ $item['total_cantidad'] }}</td>
                                    <td>${{ number_format($item['total_valor'], 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="collapse" 
                                                data-target="#detalleItemPequena{{ $loop->index }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="p-0">
                                        <div id="detalleItemPequena{{ $loop->index }}" class="collapse">
                                            <div class="p-3 bg-light">
                                                <h5>Detalle de ventas</h5>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Cliente</th>
                                                            <th>Técnico</th>
                                                            <th>Cantidad</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($item['ventas'] as $venta)
                                                        <tr>
                                                            <td>{{ $venta['fecha'] }}</td>
                                                            <td>{{ $venta['cliente'] }}</td>
                                                            <td>{{ $venta['tecnico'] }}</td>
                                                            <td>{{ $venta['cantidad'] }}</td>
                                                            <td>${{ number_format($venta['total'], 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay items registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Llaves por Técnico -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Llaves Vendidas por Técnico</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">Total: {{ $totales['totalLlaves'] }} (${{ number_format($totales['totalValorLlaves'], 2) }})</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Técnico</th>
                                    <th>Total Llaves</th>
                                    <th>Valor Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($llavesPorTecnico as $tecnico)
                                <tr>
                                    <td>{{ $tecnico['tecnico'] }}</td>
                                    <td>{{ $tecnico['total_llaves'] }}</td>
                                    <td>${{ number_format($tecnico['total_valor'], 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="collapse" 
                                                data-target="#detalleLlaves{{ $loop->index }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="p-0">
                                        <div id="detalleLlaves{{ $loop->index }}" class="collapse">
                                            <div class="p-3 bg-light">
                                                <h5>Detalle de llaves</h5>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Llave</th>
                                                            <th>Cantidad</th>
                                                            <th>Valor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($tecnico['llaves'] as $llave)
                                                        @foreach($llave['almacenes'] as $almacenId => $almacen)
                                                        <tr>
                                                            <td>{{ $llave['nombre'] }}</td>
                                                            <td>{{ $almacen['cantidad'] }}</td>
                                                            <td>${{ number_format($almacen['total'], 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay llaves registradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gastos y Costos -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Gastos por Van</h3>
                    <div class="card-tools">
                        <span class="badge badge-danger">Total: ${{ number_format($totales['gastosGrande'] + $totales['gastosPequena'], 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs" id="gastosTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="grande-tab" data-toggle="tab" href="#grande" role="tab">
                                {{ $vanGrande }} (${{ number_format($totales['gastosGrande'], 2) }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pequena-tab" data-toggle="tab" href="#pequena" role="tab">
                                {{ $vanPequena }} (${{ number_format($totales['gastosPequena'], 2) }})
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="grande" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Descripción</th>
                                            <th>Valor</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($gastosVanGrande as $gasto)
                                        <tr>
                                            <td>{{ Carbon::parse($gasto->f_gastos)->format('m/d/Y') }}</td>
                                            <td>{{ $gasto->descripcion }}</td>
                                            <td>${{ number_format($gasto->valor, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $gasto->estatus == 'pagado' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($gasto->estatus) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No hay gastos registrados</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pequena" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Descripción</th>
                                            <th>Valor</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($gastosVanPequena as $gasto)
                                        <tr>
                                            <td>{{ Carbon::parse($gasto->f_gastos)->format('m/d/Y') }}</td>
                                            <td>{{ $gasto->descripcion }}</td>
                                            <td>${{ number_format($gasto->valor, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $gasto->estatus == 'pagado' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($gasto->estatus) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No hay gastos registrados</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Costos por Van</h3>
                    <div class="card-tools">
                        <span class="badge badge-info">Total: ${{ number_format($totales['costosGrande'] + $totales['costosPequena'], 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs" id="costosTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="costos-grande-tab" data-toggle="tab" href="#costos-grande" role="tab">
                                {{ $vanGrande }} (${{ number_format($totales['costosGrande'], 2) }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="costos-pequena-tab" data-toggle="tab" href="#costos-pequena" role="tab">
                                {{ $vanPequena }} (${{ number_format($totales['costosPequena'], 2) }})
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="costos-grande" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Descripción</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($costosVanGrande as $costo)
                                        <tr>
                                            <td>{{ Carbon::parse($costo->f_costos)->format('m/d/Y') }}</td>
                                            <td>{{ $costo->descripcion }}</td>
                                            <td>${{ number_format($costo->valor, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No hay costos registrados</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="costos-pequena" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Descripción</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($costosVanPequena as $costo)
                                        <tr>
                                            <td>{{ Carbon::parse($costo->f_costos)->format('m/d/Y') }}</td>
                                            <td>{{ $costo->descripcion }}</td>
                                            <td>${{ number_format($costo->valor, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No hay costos registrados</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Inicializar DataTables en las tablas principales
            $('.table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false,
                "ordering": false,
                "paging": false,
                "searching": false,
                "info": false
            });
        });
    </script>
@stop