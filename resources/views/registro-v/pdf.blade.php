
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo #{{ $registroV->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            color: #333;
            max-width: 210mm;
            max-height: 297mm;
            overflow: hidden;
        }
        .recibo-container {
            width: 135mm;
            max-width: 135mm;
            margin: 2mm auto;
            padding: 2mm 3mm;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px 0 #2222;
            border: 1px solid #e3e8f0;
            min-height: 100mm;
            max-height: 260mm;
            overflow: visible;
        }
        .header {
            text-align: center;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1px dashed #ccc;
        }
        .header h1 {
            font-size: 12px;
            margin: 2mm 0;
            color: #000;
        }
        .header p {
            margin: 0.5mm 0;
            font-size: 9px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        .header-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 2mm;
        }
        .brand-title {
            font-size: 18px;
            color: #222;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 1mm;
        }
        .brand-address {
            font-size: 10px;
            color: #6c7a89;
            margin-bottom: 2mm;
        }
        .divider {
            border-top: 1.5px dashed #e3e8f0;
            margin: 3mm 0;
        }
        }
        .info-table td {
            padding: 2mm 1mm;
            vertical-align: top;
            margin-bottom: 2mm;
        }
        .status {
            padding: 1mm 0.5mm;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }
        .status.paid {
            background: #e6f9e6;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }
        .status.partial {
            background: #fffbe6;
            color: #bfa100;
            border: 1px solid #bfa100;
        }
        .status.pending {
            background: #ffe6e6;
            color: #c62828;
            border: 1px solid #c62828;
        }
        .status.other {
            background: #e3e8f0;
            color: #333;
            border: 1px solid #aaa;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            width: 70%;
            word-break: break-word;
        }
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5mm 0;
            font-size: 9px;
        }
        .productos-table th {
            text-align: left;
            padding: 2mm 1mm;
            border-bottom: 1px solid #000;
        }
        .productos-table td {
            padding: 1mm;
            border-bottom: 1px dashed #eee;
        }
        .text-right {
            text-align: right;
        }
        .totales-section {
            margin-top: 5mm;
            border-top: 1px dashed #ccc;
            padding-top: 1mm;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .total-grande {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            font-size: 11px;
            margin-top: 1mm;}
        .footer {
            margin-top: 5mm;
            text-align: center;
            font-size: 9px;
            font-size: 8px;
            border-top: 1px dashed #ccc;
            padding-top: 1.5mm;}
        .footer {
            margin-top: 5mm;
            text-align: center;
            font-size: 9px;
            font-size: 8px;
            border-top: 2px solid #e3e8f0;
            padding-top: 1.5mm;
            background: linear-gradient(90deg, #e3e8f0 0%, #f7f9fc 100%);
            border-radius: 0 0 8px 8px;
            color: #6c7a89;}
        .pagos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
            margin-top: 2mm;
            font-size: 9px;}
        .pagos-table th {
            text-align: left;
            padding: 2mm 1mm;
            padding: 1mm;
            margin-top: 1.5mm;
            border-radius: 2mm;
            font-size: 9px;
            border-bottom: 1px dashed #eee;
        }
        .resumen-pagos {
            text-align: right;
            background-color: #f5f8fd;
            margin-bottom: 0.5mm;
            margin-top: 3mm;
            border-radius: 2mm;
            font-size: 11px;
        }
            margin-bottom: 1mm;
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
            background: #f7f9fc; border: 1.5px solid #e3e8f0; border-radius: 8px; padding: 6px 10px; margin-bottom: 4mm; margin-top: 1mm;
        .resumen-total {
            font-weight: bold;
            color: #2c3e50;
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
        .no-pagos {
            text-align: center;
            font-style: italic;
            padding: 3mm 0;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="recibo-container">
        <div class="header">
            <img src="{{ public_path('/images/Logo1.png') }}" alt="Logo" class="header-logo" style="width:200px;height:200px;">
            <span style="display:block; font-size:10px; color:#bbb; margin-top:2px;"><b>Autokeys Locksmith<b>, 1989 covington pike, Memphis TN 38128, United States</span>
            <div class="divider"></div>
            <h2>RECIBO DE VENTA</h2>
            <p>N° {{ $registroV->id }}</p>
            <p>Fecha: {{ \Carbon\Carbon::parse($registroV->fecha_h)->format('m/d/Y') }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td>
                    <table width="100%">
                        @php
                            $clienteInfo = \App\Models\Cliente::where('id_cliente', $registroV->id_cliente)->first();
                        @endphp
                        <tr>
                            <td class="info-label">Cliente:</td>
                            <td class="info-value">{{ $clienteInfo ? $clienteInfo->nombre : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Teléfono:</td>
                            <td class="info-value">{{ $clienteInfo ? $clienteInfo->telefono : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Dirección:</td>
                            <td class="info-value">{{ $clienteInfo ? $clienteInfo->direccion : 'N/A' }}</td>
                        </tr>
                        @if($registroV->marca || $registroV->modelo || $registroV->año)
                        <tr>
                            <td class="info-label">Vehículo:</td>
                            <td class="info-value">
                                {{ $registroV->marca ?: 'N/A' }} {{ $registroV->modelo ?: '' }}@if($registroV->año) ({{ $registroV->año }})@endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>

                <td>
                    <table width="100%">
                        <tr>
                            <td class="info-label">Técnico:</td>
                            <td class="info-value">{{ $registroV->empleado->nombre }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Estatus:</td>
                            <td class="info-value">
                                <span class="status 
                                    @if($registroV->estatus == 'pagado') paid
                                    @elseif($registroV->estatus == 'parcialementep') partial
                                    @elseif($registroV->estatus == 'pendiente') pending
                                    @else other
                                    @endif">
                                    @if($registroV->estatus == 'pagado') Pagado
                                    @elseif($registroV->estatus == 'parcialementep') Parcial
                                    @elseif($registroV->estatus == 'pendiente') Pendiente
                                    @else Otro
                                    @endif
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
<div class="totales-section">
<h3>Trabajo</h3>
        @foreach($items as $itemGroup)
            @php
                $trabajoNombre = $itemGroup['trabajo'] ?? $itemGroup['job'] ?? $itemGroup['trabajo_nombre'] ?? '';
                $precioTrabajo = isset($itemGroup['precio_trabajo']) && $itemGroup['precio_trabajo'] ? number_format($itemGroup['precio_trabajo'], 2) : null;
            @endphp
            @if($trabajoNombre)
                <div class="titulo-job" style="margin-top: 10px; margin-bottom: 3px;">
                    <strong>{{ $trabajoNombre }}@if($precioTrabajo) - ${{ $precioTrabajo }}@endif</strong>
                </div>

                @if(isset($itemGroup['descripcion']) && $itemGroup['descripcion'])
                <div class="trabajo-descripcion">
                    <strong>Descripción:</strong> {{ $itemGroup['descripcion'] }}
                </div>
                <hr style="width: 70px; margin-left: 0; border: none; border-top: 1px solid #e3e8f0; height: 1px; background: #e3e8f0;">
                @endif
            @endif
        @endforeach

        
            <div class="total-row total-grande">
                @if(isset($registroV->monto_ce) && $registroV->monto_ce > 0)
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Descuento:</span>
                        <span style="color:#c62828;">- ${{ number_format($registroV->monto_ce, 2) }}</span>
                    </div>
                @endif
                <span>TOTAL:</span>
                <span>${{ number_format($registroV->valor_v, 2) }}</span>
            </div>

        </div>
<br>

        <div class="payment-history-box" style="background: #f7f9fc; border: 1.5px solid #e3e8f0; border-radius: 10px; padding: 18px 22px; margin-bottom: 3mm; margin-top: 2mm;">
            <div class="section-title" style="margin-bottom: 14px; font-size: 14px; color: #2c3e50;">HISTORIAL DE PAGOS</div>
            @if(count($registroV->pagos) > 0)
                <table class="pagos-table" style="margin-bottom: 12px;">
                    <thead>
                        <tr>
                            <th width="25%" style="padding-bottom: 6px;">Fecha</th>
                            <th width="35%" style="padding-bottom: 6px;">Método</th>
                            <th width="40%" style="text-align: right; padding-bottom: 6px;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registroV->pagos as $pago)
                            <tr style="height: 28px;">
                                <td style="padding-bottom: 4px;">{{ \Carbon\Carbon::parse($pago['fecha'])->format('m/d/Y') }}</td>
                                <td style="padding-bottom: 4px;">
                                    @php
                                        $metodoPago = collect($tiposDePago)->firstWhere('id', $pago['metodo_pago'] ?? null);
                                    @endphp
                                    {{ $metodoPago->name ?? ($pago['metodo_pago'] ?? 'N/A') }}
                                </td>
                                <td style="text-align: right; padding-bottom: 4px;">${{ number_format($pago['monto'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <div class="resumen-pagos" style="margin-top: 10px;">
                    <div class="resumen-row" style="margin-bottom: 8px;">
                        <span>Total Pagado:</span>
                        <span>${{ number_format($total_pagado, 2) }}</span>
                    </div>
                    <div class="resumen-row resumen-total" style="margin-bottom: 0;">
                        <span>Saldo Pendiente:</span>
                        <span>${{ number_format($saldo_pendiente, 2) }}</span>
                    </div>
                </div>
            @else
                <div class="no-pagos">No se han registrado pagos</div>
            @endif
        </div>

        <div class="footer">
            <p>¡Gracias por su preferencia!</p>
            <div class="divider"></div>
            <p>Este documento es válido como comprobante de pago</p>
        </div>
    </div>
</body>
</html>