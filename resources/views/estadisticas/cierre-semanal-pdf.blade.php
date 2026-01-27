<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cierre Semanal</title>
    <style>
        @page {
            size: landscape;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 11px;
        }
        .card {
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .card-body {
            padding: 0.5rem;
        }
        .card-title {
            color: #495057;
            margin: 0;
            font-size: 13px;
        }
        h1 {
            font-size: 16px;
            margin: 0;
        }
        h3 {
            font-size: 13px;
            margin: 0;
        }
        .table {
            width: 100%;
            margin-bottom: 0.5rem;
            color: #495057;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.3rem;
            vertical-align: top;
            border: 1px solid #dee2e6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom-width: 2px;
            background-color: #f8f9fa;
        }
        .bg-ventas {
            background-color: #e3f2fd;
        }
        .bg-costos {
            background-color: #fff3e0;
        }
        .bg-llaves {
            background-color: #f8f9fa;
        }
        .bg-gastos {
            background-color: #e8f5e9;
        }
        .bg-resumen {
            background-color: #fff3e0;
        }
        .bg-retiro {
            background-color: #f3e5f5;
        }
        .bg-final {
            background-color: #e3f2fd;
        }
        .text-ganancia {
            color: #2e7d32;
        }
        .text-perdida {
            color: #c62828;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .text-muted {
            color: #6c757d;
        }
        .badge {
            display: inline-block;
            padding: 0.2em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .bg-success {
            background-color: #28a745!important;
            color: white!important;
        }
        .bg-primary {
            background-color: #007bff!important;
            color: white!important;
        }
        .bg-danger {
            background-color: #dc3545!important;
            color: white!important;
        }
        .bg-warning {
            background-color: #ffc107!important;
            color: black!important;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -5px;
            margin-left: -5px;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 5px;
            padding-left: 5px;
        }
        .info-box {
            display: flex;
            margin-bottom: 1rem;
            min-height: 70px;
            padding: 0.5rem;
            background-color: #fff;
            border-radius: 0.25rem;
            border: 1px solid #dee2e6;
        }
        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            font-size: 1.5rem;
            text-align: center;
            border-radius: 0.25rem;
            background-color: rgba(0,0,0,0.1);
        }
        .info-box-content {
            flex: 1;
            padding: 0 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .info-box-text {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.8rem;
        }
        .info-box-number {
            display: block;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .small {
            font-size: 85%;
        }
        .border-bottom {
            border-bottom: 1px solid #dee2e6!important;
        }
        .pb-2 {
            padding-bottom: 0.5rem!important;
        }
        .mt-1 {
            margin-top: 0.25rem!important;
        }
        .mt-2 {
            margin-top: 0.5rem!important;
        }
        .mb-1 {
            margin-bottom: 0.25rem!important;
        }
        .mb-2 {
            margin-bottom: 0.5rem!important;
        }
        .mb-3 {
            margin-bottom: 1rem!important;
        }
        .pl-3 {
            padding-left: 1rem!important;
        }
        .d-flex {
            display: flex!important;
        }
        .justify-content-between {
            justify-content: space-between!important;
        }
        .align-items-center {
            align-items: center!important;
        }
        .flex-column {
            flex-direction: column!important;
        }
        .ml-2 {
            margin-left: 0.5rem!important;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Cierre Semanal - Del {{ $startDate }} al {{ $endDate }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resumen Financiero Semanal</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th class="text-center bg-ventas">Ventas Totales</th>
                        <th class="text-center bg-costos">Total Costos</th>
                        <th class="text-center bg-llaves">Costos de Llaves</th>
                        <th class="text-center bg-gastos">Total Gastos</th>
                        <th class="text-center bg-resumen">Ganancia antes del Dueño</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td class="font-weight-bold bg-ventas">${{ number_format($totales['totalVentas'] ?? 0, 2) }}</td>
                        <td class="font-weight-bold bg-costos">${{ number_format($totales['totalCostos'] ?? 0, 2) }}</td>
                        <td class="font-weight-bold bg-llaves">${{ number_format($totalCostosLlaves ?? 0, 2) }}</td>
                        <td class="font-weight-bold bg-gastos">${{ number_format($totales['totalGastos'] ?? 0, 2) }}</td>
                        <td class="font-weight-bold {{ $ganancia >= 0 ? 'text-ganancia' : 'text-perdida' }} bg-resumen">
                            ${{ number_format($ganancia ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right font-weight-bold">Retiro Dueño:</td>
                        <td class="text-center font-weight-bold {{ $retiroDueño >= 0 ? 'text-ganancia' : 'text-perdida' }} bg-retiro">
                            ${{ number_format($retiroDueño ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right font-weight-bold">Ganancia Total:</td>
                        <td class="text-center font-weight-bold {{ $gananciaFinal >= 0 ? 'text-ganancia' : 'text-perdida' }} bg-final">
                            ${{ number_format($gananciaFinal ?? 0, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resumen de Ventas</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Técnico</th>
                        <th class="text-right bg-ventas">Contado</th>
                        <th class="text-right bg-ventas">Crédito</th>
                        <th class="text-right bg-ventas">Ingresos</th>
                        <th class="text-right">Total</th>
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
                    @php
                        $totalContado += $item['ventas_contado'];
                        $totalCredito += $item['ventas_credito'];
                        $totalRecibidos += $ingresosRecibidos[$index]['total'] ?? 0;
                        $totalGeneral += $item['total_ventas'] + ($ingresosRecibidos[$index]['total'] ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $item['tecnico'] }}</td>
                        <td class="text-right bg-ventas">${{ number_format($item['ventas_contado'], 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($item['ventas_credito'], 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($ingresosRecibidos[$index]['total'] ?? 0, 2) }}</td>
                        <td class="text-right font-weight-bold">${{ number_format($item['total_ventas'] + ($ingresosRecibidos[$index]['total'] ?? 0), 2) }}</td>
                    </tr>
                    @endforeach

                    <tr class="font-weight-bold">
                        <td>TOTAL</td>
                        <td class="text-right bg-ventas">${{ number_format($totalContado, 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($totalCredito, 2) }}</td>
                        <td class="text-right bg-ventas">${{ number_format($totalRecibidos, 2) }}</td>
                        <td class="text-right">${{ number_format($totalGeneral, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ventas Detalladas por Técnico</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
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
                        <tr>
                            <td class="font-weight-bold">{{ $tecnico['tecnico'] }}</td>
                            <td class="text-right">{{ count($tecnico['ventas']) }}</td>
                            <td class="text-right">${{ number_format($tecnico['total_ventas'], 2) }}</td>
                            <td class="text-right">${{ number_format($tecnico['ventas']->sum('total_pagado'), 2) }}</td>
                            <td class="text-right font-weight-bold {{ $tecnico['ganancia_total'] >= 0 ? 'text-ganancia' : 'text-perdida' }}">
                                ${{ number_format($tecnico['ganancia_total'], 2) }}
                            </td>
                        </tr>

                        @foreach($tecnico['ventas'] as $venta)
                        <tr>
                            <td colspan="5" style="padding: 0;">
                                <div style="padding: 0.5rem;">
                                    <table class="table table-sm" style="margin-bottom: 0.5rem;">
                                        <tr>
                                            <td class="font-weight-bold">#{{ $venta['id'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y') }}</td>
                                            <td>{{ $venta['cliente'] }}</td>
                                            <td class="text-right">${{ number_format($venta['valor_total'], 2) }}</td>
                                            <td>
                                                <span class="badge {{ $venta['tipo_venta'] === 'contado' ? 'bg-success' : 'bg-primary' }}">
                                                    {{ ucfirst($venta['tipo_venta']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $venta['estatus'] === 'pagado' ? 'bg-success' : ($venta['estatus'] === 'pendiente' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($venta['estatus']) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(isset($venta['pagos']) && count($venta['pagos']) > 0)
                                                    @php
                                                        $paymentMethods = [];
                                                        foreach($venta['pagos'] as $pago) {
                                                            $metodoPagoId = $pago['metodo_pago'] ?? null;
                                                            if ($metodoPagoId !== null) {
                                                                $paymentMethods[] = $tiposDePago[$metodoPagoId] ?? 'Método ' . $metodoPagoId;
                                                            }
                                                        }
                                                        $paymentMethods = array_unique($paymentMethods);
                                                    @endphp
                                                    {{ implode(', ', $paymentMethods) }}
                                                @else
                                                    Sin pagos
                                                @endif
                                            </td>
                                            <td class="text-right">${{ number_format($venta['total_pagado'] ?? 0, 2) }}</td>
                                            <td class="text-right font-weight-bold {{ ($venta['ganancia_bruta'] ?? 0) >= 0 ? 'text-ganancia' : 'text-perdida' }}">
                                                ${{ number_format($venta['ganancia_bruta'] ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="mb-2">
                                        <h6 class="font-weight-bold mb-1">Trabajos realizados:</h6>
                                        <div class="pl-2">
                                            @foreach($venta['trabajos'] as $trabajo)
                                            <div class="mb-2 border-bottom pb-1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="font-weight-bold">{{ $trabajo['nombre'] }}</span>
                                                    <span>${{ number_format($trabajo['precio_trabajo'], 2) }}</span>
                                                </div>
                                                @if($trabajo['descripcion'])
                                                    <div class="text-muted small mt-1">{{ $trabajo['descripcion'] }}</div>
                                                @endif
                                                @if(count($trabajo['productos']) > 0)
                                                    <div class="mt-1">
                                                        <span class="small font-weight-bold">Productos utilizados:</span>
                                                        <table class="table table-sm" style="margin-top: 0.2rem; margin-bottom: 0.5rem;">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Producto</th>
                                                                    <th class="text-right">Cantidad</th>
                                                                    <th class="text-right">Precio</th>
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
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if(count($venta['costos']) > 0 || count($venta['gastos']) > 0)
                                    <div class="row">
                                        @if(count($venta['costos']) > 0)
                                        <div class="col-md-6">
                                            <div style="margin-bottom: 0.5rem;">
                                                <h6 class="font-weight-bold mb-1">Costos asociados</h6>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
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
                                                                @php
                                                                    $metodoPago = $costo['metodo_pago_id'] ?? null;
                                                                    $metodoPagoNombre = 'Método Desconocido';
                                                                    if ($metodoPagoId) {
                                                                        if (is_array($metodosPago)) {
                                                                            $metodoPagoNombre = $metodosPago[$metodoPagoId] ?? 'Método ' . $metodoPagoId;
                                                                        } elseif (is_object($metodosPago) && method_exists($metodosPago, 'firstWhere')) {
                                                                            $metodoPago = $metodosPago->firstWhere('id', $metodoPagoId);
                                                                            $metodoPagoNombre = $metodoPago ? $metodoPago->name : 'Método ' . $metodoPagoId;
                                                                        } else {
                                                                            $metodoPagoNombre = is_string($metodoPagoId) ? $metodoPagoId : 'Método ' . $metodoPagoId;
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{ $metodoPagoNombre }}
                                                            </td>
                                                            <td class="text-right text-danger">${{ number_format($costo['valor'], 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                        <tr class="font-weight-bold">
                                                            <td colspan="3">Total costos</td>
                                                            <td class="text-right">${{ number_format($venta['total_costos'], 2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if(count($venta['gastos']) > 0)
                                        <div class="col-md-6">
                                            <div style="margin-bottom: 0.5rem;">
                                                <h6 class="font-weight-bold mb-1">Gastos asociados</h6>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
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
                                                                @php
                                                                    $metodoPago = $gasto['metodo_pago_id'] ?? null;
                                                                    // Mostrar el contenido completo para depuración
                                                                    echo '<pre>' . print_r($metodoPago, true) . '</pre>';
                                                                @endphp
                                                            </td>
                                                            <td class="text-right text-warning">${{ number_format($gasto['valor'], 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                        <tr class="font-weight-bold">
                                                            <td colspan="3">Total gastos</td>
                                                            <td class="text-right">${{ number_format($venta['total_gastos'], 2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    @if(isset($venta['pagos']) && count($venta['pagos']) > 0)
                                    <div class="mt-1">
                                        <h6 class="font-weight-bold mb-1">Detalle de Pagos</h6>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Método Pago</th>
                                                    <th>Cobró</th>
                                                    <th class="text-right">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($venta['pagos'] as $pago)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($pago['fecha'] ?? now())->format('d/m/Y') }}</td>
                                                    <td>
                                                        @php
                                                            $metodoPago = $pago['metodo_pago'] ?? null;
                                                            $metodoPagoNombre = 'Método Desconocido';
                                                            
                                                            if ($metodoPago) {
                                                                if (is_string($metodoPago)) {
                                                                    // Si ya es un string, usarlo directamente
                                                                    $metodoPagoNombre = $metodoPago;
                                                                } else if (is_array($metodoPago) && isset($metodoPago['App\Models\TiposDePago'])) {
                                                                    // Si es un array con el modelo TiposDePago anidado
                                                                    $metodoPagoNombre = $metodoPago['App\Models\TiposDePago']['name'] ?? 'Método ' . ($metodoPago['App\Models\TiposDePago']['id'] ?? 'N/A');
                                                                } else if (is_array($metodoPago) && isset($metodoPago['name'])) {
                                                                    // Si es un array con name e id directamente
                                                                    $metodoPagoNombre = $metodoPago['name'];
                                                                } else if (is_numeric($metodoPago) && is_array($metodosPago) && isset($metodosPago[$metodoPago])) {
                                                                    // Si es un ID numérico y tenemos el array de métodos
                                                                    $metodoPagoNombre = $metodosPago[$metodoPago];
                                                                } else if (is_object($metodoPago) && isset($metodoPago->name)) {
                                                                    // Si es un objeto con propiedad name
                                                                    $metodoPagoNombre = $metodoPago->name;
                                                                } else if (is_object($metodoPago) && isset($metodoPago->{"App\Models\TiposDePago"})) {
                                                                    // Si es un objeto con TiposDePago anidado
                                                                    $metodoPagoNombre = $metodoPago->{"App\Models\TiposDePago"}->name ?? 'Método ' . ($metodoPago->{"App\Models\TiposDePago"}->id ?? 'N/A');
                                                                } else {
                                                                    // Cualquier otro caso, intentar convertirlo a string
                                                                    $metodoPagoNombre = (string)$metodoPago;
                                                                }
                                                            }
                                                        @endphp
                                                        {{ $metodoPagoNombre }}
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
                                                <tr class="font-weight-bold">
                                                    <td colspan="3">Total pagado</td>
                                                    <td class="text-right">${{ number_format($venta['total_pagado'] ?? 0, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de Costos y Gastos</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">Técnico</th>
                        <th colspan="3" class="text-center bg-costos">Costos</th>
                        <th colspan="3" class="text-center bg-gastos">Gastos</th>
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
                                <td rowspan="{{ $maxRows }}" class="font-weight-bold">
                                    {{ $item['tecnico'] }}
                                </td>
                            @endif
                            
                            @if(isset($item['costos'][$i]))
                                <td class="bg-costos">{{ $item['costos'][$i]['descripcion'] }}</td>
                                <td class="bg-costos">
                                    @php
                                        $costo = $item['costos'][$i];
                                        $metodoPago = $costo['metodo_pago'] ?? 'Desconocido';
                                        
                                        // Si hay un array de métodos de pago, intentar obtener el nombre del primero
                                        if (isset($costo['metodos_pago']) && is_array($costo['metodos_pago']) && count($costo['metodos_pago']) > 0) {
                                            $primerMetodo = $costo['metodos_pago'][0];
                                            if (is_object($primerMetodo) && isset($primerMetodo->name)) {
                                                // Para objetos de modelo Eloquent
                                                $metodoPago = $primerMetodo->name;
                                            } elseif (is_array($primerMetodo) && isset($primerMetodo['name'])) {
                                                // Para arrays asociativos
                                                $metodoPago = $primerMetodo['name'];
                                            } elseif (is_string($primerMetodo)) {
                                                // Para strings directos (como en nómina)
                                                $metodoPago = $primerMetodo;
                                            }
                                        }
                                        
                                        echo htmlspecialchars($metodoPago);
                                    @endphp
                                </td>
                                <td class="text-right bg-costos">${{ number_format($item['costos'][$i]['total'], 2) }}</td>
                            @else
                                <td colspan="3" class="bg-costos"></td>
                            @endif

                            @if(isset($item['gastos'][$i]))
                                <td class="bg-gastos">{{ $item['gastos'][$i]['descripcion'] }}</td>
                                <td class="bg-gastos">
                                    @php
                                        $gasto = $item['gastos'][$i];
                                        $metodoPago = $gasto['metodo_pago'] ?? 'Desconocido';
                                        
                                        // Si hay un array de métodos de pago, intentar obtener el nombre del primero
                                        if (isset($gasto['metodos_pago']) && is_array($gasto['metodos_pago']) && count($gasto['metodos_pago']) > 0) {
                                            $primerMetodo = $gasto['metodos_pago'][0];
                                            if (is_object($primerMetodo) && isset($primerMetodo->name)) {
                                                // Para objetos de modelo Eloquent
                                                $metodoPago = $primerMetodo->name;
                                            } elseif (is_array($primerMetodo) && isset($primerMetodo['name'])) {
                                                // Para arrays asociativos
                                                $metodoPago = $primerMetodo['name'];
                                            } elseif (is_string($primerMetodo)) {
                                                // Para strings directos (como en nómina)
                                                $metodoPago = $primerMetodo;
                                            }
                                        }
                                        
                                        echo htmlspecialchars($metodoPago);
                                    @endphp
                                </td>
                                <td class="text-right bg-gastos">${{ number_format($item['gastos'][$i]['total'], 2) }}</td>
                            @else
                                <td colspan="3" class="bg-gastos"></td>
                            @endif
                        </tr>
                        @endfor
                    @endforeach
                    
                    <tr class="font-weight-bold">
                        <td colspan="1">TOTALES</td>
                        <td colspan="2" class="bg-costos"></td>
                        <td class="text-right bg-costos">${{ number_format($totales['totalCostos'] ?? 0, 2) }}</td>
                        <td colspan="2" class="bg-gastos"></td>
                        <td class="text-right bg-gastos">${{ number_format($totales['totalGastos'] ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Llaves Utilizadas por Técnico</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">Técnico</th>
                        <th rowspan="2" class="align-middle">Llave</th>
                        @foreach($almacenesDisponibles as $almacen)
                            <th colspan="2" class="text-center bg-llaves">{{ $almacen['nombre'] }}</th>
                        @endforeach
                        <th rowspan="2" class="text-center">Total</th>
                        <th rowspan="2" class="text-center">Valor</th>
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
                            <td>{{ $llave['nombre'] }} (ID: {{ $llave['id_producto'] }})</td>
                            @foreach($almacenesDisponibles as $almacen)
                                <td class="text-right bg-llaves">{{ $llave['almacenes'][$almacen['id_almacen']]['cantidad'] ?? 0 }}</td>
                                <td class="text-right bg-llaves">${{ number_format($llave['almacenes'][$almacen['id_almacen']]['total'] ?? 0, 2) }}</td>
                            @endforeach
                            <td class="text-right font-weight-bold">{{ $llave['total_cantidad'] }}</td>
                            <td class="text-right font-weight-bold">${{ number_format($llave['total_valor'], 2) }}</td>
                            
                            @php
                                $totalGeneralLlaves += $llave['total_cantidad'];
                                $totalGeneralValorLlaves += $llave['total_valor'];
                            @endphp
                        </tr>
                        @endforeach
                    @endforeach
                    
                    <tr class="font-weight-bold">
                        <td colspan="2">TOTALES</td>
                        @foreach($almacenesDisponibles as $almacen)
                            @php
                                $totalAlmacen = 0;
                                $totalValorAlmacen = 0;
                                foreach($llavesPorTecnico as $tecnico) {
                                    foreach($tecnico['llaves'] as $llave) {
                                        $totalAlmacen += $llave['almacenes'][$almacen['id_almacen']]['cantidad'] ?? 0;
                                        $totalValorAlmacen += $llave['almacenes'][$almacen['id_almacen']]['total'] ?? 0;
                                    }
                                }
                            @endphp
                            <td class="text-right bg-llaves">{{ $totalAlmacen }}</td>
                            <td class="text-right bg-llaves">${{ number_format($totalValorAlmacen, 2) }}</td>
                        @endforeach
                        <td class="text-right">{{ $totalGeneralLlaves }}</td>
                        <td class="text-right">${{ number_format($totalGeneralValorLlaves, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Descargas Manuales</h3>
        </div>
        <div class="card-body">
            <div class="info-box bg-costos">
                <div class="info-box-content">
                    <span class="info-box-text">Total Descargas</span>
                    <span class="info-box-number">{{ $totalDescargas }}</span>
                </div>
            </div>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Producto</th>
                        <th>Motivo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cargasDescargas as $movimiento)
                        <tr>
                            <td>{{ $movimiento['usuario'] }}</td>
                            <td>{{ $movimiento['producto'] }} (ID: {{ $movimiento['id_producto'] }})</td>
                            <td>{{ $movimiento['motivo'] }}</td>
                            <td>{{ $movimiento['fecha'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ventas por Lugar de Venta</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Lugar de Venta</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Monto</th>
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
                        <td>{{ $lugar['nombre'] }}</td>
                        <td class="text-right">{{ $lugar['cantidad'] }}</td>
                        <td class="text-right">${{ number_format($lugar['monto'], 2) }}</td>
                    </tr>
                    @endforeach

                    <tr class="font-weight-bold">
                        <td>TOTAL</td>
                        <td class="text-right">{{ $totalVentas }}</td>
                        <td class="text-right">${{ number_format($totalMonto, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ventas al Contado por Trabajo</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Trabajo</th>
                                <th class="text-right">Monto</th>
                                <th>Métodos de Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $ventasPorTrabajoContado = $ventasPorTrabajo['contado'] ?? [];
                                $totalContado = $ventasPorTrabajo['total_contado'] ?? 0;
                            @endphp
                            @foreach($ventasPorTrabajoContado as $trabajo => $data)
                            <tr>
                                <td>{{ $trabajos[$trabajo] ?? $trabajo ?? 'Sin especificar' }}</td>
                                <td class="text-right">${{ number_format($data['total'], 2) }}</td>
                                <td>
                                    @foreach($data['metodos'] as $metodo => $detalle)
                                    <div class="mb-1">
                                        <span class="badge bg-light text-dark">
                                            @if(is_array($detalle) && isset($detalle['nombre']))
                                                {{ $detalle['nombre'] }}: ${{ number_format($detalle['total'] ?? 0, 2) }}
                                            @else
                                                {{ $metodosPago[$metodo] ?? 'Método ' . $metodo }}: ${{ number_format(is_array($detalle) ? ($detalle['total'] ?? 0) : $detalle, 2) }}
                                            @endif
                                        </span>
                                    </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>TOTAL</td>
                                <td class="text-right">${{ number_format($totalContado, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ventas a Crédito por Trabajo</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Trabajo</th>
                                <th class="text-right">Monto</th>
                                <th>Métodos de Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $ventasPorTrabajoCredito = $ventasPorTrabajo['credito'] ?? [];
                                $totalCredito = $ventasPorTrabajo['total_credito'] ?? 0;
                            @endphp
                            @foreach($ventasPorTrabajoCredito as $trabajo => $data)
                            <tr>
                                <td>{{ $trabajos[$trabajo] ?? $trabajo ?? 'Sin especificar' }}</td>
                                <td class="text-right">${{ number_format($data['total'], 2) }}</td>
                                <td>
                                    @foreach($data['metodos'] as $metodo => $detalle)
                                    <div class="mb-1">
                                        <span class="badge bg-light text-dark">
                                            @if(is_array($detalle) && isset($detalle['nombre']))
                                                {{ $detalle['nombre'] }}: ${{ number_format($detalle['total'] ?? 0, 2) }}
                                            @else
                                                {{ $metodosPago[$metodo] ?? 'Método ' . $metodo }}: ${{ number_format(is_array($detalle) ? ($detalle['total'] ?? 0) : $detalle, 2) }}
                                            @endif
                                        </span>
                                    </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>TOTAL</td>
                                <td class="text-right">${{ number_format($totalCredito, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resumen de Trabajos Realizados</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Tipo de Trabajo</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalTrabajos = 0; @endphp
                    
                    @foreach($resumenTrabajos as $trabajo => $data)
                    @php $totalTrabajos += $data['cantidad']; @endphp
                    <tr>
                        <td>{{ explode('-', $data['nombre'])[0] }}</td>
                        <td class="text-right">{{ $data['cantidad'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td>TOTAL</td>
                        <td class="text-right">{{ $totalTrabajos }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ventas por Cliente</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-right">Contado</th>
                        <th class="text-right">Crédito</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalContadoClientes = 0;
                        $totalCreditoClientes = 0;
                    @endphp
                    
                    @foreach($ventasPorCliente as $cliente)
                    @php
                        $totalContadoClientes += $cliente['ventas_contado'];
                        $totalCreditoClientes += $cliente['ventas_credito'];
                    @endphp
                    <tr>
                        <td>{{ $cliente['cliente'] }}</td>
                        <td class="text-right">${{ number_format($cliente['ventas_contado'], 2) }}</td>
                        <td class="text-right">${{ number_format($cliente['ventas_credito'], 2) }}</td>
                        <td class="text-right font-weight-bold">${{ number_format($cliente['total_ventas'], 2) }}</td>
                    </tr>
                    @endforeach

                    <tr class="font-weight-bold">
                        <td>TOTAL</td>
                        <td class="text-right">${{ number_format($totalContadoClientes, 2) }}</td>
                        <td class="text-right">${{ number_format($totalCreditoClientes, 2) }}</td>
                        <td class="text-right">${{ number_format($totalContadoClientes + $totalCreditoClientes, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>