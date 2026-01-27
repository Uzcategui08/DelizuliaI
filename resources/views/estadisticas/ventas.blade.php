@extends('adminlte::page')

@section('title', 'Estadísticas Financieras')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
        <h1>Reporte Financiero - {{ \Carbon\Carbon::create($yearSelected, $monthSelected, 1)->format('F Y') }}</h1>
        
        <form method="GET" action="{{ route('estadisticas.ventas') }}" class="form-inline">
            <div class="form-group mr-2">
                <select name="month" class="form-control">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $month == $monthSelected ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->monthName }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mr-2">
                <select name="year" class="form-control">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            
            @if(request()->has('month') || request()->has('year'))
                <a href="{{ route('estadisticas.ventas') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            @endif
        </form>
    </div>
@stop

@section('content')
@if($noData)
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h3>No hay datos de ventas para {{ DateTime::createFromFormat('!m', $monthSelected)->format('F') }} de {{ $yearSelected }}</h3>
        <p class="mt-3">Por favor, seleccione otro período.</p>
    </div>
@else
<div class="container-fluid px-0">  <!-- px-0 para eliminar padding horizontal -->
    <div class="card shadow-sm rounded-0" style="border-left: 4px solid #0d6efd;">  <!-- rounded-0 para esquinas rectas -->
    <div class="card-body p-4">
<form action="{{ route('generatePdfTotal.pdf') }}" method="GET" target="_blank">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <label for="fecha_inicio" class="form-label small mb-1">Fecha Inicio</label>
            <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="fecha_inicio" 
                   value="{{ old('fecha_inicio', $fecha_inicio ?? '') }}">
        </div>
        <div class="col-md-4">
            <label for="fecha_fin" class="form-label small mb-1">Fecha Fin</label>
            <input type="date" class="form-control form-control-sm" id="fecha_fin" name="fecha_fin" 
                   value="{{ old('fecha_fin', $fecha_fin ?? '') }}">
        </div>
        <input type="hidden" name="month" value="{{ $monthSelected }}">
        <input type="hidden" name="year" value="{{ $yearSelected }}">
        <div class="col-md-4">
            <button type="submit" class="btn btn-sm btn-primary mt-3">
                <i class="fas fa-file-pdf me-1"></i> Generar PDF
            </button>
        </div>
    </div>
</form>
    </div>
</div>
</div>
    <div class="card mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h3 class="card-title">Resumen Ejecutivo</h3>
            <div class="card-tools">
                <span class="badge bg-light text-dark">
                    {{ \Carbon\Carbon::create($yearSelected, $monthSelected, 1)->format('F Y') }}
                </span>
            </div>
<button onclick="window.print()" class="btn btn-secondary">
    <i class="fas fa-print"></i> Imprimir Página
</button>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">Facturación</div>
                        <div class="card-body">
                            <h4 class="card-title">${{ number_format($stats['ventas']['facturacion'], 2) }}</h4>
                            <p class="card-text">
                                <small class="text-muted">
                                    Cobrado: ${{ number_format($stats['ventas']['cobrado_mes'], 2) }}<br>
                                    Transacciones: {{ $stats['ventas']['num_transacciones'] }}<br>
                                    Ticket promedio: ${{ number_format($stats['ventas']['ticket_promedio'], 2) }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">Utilidad Bruta</div>
                        <div class="card-body">
                            <h4 class="card-title">${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</h4>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ number_format($stats['costos']['porcentaje_utilidad_bruta'], 2) }}% de facturación<br>
                                    Costo venta: ${{ number_format($stats['costos']['total_costo_venta'], 2) }}<br>
                                    ({{ number_format($stats['costos']['porcentaje_costo_venta'], 2) }}%)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">Utilidad Neta</div>
                        <div class="card-body">
                            <h4 class="card-title">${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</h4>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ number_format($stats['resultados']['porcentaje_utilidad_neta'], 2) }}% de facturación<br>
                                    Total gastos: ${{ number_format($stats['gastos']['total_gastos'], 2) }}<br>
                                    ({{ number_format($stats['gastos']['porcentaje_gastos'], 2) }}%)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h3 class="card-title">Detalle de Ventas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Concepto</th>
                            <th>Valor</th>
                            <th>% Facturación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Facturación Total</td>
                            <td>${{ number_format($stats['ventas']['facturacion'], 2) }}</td>
                            <td>100.00%</td>
                        </tr>
                        <tr>
                            <td>Cobrado del Mes</td>
                            <td>${{ number_format($stats['ventas']['cobrado_mes'], 2) }}</td>
                            <td>{{ number_format($stats['ventas']['cobrado_mes'] / $stats['ventas']['facturacion'] * 100, 2) }}%</td>
                        </tr>
                        <tr>
                            <td>Evolución Facturación (vs mes anterior)</td>
                            <td colspan="2">{{ number_format($stats['ventas']['evolucion_facturacion'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detalle de Costos y Gastos -->
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h3 class="card-title">Detalle de Costos y Gastos</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Concepto</th>
                        <th>Valor</th>
                        <th>% Facturación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Costo de Venta</strong></td>
                        <td>${{ number_format($stats['costos']['total_costos_mes'], 2) }}</td>
                        <td>{{ number_format($stats['costos']['porcentaje_total_costos'], 2) }}%</td>
                    </tr>
                    <tr>
                        <td><strong>Utilidad Bruta</strong></td>
                        <td>${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</td>
                        <td>{{ number_format($stats['costos']['porcentaje_utilidad_bruta'], 2) }}%</td>
                    </tr>
                                        
                    @foreach($stats['gastos']['por_subcategoria'] as $item)
                    <tr>
                        <td>&nbsp;&nbsp;{{ $item['nombre'] }}</td>
                        <td>${{ number_format($item['total'], 2) }}</td>
                        <td>{{ number_format($item['porcentaje'], 2) }}%</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td><strong>Total Gastos</strong></td>
                        <td>${{ number_format($stats['gastos']['total_gastos'], 2) }}</td>
                        <td>{{ number_format($stats['gastos']['porcentaje_gastos'], 2) }}%</td>
                    </tr>
                    <tr>
                        <td><strong>Utilidad Neta</strong></td>
                        <td>${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</td>
                        <td>{{ number_format($stats['resultados']['porcentaje_utilidad_neta'], 2) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>

@endif
@stop

@section('css')
<style>
    .card-header {
        font-weight: bold;
    }
    .table th {
        background-color: #343a40;
        color: white;
    }
    .table tbody tr:hover {
        background-color: rgba(0,0,0,.05);
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .container-fluid, .container-fluid * {
            visibility: visible;
        }
        .container-fluid {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .card-header, .table th {
            background-color: #343a40 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
        }
        .btn {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
    }
</style>
@stop