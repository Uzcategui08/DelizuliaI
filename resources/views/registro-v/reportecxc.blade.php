<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Cuentas por Cobrar</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
            font-size: 12px;
            color: #333;
        }
        .recibo-container {
            width: 140mm;
            max-width: 140mm;
            margin: 0 auto;
            padding: 5mm;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
            padding-bottom: 3mm;
            border-bottom: 1px dashed #ccc;
        }
        .header-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            display: block;
            margin: 0 auto 8px auto;
        }
        .header h1 {
            font-size: 18px;
            margin: 2mm 0;
            color: #000;
        }
        .header p {
            margin: 1mm 0;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        .info-table td {
            padding: 2mm 1mm;
            vertical-align: top;
            border: none;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            width: 70%;
            word-break: break-word;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0;
            font-size: 10px;
        }
        th {
            text-align: left;
            padding: 2mm 1mm;
            border-bottom: 1px solid #000;
        }
        td {
            padding: 1mm;
            border-bottom: 1px dashed #eee;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .total-row td {
            border-top: 1px solid #ddd;
            border-bottom: none;
            padding: 2mm 1mm;
        }
        .footer {
            margin-top: 5mm;
            text-align: center;
            font-size: 9px;
            border-top: 1px dashed #ccc;
            padding-top: 3mm;
        }
        .cliente-header {
            background-color: #f5f5f5;
            padding: 3mm;
            margin: 5mm 0 2mm 0;
            border-left: 4px solid #2c3e50;
            font-weight: bold;
            font-size: 12px;
        }
        .divider {
            border-top: 1px dashed #ccc;
            margin: 3mm 0;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2mm;
            color: #2c3e50;
        }
        .periodo-info {
            text-align: center;
            margin-bottom: 5mm;
            font-size: 11px;
        }
        .resumen-section {
            margin-top: 10mm;
            padding: 3mm;
            background-color: #f5f5f5;
            border-radius: 2mm;
            font-size: 10px;
        }
        .resumen-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .resumen-total {
            font-weight: bold;
            margin-top: 2mm;
            padding-top: 2mm;
            border-top: 1px dashed #ccc;
        }
        .trabajo-info {
            margin-bottom: 2mm;
            padding-left: 2mm;
            border-left: 2px solid #ddd;
        }
        .trabajo-nombre {
            font-weight: bold;
        }
        .trabajo-descripcion {
            font-size: 10px;
            color: #555;
            margin-top: 1mm;
        }
        .productos-table {
            width: 100%;
            margin-top: 2mm;
            font-size: 9px;
        }
        .productos-table td {
            padding: 1mm 0;
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="recibo-container">
        <div class="header">
            <img src="{{ public_path('/images/Logo1.png') }}" alt="Logo" class="header-logo">
            <h1>RECIBO</h1>
            <p>Generado el: {{ date('m/d/Y') }}</p>
        </div>
        <div class="periodo-info">
            <p>Período: {{ \Carbon\Carbon::parse($fechaDesde)->format('m/d/Y') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('m/d/Y') }}</p>
        </div>

        @foreach($data as $item)
        @php
            $cliente = \App\Models\Cliente::find($item->id_cliente);
        @endphp
        <div class="cliente-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>{{ $cliente->nombre }}</span> -
            <span style="font-weight: normal; font-size: 11px;">
                Tel: {{ $cliente->telefono ?? 'n/a' }} &nbsp; | &nbsp; 
                Dir: {{ $cliente ? $cliente->direccion : 'n/a' }}
            </span>
        </div>
        
        @foreach($item->ventas as $venta)
        <div style="margin-bottom: 3mm;">
            <table>
                <thead>
                    <tr>
                        <th width="12%">Factura #{{ $venta->id }}</th>
                        <th width="15%">Técnico</th>
                        <th width="12%">Fecha</th>
                        <th width="16%" class="text-right">Monto Total</th>
                        <th width="13%" class="text-right">Descuento</th>
                        <th width="16%" class="text-right">Monto Neto</th>
                        <th width="16%" class="text-right">Pagado</th>
                        <th width="16%" class="text-right">Saldo</th>
                        <th width="10%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>
                            {{
                                optional(\App\Models\Empleado::find($venta->id_empleado))->nombre
                                ?? 'n/a'
                            }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($venta->fecha_h)->format('m/d/Y') }}</td>
                        <td class="text-right">
                            ${{ number_format($venta->monto_total ?? ($venta->valor_v + ($venta->descuento ?? 0)), 2) }}
                        </td>
                        <td class="text-right">
                            @php
                                $descuento = $venta->descuento ?? 0;
                            @endphp
                            @if($descuento > 0)
                                -${{ number_format($descuento, 2) }}
                            @else
                                $0.00
                            @endif
                        </td>
                        <td class="text-right">${{ number_format($venta->valor_v, 2) }}</td>
                        <td class="text-right">${{ number_format($venta->total_pagado, 2) }}</td>
                        <td class="text-right">${{ number_format($venta->valor_v - $venta->total_pagado, 2) }}</td>
                        <td>
                            @if($venta->valor_v == $venta->total_pagado)
                                Pagado
                            @elseif($venta->total_pagado > 0)
                                Parcial
                            @else
                                Pendiente
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Detalle de trabajos -->
            @foreach($venta->items as $itemGroup)
            <div class="trabajo-info">
                <div class="trabajo-nombre">{{ $itemGroup->trabajo }} - ${{ $itemGroup->precio_trabajo }}</div>
                @if($itemGroup->descripcion)
                <div class="trabajo-descripcion">{{ $itemGroup->descripcion }}</div>
                @endif
            </div>
            @endforeach
        </div>
            {{-- Detalle del vehículo: mostrar siempre, solo con los campos de la migración/modelo --}}
            <div style="margin: 2mm 0 2mm 0; padding: 2mm; background: #f4f4f4; border-left: 3px solid #c2c2c2; border-radius: 2mm; font-size: 10px;">
                <h3>Detalles del vehículo:</h3>
                <span><strong>Marca:</strong> {{ $venta->marca ?? '-' }} - </span><span><strong>Modelo:</strong> {{ $venta->modelo ?? '-' }}</span> - <span><strong>Año:</strong> {{ $venta->año ?? '-' }}</span><br>
            </div>
        @endforeach

        <table>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3">TOTAL CLIENTE</td>
                    <td class="text-right">${{ number_format($item->total_bruto ?? ($item->total_ventas_monto + ($item->total_descuento ?? 0)), 2) }}</td>
                    <td class="text-right">- ${{ number_format($item->total_descuento ?? 0, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total_ventas_monto, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total_pagado, 2) }}</td>
                    <td class="text-right">${{ number_format($item->saldo_pendiente, 2) }}</td>
                    <td>
                        @if($item->total_ventas_monto > 0)
                            {{ number_format(($item->total_pagado / $item->total_ventas_monto) * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
        @endforeach

        <div class="resumen-section">
            <div class="section-title">RESUMEN GENERAL</div>
            <div class="resumen-row">
                <span>Total Bruto:</span>
                <span>${{ number_format($data->sum('total_bruto'), 2) }}</span>
            </div>
            <div class="resumen-row">
                <span>Total Descuentos:</span>
                <span>- ${{ number_format($data->sum('total_descuento'), 2) }}</span>
            </div>
            <div class="resumen-row">
                <span>Total Ventas Neto:</span>
                <span>${{ number_format($data->sum('total_ventas_monto'), 2) }}</span>
            </div>
            <div class="resumen-row">
                <span>Total Pagado:</span>
                <span>${{ number_format($data->sum('total_pagado'), 2) }}</span>
            </div>
            <div class="resumen-row resumen-total">
                <span>Saldo Pendiente:</span>
                <span>${{ number_format($totalSaldo, 2) }}</span>
            </div>
            <div class="resumen-row">
                <span>Porcentaje Pagado:</span>
                <span>
                    @if($data->sum('total_ventas_monto') > 0)
                        {{ number_format(($data->sum('total_pagado') / $data->sum('total_ventas_monto')) * 100, 2) }}%
                    @else
                        0%
                    @endif
                </span>
            </div>
        </div>

    </div>
</body>
</html>