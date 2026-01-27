@csrf
<div class="row g-3 mb-3">
  <div class="col-md-6">
    <label class="form-label fw-semibold">Nombre</label>
    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $pago->nombre) }}" placeholder="Ej: Pago proveedor" required>
    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3">
    <label class="form-label fw-semibold">Monto</label>
    <div class="input-group">
      <span class="input-group-text">$</span>
      <input type="number" step="0.01" min="0" name="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto', $pago->monto) }}" required>
    </div>
    @error('monto')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3">
    <label class="form-label fw-semibold">Fecha</label>
    <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', optional($pago->fecha)->format('Y-m-d')) }}" required>
    @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4">
    <div class="d-flex flex-column align-items-start gap-2 p-2">
      <div class="form-check d-flex align-items-center gap-2">
        <input class="form-check-input" style="transform: scale(1.15);" type="checkbox" id="aprobadoCheck" name="aprobado" value="1" {{ old('aprobado', $pago->aprobado) ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="aprobadoCheck">Aprobado</label>
      </div>
      <div class="form-check d-flex align-items-center gap-2">
        <input class="form-check-input" style="transform: scale(1.15);" type="checkbox" id="pagadoCheck" name="pagado" value="1" {{ old('pagado', $pago->pagado) ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="pagadoCheck">Pagado</label>
      </div>
      <div class="form-check d-flex align-items-center gap-2">
        <input class="form-check-input" style="transform: scale(1.15);" type="checkbox" id="largoPlazoCheck" name="largo_plazo" value="1" {{ old('largo_plazo', $pago->largo_plazo) ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="largoPlazoCheck">Largo plazo</label>
      </div>
    </div>
  </div>
</div>
<div class="mb-3">
  <label class="form-label fw-semibold">Descripción</label>
  <textarea name="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Detalle o referencia">{{ old('descripcion', $pago->descripcion) }}</textarea>
  @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="d-flex justify-content-between mt-3">
  <a href="{{ route('payment-controls.index') }}" class="btn btn-outline-secondary">Volver</a>
  <button type="submit" class="btn btn-primary">Guardar</button>
</div>
