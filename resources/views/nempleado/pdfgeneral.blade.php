<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 5mm;
            background-color: #f9f9f9;
        }
        
        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        .reporte-container {
            width: 95%;
            margin: 0 auto;
            border: 1px solid #e0e0e0;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1a5276;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #1a5276;
            font-weight: bold;
        }
        
        .header p {
            margin: 3px 0;
            color: #555;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        
        th {
            background-color: #1a5276;
            color: white;
            font-weight: bold;
            padding: 6px;
            text-align: left;
            font-size: 9px;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: 5px;
            border: 1px solid #e0e0e0;
            vertical-align: top;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .empleado-row {
            background-color: #f5f5f5;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #eaf2f8;
            border-top: 2px solid #1a5276;
            border-bottom: 2px solid #1a5276;
        }
        
        .negative {
            color: #e74c3c;
        }
        
        .positive {
            color: #27ae60;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .summary-item {
            padding: 5px;
        }
        
        .summary-title {
            font-weight: bold;
            font-size: 9px;
            color: #1a5276;
            margin-bottom: 3px;
        }
        
        .summary-value {
            font-size: 10px;
        }
        
        .payment-methods {
            margin-top: 10px;
        }
        
        .payment-methods table {
            width: 100%;
            font-size: 9px;
        }
        
        .payment-methods th {
            background-color: #2980b9;
            padding: 4px;
        }
        
        .metodo-pago {
            font-size: 8px;
            line-height: 1.2;
        }
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
            margin: -15px;
        }

        .summary-cell {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
            width: 25%; 
        }

        .summary-title {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .summary-value {
            font-size: 1.1rem;
            color: #2d3748;
        }

        .summary-value.negative {
            color: #e53e3e;
        }

        .summary-value.text-bold {
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="reporte-container">
        <div class="header">
            <h1>REPORTE GENERAL DE NÓMINA</h1>
            <p>Período: {{ date('d/m/Y', strtotime($fechaDesde)) }} al {{ date('d/m/Y', strtotime($fechaHasta)) }}</p>
            <p>Documento generado el: {{ $fechaGeneracion }}</p>
        </div>

        <div class="summary-card">
            @if(!empty($metodosPagoGlobales))
            <div class="payment-methods">
                <div class="summary-title">Distribución de Pagos</div>
                <table>
                    <thead>
                        <tr>
                            <th>Método de Pago</th>
                            <th class="text-right">Monto</th>
                            <th class="text-right">Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($metodosPagoGlobales as $nombre => $monto)
                        <tr>
                            <td>{{ ucfirst($nombre) }}</td>
                            <td class="text-right">${{ number_format($monto, 2) }}</td>
                            <td class="text-right">{{ number_format(($monto / $totales['netoPagado']) * 100, 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="15%">Nombre</th>
                    <th width="10%">Cédula</th>
                    <th width="10%" class="text-right">Salario Base</th>
                    <th width="10%" class="text-right">Total Devengado</th>
                    <th width="10%" class="text-right">Descuentos</th>
                    <th width="10%" class="text-right">Abonos</th>
                    <th width="10%" class="text-right">Neto Pagado</th>
                    <th width="10%">Métodos de Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $empleado)
                <tr class="empleado-row">
                    <td>{{ $empleado['id'] }}</td>
                    <td>{{ $empleado['nombre'] }}</td>
                    <td>{{ $empleado['cedula'] }}</td>
                    <td class="text-right">${{ number_format($empleado['salario_base'], 2) }}</td>
                    <td class="text-right">${{ number_format($empleado['totalPagado'], 2) }}</td>
                    <td class="text-right negative">${{ number_format($empleado['totalDescuentos'], 2) }}</td>
                    <td class="text-right positive">${{ number_format($empleado['totalAbonos'], 2) }}</td>
                    <td class="text-right text-bold">${{ number_format($empleado['netoPagado'], 2) }}</td>
                    <td class="metodo-pago">
                        @foreach($empleado['metodos_pago'] as $metodo)
                        {{ ucfirst($metodo['nombre']) }}: ${{ number_format($metodo['monto'], 2) }}<br>
                        @endforeach
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-bold">TOTALES GENERALES</td>
                    <td class="text-right text-bold">${{ number_format($totales['totalSalarioBase'], 2) }}</td>
                    <td class="text-right text-bold">${{ number_format($totales['totalPagado'], 2) }}</td>
                    <td class="text-right text-bold negative">${{ number_format($totales['totalDescuentos'], 2) }}</td>
                    <td class="text-right text-bold positive">${{ number_format($totales['totalAbonos'], 2) }}</td>
                    <td class="text-right text-bold">${{ number_format($totales['netoPagado'], 2) }}</td>
                    <td></td>
                </tr>
            </tbody>t
        </table>
    </div>
</body>
</html>