@extends('adminlte::page')

@section('title', 'Venta #' . $venta->id)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1>Venta #{{ $venta->id }}</h1>
  <div>
    <a href="{{ route('simple-sales.edit', $venta) }}" class="btn btn-primary">Editar</a>
    <a href="{{ route('simple-sales.index') }}" class="btn btn-secondary">Volver</a>
  </div>
</div>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-3"><strong>Fecha:</strong> {{ optional($venta->fecha_h)->format('Y-m-d') }}</div>
            <div class="col-md-3"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? '-' }}</div>
            <div class="col-md-3"><strong>Vendedor:</strong> {{ $venta->empleado->nombre ?? '-' }}</div>
            <div class="col-md-3"><strong>Zona:</strong> {{ $venta->zona ?? '-' }}</div>
          </div>

          <p class="text-muted small">Precio mostrado es unitario; el subtotal incluye los kilos de la línea.</p>
          <div class="table-responsive mb-3">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Producto</th>
                  <th>Almacén</th>
                  <th class="text-end">Cant.</th>
                  <th class="text-end">Precio</th>
                  <th class="text-end">Kilos</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @php $total = 0; @endphp
                @foreach($lineas as $l)
                  @php 
                    $k = isset($l['kilos']) ? (float)$l['kilos'] : 1;
                    $incluye = isset($l['precio_incluye_kilos']) ? (bool)$l['precio_incluye_kilos'] : false;
                    $subtotal = ($l['cantidad'] ?? 0) * ($l['precio'] ?? 0);
                    if (!$incluye) { $subtotal *= $k; }
                    $total += $subtotal; 
                    // Mostrar precio unitario: si el precio ya incluye kilos, derivamos unitario dividiendo entre k
                    $precioUnit = $incluye && $k>0 ? (($l['precio'] ?? 0)/$k) : ($l['precio'] ?? 0);
                  @endphp
                  <tr>
                    <td>{{ \App\Models\Producto::find($l['producto'])->item ?? ('#'.$l['producto']) }}</td>
                    <td>{{ \App\Models\Almacene::find($l['almacen'])->nombre ?? ('#'.$l['almacen']) }}</td>
                    <td class="text-end">{{ $l['cantidad'] }}</td>
                    <td class="text-end">${{ number_format($precioUnit,2) }}</td>
                    <td class="text-end">{{ number_format($k,3) }}</td>
                    <td class="text-end">${{ number_format($subtotal,2) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          @php 
            $totalKilos = 0; 
            foreach ($lineas as $l) { $totalKilos += (float)($l['cantidad'] ?? 0) * (float)($l['kilos'] ?? 1); }
          @endphp
          <div class="row">
            <div class="col-md-4 ms-auto">
              <div class="d-flex justify-content-between"><span>Total Kilos:</span><strong>{{ number_format($totalKilos,3) }}</strong></div>
              <div class="d-flex justify-content-between"><span>Total Bruto:</span><strong>${{ number_format($venta->total_bruto,2) }}</strong></div>
              <div class="d-flex justify-content-between"><span>Descuento:</span><strong>${{ number_format($venta->descuento,2) }}</strong></div>
              <div class="d-flex justify-content-between"><span>Total Neto:</span><strong>${{ number_format($venta->total_neto,2) }}</strong></div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>
@stop
