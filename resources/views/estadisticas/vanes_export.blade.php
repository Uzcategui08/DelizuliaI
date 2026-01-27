{{-- No extender adminlte para exportaciones PDF/Excel --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas Vans</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8fafc;
            color: #222;
        }
        h2 {
            color: #007bff;
            margin-bottom: 10px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: #fff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f2f6fc;
        }
        tr:hover {
            background: #e9ecef;
        }
        .resumen {
            margin-bottom: 20px;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <h2>Estadísticas de Vans y Técnicos</h2>
    <div class="resumen">Desde: <b>{{ $startDate }}</b> - Hasta: <b>{{ $endDate }}</b></div>
    <table>
        <thead>
            <tr>
                <th colspan="2">Van</th>
                <th>Ventas</th>
                <th>Costos</th>
                <th>Gastos</th>
                <th>Items</th>
                <th>Llaves</th>
                <th>Utilidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">{{ $vanGrande }}</td>
                <td>${{ number_format($totales['ventasGrande'], 2) }}</td>
                <td>${{ number_format($totales['costosGrande'], 2) }}</td>
                <td>${{ number_format($totales['gastosGrande'], 2) }}</td>
                <td>{{ $totales['itemsGrande'] }}</td>
                <td>{{ $totales['totalLlaves'] }}</td>
                <td>${{ number_format($totales['utilidadGrande'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ $vanPequena }}</td>
                <td>${{ number_format($totales['ventasPequena'], 2) }}</td>
                <td>${{ number_format($totales['costosPequena'], 2) }}</td>
                <td>${{ number_format($totales['gastosPequena'], 2) }}</td>
                <td>{{ $totales['itemsPequena'] }}</td>
                <td>{{ $totales['totalLlaves'] }}</td>
                <td>${{ number_format($totales['utilidadPequena'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
