<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <!-- Primera fila de campos -->
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="id_cliente" class="form-label fw-bold">{{ __('Cliente') }}</label>
                    <select name="id_cliente" class="form-control select2 @error('id_cliente') is-invalid @enderror" id="id_cliente" required>
                        <option value="">{{ __('Seleccionar Cliente') }}</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente', $presupuesto?->id_cliente) == $cliente->id_cliente ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_cliente')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="f_presupuesto" class="form-label fw-bold">{{ __('Fecha Presupuesto') }}</label>
                    <input type="date" name="f_presupuesto" class="form-control @error('f_presupuesto') is-invalid @enderror"
                           value="{{ old('f_presupuesto', $presupuesto?->f_presupuesto) }}" id="f_presupuesto" required>
                    @error('f_presupuesto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="validez" class="form-label fw-bold">{{ __('Validez') }}</label>
                    <input type="date" name="validez" class="form-control @error('validez') is-invalid @enderror"
                           value="{{ old('validez', $presupuesto?->validez) }}" id="validez" required>
                    @error('validez')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Segunda fila de campos -->
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="descuento" class="form-label fw-bold">{{ __('Descuento') }}</label>
                    <div class="input-group">
                        <input type="number" name="descuento" class="form-control @error('descuento') is-invalid @enderror"
                               value="{{ old('descuento', $presupuesto?->descuento) }}" id="descuento"
                               placeholder="0" min="0" max="100">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    @error('descuento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="iva" class="form-label fw-bold">{{ __('Taxes') }}</label>
                    <select name="iva" class="form-control @error('iva') is-invalid @enderror" id="iva">
                        <option value="0" {{ old('iva', $presupuesto?->iva) == 0 ? 'selected' : '' }}>0%</option>
                        <option value="7.5" {{ old('iva', $presupuesto?->iva) == 7.5 ? 'selected' : '' }}>7.5%</option>
                    </select>
                    @error('iva')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @if(auth()->user()->hasRole('admin'))
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="estado" class="form-label fw-bold">{{ __('Estado') }}</label>
                    <select name="estado" class="form-control @error('estado') is-invalid @enderror" id="estado">
                        <option value="pendiente" {{ (old('estado', $presupuesto?->estado) == 'pendiente' || !isset($presupuesto)) ? 'selected' : '' }}>
                            Pendiente
                        </option>
                        <option value="aprobado" {{ old('estado', $presupuesto?->estado) == 'aprobado' ? 'selected' : '' }}>
                            Aprobado
                        </option>
                        <option value="rechazado" {{ old('estado', $presupuesto?->estado) == 'rechazado' ? 'selected' : '' }}>
                            Rechazado
                        </option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @endif
        </div>

        <!-- Sección de trabajos -->
        <div class="form-group mb-4" id="items-container">
            <label for="items" class="form-label fw-bold">{{ __('Trabajos') }}</label>
            
            @if($presupuesto && $presupuesto->items)
                @foreach($presupuesto->items as $index => $item)
                <div class="item-group mb-4 p-3 border rounded" data-index="{{ $index }}">
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <label class="form-label">{{ __('Descripción del Trabajo') }}</label>
                            <textarea name="items[{{ $index }}][descripcion]" class="form-control" placeholder="Descripción del trabajo">{{ $item['descripcion'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Precio') }}</label>
                            <input type="number" step="0.01" name="items[{{ $index }}][precio]" 
                                   class="form-control" placeholder="0.00"
                                   value="{{ $item['precio'] ?? '' }}">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-remove-item mt-4">×</button>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        <div class="d-flex justify-content-center mb-4">
            <button type="button" class="btn btn-outline-primary btn-add-item">
                <i class="fas fa-plus me-2"></i> {{ __('Agregar Trabajo') }}
            </button>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('Cancelar') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('Guardar Presupuesto') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();
    
    let itemIndex = {{ $presupuesto && $presupuesto->items ? count($presupuesto->items) : 0 }};

    function addNewItem(itemData = {descripcion: '', precio: ''}) {
        const newItemGroup = $(`
            <div class="item-group mb-4 p-3 border rounded" data-index="${itemIndex}">
                <div class="row mb-2">
                    <div class="col-md-8">
                        <label class="form-label">{{ __('Descripción del Trabajo') }}</label>
                        <textarea name="items[${itemIndex}][descripcion]" class="form-control" 
                                  placeholder="Descripción del trabajo">${itemData.descripcion}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Precio') }}</label>
                        <input type="number" step="0.01" name="items[${itemIndex}][precio]" 
                               class="form-control" placeholder="0.00"
                               value="${itemData.precio}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-item mt-4">×</button>
                    </div>
                </div>
            </div>
        `);

        $('#items-container').append(newItemGroup);
        itemIndex++;
    }

    $(document).on('click', '.btn-add-item', function() {
        addNewItem();
    });

    $(document).on('click', '.btn-remove-item', function() {
        $(this).closest('.item-group').remove();
    });

    if ($('.item-group').length === 0) {
        addNewItem();
    }
});
</script>