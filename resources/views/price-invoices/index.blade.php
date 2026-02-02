@extends('adminlte::page')

@section('title', 'Facturas por Kg')

@section('css')
<style>
  /* Mobile-friendly “card rows” for index table */
  @media (max-width: 576px) {
    #tabla-index thead { display: none; }

    #tabla-index tbody tr {
      display: block;
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      margin-bottom: .75rem;
      overflow: hidden;
      background: #fff;
    }
    #tabla-index tbody td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .75rem;
      padding: .6rem .75rem;
      border: 0;
      border-bottom: 1px solid #f1f1f1;
    }
    #tabla-index tbody td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #6c757d;
      flex: 1 1 45%;
      min-width: 40%;
    }
    #tabla-index tbody td:last-child {
      border-bottom: 0;
      justify-content: flex-end;
    }
    #tabla-index tbody td:last-child::before { content: ''; }

    #tabla-index tbody td .btn {
      min-height: 40px;
      min-width: 96px;
    }
  }
</style>
@stop

@section('content_header')
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
    <h1>Facturas por Kg</h1>
    <a class="btn btn-primary" href="{{ route('price-invoices.create') }}"><i class="fas fa-plus"></i> Nueva</a>
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
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="card">
        <div class="card-header"><span class="card-title">Histórico</span></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tabla-index">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Fecha</th>
                  <th>Lista</th>
                  <th class="text-end">Tasa</th>
                  <th class="text-end">Base</th>
                  <th class="text-end">IVA</th>
                  <th class="text-end">Total</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($invoices as $inv)
                  <tr>
                    <td data-label="#">{{ $inv->id }}</td>
                    <td data-label="Fecha">{{ optional($inv->fecha)->format('Y-m-d') }}</td>
                    <td data-label="Lista">{{ $inv->priceList?->code ?? '-' }}</td>
                    <td data-label="Tasa" class="text-end">{{ $fmtNum($inv->tasa, 6) }}</td>
                    <td data-label="Base" class="text-end">{{ $fmtMoney($inv->base_total) }}</td>
                    <td data-label="IVA" class="text-end">{{ $fmtMoney($inv->iva_total) }}</td>
                    <td data-label="Total" class="text-end fw-bold">{{ $fmtMoney($inv->total) }}</td>
                    <td data-label="Acciones">
                      <a class="btn btn-sm btn-info" href="{{ route('price-invoices.show', $inv) }}">Ver</a>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="text-center text-muted">Sin registros</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end">
            {{ $invoices->links() }}
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
@stop
