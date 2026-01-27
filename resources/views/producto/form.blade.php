<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="id_producto" class="form-label fw-bold">{{ __('ID Producto') }}</label>
                    <input type="text" name="id_producto" class="form-control @error('id_producto') is-invalid @enderror" 
                           value="{{ old('id_producto', $producto?->id_producto) }}" id="id_producto" 
                           placeholder="Ej: 001" required>
                    @error('id_producto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="item" class="form-label fw-bold">{{ __('Nombre') }}</label>
                    <input type="text" name="item" class="form-control @error('item') is-invalid @enderror" 
                           value="{{ old('item', $producto?->item) }}" id="item" 
                           placeholder="Ej: Producto Principal" required>
                    @error('item')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <label for="marca" class="fw-medium">{{ __('Marca') }}</label>
                    <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                           id="marca" placeholder="Ej: Marca del Producto"
                           value="{{ old('marca', $producto?->marca) }}" required>
                    @error('marca')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="t_llave" class="form-label fw-bold">{{ __('Tipo de producto') }}</label>
                    <input type="text" name="t_llave" class="form-control @error('t_llave') is-invalid @enderror" 
                           value="{{ old('t_llave', $producto?->t_llave) }}" id="t_llave" 
                           placeholder="Ej: Lacteo">
                    @error('t_llave')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="sku" class="form-label fw-bold">{{ __('Codigo de barras') }}</label>
                    <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" 
                           value="{{ old('sku', $producto?->sku) }}" id="sku" 
                           placeholder="Ej: 12345" required>
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="precio" class="form-label fw-bold">{{ __('Precio') }}</label>
                    <div class="input-group">
                        <input type="number" name="precio" class="form-control @error('precio') is-invalid @enderror" 
                               value="{{ old('precio', $producto?->precio) }}" id="precio" 
                               placeholder="0.00" min="0" step="0.01" required>
                    </div>
                    @error('precio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="kilos_promedio" class="form-label fw-bold">{{ __('Kilos Promedio (por unidad)') }}</label>
                    <div class="input-group">
                        <input type="number" name="kilos_promedio" class="form-control @error('kilos_promedio') is-invalid @enderror" 
                               value="{{ old('kilos_promedio', $producto?->kilos_promedio ?? 1) }}" id="kilos_promedio" 
                               placeholder="1.000" min="0" step="0.001">
                    </div>
                    @error('kilos_promedio')
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
                    {{ __('Guardar Producto') }}
                </button>
            </div>
        </div>
    </div>
</div>