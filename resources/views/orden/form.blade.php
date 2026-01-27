<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="id_cliente" class="form-label fw-bold">{{ __('Cliente') }}</label>
                    <select name="id_cliente" class="form-control select2 @error('id_cliente') is-invalid @enderror" id="id_cliente" required>
                        <option value="">{{ __('Seleccionar Cliente') }}</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente', $orden?->id_cliente) == $cliente->id_cliente ? 'selected' : '' }}>
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
                    <label for="f_orden" class="form-label fw-bold">{{ __('Fecha de Orden') }}</label>
                    <input type="date" name="f_orden" class="form-control @error('f_orden') is-invalid @enderror"
                           value="{{ old('f_orden', $orden?->f_orden) }}" id="f_orden" required>
                    @error('f_orden')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="id_tecnico" class="form-label fw-bold">{{ __('Técnico') }}</label>
                    <select name="id_tecnico" class="form-control select2" id="id_tecnico" required>
                        <option value="" selected>{{ __('Seleccionar Técnico') }}</option>
                        @foreach($empleado as $tecnico)
                            <option value="{{ $tecnico->id_empleado }}" {{ old('id_tecnico', $orden?->id_tecnico) == $tecnico->id_empleado ? 'selected' : '' }}>
                                {{ $tecnico->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_tecnico')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="direccion" class="form-label fw-bold">{{ __('Dirección') }}</label>
                    <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                           value="{{ old('direccion', $orden?->direccion) }}" id="direccion"
                           placeholder="Dirección del servicio" required>
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="estado" class="form-label fw-bold">{{ __('Estado') }}</label>
                    <select name="estado" class="form-control @error('estado') is-invalid @enderror" id="estado" required>
                        <option value="pendiente" {{ old('estado', $orden?->estado) == 'pendiente' ? 'selected' : '' }}>
                            Pendiente
                        </option>
                        <option value="en_proceso" {{ old('estado', $orden?->estado) == 'en_proceso' ? 'selected' : '' }}>
                            En Proceso
                        </option>
                        <option value="completado" {{ old('estado', $orden?->estado) == 'completado' ? 'selected' : '' }}>
                            Completado
                        </option>
                        <option value="cancelado" {{ old('estado', $orden?->estado) == 'cancelado' ? 'selected' : '' }}>
                            Cancelado
                        </option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group mb-4" id="items-container">
            <label for="items" class="form-label fw-bold">{{ __('Trabajo') }}</label>
            
            @php
                $itemsExistentes = $orden->items ?? [];
                if (is_string($itemsExistentes)) {
                    $itemsExistentes = json_decode($itemsExistentes, true) ?? [];
                }
            @endphp
            
            @if(!empty($itemsExistentes))
                @foreach($itemsExistentes as $index => $item)
                <div class="item-group mb-4 p-3 border rounded" data-index="{{ $index }}">
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <label class="form-label">{{ __('Descripción') }}</label>
                            <input type="text" name="items[{{ $index }}][descripcion]" 
                                   class="form-control" placeholder="Descripción del item"
                                   value="{{ $item['descripcion'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Precio') }}</label>
                            <input type="text" name="items[{{ $index }}][cantidad]" 
                                   class="form-control" placeholder="Precio" min="0.01"
                                   value="{{ number_format(floatval($item['cantidad'] ?? 0), 2, '.', '') }}"
                                   oninput="formatNumber(this)"
                                   onblur="validateNumber(this)">
                            <div class="invalid-feedback">Por favor, ingrese un número válido (ejemplo: 123.90 o 123,90)</div>
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
                    {{ __('Guardar Orden') }}
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

    let itemIndex = {{ !empty($itemsExistentes) ? count($itemsExistentes) : 0 }};

    function formatNumber(input) {
        let value = input.value.replace(',', '.');
        value = value.replace(/[^\d.]/g, '');
        value = value.replace(/\./g, match => (value.indexOf('.') === value.lastIndexOf('.') ? '.' : ''));
        if (value.startsWith('.')) value = '0' + value;
        if (value.includes('.')) {
            let parts = value.split('.');
            value = parts[0] + '.' + parts[1].slice(0, 2);
        }
        input.value = value;
    }

    function validateNumber(input) {
        const value = input.value;
        if (value === '') return;

        const num = parseFloat(value.replace(',', '.'));
        if (!isNaN(num)) {
            input.value = num.toFixed(2);
            input.classList.remove('is-invalid');
            input.setCustomValidity('');
        } else {
            input.classList.add('is-invalid');
            input.setCustomValidity('Por favor, ingrese un número válido');
        }
    }

    function addNewItem(itemData = {descripcion: '', cantidad: ''}) {
        const cantidad = itemData.cantidad ? itemData.cantidad.replace(',', '.') : '';
        
        const newItemGroup = $(`
            <div class="item-group mb-4 p-3 border rounded" data-index="${itemIndex}">
                <div class="row mb-2">
                    <div class="col-md-8">
                        <label class="form-label">{{ __('Descripción') }}</label>
                        <input type="text" name="items[${itemIndex}][descripcion]" 
                               class="form-control" placeholder="Descripción"
                               value="${itemData.descripcion}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Precio') }}</label>
                        <input type="text" name="items[${itemIndex}][cantidad]" 
                               class="form-control" placeholder="Precio" min="0.01"
                               value="${cantidad}"
                               oninput="formatNumber(this)"
                               onblur="validateNumber(this)">
                        <div class="invalid-feedback">Por favor, ingrese un número válido (ejemplo: 123.90 o 123,90)</div>
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