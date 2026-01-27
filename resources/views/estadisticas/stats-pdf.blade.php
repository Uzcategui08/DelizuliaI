
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 22px; margin-bottom: 5px; }
        .header p { font-size: 14px; margin: 0; }
        .summary-table { width: 100%; margin: 20px 0 30px 0; border-collapse: collapse; }
        .summary-table td { font-size: 13px; padding: 8px 10px; border: none; }
        .summary-label { font-weight: bold; background: #f5f5f5; }
        .summary-value { font-size: 15px; color: #0d6efd; font-weight: bold; }
        .section { margin-bottom: 10px; }
        .section-title { background: #222; color: #fff; padding: 6px; font-weight: bold; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { 
            padding: 4px; 
            border: 1px solid #bbb; /* Add border to all sides */
            font-size: 10px;
        }
        th { background: #f2f2f2; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { font-weight: bold; }
        .signature { margin-top: 40px; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Período:</strong> {{ $mes }}</p>
        <p><strong>Generado el:</strong> {{ $date }}</p>
    </div>

    <!-- Resumen Compacto Mejorado -->
    <table class="summary-table">
        <tr>
            <td class="summary-label">Facturación</td>
            <td class="summary-value">${{ number_format($stats['ventas']['facturacion'], 2) }}</td>
            <td class="summary-label">Cobrado</td>
            <td class="summary-value">${{ number_format($stats['ventas']['cobrado_mes'], 2) }}</td>
            <td class="summary-label">Transacciones</td>
            <td class="summary-value">{{ $stats['ventas']['num_transacciones'] }}</td>
        </tr>
        <tr>
            <td class="summary-label">Ticket Promedio</td>
            <td class="summary-value">${{ number_format($stats['ventas']['ticket_promedio'], 2) }}</td>
            <td class="summary-label">Utilidad Bruta</td>
            <td class="summary-value">${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</td>
            <td class="summary-label">Utilidad Neta</td>
            <td class="summary-value">${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</td>
        </tr>
        <tr>
            <td class="summary-label">Total Gastos</td>
            <td class="summary-value">${{ number_format($stats['gastos']['total_gastos'], 2) }}</td>
            <td class="summary-label">Total Costos</td>
            <td class="summary-value">${{ number_format($stats['costos']['total_costos_mes'], 2) }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- Tabla Detallada de Ventas (registroV) -->
    <div class="section">
        <div class="section-title">DETALLE DE VENTAS ({{ $registros->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Valor</th>
                    <th>Estatus</th>
                    <th>Tipo Venta</th>
                    <th>Titular</th>
                    <th>Lugar Venta</th>
                    <th>Porcentaje C</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año</th>
                    <th>Técnico</th>
                    <th>Trabajos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $registro)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($registro->fecha_h)->format('d/m/Y') }}</td>
                    <td>{{ $registro->cliente }}</td>
                    <td>{{ $registro->telefono }}</td>
                    <td class="text-right">${{ number_format($registro->valor_v, 2) }}</td>
                    <td>{{ $registro->estatus }}</td>
                    <td>{{ $registro->tipo_venta }}</td>
                    <td>{{ $registro->titular_c }}</td>
                    <td>{{ $registro->lugarventa }}</td>
                    <td>{{ $registro->porcentaje_c }}</td>
                    <td>{{ $registro->marca }}</td>
                    <td>{{ $registro->modelo }}</td>
                    <td>{{ $registro->año }}</td>
                    <td>{{ $registro->empleado->nombre ?? 'N/A' }}</td>
                    <td>
                        @php
                            $items = json_decode($registro->items, true);
                            $trabajos = array_column($items ?? [], 'trabajo_nombre');
                        @endphp
                        {{ implode(', ', $trabajos) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Gastos y Costos Detallados -->
    <div class="section">
        <div class="section-title">GASTOS DEL MES ({{ $gastos->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gastos as $gasto)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($gasto->f_gastos)->format('d/m/Y') }}</td>
                    <td>{{ $gasto->descripcion ?? 'N/A' }}</td>
                    <td class="text-right">${{ number_format($gasto->valor, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="section">
        <div class="section-title">COSTOS DEL MES ({{ $costos->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($costos as $costo)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($costo->f_costos)->format('d/m/Y') }}</td>
                    <td>{{ $costo->descripcion ?? 'N/A' }}</td>
                    <td class="text-right">${{ number_format($costo->valor, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Firma -->
    <div class="signature">
        <p>Generado por: {{ auth()->user()->name }}</p>
        <div class="signature-line"></div>
        <p>Firma Autorizada</p>
    </div>
</body>
</html>