@extends('adminlte::page')

@section('title', 'Cierre Mensual')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="text-gray-800">Cierre Mensual - {{ \Carbon\Carbon::create($yearSelected, $monthSelected, 1)->format('F Y') }}</h1>
    
    <form method="GET" action="{{ route('cierre.mensual') }}" class="form-inline">
        <div class="form-group mr-2">
            <select name="month" class="form-control form-control-sm">
                @foreach(range(1, 12) as $month)
                    <option value="{{ $month }}" {{ $month == $monthSelected ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($month)->monthName }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group mr-2">
            <select name="year" class="form-control form-control-sm">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-filter"></i> Filtrar
        </button>
    </form>
</div>
@stop

@section('content')
<div class="card mb-4 border-top-0 shadow-soft">
    <div class="card-header bg-light">
        <h3 class="card-title text-gray-800">Resumen Financiero Mensual</h3>
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
                        <th class="text-center bg-resumen">Ganancia Neta</th>
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
                    @endphp
                    
                    @foreach($reporteVentas as $index => $item)
                    <tr>
                        <td class="font-weight-bold">{{ $item['tecnico'] }}</td>
                        <td class="text-right bg-ventas">${{ number_format($item['ventas_contado'], 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($item['ventas_credito'], 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($ingresosRecibidos[$index]['total'] ?? 0, 2) }}</td>
                        <td class="text-right font-weight-bold bg-gray-100">${{ number_format($item['total_ventas'] + ($ingresosRecibidos[$index]['total'] ?? 0), 2) }}</td>
                        
                        @php
                            $totalContado += $item['ventas_contado'];
                            $totalCredito += $item['ventas_credito'];
                            $totalRecibidos += $ingresosRecibidos[$index]['total'] ?? 0;
                            $totalGeneral += $item['total_ventas'] + ($ingresosRecibidos[$index]['total'] ?? 0);
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
                            $maxRows = max(count($item['costos']), count($item['gastos']));
                        @endphp
                        
                        @for($i = 0; $i < $maxRows; $i++)
                        <tr>
                            @if($i === 0)
                                <td rowspan="{{ $maxRows }}" class="align-middle font-weight-bold">{{ $item['tecnico'] }}</td>
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
                            <td>{{ $llave['nombre'] }}</td>
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
                    
                    <!-- Totales -->
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
                                <td>{{ $trabajo ?: 'Sin especificar' }}</td>
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
                                <td>{{ $trabajo ?: 'Sin especificar' }}</td>
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
                        <td>{{ $data['nombre'] }}</td>
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
</style>
@stop

@section('js')
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
</script>
@stop

@stop