<div class="card shadow-sm">
    <div class="card-body">
        <div class="row align-items-end"> 
            <div class="col-md-5">
                <div class="form-group mb-3">
                    <label for="id_producto" class="form-label fw-bold">{{ __('Producto') }}</label>
                    <select name="id_producto" class="form-select select2 @error('id_producto') is-invalid @enderror" id="id_producto" required style="width: 100%;">
                        <option value="">Seleccione un producto</option>
                        @foreach ($productos as $producto)
                        <option value="{{ $producto->id_producto }}" {{ old('id_producto', $inventario?->id_producto) == $producto->id_producto ? 'selected' : '' }}>
                            {{ $producto->item }} (ID: {{ $producto->id_producto }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_producto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group mb-3">
                    <label for="id_almacen" class="form-label fw-bold">{{ __('Almacén') }}</label>
                    <select name="id_almacen" class="form-select select2 @error('id_almacen') is-invalid @enderror" id="id_almacen" required style="width: 100%;">
                        <option value="">Seleccione un almacén</option>
                        @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id_almacen }}" {{ old('id_almacen', $inventario?->id_almacen) == $almacen->id_almacen ? 'selected' : '' }}>
                            {{ $almacen->nombre }} (ID: {{ $almacen->id_almacen }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_almacen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group mb-3">
                    <label for="cantidad" class="form-label fw-bold">{{ __('Cantidad') }}</label>
                    <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" 
                           value="{{ old('cantidad', $inventario?->cantidad) }}" id="cantidad" 
                           min="0" required style="height: 38px;">
                    @error('cantidad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('Cancelar') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('Guardar Inventario') }}
                </button>
            </div>
        </div>
    </div>
</div>


