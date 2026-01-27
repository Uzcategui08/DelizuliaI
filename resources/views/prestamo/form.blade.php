<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Información del prestamo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="f_prestamo" class="form-label">{{ __('Fecha') }}</label>
                            <input type="date" name="f_prestamo" class="form-control @error('f_prestamo') is-invalid @enderror" 
                                   value="{{ old('f_prestamo', $prestamo?->f_prestamo ?? date('Y-m-d')) }}" id="f_prestamo">
                            {!! $errors->first('f_prestamo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="id_empleado" class="form-label fw-bold">{{ __('Técnico') }}</label>
                            <select name="id_empleado" class="form-control select2" id="id_empleado" required>
                                <option value="" selected>{{ __('Seleccionar Técnico') }}</option>
                                @foreach($empleado as $tecnico)
                                    <option value="{{ $tecnico->id_empleado }}" {{ old('id_empleado', $prestamo?->id_empleado) == $tecnico->id_empleado ? 'selected' : '' }}>
                                        {{ $tecnico->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_empleado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="valor" class="form-label">{{ __('Valor Total') }}</label>
                            <input type="number" step="0.01" name="valor" class="form-control @error('valor') is-invalid @enderror" 
                                   value="{{ old('valor', $prestamo?->valor) }}" id="valor" placeholder="0.00">
                            {!! $errors->first('valor', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                    <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" placeholder="Descripción del prestamo">{{ old('descripcion', $prestamo?->descripcion) }}</textarea>
                    {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="subcategoria" class="form-label fw-bold">{{ __('Subcategoría') }}</label>
                            <select name="subcategoria" class="form-control select2" id="subcategoria" required>
                                <option value="" disabled>{{ __('Seleccionar Subcategoría') }}</option>
                                @foreach($categorias as $categoria)
<option value="{{ $categoria->id_categoria }}"
    {{ (old('subcategoria', $prestamo->subcategoria ?? null)) == $categoria->id_categoria ? 'selected' : '' }}>
    {{ $categoria->nombre }}
</option>
                                @endforeach
                            </select>
                            @error('subcategoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="estatus" class="form-label">{{ __('Estatus') }}</label>
                            <select name="estatus" id="estatus" class="form-control @error('estatus') is-invalid @enderror">
                                <option value="pendiente" {{ old('estatus', $prestamo?->estatus) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="parcialmente pagado" {{ old('estatus', $prestamo?->estatus) == 'parcialmente pagado' ? 'selected' : '' }}>Parcialmente Pagado</option>
                                <option value="pagado" {{ old('estatus', $prestamo?->estatus) == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            </select>
                            {!! $errors->first('estatus', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Registro de Pagos Parciales</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>Valor Total:</strong> 
                            <span id="valor-total">${{ number_format($prestamo->valor ?? 0, 2) }}</span>
                        </div>
                        <div>
                            <strong>Total Pagado:</strong> 
                            <span id="total-pagado">$0.00</span>
                        </div>
                        <div>
                            <strong>Saldo Pendiente:</strong> 
                            <span id="saldo-pendiente">${{ number_format($prestamo->valor ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Monto</label>
                        <input type="number" step="0.01" id="pago_monto" class="form-control" placeholder="0.00">
                        <small class="text-muted">Monto máximo: <span id="maximo-pago">${{ number_format($prestamo->valor ?? 0, 2) }}</span></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="pago_metodo" class="form-label fw-bold">{{ __('Método de Pago') }}</label>
                            <select name="pago_metodo" class="form-control select2 @error('pago_metodo') is-invalid @enderror" id="pago_metodo">
                                <option value="" selected>{{ __('Seleccionar Método de Pago') }}</option>
                                @foreach($metodos as $metodo)
                                    <option value="{{ $metodo->id }}" 
                                        {{ old('pago_metodo', isset($prestamo) ? $prestamo->pago_metodo : '') == $metodo->id ? 'selected' : '' }}>
                                        {{ $metodo->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pago_metodo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha</label>
                        <input type="date" id="pago_fecha" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <button type="button" id="btn-agregar-pago" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Agregar Pago
                </button>

                <div class="mt-4" id="lista-pagos">
                    @if(!empty($prestamo->pagos) && is_array($prestamo->pagos))
                        @foreach($prestamo->pagos as $index => $pago)
                        <div class="pago-item card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">${{ number_format($pago['monto'], 2) }}</span>
                                        <span class="text-muted ms-2">({{ $pago['metodo_pago'] }})</span>
                                        <small class="text-muted ms-2">{{ $pago['fecha'] }}</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago" data-index="{{ $index }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">No hay pagos registrados</div>
                    @endif
                </div>
            </div>
        </div>

        <input type="hidden" name="pagos" id="pagos_json" 
        value='@json(old('pagos', $prestamo->pagos ?? []))'>
        
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
    const metodosPago = @json($metodos ?? []);

    let valorTotal = parseFloat($('#valor').val()) || 0;
    let totalPagado = 0;
    let saldoPendiente = valorTotal;

    actualizarResumen();

    $('#valor').on('change', function() {
        valorTotal = parseFloat($(this).val()) || 0;
        actualizarResumen();
        actualizarMaximoPago();
    });

    function actualizarResumen() {
        totalPagado = calcularTotalPagado();
        saldoPendiente = valorTotal - totalPagado;
        
        $('#valor-total').text('$' + valorTotal.toFixed(2));
        $('#total-pagado').text('$' + totalPagado.toFixed(2));
        $('#saldo-pendiente').text('$' + (saldoPendiente > 0 ? saldoPendiente.toFixed(2) : '0.00'));

        actualizarEstatus();
    }

    function actualizarMaximoPago() {
        $('#maximo-pago').text('$' + saldoPendiente.toFixed(2));
        $('#pago_monto').attr('max', saldoPendiente);
    }

    function calcularTotalPagado() {
        const pagosJson = $('#pagos_json').val() || '[]';
        try {
            const jsonStr = pagosJson.replace(/^"|"$/g, '');
            const pagos = JSON.parse(jsonStr);
            return pagos.reduce((total, pago) => total + parseFloat(pago.monto), 0);
        } catch (e) {
            return 0;
        }
    }

    function actualizarEstatus() {
        if (saldoPendiente <= 0.01) { 
            $('#estatus').val('pagado');
        } else if (totalPagado > 0) {
            $('#estatus').val('parcialmente_pagado');
        } else {
            $('#estatus').val('pendiente');
        }
    }

    const $pagoMetodo = $('#pago_metodo');
    $pagoMetodo.empty();
    $pagoMetodo.append('<option value="">Seleccionar método</option>');
    metodosPago.forEach(metodo => {
        $pagoMetodo.append($('<option>', {
            value: metodo.id, 
            text: metodo.name  
        }));
    });

    $('#btn-agregar-pago').click(function() {
        const monto = parseFloat($('#pago_monto').val());
        const metodoId = $('#pago_metodo').val();
        const fecha = $('#pago_fecha').val();
        
        if (!monto || monto <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Monto inválido',
                html: 'Por favor ingrese un <b>monto válido</b> mayor a cero',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                $('#pago_monto').val('').focus();
            });
            return;
        }
        
        if (monto > saldoPendiente) {
            Swal.fire({
                icon: 'error',
                title: 'Saldo insuficiente',
                html: `El monto excede el saldo pendiente de <strong>$${saldoPendiente.toFixed(2)}</strong>`,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                $('#pago_monto').val(saldoPendiente.toFixed(2)).focus();
            });
            return;
        }
        
        if (!metodoId) {
            Swal.fire({
                icon: 'error',
                title: 'Método requerido',
                html: 'Por favor seleccione un <b>método de pago</b>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                $('#pago_metodo').focus();
            });
            return;
        }
        
        const pagosJson = $('#pagos_json').val() || '[]';
        let pagos = [];
        
        try {
            pagos = JSON.parse(pagosJson);
            if (!Array.isArray(pagos)) pagos = [];
        } catch (e) {
            console.error('Error parseando pagos:', e);
        }
        
        pagos.push({
            monto: monto,
            metodo_pago: metodoId,
            fecha: fecha
        });
        
        $('#pagos_json').val(JSON.stringify(pagos));
        
        actualizarListaPagos();
        actualizarResumen();
        actualizarMaximoPago();
        
        $('#pago_monto').val('').focus();
    });

    function actualizarListaPagos() {
        const pagosJson = $('#pagos_json').val() || '[]';
        $('#lista-pagos').empty();
        
        try {
            const pagos = JSON.parse(pagosJson);
            
            if (pagos.length === 0) {
                $('#lista-pagos').html('<div class="alert alert-warning">No hay pagos registrados</div>');
                return;
            }
            
            pagos.forEach((pago, index) => {
                const metodoPago = metodosPago.find(m => m.id == pago.metodo_pago);
                const metodoNombre = metodoPago ? metodoPago.name : 'Método desconocido';
                
                $('#lista-pagos').append(`
                    <div class="pago-item card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">$${parseFloat(pago.monto).toFixed(2)}</span>
                                    <span class="text-muted ms-2">(${metodoNombre})</span>
                                    <small class="text-muted ms-2">${pago.fecha}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });
        } catch (e) {
            console.error('Error mostrando pagos:', e);
            $('#lista-pagos').html('<div class="alert alert-danger">Error mostrando los pagos</div>');
        }
    }

    $(document).on('click', '.btn-eliminar-pago', function() {
        const index = $(this).data('index');
        const pagosJson = $('#pagos_json').val() || '[]';
        
        try {
            let pagos = JSON.parse(pagosJson);
            
            if (index >= 0 && index < pagos.length) {
                pagos.splice(index, 1);
                
                $('#pagos_json').val(JSON.stringify(pagos));
                actualizarListaPagos();
                actualizarResumen();
                actualizarMaximoPago();
            }
        } catch (e) {
            console.error('Error eliminando pago:', e);
        }
    });

    actualizarListaPagos();
    actualizarMaximoPago();
});

</script>

<style>
    .pago-item {
        transition: all 0.3s ease;
    }
    .pago-item:hover {
        background-color: #f8f9fa;
    }
    .btn-eliminar-pago {
        transition: all 0.2s ease;
    }
    .btn-eliminar-pago:hover {
        transform: scale(1.1);
    }
</style>