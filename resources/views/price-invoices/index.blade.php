@extends('adminlte::page')

@section('title', 'Facturas por Kg')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1>Facturas por Kg</h1>
    <a class="btn btn-primary" href="{{ route('price-invoices.create') }}"><i class="fas fa-plus"></i> Nueva</a>
  </div>
@stop

@section('content')
<section class="content container-fluid">
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
            <table class="table table-bordered table-striped">
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
                    <td>{{ $inv->id }}</td>
                    <td>{{ optional($inv->fecha)->format('Y-m-d') }}</td>
                    <td>{{ $inv->priceList?->code ?? '-' }}</td>
                    <td class="text-end">{{ number_format((float)$inv->tasa, 6, '.', ',') }}</td>
                    <td class="text-end">Bs {{ number_format((float)$inv->base_total, 2, '.', ',') }}</td>
                    <td class="text-end">Bs {{ number_format((float)$inv->iva_total, 2, '.', ',') }}</td>
                    <td class="text-end fw-bold">Bs {{ number_format((float)$inv->total, 2, '.', ',') }}</td>
                    <td>
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
