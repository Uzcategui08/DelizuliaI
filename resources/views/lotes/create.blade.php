@extends('adminlte::page')

@section('title', 'Crear Lote')

@section('content_header')
    <h1 class="m-0">Crear lote</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lotes.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fecha_inicio">Fecha inicio (opcional)</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="dias">Días para registrar merma (desde día 2)</label>
                    <select id="dias" name="dias[]" class="form-control" multiple>
                        @for($d = 2; $d <= 60; $d++)
                            <option value="{{ $d }}" @if(collect(old('dias', []))->contains($d)) selected @endif>
                                Día {{ $d }}
                            </option>
                        @endfor
                    </select>
                    <small class="text-muted">Selecciona los días en los que quieres registrar merma del lote.</small>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Productos (Día 1 - cantidad inicial)</h5>
                    <button type="button" id="addRow" class="btn btn-sm btn-secondary">
                        <i class="fas fa-plus"></i> Agregar producto
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table" id="productosTable">
                        <thead>
                            <tr>
                                <th style="width: 55%">Producto</th>
                                <th style="width: 25%">Cantidad</th>
                                <th style="width: 15%" class="text-right">Kg/u</th>
                                <th style="width: 20%" class="text-right">Kg</th>
                                <th style="width: 20%" class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- filas por JS -->
                        </tbody>
                    </table>
                </div>

                <input type="hidden" id="productosCount" value="0">
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('lotes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar lote
                </button>
            </div>
        </div>
    </form>
@stop

@section('plugins.Select2', true)

@section('js')
<script>
    const productos = @json($productos);

    const initialProducts = @json(old('productos') ?: []);

    const kilosPorProducto = Object.fromEntries(
        (productos || []).map(p => [String(p.id_producto), parseFloat(p.kilos_promedio ?? 1)])
    );

    function buildProductOptions(selectedId) {
        return productos.map(p => {
            const text = `${p.item} - ${p.marca ?? ''}`.trim();
            const selected = selectedId && String(selectedId) === String(p.id_producto) ? 'selected' : '';
            return `<option value="${p.id_producto}" ${selected}>${text}</option>`;
        }).join('');
    }

    function addRow(initial = {}) {
        const tbody = document.querySelector('#productosTable tbody');
        const index = parseInt(document.getElementById('productosCount').value || '0', 10);
        document.getElementById('productosCount').value = String(index + 1);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="productos[${index}][id_producto]" class="form-control" required>
                    <option value="">-- Selecciona --</option>
                    ${buildProductOptions(initial.id_producto)}
                </select>
            </td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-outline-secondary btn-dec">-</button>
                    </div>
                    <input type="number" name="productos[${index}][cantidad]" class="form-control cantidad" value="${initial.cantidad ?? 0}" min="0" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary btn-inc">+</button>
                    </div>
                </div>
            </td>
            <td class="text-right">
                <input type="number" name="productos[${index}][kilos_por_unidad]" class="form-control text-right kilos" value="${(initial.kilos_por_unidad ?? '').toString()}" min="0.001" step="0.001" required>
            </td>
            <td class="text-right">
                <span class="badge badge-light kg-badge">0.000 kg</span>
            </td>
            <td class="text-right">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        function refreshKg() {
            const select = tr.querySelector('select');
            const input = tr.querySelector('input.cantidad');
            const kilosInput = tr.querySelector('input.kilos');
            const kpu = parseFloat(kilosInput.value || (kilosPorProducto[String(select.value)] ?? 1));
            const qty = parseInt(input.value || '0', 10);
            const kg = (qty * kpu);
            tr.querySelector('.kg-badge').textContent = `${kg.toFixed(3)} kg`;
        }

        function seedKilosIfEmptyOrNotManual() {
            const select = tr.querySelector('select');
            const kilosInput = tr.querySelector('input.kilos');
            const shouldSeed = (!kilosInput.value) || (kilosInput.dataset.manual === '0');
            if (!shouldSeed) {
                return;
            }

            const kpuDefault = kilosPorProducto[String(select.value)] ?? 1;
            kilosInput.value = String(kpuDefault);
            kilosInput.dataset.manual = '0';
        }

        tr.querySelector('.btn-remove').addEventListener('click', () => tr.remove());
        tr.querySelector('.btn-dec').addEventListener('click', () => {
            const input = tr.querySelector('input.cantidad');
            input.value = Math.max(0, (parseInt(input.value || '0', 10) - 1));
            refreshKg();
        });
        tr.querySelector('.btn-inc').addEventListener('click', () => {
            const input = tr.querySelector('input.cantidad');
            input.value = (parseInt(input.value || '0', 10) + 1);
            refreshKg();
        });

        tr.querySelector('select').addEventListener('change', () => {
            seedKilosIfEmptyOrNotManual();
            refreshKg();
        });
        tr.querySelector('input.cantidad').addEventListener('input', refreshKg);
        tr.querySelector('input.kilos').addEventListener('input', () => {
            tr.querySelector('input.kilos').dataset.manual = '1';
            refreshKg();
        });

        // Si viene precargado, se considera manual.
        const kilosInput = tr.querySelector('input.kilos');
        if (kilosInput.value) {
            kilosInput.dataset.manual = '1';
        } else {
            kilosInput.dataset.manual = '0';
        }

        seedKilosIfEmptyOrNotManual();

        refreshKg();

        tbody.appendChild(tr);
    }

    document.getElementById('addRow').addEventListener('click', () => addRow());

    // Init select2
    $(function() {
        $('#dias').select2({
            placeholder: 'Selecciona días (opcional)',
            width: '100%'
        });
    });

    // Seed rows
    if (Array.isArray(initialProducts) && initialProducts.length > 0) {
        initialProducts.forEach(p => addRow({
            id_producto: p.id_producto,
            cantidad: p.cantidad ?? p.cantidad_inicial ?? 0,
            kilos_por_unidad: p.kilos_por_unidad,
        }));
    } else {
        addRow();
    }
</script>
@stop
