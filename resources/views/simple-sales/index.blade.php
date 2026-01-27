@extends('adminlte::page')

@section('title', 'Ventas Rápidas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1>Ventas Rápidas</h1>
  <a href="{{ route('simple-sales.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Venta</a>
</div>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Fecha</th>
                  <th>Cliente</th>
                  <th>Vendedor</th>
                  <th>Zona</th>
                  <th class="text-end"># Prod.</th>
                  <th class="text-end">Kg</th>
                  <th class="text-end">Bruto</th>
                  <th class="text-end">Desc.</th>
                  <th class="text-end">Neto</th>
                </tr>
              </thead>
              <tbody>
                @forelse($ventas as $v)
                <tr>
                  <td><a href="{{ route('simple-sales.show',$v) }}">{{ $v->id }}</a></td>
                  <td>{{ optional($v->fecha_h)->format('Y-m-d') }}</td>
                  <td>{{ $v->cliente->nombre ?? '-' }}</td>
                  <td>{{ $v->empleado->nombre ?? '-' }}</td>
                  <td>{{ $v->zona ?? '-' }}</td>
                  <td class="text-end">{{ is_array($v->items) ? collect($v->items)->sum('cantidad') : (collect(json_decode($v->items,true) ?: [])->sum('cantidad')) }}</td>
                  <td class="text-end">
                    @php
                      $lineas = is_array($v->items) ? $v->items : (json_decode($v->items,true) ?: []);
                      $kg = collect($lineas)->sum(function($l){ return (float)($l['cantidad'] ?? 0) * (float)($l['kilos'] ?? 1); });
                    @endphp
                    {{ number_format($kg,3) }}
                  </td>
                  <td class="text-end">${{ number_format($v->total_bruto,2) }}</td>
                  <td class="text-end">${{ number_format($v->descuento,2) }}</td>
                  <td class="text-end">${{ number_format($v->total_neto,2) }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted">Sin ventas</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div>
            {{ $ventas->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@stop
