@extends('adminlte::page')

@section('title', 'Factura #' . $invoice->id)

@section('css')
<style>
  /* Mobile-friendly “card rows” for detail table */
  @media (max-width: 576px) {
    #tabla-detalle thead { display: none; }
    #tabla-detalle tfoot { display: none; }

    #tabla-detalle tbody tr {
      display: block;
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      margin-bottom: .75rem;
      overflow: hidden;
      background: #fff;
    }
    #tabla-detalle tbody td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .75rem;
      padding: .6rem .75rem;
      border: 0;
      border-bottom: 1px solid #f1f1f1;
    }
    #tabla-detalle tbody td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #6c757d;
      flex: 1 1 45%;
      min-width: 40%;
    }
    #tabla-detalle tbody td:last-child { border-bottom: 0; }
  }
</style>
@stop

@section('content_header')
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
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
            <div class="col-6 col-md-3"><strong>Fecha:</strong> {{ optional($invoice->fecha)->format('Y-m-d') }}</div>
            <div class="col-6 col-md-3"><strong>Lista:</strong> {{ $invoice->priceList?->code ?? '-' }}</div>
            <div class="col-6 col-md-3 mt-2 mt-md-0"><strong>Tasa:</strong> {{ $fmtNum($invoice->tasa, 6) }}</div>
            <div class="col-6 col-md-3 mt-2 mt-md-0"><strong>IVA:</strong> {{ number_format(((float)$invoice->iva_rate ?? 0.16) * 100, 0) }}%</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><span class="card-title">Detalle</span></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tabla-detalle">
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
                    <td data-label="Producto">
                      {{ $p?->item ?? ('ID ' . ($l['producto'] ?? '-')) }}
                      @if(!empty($l['has_iva']))
                        <span class="badge bg-secondary">IVA</span>
                      @endif
                    </td>
                    <td data-label="Kg" class="text-end">{{ $fmtNum($l['kg'] ?? 0, 2) }}</td>
                    <td data-label="Precio/Kg" class="text-end">{{ $fmtNum($l['price_per_kg'] ?? 0, 2) }}</td>
                    <td data-label="Unit Bs" class="text-end">{{ $fmtMoney($l['unit_bs'] ?? 0) }}</td>
                    <td data-label="Base" class="text-end">{{ $fmtMoney($l['base_bs'] ?? 0) }}</td>
                    <td data-label="IVA" class="text-end">{{ $fmtMoney($l['iva_bs'] ?? 0) }}</td>
                    <td data-label="Total" class="text-end fw-bold">{{ $fmtMoney($l['total_bs'] ?? 0) }}</td>
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

          <div class="d-sm-none mt-3">
            <div class="border rounded p-3 bg-light">
              <div class="d-flex justify-content-between"><span class="text-muted">Base Imponible</span><strong>{{ $fmtMoney($invoice->base_total) }}</strong></div>
              <div class="d-flex justify-content-between mt-2"><span class="text-muted">IVA</span><strong>{{ $fmtMoney($invoice->iva_total) }}</strong></div>
              <div class="d-flex justify-content-between mt-2"><span class="text-muted">Total</span><strong>{{ $fmtMoney($invoice->total) }}</strong></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
@stop
