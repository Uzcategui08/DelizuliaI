@extends('adminlte::page')

@section('title', 'Factura por Kg')

@section('content_header')
  <h1>Nueva Factura por Kg</h1>
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
        <div class="card-header d-flex justify-content-between align-items-center">
          <span class="card-title">Datos</span>
          <a class="btn btn-secondary" href="{{ route('price-invoices.index') }}">Volver</a>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('price-invoices.store') }}">
            @csrf

            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', now()->format('Y-m-d')) }}">
                @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-3">
                <label class="form-label">Lista</label>
                <select name="price_list_id" id="price-list" class="form-control @error('price_list_id') is-invalid @enderror">
                  @foreach($listas as $l)
                    <option value="{{ $l->id }}" {{ old('price_list_id', $listas->first()?->id)==$l->id?'selected':'' }}>
                      {{ $l->code }} - {{ $l->name }}
                    </option>
                  @endforeach
                </select>
                @error('price_list_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Tasa del día</label>
                <input type="number" step="0.000001" min="0" name="tasa" id="tasa" class="form-control @error('tasa') is-invalid @enderror" value="{{ old('tasa', $tasaDefault) }}" {{ !empty($tasaLocked) ? 'readonly' : '' }} {{ empty($tasaLocked) ? 'required' : '' }}>
                @error('tasa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="text-muted small">
                  Se aplica a toda la factura.
                  @if(!empty($tasaLocked))
                    (Ya fue guardada para {{ $tasaDate ?? now()->format('Y-m-d') }}.)
                  @else
                    (No hay tasa guardada para {{ $tasaDate ?? now()->format('Y-m-d') }}: se pedirá una sola vez y quedará fija para el resto del día.)
                  @endif
                </div>
              </div>
            </div>

            <div class="card mb-3">
              <div class="card-header d-flex justify-content-between align-items-center">
                <span>Productos (Kg)</span>
                <button type="button" id="add-row" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Agregar</button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="tabla">
                    <thead class="table-light">
                      <tr>
                        <th style="width:34%">Producto</th>
                        <th style="width:10%" class="text-end">Kg</th>
                        <th style="width:12%" class="text-end">Precio/Kg</th>
                        <th style="width:12%" class="text-end">Unit Bs</th>
                        <th style="width:12%" class="text-end">Base</th>
                        <th style="width:10%" class="text-end">IVA</th>
                        <th style="width:10%" class="text-end">Total</th>
                        <th style="width:10%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light">
                      <tr>
                        <th colspan="4" class="text-end">Base Imponible:</th>
                        <th class="text-end" id="base-total">Bs 0.00</th>
                        <th class="text-end" id="iva-total">Bs 0.00</th>
                        <th class="text-end fw-bold" id="grand-total">Bs 0.00</th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <p class="text-muted small mt-2">
                  Cálculo: Unit Bs = Precio/Kg (lista) × Tasa. Base = Kg × Unit Bs. IVA = Base × 16% (solo productos marcados con IVA en la lista).
                </p>
              </div>
            </div>

            <input type="hidden" name="items" id="items-json" value="[]">

            <div class="d-flex justify-content-end">
              <button class="btn btn-primary btn-lg" type="submit">Guardar</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>
@stop

@section('js')
<script>
(function(){
  const productos = @json($productos);
  const ivaRate = {{ $ivaRate }};

  const tbody = document.querySelector('#tabla tbody');
  const priceListEl = document.getElementById('price-list');
  const tasaEl = document.getElementById('tasa');

  const baseTotalEl = document.getElementById('base-total');
  const ivaTotalEl = document.getElementById('iva-total');
  const grandTotalEl = document.getElementById('grand-total');
  const itemsEl = document.getElementById('items-json');

  let itemsMap = {}; // productId -> {price_per_kg, has_iva}

  function fmt(n){
    return 'Bs ' + Number(n||0).toFixed(2);
  }

  async function loadListItems(){
    const listId = priceListEl.value;
    if(!listId){ itemsMap = {}; return; }
    const res = await fetch(`/listas-precios/${listId}/items`, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
    const data = await res.json();
    itemsMap = {};
    (data || []).forEach(it => {
      itemsMap[String(it.id_producto)] = {price_per_kg: Number(it.price_per_kg||0), has_iva: !!it.has_iva};
    });
  }

  function rowHtml(){
    const prodOpts = productos.map(p=>`<option value="${p.id_producto}">${p.item}</option>`).join('');
    return `<tr>
      <td>
        <select class="form-control prod"><option value="">Seleccione...</option>${prodOpts}</select>
        <div class="small mt-1">
          <span class="badge bg-secondary iva-badge" style="display:none">IVA</span>
        </div>
      </td>
      <td><input type="number" class="form-control kg text-end" min="0" step="0.001" value="0"></td>
      <td><input type="text" class="form-control pricekg text-end" value="0.0000" readonly></td>
      <td><input type="text" class="form-control unitbs text-end" value="0.00" readonly></td>
      <td class="text-end base">Bs 0.00</td>
      <td class="text-end iva">Bs 0.00</td>
      <td class="text-end total">Bs 0.00</td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-danger del">X</button></td>
    </tr>`;
  }

  function recalc(){
    const tasa = Number(String(tasaEl.value||'0').replace(',', '.'));
    let baseTotal = 0;
    let ivaTotal = 0;
    const out = [];

    tbody.querySelectorAll('tr').forEach(tr=>{
      const prod = tr.querySelector('.prod').value;
      const kg = Number(String(tr.querySelector('.kg').value||'0').replace(',', '.'));
      const meta = itemsMap[String(prod)] || {price_per_kg: 0, has_iva: false};

      const pricePerKg = Number(meta.price_per_kg || 0);
      const hasIva = !!meta.has_iva;

      tr.querySelector('.pricekg').value = pricePerKg.toFixed(4);
      const badge = tr.querySelector('.iva-badge');
      badge.style.display = hasIva ? 'inline-block' : 'none';

      const unitBs = pricePerKg * (tasa > 0 ? tasa : 0);
      tr.querySelector('.unitbs').value = unitBs.toFixed(2);

      let base = 0;
      let iva = 0;
      if (prod && kg > 0) {
        base = kg * unitBs;
        iva = hasIva ? (base * ivaRate) : 0;
        out.push({producto: Number(prod), kg: kg});
      }

      baseTotal += base;
      ivaTotal += iva;

      tr.querySelector('.base').textContent = fmt(base);
      tr.querySelector('.iva').textContent = fmt(iva);
      tr.querySelector('.total').textContent = fmt(base + iva);
    });

    baseTotalEl.textContent = fmt(baseTotal);
    ivaTotalEl.textContent = fmt(ivaTotal);
    grandTotalEl.textContent = fmt(baseTotal + ivaTotal);
    itemsEl.value = JSON.stringify(out);
  }

  document.getElementById('add-row').addEventListener('click', ()=>{
    tbody.insertAdjacentHTML('beforeend', rowHtml());
  });

  tbody.addEventListener('input', e=>{
    if(e.target.matches('.kg')) recalc();
  });
  tbody.addEventListener('change', e=>{
    if(e.target.matches('.prod')) recalc();
  });
  tbody.addEventListener('click', e=>{
    if(e.target.closest('.del')){ e.target.closest('tr').remove(); recalc(); }
  });

  priceListEl.addEventListener('change', async ()=>{
    await loadListItems();
    recalc();
  });

  tasaEl.addEventListener('input', recalc);

  (async function init(){
    await loadListItems();
    document.getElementById('add-row').click();
    recalc();
  })();
})();
</script>
@stop
