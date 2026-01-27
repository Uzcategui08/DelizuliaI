@extends('adminlte::page')

@section('title', 'Editar Inventario')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark">
        <i class="fas fa-boxes"></i> Editar Inventario
    </h1>
    <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Ajuste de Inventario
                    </h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="icon fas fa-check"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="icon fas fa-exclamation-triangle"></i>
                        <strong>Error!</strong> Revise los siguientes campos:
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <div class="info-box bg-light mb-4">
                        <div class="info-box-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="info-box-text">Producto</span>
                                    <span class="info-box-number">{{ $inventario->producto->item ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="info-box-text">Almacén</span>
                                    <span class="info-box-number">{{ $inventario->almacene->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="info-box-text">Stock Actual</span>
                                    <span class="info-box-number">{{ $inventario->cantidad }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('inventarios.actualizar-ajustes', $inventario->id_inventario) }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="tipo_ajuste" class="form-label fw-bold">Tipo de Ajuste</label>
                            <select name="tipo_ajuste" id="tipo_ajuste" 
                                 class="form-select form-select-lg shadow-sm" required
                                 style="border: 2px solid #dee2e6; border-radius: 0.375rem; padding: 0.375rem 0.75rem;">
                                <option value="">Seleccione tipo</option>
                                <option value="compra" {{ old('tipo_ajuste') == 'compra' ? 'selected' : '' }}>Compra (sumar)</option>
                                <option value="resta" {{ old('tipo_ajuste') == 'resta' ? 'selected' : '' }}>Ajuste (disminuir)</option>
                                <option value="ajuste" {{ old('tipo_ajuste') == 'ajuste' ? 'selected' : '' }}>Ajuste (sumar)</option>
                                <option value="ajuste2" {{ old('tipo_ajuste') == 'ajuste2' ? 'selected' : '' }}>Salida (resta)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cantidad_ajuste" class="form-label">Cantidad a Ajustar</label>
                            <input type="number" name="cantidad_ajuste" id="cantidad_ajuste" 
                                   class="form-control" min="1" value="{{ old('cantidad_ajuste') }}" 
                                   required oninput="validarCantidad()">
                            <small id="cantidadHelp" class="form-text text-muted">
                                Cantidad mínima: 1 unidad
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="fecha_ajuste" class="form-label">Fecha del Ajuste</label>
                            <input type="date" name="fecha_ajuste" id="fecha_ajuste" 
                                   class="form-control" value="{{ old('fecha_ajuste', now()->format('Y-m-d')) }}">
                            <small id="fechaHelp" class="form-text text-muted">
                                Fecha en que se realizó el ajuste
                            </small>
                        </div>

                        <div id="cierreGroup" class="form-group" style="display: none;">
                            <label for="cierre" class="form-label">¿Incluir en Cierre?</label>
                            <div class="form-check">
                                <input type="hidden" name="cierre" value="0">
                                <input class="form-check-input" type="checkbox" name="cierre" id="cierre" value="1">
                                <label class="form-check-label" for="cierre">
                                    Marcar si este ajuste debe incluirse en el cierre
                                </label>
                            </div>
                        </div>

                        <div id="precioGroup" class="form-group" style="display: none;">
                            <label for="precio_llave" class="form-label">Precio unitario de la llave</label>
                            <input type="number" step="0.01" min="0" name="precio_llave" id="precio_llave"
                                   class="form-control"
                                   value="{{ old('precio_llave', optional($inventario->producto)->precio) }}">
                            <small class="form-text text-muted">
                                Se usará para los reportes cuando el ajuste sea una salida manual.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" 
                                      rows="3" placeholder="Motivo del ajuste...">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Guardar Ajuste
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<style>
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,0.1);
        border-radius: .25rem;
    }
    .info-box-text {
        font-size: .875rem;
        color: #6c757d;
    }
    .info-box-number {
        font-size: 1.25rem;
        font-weight: 600;
    }
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
    }
</style>
@stop

@section('js')
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "Seleccione tipo",
            allowClear: true
        });

        // Validación de cantidad
        function validarCantidad() {
            const cantidad = document.getElementById('cantidad_ajuste').value;
            const tipoAjuste = document.getElementById('tipo_ajuste').value;
            const stockActual = {{ $inventario->cantidad }};
            
            if (tipoAjuste === 'resta' && parseInt(cantidad) > stockActual) {
                alert('No puede restar más cantidad que el stock actual');
                document.getElementById('cantidad_ajuste').value = '';
            }
        }

        // Asignar evento al cambiar el tipo de ajuste
        document.getElementById('tipo_ajuste').addEventListener('change', validarCantidad);
    });
    function validarCantidad() {
        const input = document.getElementById('cantidad_ajuste');
        const helpText = document.getElementById('cantidadHelp');
        
        if (input.value < 1) {
            helpText.textContent = 'Cantidad mínima: 1 unidad';
            input.setCustomValidity('La cantidad debe ser mayor o igual a 1');
        } else {
            helpText.textContent = 'Cantidad mínima: 1 unidad';
            input.setCustomValidity('');
        }
    }

    function toggleCierreField() {
        const tipoAjuste = document.getElementById('tipo_ajuste').value;
        const cierreGroup = document.getElementById('cierreGroup');
        const precioGroup = document.getElementById('precioGroup');
        const precioInput = document.getElementById('precio_llave');
        
        if (tipoAjuste === 'ajuste2') {
            cierreGroup.style.display = 'block';
            if (precioGroup) {
                precioGroup.style.display = 'block';
            }
            if (precioInput) {
                precioInput.required = true;
            }
        } else {
            cierreGroup.style.display = 'none';
            if (precioGroup) {
                precioGroup.style.display = 'none';
            }
            if (precioInput) {
                precioInput.required = false;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        validarCantidad();
        toggleCierreField();

        document.getElementById('tipo_ajuste').addEventListener('change', toggleCierreField);
    });
</script>
@stop