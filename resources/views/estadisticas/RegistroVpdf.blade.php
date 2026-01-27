<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 9px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .header { text-align: center; margin-bottom: 10px; }
        .summary { margin-top: 15px; }
        .page-break { page-break-after: always; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="header">
      <div class="header">
        <h3>{{ $title }}</h3>
        <p>Periodo: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</p>
        <p>Generado el: {{ $date }}</p>
    </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="6%">Fecha</th>
                <th width="8%">Cliente</th>
                <th width="8%">Teléfono</th>
                <th width="10%">Trabajo</th>
                <th width="8%">Vehículo</th>
                <th width="6%">Año</th>
                <th width="8%">Venta</th>
                <th width="8%">Estatus</th>
                <th width="8%">Lugar</th>
                <th width="8%">Empleado</th>
                <th width="6%">% Com.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $registro)
            <tr>
                <td class="nowrap">{{ $registro->fecha_h->format('m/d/y') }}</td>
                <td>{{ Str::limit($registro->cliente, 15) }}</td>
                <td>{{ $registro->telefono }}</td>
                <td>{{ Str::limit($registro->trabajo, 20) }}</td>
                <td>{{ $registro->marca }} {{ Str::limit($registro->modelo, 10) }}</td>
                <td class="text-center">{{ $registro->año }}</td>
                <td class="text-right">${{ number_format($registro->valor_v, 2) }}</td>
                <td class="text-center">{{ $registro->estatus }}</td>
                <td>{{ Str::limit($registro->lugarventa, 10) }}</td>
                <td>{{ $registro->empleado->nombre }}</td>
                <td class="text-center">{{ $registro->porcentaje_c }}</td>
            </tr>
            @endforeach
        </tbody>
      </table>
        <div class="summary">
          <table style="width: auto; float: right;">
              <tr>
                  <td><strong>Periodo:</strong></td>
                  <td>{{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</td>
              </tr>
              <tr class="text-bold">
                  <td>Total Ventas:</td>
                  <td class="text-right">${{ number_format($totalVentas, 2) }}</td>
              </tr>
          </table>
      </div>
</body>
</html>