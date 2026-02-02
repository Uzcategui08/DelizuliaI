@extends('adminlte::page')

@section('title', 'Editar Lista ' . $priceList->code)

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1>Lista {{ $priceList->code }} - Precios por Kg</h1>
    <a class="btn btn-secondary" href="{{ route('price-lists.index') }}">Volver</a>
  </div>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-12">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">Hay errores en el formulario.</div>
      @endif

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span class="card-title">Productos</span>
          <div class="text-muted small">IVA: marcar productos con IVA (16%)</div>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('price-lists.update', $priceList) }}">
            @csrf
            @method('PUT')

            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-light">
                  <tr>
                    <th style="width:10%">ID</th>
                    <th style="width:55%">Producto</th>
                    <th style="width:20%" class="text-end">Precio / Kg</th>
                    <th style="width:15%" class="text-center">IVA</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($productos as $p)
                    @php
                      $it = $items->get($p->id_producto);
                      $price = old('items.' . $p->id_producto . '.price_per_kg', $it?->price_per_kg ?? '');
                      $has = old('items.' . $p->id_producto . '.has_iva', $it?->has_iva ?? false);
                    @endphp
                    <tr>
                      <td>{{ $p->id_producto }}</td>
                      <td>{{ $p->item }} <span class="text-muted">{{ $p->marca }}</span></td>
                      <td>
                        <input
                          type="number"
                          step="0.0001"
                          min="0"
                          class="form-control text-end"
                          name="items[{{ $p->id_producto }}][price_per_kg]"
                          value="{{ $price }}"
                        >
                      </td>
                      <td class="text-center">
                        <input type="hidden" name="items[{{ $p->id_producto }}][has_iva]" value="0">
                        <input type="checkbox" name="items[{{ $p->id_producto }}][has_iva]" value="1" {{ $has ? 'checked' : '' }}>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-end">
              <button class="btn btn-primary btn-lg" type="submit">Guardar Lista</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>
@stop
