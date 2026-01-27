<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <!-- Fila con 3 campos en línea -->
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="nombre" class="form-label fw-bold">{{ __('Nombre') }}</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                           value="{{ old('nombre', $cliente?->nombre) }}" id="nombre" 
                           placeholder="Nombre del Cliente" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="telefono" class="form-label fw-bold">{{ __('Teléfono') }}</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                           value="{{ old('telefono', $cliente?->telefono) }}" id="telefono" 
                           placeholder="Teléfono del Cliente">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="direccion" class="form-label fw-bold">{{ __('Dirección') }}</label>
                    <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                           value="{{ old('direccion', $cliente?->direccion) }}" id="direccion" 
                           placeholder="Dirección del Cliente">
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('Cancelar') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('Guardar Cliente') }}
                </button>
            </div>
        </div>
    </div>
</div>