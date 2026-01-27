@extends('adminlte::page')

@section('title', 'Cargar leche (litros por día)')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Cargar leche (litros por día)</h1>
        <a href="{{ route('milk-entries.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list mr-1"></i> Ver resumen semanal
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Registro de ingresos</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('milk-entries.store') }}" method="POST">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="entries-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 170px;">Fecha</th>
                                            <th>Proveedor</th>
                                            <th style="width: 140px;">Litros</th>
                                            <th style="width: 170px;">Monto (opcional)</th>
                                            <th style="width: 110px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="entry-row">
                                            <td>
                                                <input type="date" name="entries[0][date]" class="form-control" required>
                                            </td>
                                            <td>
                                                <select name="entries[0][payee_id]" class="form-control payee-select">
                                                    <option value="">-- Seleccionar --</option>
                                                    @foreach($payees as $payee)
                                                        <option value="{{ $payee->id }}">{{ $payee->name }}</option>
                                                    @endforeach
                                                    <option value="__new">+ Nuevo proveedor...</option>
                                                </select>
                                                <input type="text" name="entries[0][payee_name]" class="form-control mt-2 payee-name" placeholder="Nombre nuevo proveedor" style="display:none">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="entries[0][liters]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="entries[0][amount]" class="form-control">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                                    <i class="fas fa-trash mr-1"></i> Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end" style="gap: 0.5rem;">
                                <button type="button" id="add-row" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Agregar fila
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let idx = 1;

        const bindRow = (row) => {
            const select = row.querySelector('.payee-select');
            const input = row.querySelector('.payee-name');
            const removeBtn = row.querySelector('.remove-row');

            if (select && input) {
                select.addEventListener('change', function(){
                    if (this.value === '__new') {
                        input.style.display = '';
                    } else {
                        input.style.display = 'none';
                        input.value = '';
                    }
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    const tbody = document.querySelector('#entries-table tbody');
                    if (!tbody) {
                        return;
                    }
                    if (tbody.querySelectorAll('tr').length > 1) {
                        row.remove();
                    } else {
                        row.querySelectorAll('input').forEach(i => i.value = '');
                        row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                        const payeeName = row.querySelector('.payee-name');
                        if (payeeName) {
                            payeeName.style.display = 'none';
                        }
                    }
                });
            }
        };

        const addBtn = document.getElementById('add-row');
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const tbody = document.querySelector('#entries-table tbody');
                const template = document.querySelector('.entry-row');
                if (!tbody || !template) {
                    return;
                }

                const newRow = template.cloneNode(true);

                newRow.querySelectorAll('input, select').forEach((el) => {
                    if (el.name) {
                        el.name = el.name.replace(/entries\[\d+\]/, 'entries[' + idx + ']');
                    }

                    if (el.tagName.toLowerCase() === 'select') {
                        el.selectedIndex = 0;
                    } else {
                        el.value = '';
                    }

                    if (el.classList.contains('payee-name')) {
                        el.style.display = 'none';
                    }
                });

                tbody.appendChild(newRow);
                bindRow(newRow);
                idx++;
            });
        }

        const initialRow = document.querySelector('.entry-row');
        if (initialRow) {
            bindRow(initialRow);
        }
    });
</script>
@endpush
