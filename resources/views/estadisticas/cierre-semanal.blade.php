@extends('adminlte::page')

@section('title', 'Cierre Semanal')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="text-gray-800">Cierre Semanal - Del {{ $startOfWeek->format('d M Y') }} al {{ $endOfWeek->format('d M Y') }}</h1>
    <form method="GET" action="{{ route('cierre.semanal') }}" class="form-inline" id="filterForm">
        <div class="form-group mr-2">
            <div class="input-group">
                <input type="date" class="form-control form-control-sm" name="start_date" 
                       value="{{ $startOfWeek->format('Y-m-d') }}" />
                <div class="input-group-append">
                </div>
                <input type="date" class="form-control form-control-sm" name="end_date" 
                       value="{{ $endOfWeek->format('Y-m-d') }}" />
            </div>
        </div>

        <input type="hidden" name="year" value="{{ $yearSelected }}" />
        <input type="hidden" name="week" value="{{ $weekSelected }}" />
        
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-filter"></i> Filtrar
        </button>
    </form>

    <div class="btn-group">
        <form action="{{ route('cierre-ventas-semanal.export-pdf') }}" method="GET" class="d-inline">
            <input type="hidden" name="start_date" value="{{ $startOfWeek->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $endOfWeek->format('Y-m-d') }}">
            <button type="submit" class="btn btn-primary" title="Exportar a PDF">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </form>
        <form action="{{ route('cierre-ventas-semanal.export-excel') }}" method="GET" class="d-inline">
            <input type="hidden" name="start_date" value="{{ $startOfWeek->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $endOfWeek->format('Y-m-d') }}">
            <button type="submit" class="btn btn-success" title="Exportar a Excel">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
        </form>
    </div>
    
</div>
@stop

@section('content')
<div class="card mb-4 border-top-0 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Resumen Financiero Semanal</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center bg-ventas">Ventas Totales</th>
                        <th class="text-center bg-costos">Total Costos</th>
                        <th class="text-center bg-llaves">Costos de Llaves</th>
                        <th class="text-center bg-gastos">Total Gastos</th>
                        <th class="text-center bg-resumen">Ganancia antes del Dueño</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td class="font-weight-bold bg-ventas">${{ number_format($totalVentas ?? 0, 2) }}</td>
                        <td class="font-weight-bold bg-costos">${{ number_format($totalCostos ?? 0, 2) }}</td>
                        <td class="font-weight-bold bg-llaves">${{ number_format($totalCostosLlaves ?? 0, 2) }}</td>
                        <td class="font-weight-bold bg-gastos">${{ number_format($totalGastos ?? 0, 2) }}</td>
                        <td class="font-weight-bold {{ $ganancia >= 0 ? 'text-ganancia' : 'text-perdida' }} bg-resumen">
                            ${{ number_format($ganancia ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr class="text-center bg-gray-100">
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold bg-retiro">Retiro Dueño</td>
                    </tr>
                    <tr class="text-center bg-gray-200">
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold {{ $retiroDueño >= 0 ? 'text-ganancia' : 'text-perdida' }} bg-retiro">
                            ${{ number_format($retiroDueño ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr class="text-center bg-gray-100">
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold bg-final">Ganancia Total</td>
                    </tr>
                    <tr class="text-center bg-gray-200">
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold text-muted">-</td>
                        <td class="font-weight-bold {{ $gananciaFinal >= 0 ? 'text-ganancia' : 'text-perdida' }} bg-final">
                            ${{ number_format($gananciaFinal ?? 0, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Resumen de Ventas</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="align-middle">Técnico</th>
                        <th class="text-right bg-ventas">Ventas al Contado</th>
                        <th class="text-right bg-ventas">Ventas a Crédito</th>
                        <th class="text-right bg-ventas">Ingresos Recibidos</th>
                        <th class="text-right bg-gray-200">Total General</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalContado = 0;
                        $totalCredito = 0;
                        $totalRecibidos = 0;
                        $totalGeneral = 0;

                        $tecnicosMap = [];
                        foreach($reporteVentas as $item) {
                            $tecnicosMap[$item['tecnico']] = [
                                'ventas_contado' => $item['ventas_contado'],
                                'ventas_credito' => $item['ventas_credito'],
                                'total_ventas' => $item['total_ventas']
                            ];
                        }

                        foreach($ingresosRecibidos as $ingreso) {
                            if (!isset($tecnicosMap[$ingreso['tecnico']])) {
                                $tecnicosMap[$ingreso['tecnico']] = [
                                    'ventas_contado' => 0,
                                    'ventas_credito' => 0,
                                    'total_ventas' => 0
                                ];
                            }
                        }
                    @endphp
                    
                    @foreach($tecnicosMap as $tecnicoNombre => $datosVentas)
                    @php
                        $ingreso = collect($ingresosRecibidos)->firstWhere('tecnico', $tecnicoNombre);
                        $ingresoTotal = $ingreso['total'] ?? 0;

                        $ventasContado = $datosVentas['ventas_contado'] instanceof \Illuminate\Support\Collection
                            ? $datosVentas['ventas_contado']->sum()
                            : ($datosVentas['ventas_contado'] ?? 0);

                        $ventasCredito = $datosVentas['ventas_credito'] instanceof \Illuminate\Support\Collection
                            ? $datosVentas['ventas_credito']->sum()
                            : ($datosVentas['ventas_credito'] ?? 0);

                        $ventasTotales = $datosVentas['total_ventas'] instanceof \Illuminate\Support\Collection
                            ? $datosVentas['total_ventas']->sum()
                            : ($datosVentas['total_ventas'] ?? ($ventasContado + $ventasCredito));
                    @endphp
                    <tr>
                        <td class="font-weight-bold">{{ $tecnicoNombre }}</td>
                        <td class="text-right bg-ventas">${{ number_format($ventasContado, 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($ventasCredito, 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($ingresoTotal, 2) }}</td>
                        <td class="text-right font-weight-bold bg-gray-100">${{ number_format($ventasTotales + $ingresoTotal, 2) }}</td>
                        
                        @php
                            $totalContado += $ventasContado;
                            $totalCredito += $ventasCredito;
                            $totalRecibidos += $ingresoTotal;
                            $totalGeneral += $ventasTotales + $ingresoTotal;
                        @endphp
                    </tr>
                    @endforeach

                    <tr class="font-weight-bold bg-gray-100">
                        <td>TOTAL</td>
                        <td class="text-right bg-ventas">${{ number_format($totalContado, 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($totalCredito, 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($totalRecibidos, 2) }}</td>
                        <td class="text-right bg-gray-200">${{ number_format($totalGeneral, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Ventas Detalladas por Técnico</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Técnico</th>
                        <th class="text-right">Ventas</th>
                        <th class="text-right">Valor Total</th>
                        <th class="text-right">Pagado</th>
                        <th class="text-right">Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventasDetalladasPorTecnico as $tecnico)
                        <tr data-toggle="collapse" data-target="#ventas-tecnico-{{ $loop->index }}" class="clickable-row bg-gray-200">
                            <td class="font-weight-bold">{{ $tecnico['tecnico'] }}</td>
                            <td class="text-right">{{ count($tecnico['ventas']) }}</td>
                            <td class="text-right">${{ number_format($tecnico['total_ventas'], 2) }}</td>
                            <td class="text-right">${{ number_format($tecnico['ventas']->sum('total_pagado'), 2) }}</td>
                            <td class="text-right font-weight-bold {{ $tecnico['ganancia_total'] >= 0 ? 'text-success' : 'text-danger' }}">
                                ${{ number_format($tecnico['ganancia_total'], 2) }}
                            </td>
                        </tr>

                        <tr id="ventas-tecnico-{{ $loop->index }}" class="collapse">
                            <td colspan="5" class="p-0">
                                <div class="p-3">
                                    <table class="table table-sm mb-0">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th>ID Venta</th>
                                                <th>Fecha</th>
                                                <th>Cliente</th>
                                                <th class="text-right">Valor</th>
                                                <th>Tipo</th>
                                                <th>Estatus</th>
                                                <th>Métodos Pago</th>
                                                <th class="text-right">Pagado</th>
                                                <th class="text-right">Ganancia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tecnico['ventas'] as $venta)
                                            <tr data-toggle="collapse" data-target="#detalle-venta-{{ $loop->parent->index }}-{{ $loop->index }}" class="clickable-row">
                                                <td class="font-weight-bold">#{{ $venta['id'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y') }}</td>
                                                <td>{{ $venta['cliente'] }}</td>
                                                <td class="text-right">${{ number_format($venta['valor_total'], 2) }}</td>
                                                <td>
                                                    <span class="badge badge-pill {{ $venta['tipo_venta'] === 'contado' ? 'bg-success' : 'bg-primary' }}">
                                                        {{ ucfirst($venta['tipo_venta']) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-pill 
                                                        {{ $venta['estatus'] === 'pagado' ? 'bg-success' : 
                                                           ($venta['estatus'] === 'pendiente' ? 'bg-danger' : 'bg-warning') }}">
                                                        {{ ucfirst($venta['estatus']) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(isset($venta['pagos']) && count($venta['pagos']) > 0)
                                                        @php
                                                            $paymentMethods = [];
                                                            foreach($venta['pagos'] as $pago) {
                                                                foreach($tiposDePago as $tipo) {
                                                                    if($tipo->id == ($pago['metodo_pago'] ?? null)) {
                                                                        $paymentMethods[] = $tipo->name;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        {{ implode(', ', array_unique($paymentMethods)) }}
                                                    @else
                                                        Sin pagos
                                                    @endif
                                                </td>
                                                <td class="text-right">${{ number_format($venta['total_pagado'] ?? 0, 2) }}</td>
                                                <td class="text-right font-weight-bold {{ ($venta['ganancia_bruta'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    ${{ number_format($venta['ganancia_bruta'] ?? 0, 2) }}
                                                </td>
                                            </tr>
                                            
                                            <tr id="detalle-venta-{{ $loop->parent->index }}-{{ $loop->index }}" class="collapse">
                                                <td colspan="9" class="p-0">
                                                    <div class="p-3">
                                                        <div class="mb-3">
                                                            <h6 class="font-weight-bold mb-2">Trabajos realizados:</h6>
                                                            <div class="pl-3">
                                                                @foreach($venta['trabajos'] as $trabajo)
                                                                <div class="mb-2 border-bottom pb-2">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="font-weight-bold">{{ $trabajo['trabajo'] }}</span>
                                                                        <span>${{ number_format($trabajo['precio_trabajo'], 2) }}</span>
                                                                    </div>
                                                                    @if($trabajo['descripcion'])
                                                                        <div class="text-muted small mt-1">{{ $trabajo['descripcion'] }}</div>
                                                                    @endif
                                                                    @if(count($trabajo['productos']) > 0)
                                                                        <div class="mt-2">
                                                                            <span class="small font-weight-bold">Productos utilizados:</span>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-sm table-borderless mb-0">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>ID Producto</th>
                                                                                            <th>Producto</th>
                                                                                            <th class="text-right">Cantidad</th>
                                                                                            <th class="text-right">Precio Unitario</th>
                                                                                            <th class="text-right">Total</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach($trabajo['productos'] as $producto)
                                                                                        <tr>
                                                                                            <td>{{ $producto['producto'] ?? 'N/A' }}</td>
                                                                                            <td>{{ $producto['nombre'] }}</td>
                                                                                            <td class="text-right">{{ $producto['cantidad'] }}</td>
                                                                                            <td class="text-right">${{ number_format($producto['precio'], 2) }}</td>
                                                                                            <td class="text-right">${{ number_format($producto['cantidad'] * $producto['precio'], 2) }}</td>
                                                                                        </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            @if(count($venta['costos']) > 0)
                                                            <div class="col-md-6">
                                                                <div class="card border-0 shadow-sm">
                                                                    <div class="card-header bg-light py-2">
                                                                        <h6 class="mb-0 font-weight-bold">Costos asociados</h6>
                                                                    </div>
                                                                    <div class="card-body p-0">
                                                                        <table class="table table-sm mb-0">
                                                                            <thead>
                                                                                <tr class="bg-gray-100">
                                                                                    <th>ID</th>
                                                                                    <th>Descripción</th>
                                                                                    <th>Método Pago</th>
                                                                                    <th class="text-right">Monto</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($venta['costos'] as $costo)
                                                                                <tr>
                                                                                    <td>#{{ $costo['id'] }}</td>
                                                                                    <td>{{ $costo['descripcion'] }} <small class="text-muted">({{ $costo['subcategoria'] }})</small></td>
                                                                                    <td>
                                                                                        @foreach($tiposDePago as $tipo)
                                                                                            @if($tipo->id == ($costo['metodo_pago_id'] ?? null))
                                                                                                {{ $tipo->name }}
                                                                                                @break
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </td>
                                                                                    <td class="text-right text-danger">${{ number_format($costo['valor'], 2) }}</td>
                                                                                </tr>
                                                                                @endforeach
                                                                                <tr class="font-weight-bold bg-gray-100">
                                                                                    <td colspan="3">Total costos</td>
                                                                                    <td class="text-right">${{ number_format($venta['total_costos'], 2) }}</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            
                                                            @if(count($venta['gastos']) > 0)
                                                            <div class="col-md-6">
                                                                <div class="card border-0 shadow-sm">
                                                                    <div class="card-header bg-light py-2">
                                                                        <h6 class="mb-0 font-weight-bold">Gastos asociados</h6>
                                                                    </div>
                                                                    <div class="card-body p-0">
                                                                        <table class="table table-sm mb-0">
                                                                            <thead>
                                                                                <tr class="bg-gray-100">
                                                                                    <th>ID</th>
                                                                                    <th>Descripción</th>
                                                                                    <th>Método Pago</th>
                                                                                    <th class="text-right">Monto</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($venta['gastos'] as $gasto)
                                                                                <tr>
                                                                                    <td>#{{ $gasto['id'] }}</td>
                                                                                    <td>{{ $gasto['descripcion'] }} <small class="text-muted">({{ $gasto['subcategoria'] }})</small></td>
                                                                                    <td>
                                                                                        @foreach($tiposDePago as $tipo)
                                                                                            @if($tipo->id == ($gasto['metodo_pago_id'] ?? null))
                                                                                                {{ $tipo->name }}
                                                                                                @break
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </td>
                                                                                    <td class="text-right text-warning">${{ number_format($gasto['valor'], 2) }}</td>
                                                                                </tr>
                                                                                @endforeach
                                                                                <tr class="font-weight-bold bg-gray-100">
                                                                                    <td colspan="3">Total gastos</td>
                                                                                    <td class="text-right">${{ number_format($venta['total_gastos'], 2) }}</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <div class="mt-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-header bg-light py-2">
                                                                    <h6 class="mb-0 font-weight-bold">Detalle de Pagos</h6>
                                                                </div>
                                                                <div class="card-body p-0">
                                                                    <table class="table table-sm mb-0">
                                                                        <thead>
                                                                            <tr class="bg-gray-100">
                                                                                <th>Fecha</th>
                                                                                <th>Método Pago</th>
                                                                                <th>Cobró</th>
                                                                                <th class="text-right">Monto</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @if(isset($venta['pagos']) && count($venta['pagos']) > 0)
                                                                                @foreach($venta['pagos'] as $pago)
                                                                                <tr>
                                                                                    <td>{{ \Carbon\Carbon::parse($pago['fecha'] ?? now())->format('d/m/Y') }}</td>
                                                                                    <td>
                                                                                        @foreach($tiposDePago as $tipo)
                                                                                            @if($tipo->id == ($pago['metodo_pago'] ?? null))
                                                                                                {{ $tipo->name }}
                                                                                                @break
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </td>
                                                                                    <td>
                                                                                        @if(isset($pago['cobrador_id']))
                                                                                            {{ App\Models\Empleado::find($pago['cobrador_id'])->nombre ?? 'Desconocido' }}
                                                                                        @else
                                                                                            Desconocido
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-right">${{ number_format($pago['monto'] ?? 0, 2) }}</td>
                                                                                </tr>
                                                                                @endforeach
                                                                                <tr class="font-weight-bold bg-gray-100">
                                                                                    <td colspan="3">Total pagado</td>
                                                                                    <td class="text-right">${{ number_format($venta['total_pagado'] ?? 0, 2) }}</td>
                                                                                </tr>
                                                                            @else
                                                                                <tr>
                                                                                    <td colspan="3" class="text-center text-muted">No hay registros de pagos</td>
                                                                                </tr>
                                                                            @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Detalle de Costos y Gastos</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="align-middle">Técnico</th>
                        <th colspan="3" class="text-center border-left bg-costos">Costos</th>
                        <th colspan="3" class="text-center border-left bg-gastos">Gastos</th>
                    </tr>
                    <tr>
                        <th class="bg-costos">Descripción</th>
                        <th class="bg-costos">Método Pago</th>
                        <th class="text-right bg-costos">Total</th>
                        <th class="bg-gastos">Descripción</th>
                        <th class="bg-gastos">Método Pago</th>
                        <th class="text-right bg-gastos">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reporteCostosGastos as $item)
                        @php
                            $costosCount = count($item['costos'] ?? []);
                            $gastosCount = count($item['gastos'] ?? []);
                            $maxRows = max($costosCount, $gastosCount);
                        @endphp
                        
                        @for($i = 0; $i < $maxRows; $i++)
                        <tr>
                            @if($i === 0)
                                <td rowspan="{{ $maxRows }}" class="align-middle font-weight-bold">
                                    {{ $item['tecnico'] }}
                                </td>
                            @endif
                            
                            @if(isset($item['costos'][$i]))
                                <td class="bg-costos">{{ $item['costos'][$i]['descripcion'] }}</td>
                                <td class="bg-costos">{{ $item['costos'][$i]['metodo_pago'] }}</td>
                                <td class="text-right bg-costos">${{ number_format($item['costos'][$i]['total'], 2) }}</td>
                            @else
                                <td colspan="3" class="bg-costos"></td>
                            @endif

                            @if(isset($item['gastos'][$i]))
                                <td class="bg-gastos">{{ $item['gastos'][$i]['descripcion'] }}</td>
                                <td class="bg-gastos">{{ $item['gastos'][$i]['metodo_pago'] }}</td>
                                <td class="text-right bg-gastos">${{ number_format($item['gastos'][$i]['total'], 2) }}</td>
                            @else
                                <td colspan="3" class="bg-gastos"></td>
                            @endif
                        </tr>
                        @endfor
                    @endforeach
                    
                    <tr class="font-weight-bold bg-gray-100">
                        <td>TOTALES</td>
                        <td colspan="2" class="bg-costos"></td>
                        <td class="text-right bg-costos">${{ number_format($totalCostos, 2) }}</td>
                        <td colspan="2" class="bg-gastos"></td>
                        <td class="text-right bg-gastos">${{ number_format($totalGastos, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Llaves Utilizadas por Técnico</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="align-middle">Técnico</th>
                        <th rowspan="2" class="align-middle">Llave</th>
                        @foreach($almacenesDisponibles as $almacen)
                            <th colspan="2" class="text-center border-left bg-llaves">{{ $almacen->nombre }}</th>
                        @endforeach
                        <th rowspan="2" class="align-middle text-center border-left">Total</th>
                        <th rowspan="2" class="align-middle text-center border-left bg-gray-200">Valor</th>
                    </tr>
                    <tr>
                        @foreach($almacenesDisponibles as $almacen)
                            <th class="text-right bg-llaves">Cantidad</th>
                            <th class="text-right bg-llaves">Valor</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalGeneralLlaves = 0;
                        $totalGeneralValorLlaves = 0;
                    @endphp
                    
                    @foreach($llavesPorTecnico as $tecnico)
                        @foreach($tecnico['llaves'] as $llave)
                        <tr>
                            <td class="font-weight-bold">{{ $tecnico['tecnico'] }}</td>
                            <td>{{ $llave['nombre'] }} - {{ $llave['id_producto'] }}</td>
                            @foreach($almacenesDisponibles as $almacen)
                                <td class="text-right bg-llaves">{{ $llave['almacenes'][$almacen->id]['cantidad'] ?? 0 }}</td>
                                <td class="text-right bg-llaves">${{ number_format($llave['almacenes'][$almacen->id]['total'] ?? 0, 2) }}</td>
                            @endforeach
                            <td class="text-right font-weight-bold">{{ $llave['total_cantidad'] }}</td>
                            <td class="text-right font-weight-bold bg-gray-100">${{ number_format($llave['total_valor'], 2) }}</td>
                            
                            @php
                                $totalGeneralLlaves += $llave['total_cantidad'];
                                $totalGeneralValorLlaves += $llave['total_valor'];
                            @endphp
                        </tr>
                        @endforeach
                    @endforeach
                    
                    <tr class="font-weight-bold bg-gray-100">
                        <td colspan="2">TOTALES</td>
                        @foreach($almacenesDisponibles as $almacen)
                            <td class="text-right bg-llaves">
                                @php
                                    $totalAlmacen = 0;
                                    foreach($llavesPorTecnico as $tecnico) {
                                        foreach($tecnico['llaves'] as $llave) {
                                            $totalAlmacen += $llave['almacenes'][$almacen->id]['cantidad'] ?? 0;
                                        }
                                    }
                                    echo $totalAlmacen;
                                @endphp
                            </td>
                            <td class="text-right bg-llaves">
                                @php
                                    $totalValorAlmacen = 0;
                                    foreach($llavesPorTecnico as $tecnico) {
                                        foreach($tecnico['llaves'] as $llave) {
                                            $totalValorAlmacen += $llave['almacenes'][$almacen->id]['total'] ?? 0;
                                        }
                                    }
                                    echo '$' . number_format($totalValorAlmacen, 2);
                                @endphp
                            </td>
                        @endforeach
                        <td class="text-right">{{ $totalGeneralLlaves }}</td>
                        <td class="text-right bg-gray-200">${{ number_format($totalGeneralValorLlaves, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Descargas Manuales</h3>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="info-box bg-costos">
                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Descargas</span>
                        <span class="info-box-number">{{ $totalDescargas }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Usuario</th>
                        <th class="text-center">Producto</th>
                        <th class="text-center">Motivo</th>
                        <th class="text-center">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cargasDescargas as $movimiento)
                        <tr>
                            <td class="text-center">{{ $movimiento['usuario'] }}</td>
                            <td class="text-center">{{ $movimiento['producto'] }} (ID: {{ $movimiento['id_producto'] }})</td>
                            <td class="text-center">{{ $movimiento['motivo'] }}</td>
                            <td class="text-center">{{ $movimiento['fecha'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Ventas por Lugar de Venta</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Lugar de Venta</th>
                        <th class="text-right bg-ventas">Cantidad de Ventas</th>
                        <th class="text-right bg-ventas">Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalVentas = 0;
                        $totalMonto = 0;
                    @endphp
                    
                    @foreach($ventasPorLugarVenta as $lugar)
                    @php
                        $totalVentas += $lugar['cantidad'];
                        $totalMonto += $lugar['monto'];
                    @endphp
                    <tr>
                        <td class="font-weight-bold">{{ $lugar['nombre'] }}</td>
                        <td class="text-right bg-ventas">{{ $lugar['cantidad'] }}</td>
                        <td class="text-right bg-ventas">${{ number_format($lugar['monto'], 2) }}</td>
                    </tr>
                    @endforeach

                    <tr class="font-weight-bold bg-gray-100">
                        <td>TOTAL</td>
                        <td class="text-right bg-ventas">{{ $totalVentas }}</td>
                        <td class="text-right bg-ventas">${{ number_format($totalMonto, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4 shadow-soft h-100">
            <div class="card-header bg-light">
                <h3 class="card-title text-gray-800">Ventas al Contado por Trabajo</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Trabajo</th>
                                <th class="text-right bg-ventas">Monto</th>
                                <th class="text-center">Métodos de Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalContado = $ventasPorTrabajo['total_contado']; @endphp
                            @foreach($ventasPorTrabajo['contado'] as $trabajo => $data)
                            <tr>
                                @php
                                    $trabajoId = $data['trabajo_id'] ?? null;
                                    $nombreTrabajo = $trabajoId && isset($trabajos[$trabajoId]) ? $trabajos[$trabajoId] : $trabajo;
                                @endphp
                                <td>{{ $nombreTrabajo }}</td>
                                <td class="text-right bg-ventas">${{ number_format($data['total'], 2) }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @foreach($data['metodos'] as $metodo => $detalle)
                                        <div class="mb-1">
                                            <span class="badge badge-pill bg-light text-dark d-flex align-items-center justify-content-between">
                                                <span class="d-flex align-items-center">
                                                    {{ $metodo }}
                                                </span>
                                                <span class="ml-2 font-weight-bold">${{ number_format($detalle['total'], 2) }}</span>
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-weight-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-right bg-ventas">${{ number_format($totalContado, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4 shadow-soft h-100">
            <div class="card-header bg-light">
                <h3 class="card-title text-gray-800">Ventas a Crédito por Trabajo</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Trabajo</th>
                                <th class="text-right bg-ventas">Monto</th>
                                <th class="text-center">Métodos de Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalCredito = $ventasPorTrabajo['total_credito']; @endphp
                            @foreach($ventasPorTrabajo['credito'] as $trabajo => $data)
                            <tr>
                                @php
                                    $trabajoId = $data['trabajo_id'] ?? null;
                                    $nombreTrabajo = $trabajoId && isset($trabajos[$trabajoId]) ? $trabajos[$trabajoId] : $trabajo;
                                @endphp
                                <td>{{ $nombreTrabajo }}</td>
                                <td class="text-right bg-ventas">${{ number_format($data['total'], 2) }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @foreach($data['metodos'] as $metodo => $detalle)
                                        <div class="mb-1">
                                            <span class="badge badge-pill bg-light text-dark d-flex align-items-center justify-content-between">
                                                <span class="d-flex align-items-center">
                                                    {{ $metodo }}
                                                </span>
                                                <span class="ml-2 font-weight-bold">${{ number_format($detalle['total'], 2) }}</span>
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-weight-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-right bg-ventas">${{ number_format($totalCredito, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft mt-4">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Resumen de Trabajos Realizados</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Tipo de Trabajo</th>
                        <th class="text-right bg-resumen">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resumenTrabajos as $trabajo => $data)
                    <tr>
                        @php
                            $trabajoId = $data['trabajo_id'] ?? null;
                            $nombreTrabajo = $trabajoId && isset($trabajos[$trabajoId]) ? $trabajos[$trabajoId] : $data['nombre'];
                        @endphp
                        <td>{{ $nombreTrabajo }}</td>
                        <td class="text-right bg-resumen">{{ $data['cantidad'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-weight-bold">
                    <tr>
                        <td>TOTAL</td>
                        <td class="text-right bg-resumen">{{ $totalTrabajos }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Ventas por Cliente</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-gray-100">
                    <tr>
                        <th>Cliente</th>
                        <th class="text-right bg-ventas">Ventas al Contado</th>
                        <th class="text-right bg-ventas">Ventas a Crédito</th>
                        <th class="text-right bg-gray-200">Total General</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalContadoClientes = 0;
                        $totalCreditoClientes = 0;
                    @endphp
                    
                    @foreach($ventasPorCliente as $cliente)
                    <tr>
                        <td class="font-weight-bold">{{ $cliente['cliente'] }}</td>
                        <td class="text-right bg-ventas">${{ number_format($cliente['ventas_contado'], 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($cliente['ventas_credito'], 2) }}</td>
                        <td class="text-right font-weight-bold bg-gray-100">${{ number_format($cliente['total_ventas'], 2) }}</td>
                        
                        @php
                            $totalContadoClientes += $cliente['ventas_contado'];
                            $totalCreditoClientes += $cliente['ventas_credito'];
                        @endphp
                    </tr>
                    @endforeach

                    <tr class="font-weight-bold bg-gray-100">
                        <td>TOTAL</td>
                        <td class="text-right bg-ventas">${{ number_format($totalContadoClientes, 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($totalCreditoClientes, 2) }}</td>
                        <td class="text-right bg-gray-200">${{ number_format($totalContadoClientes + $totalCreditoClientes, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Visualización de Datos</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="costosGastosChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@section('css')
<style>
    .bg-gray-100 {
        background-color: #f8f9fa;
    }
    .bg-retiro {
        background-color: #fff3cd;
    }
    .bg-final {
        background-color: #d4edda;
    }
    .bg-gray-200 {
        background-color: #e9ecef;
    }
    .text-ganancia {
        color: #28a745;
    }
    .text-perdida {
        color: #dc3545;
    }
        background-color: #ffeb3b;
    }
    .bg-final {
        background-color: #4caf50;
    }
    .bg-gray-200 {
        background-color: #e9ecef;
    }
    .text-gray-800 {
        color: #2d3748;
    }

    .bg-ventas {
        background-color: rgba(40, 167, 69, 0.22);
    }
    .bg-costos {
        background-color: rgba(220, 53, 69, 0.22);
    }
    .bg-gastos {
        background-color: rgba(253, 126, 20, 0.22);
    }
    .bg-llaves {
        background-color: rgba(23, 162, 184, 0.22);
    }
    .bg-resumen {
        background-color: rgba(108, 117, 125, 0.18);
    }

    .border-left-ventas {
        border-left: 4px solid #28a745;
    }
    .border-left-costos {
        border-left: 4px solid #dc3545;
    }
    .border-left-gastos {
        border-left: 4px solid #fd7e14;
    }
    .border-left-llaves {
        border-left: 4px solid #17a2b8;
    }
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.03) !important;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        border: 1px solid rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-radius: 0.35rem;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.08);
        background-color: #f8f9fa;
    }
    .shadow-soft {
        box-shadow: 0 0.15rem 0.5rem rgba(0,0,0,0.03);
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .table thead th {
        border-bottom: none;
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        vertical-align: middle;
    }
    .table td, .table th {
        border-top: 1px solid rgba(0,0,0,0.05);
        vertical-align: middle;
    }
    .table-sm td, .table-sm th {
        padding: 0.5rem;
    }

    .text-ganancia {
        color: #28a745;
    }
    .text-perdida {
        color: #dc3545;
    }

    .badge-metric {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
        font-weight: 500;
        border-radius: 0.25rem;
    }

    .card-title i {
        opacity: 0.8;
    }

    .chart-container {
        position: relative;
        min-height: 300px;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .opacity-25 {
        opacity: 0.25;
    }
    .h-100 {
        height: 100%;
    }

    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: rgba(0,0,0,0.03);
    }
    .collapse-row.collapse:not(.show) {
        display: none;
    }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el cambio en las fechas
    document.querySelectorAll('[name="start_date"]').forEach(input => {
        input.addEventListener('change', function() {
            // Actualizar las fechas en los formularios de exportación
            document.querySelectorAll('[name="start_date"]').forEach(exportInput => {
                exportInput.value = this.value;
            });
        });
    });

    document.querySelectorAll('[name="end_date"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('[name="end_date"]').forEach(exportInput => {
                exportInput.value = this.value;
            });
        });
    });

    // Manejar el envío del formulario principal
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        // Actualizar las fechas en los formularios de exportación antes de enviar
        const startDate = this.querySelector('[name="start_date"]').value;
        const endDate = this.querySelector('[name="end_date"]').value;
        
        document.querySelectorAll('[name="start_date"]').forEach(input => {
            input.value = startDate;
        });
        
        document.querySelectorAll('[name="end_date"]').forEach(input => {
            input.value = endDate;
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        var ventasCtx = document.getElementById('ventasChart').getContext('2d');
        var ventasChart = new Chart(ventasCtx, {
            type: 'doughnut',
            data: {
                labels: ['Contado', 'Crédito', 'Ingresos Recibidos'],
                datasets: [{
                    data: [{{ $totalContado }}, {{ $totalCredito }}, {{ $totalRecibidos }}],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.3)',
                        'rgba(0, 123, 255, 0.3)',
                        'rgba(108, 117, 125, 0.3)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(0, 123, 255, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribución de Ventas',
                        font: {
                            size: 16,
                            weight: '600'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        titleFont: {
                            weight: 'bold',
                            size: 14
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.formattedValue || '';
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((context.parsed / total) * 100);
                                return `${label}: $${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        var costosGastosCtx = document.getElementById('costosGastosChart').getContext('2d');
        var costosGastosChart = new Chart(costosGastosCtx, {
            type: 'bar',
            data: {
                labels: ['Costos', 'Gastos', 'Costos Llaves'],
                datasets: [{
                    label: 'Montos',
                    data: [{{ $totalCostos }}, {{ $totalGastos }}, {{ $totalCostosLlaves }}],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.3)',
                        'rgba(253, 126, 20, 0.3)',
                        'rgba(23, 162, 184, 0.3)'
                    ],
                    borderColor: [
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(253, 126, 20, 0.7)',
                        'rgba(23, 162, 184, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Costos vs Gastos',
                        font: {
                            size: 16,
                            weight: '600'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        titleFont: {
                            weight: 'bold',
                            size: 14
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                return `$${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });

    $(document).ready(function() {
        $('.clickable-row').click(function() {
            var target = $(this).data('target');
            $(target).collapse('toggle');
        });
    });
</script>
@stop

@stop