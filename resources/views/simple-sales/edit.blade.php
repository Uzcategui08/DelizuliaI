@extends('adminlte::page')

@section('title', 'Editar Venta #' . $venta->id)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1>Editar Venta #{{ $venta->id }}</h1>
  <a href="{{ route('simple-sales.show', $venta) }}" class="btn btn-secondary">Volver</a>
</div>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-12">
      @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('simple-sales.update', $venta) }}">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha_h" class="form-control" value="{{ old('fecha_h', optional($venta->fecha_h)->format('Y-m-d')) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Cliente (opcional)</label>
                <select name="id_cliente" class="form-control">
                  <option value="">Seleccione...</option>
                  @foreach($clientes as $c)
                    <option value="{{ $c->id_cliente }}" {{ (old('id_cliente',$venta->id_cliente)==$c->id_cliente)?'selected':'' }}>{{ $c->nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Vendedor (opcional)</label>
                <select name="id_empleado" class="form-control">
                  <option value="">Seleccione...</option>
                  @foreach($empleados as $e)
                    <option value="{{ $e->id_empleado }}" {{ (old('id_empleado',$venta->id_empleado)==$e->id_empleado)?'selected':'' }}>{{ $e->nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Zona</label>
                <input type="text" name="zona" class="form-control" value="{{ old('zona', $venta->zona) }}">
              </div>
            </div>

            <div class="card mb-3">
              <div class="card-header d-flex justify-content-between align-items-center">
                <span>Productos</span>
                <button type="button" id="add-row" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Agregar</button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="tabla">
                    <thead class="table-light">
                      <tr>
                        <th style="width:30%">Producto</th>
                        <th style="width:20%">Almacén</th>
                        <th style="width:10%" class="text-end">Cant.</th>
                        <th style="width:15%" class="text-end">Precio</th>
                        <th style="width:10%" class="text-end">Kilos</th>
                        <th style="width:15%" class="text-end">Subtotal</th>
                        <th style="width:10%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light">
                      <tr>
                        <th colspan="4" class="text-end">Total Bruto:</th>
                        <th class="text-end" id="total-bruto">$0.00</th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <p class="text-muted small mt-2">
                  Nota: Precio es unitario. Subtotal = Precio × Kilos. Al cambiar de producto se asignan el precio y los kilos promedio por defecto; puedes ajustarlos.
                </p>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Descuento</label>
                <input type="number" step="0.01" name="descuento" id="descuento" class="form-control" value="{{ old('descuento', $venta->descuento) }}">
              </div>
              <div class="col-md-3">
                <label class="form-label">Total Neto</label>
                <input type="text" id="total-neto" class="form-control text-end fw-bold" value="$0.00" readonly>
              </div>
              <div class="col-md-6 d-flex align-items-end justify-content-end">
                <button class="btn btn-primary btn-lg" type="submit">Guardar Cambios</button>
              </div>
            </div>

            <input type="hidden" name="items" id="items-json" value="[]">
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
  const almacenes = @json($almacenes);
  const iniciales = @json(is_array($venta->items) ? $venta->items : (json_decode($venta->items,true) ?: []));
  const tbody = document.querySelector('#tabla tbody');
  const totalBrutoEl = document.getElementById('total-bruto');
  const totalNetoEl = document.getElementById('total-neto');
  const descuentoEl = document.getElementById('descuento');
  const itemsEl = document.getElementById('items-json');

  function fmt(n){return '$'+Number(n).toFixed(2)}

  function rowHtml(selP=0, selA=0, cant=1, precio=0, kilos=1){
    const prodOpts = productos.map(p=>`<option value="${p.id_producto}" data-precio="${p.precio ?? 0}" data-kilos="${p.kilos_promedio ?? 1}" ${selP==p.id_producto?'selected':''}>${p.item}</option>`).join('');
    const almOpts = almacenes.map(a=>`<option value="${a.id_almacen}" ${selA==a.id_almacen?'selected':''}>${a.nombre}</option>`).join('');
    return `<tr>
      <td><select class="form-control prod"><option value="">Seleccione...</option>${prodOpts}</select></td>
      <td><select class="form-control alm"><option value="">Seleccione...</option>${almOpts}</select></td>
      <td><input type="number" class="form-control cant text-end" min="1" value="${cant}"></td>
      <td><input type="number" class="form-control precio text-end" min="0" step="0.01" value="${Number(precio).toFixed(2)}"></td>
      <td><input type="number" class="form-control kilos text-end" min="0" step="0.001" value="${Number(kilos).toFixed(3)}"></td>
      <td class="text-end subtotal">$0.00</td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-danger del">X</button></td>
    </tr>`
  }

  function recalc(){
    let total = 0; const items = [];
    tbody.querySelectorAll('tr').forEach(tr=>{
      const prod = parseInt(tr.querySelector('.prod').value||0);
      const alm = parseInt(tr.querySelector('.alm').value||0);
      const cant = parseFloat(tr.querySelector('.cant').value||0);
      let precio = parseFloat(tr.querySelector('.precio').value||0);
      let kilos = parseFloat(tr.querySelector('.kilos').value||0);
      const opt = tr.querySelector('.prod').selectedOptions[0];
      if (prod && (!kilos || kilos<=0)) {
        kilos = parseFloat(opt?.dataset?.kilos || 1);
        tr.querySelector('.kilos').value = kilos.toFixed(3);
      }
      if (prod && !precio) {
        const def = parseFloat(opt?.dataset?.precio || 0);
        if (def > 0) {
          tr.querySelector('.precio').value = def.toFixed(2);
          precio = def;
        }
      }
      if(prod>0 && alm>0 && cant>0){
        const sub = precio*kilos; total += sub; tr.querySelector('.subtotal').textContent = fmt(sub);
        items.push({producto:prod, almacen:alm, cantidad:cant, precio:precio, kilos:kilos});
      } else { tr.querySelector('.subtotal').textContent = '$0.00'; }
    });
    totalBrutoEl.textContent = fmt(total);
    const desc = parseFloat(descuentoEl.value||0);
    const neto = Math.max(total-desc,0);
    totalNetoEl.value = fmt(neto);
    itemsEl.value = JSON.stringify(items);
  }

  document.getElementById('add-row').addEventListener('click', ()=>{
    tbody.insertAdjacentHTML('beforeend', rowHtml());
  });

  tbody.addEventListener('input', e=>{
    if(e.target.matches('.alm,.cant,.precio,.kilos')) recalc();
  });
  tbody.addEventListener('change', e=>{
    if(e.target.matches('.prod')){
      const tr = e.target.closest('tr');
      const opt = e.target.selectedOptions[0];
      const defP = parseFloat(opt?.dataset?.precio || 0);
      const defK = parseFloat(opt?.dataset?.kilos || 1);
      if (defP>0) tr.querySelector('.precio').value = defP.toFixed(2);
      tr.querySelector('.kilos').value = defK.toFixed(3);
      recalc();
    }
  });
  tbody.addEventListener('click', e=>{
    if(e.target.closest('.del')){ e.target.closest('tr').remove(); recalc(); }
  });
  descuentoEl.addEventListener('input', recalc);

  // Cargar líneas iniciales
  if (Array.isArray(iniciales) && iniciales.length){
    iniciales.forEach(l=>{
      const precioBase = (l.precio ?? 0);
      const kilosBase = (l.kilos ?? 1);
      tbody.insertAdjacentHTML('beforeend', rowHtml(l.producto, l.almacen, l.cantidad, precioBase, kilosBase));
    });
  } else {
    document.getElementById('add-row').click();
  }
  recalc();
})();
</script>
@stop
