@extends('adminlte::page')

@section('title', 'Factura #' . $invoice->id)

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1>Factura por Kg #{{ $invoice->id }}</h1>
    <a class="btn btn-secondary" href="{{ route('price-invoices.index') }}">Volver</a>
  </div>
@stop

@section('content')
<section class="content container-fluid">
  @php
    $fmtMoney = fn($v) => ((float)($v ?? 0) > 0)
      ? ('Bs ' . number_format((float)$v, 2, '.', ','))
      : 'Bs 0';
    $fmtNum = fn($v, $d = 2) => ((float)($v ?? 0) > 0)
      ? number_format((float)$v, $d, '.', ',')
      : '0';
  @endphp
  <div class="row">
    <div class="col-md-12">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card mb-3">
        <div class="card-header"><span class="card-title">Resumen</span></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3"><strong>Fecha:</strong> {{ optional($invoice->fecha)->format('Y-m-d') }}</div>
            <div class="col-md-3"><strong>Lista:</strong> {{ $invoice->priceList?->code ?? '-' }}</div>
            <div class="col-md-3"><strong>Tasa:</strong> {{ $fmtNum($invoice->tasa, 6) }}</div>
            <div class="col-md-3"><strong>IVA:</strong> {{ number_format(((float)$invoice->iva_rate ?? 0.16) * 100, 0) }}%</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><span class="card-title">Detalle</span></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="table-light">
                <tr>
                  <th>Producto</th>
                  <th class="text-end">Kg</th>
                  <th class="text-end">Precio/Kg</th>
                  <th class="text-end">Unit Bs</th>
                  <th class="text-end">Base</th>
                  <th class="text-end">IVA</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($lineas as $l)
                  @php
                    $p = $productos->get($l['producto'] ?? 0);
                  @endphp
                  <tr>
                    <td>
                      {{ $p?->item ?? ('ID ' . ($l['producto'] ?? '-')) }}
                      @if(!empty($l['has_iva']))
                        <span class="badge bg-secondary">IVA</span>
                      @endif
                    </td>
                    <td class="text-end">{{ $fmtNum($l['kg'] ?? 0, 3) }}</td>
                    <td class="text-end">{{ $fmtNum($l['price_per_kg'] ?? 0, 4) }}</td>
                    <td class="text-end">{{ $fmtMoney($l['unit_bs'] ?? 0) }}</td>
                    <td class="text-end">{{ $fmtMoney($l['base_bs'] ?? 0) }}</td>
                    <td class="text-end">{{ $fmtMoney($l['iva_bs'] ?? 0) }}</td>
                    <td class="text-end fw-bold">{{ $fmtMoney($l['total_bs'] ?? 0) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot class="table-light">
                <tr>
                  <th colspan="4" class="text-end">Totales:</th>
                  <th class="text-end">{{ $fmtMoney($invoice->base_total) }}</th>
                  <th class="text-end">{{ $fmtMoney($invoice->iva_total) }}</th>
                  <th class="text-end fw-bold">{{ $fmtMoney($invoice->total) }}</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
@stop
