<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden - {{ $orden->id_orden }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            position: relative;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            width: 45%;
            border: 1px solid #000;
            padding: 10px;
        }
        .info-box th, .info-box td {
            border: none;
            padding: 8px;
            text-align: left;
        }
        .info-box th {
            background-color: #f2f2f2;
        }
        .date-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals th, .totals td {
            padding: 8px;
            text-align: right;
        }
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            box-sizing: border-box;
        }
        .footer table {
            width: 100%;
            border-collapse: collapse;
            height: 100px;
            padding: 20px;
        }
        .footer td {
            width: 50%;
            border: 1px solid #000;
            vertical-align: top;
            font-size: 12px;
        }
        .footer p {
            margin-bottom: 15px;
        }
        hr {
            border: 1px solid #000;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="text-align: left;">Orden Nº {{ $orden->id_orden }}</h1>
    </div>

    <div class="info-container">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <th style="width: 100%; font-size: 12px; text-align: left;">Datos de la empresa</th>
                        </tr>
                        <tr>
                            <td><hr style="border: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre:</strong> Autokeys Locksmith</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong> 1989 Covington Pike, Memphis TN 38128, United States</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono:</strong>  +1 (901) 513-9541</td>
                        </tr>
                        <tr>
                            <td><strong>E-mail:</strong> usa.autokeyslocksmith@gmail.com</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <th style="width: 100%; font-size: 12px; text-align: left;">Datos</th>
                        </tr>
                        <tr>
                            <td><hr style="border: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre del Cliente:</strong> {{ $orden->cliente->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong> {{ $orden->direccion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Técnico:</strong> {{ $orden->empleado->nombre ?? $orden->id_tecnico ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong> 
                                <span class="badge 
                                    @if($orden->estado == 'completado') badge-success 
                                    @elseif($orden->estado == 'pendiente') badge-warning 
                                    @elseif($orden->estado == 'cancelado') badge-danger 
                                    @else badge-secondary 
                                    @endif">
                                    {{ ucfirst($orden->estado) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="date-line">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;"><strong>Fecha orden:</strong> {{ \Carbon\Carbon::parse($orden->f_orden)->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    @if (is_array($orden->items) && count($orden->items) > 0)
    @php $totalOrden = 0; @endphp

        <table class="items-table" border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th>DESCRIPCIÓN</th>
                    <th>PRECIO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden->items as $itemGroup)
                    @if(isset($itemGroup['descripcion']))
                        @php
                            $precio = $itemGroup['cantidad'] ?? 0;
                            $totalOrden += $precio;
                        @endphp
                        <tr>
                            <td>{{ $itemGroup['descripcion'] }}</td>
                            <td style="text-align: right;">${{ number_format($precio, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
                
                <tr style="border-top: 2px solid #000;">
                    <td style="text-align: right; font-weight: bold;">TOTAL:</td>
                    <td style="text-align: right; font-weight: bold;">${{ number_format($totalOrden, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p>No hay items disponibles para esta orden.</p>
    @endif


    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top; font-size: 12px;">
                    <p style="margin-bottom: 15px;">Firma del técnico</p>
                    <hr style="border: 1px solid #000; margin-top: 50px;">
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top; font-size: 12px;">
                    <p style="margin-bottom: 15px;">Firma del cliente</p>
                    <hr style="border: 1px solid #000;margin-top: 50px;">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>