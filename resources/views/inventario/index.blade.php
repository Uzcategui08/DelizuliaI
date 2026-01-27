@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
<h1>Registro</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            <h3 class="mb-0">{{ __('Inventario') }}</h3>
                        </span>
                        
                        <div class="float-right">
                            <a href="{{ route('inventarios.export') }}" class="btn btn-success btn-m mr-2">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </a>
                            <a href="{{ route('inventarios.create') }}" class="btn btn-secondary btn-m" data-placement="left">
                                {{ __('Crear Nuevo') }}
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <div class="input-group" style="width: 300px; margin-right: 10px;">
                            <select class="form-control select2" id="almacen_filter" style="width: 100%;">
                                <option value="">Todos los almacenes</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id_almacen }}">{{ $almacen->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button class="btn btn-primary mr-2" type="button" id="btn-filtrar">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        
                        <button class="btn btn-outline-secondary" type="button" id="btn-limpiar" title="Limpiar filtros">
                            <i class="fas fa-broom"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table id="inventario-table" class="table table-striped table-bordered">
                            <thead class="thead">
                                <tr>
                                    <th class="text-center align-middle">ID Inventario</th>
                                    <th class="text-center align-middle">ID Producto</th>
                                    <th class="text-center align-middle">Producto</th>
                                    <th class="text-center align-middle">Cantidad</th>
                                    <th class="text-center align-middle">Almacén</th>
                                    <th class="text-center align-middle">Total $</th>
                                    <th class="text-center align-middle">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="inventario-body">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-filter fa-lg mb-2"></i><br>
                                        Seleccione un almacén y haga clic en "Filtrar" para ver los registros
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border-radius: 4px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }

    #btn-limpiar {
        transition: all 0.3s ease;
    }
    
    #btn-limpiar:hover {
        background-color: #f8f9fa;
        color: #dc3545;
    }

    .btn-action-group {
        display: flex;
        gap: 8px;
    }

    .btn-export-excel {
        background-color: #1d6f42;
        color: white;
        border-color: #1a633b;
    }

    .btn-export-excel:hover {
        background-color: #1a633b;
        color: white;
    }
    
    #inventario-table {
        width: 100% !important;
    }

    .card-header {
        padding-bottom: 1rem;
    }
    label:not(.form-check-label):not(.custom-file-label) {
        padding-top: 20px;
    }
    
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#almacen_filter').select2({
        placeholder: "Seleccione un almacén",
        allowClear: true
    });

    let dataTable = null;

    $('#btn-filtrar').click(function() {
        const almacenId = $('#almacen_filter').val();
        
        if(!almacenId) {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Por favor seleccione un almacén para filtrar',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        loadInventarioData();
    });

    $('#btn-limpiar').click(function() {
        $('#almacen_filter').val('').trigger('change');

        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }
        
        $('#inventario-body').html(`
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-filter fa-lg mb-2"></i><br>
                    Seleccione un almacén y haga clic en "Filtrar" para ver los registros
                </td>
            </tr>
        `);
    });

    function loadInventarioData() {
        const almacenId = $('#almacen_filter').val();
        const $btnFiltrar = $('#btn-filtrar');

        $btnFiltrar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Filtrando');

        if (dataTable) {
            dataTable.destroy();
            $('#inventario-table').empty();
        }
        
        $.ajax({
            url: "{{ route('inventarios.data') }}",
            type: "GET",
            data: { almacen_id: almacenId },
            dataType: "json",
            success: function(response) {
                const tableHtml = `
                    <thead class="thead">
                        <tr>
                            <th class="text-center align-middle">ID Inventario</th>
                            <th class="text-center align-middle">ID Producto</th>
                            <th class="text-center align-middle">Producto</th>
                            <th class="text-center align-middle">Cantidad</th>
                            <th class="text-center align-middle">Almacén</th>
                            <th class="text-center align-middle">Total $</th>
                            <th class="text-center align-middle">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="inventario-body"></tbody>
                `;
                
                $('#inventario-table').html(tableHtml);
                const $tbody = $('#inventario-body');
                
                if(response.data && response.data.length > 0) {
                    $.each(response.data, function(index, inventario) {
                        let cantidadClass = '';
                        if(inventario.cantidad <= 5) {
                            cantidadClass = 'bg-danger text-white p-1 rounded';
                        } else if(inventario.cantidad <= 10) {
                            cantidadClass = 'bg-warning text-dark p-1 rounded';
                        } else {
                            cantidadClass = 'bg-success text-white p-1 rounded';
                        }

                        const total = inventario.cantidad * (inventario.producto?.precio || 0);
                        const formattedTotal = '$ ' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                        const row = `
                            <tr>
                                <td class="text-center align-middle">${inventario.id_inventario}</td>
                                <td class="text-center align-middle">${inventario.producto?.id_producto || 'N/A'}</td>
                                <td class="text-center align-middle">${inventario.producto?.item || 'Producto no disponible'}</td>
                                <td class="text-center align-middle">
                                    <span class="${cantidadClass}">${inventario.cantidad}</span>
                                </td>
                                <td class="text-center align-middle">${inventario.almacene?.nombre || 'N/A'}</td>
                                <td class="text-center align-middle">${formattedTotal}</td>
                                <td class="text-center align-middle">
                                    <div class="btn-action-group d-flex justify-content-center align-items-center">
                                        <a class="btn btn-sm btn-primary mx-1" href="/inventarios/${inventario.id_inventario}">
                                            <i class="fa fa-fw fa-eye"></i> 
                                        </a>
                                        <a class="btn btn-sm btn-success mx-1" href="/inventarios/${inventario.id_inventario}/edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                <a class="btn btn-sm btn-success mx-1" href="/inventarios/${inventario.id_inventario}/ajustar">
                    <i class="fas fa-exchange-alt"></i>
                </a>
                                        <form onsubmit="return confirmDelete(this)" action="/inventarios/${inventario.id_inventario}" method="POST" class="d-inline mx-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-fw fa-trash"></i> 
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        `;
                        
                        $tbody.append(row);
                    });

                    dataTable = $('#inventario-table').DataTable({
                        responsive: true,
                        autoWidth: false,
                        ordering: false,
                        order: [[3, 'asc']],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'
                        },
                        columnDefs: [
                            { orderable: false, targets: [6] }, 
                            { className: "text-center", targets: [0, 1, 3, 4, 5, 6] } // 
                        ],
                        dom: '<"top"f>rt<"bottom"lip><"clear">',
                        initComplete: function() {
                            this.api().columns.adjust();
                        }
                    });
                } else {
                    $tbody.html(`
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-info-circle"></i> No se encontraron registros para este almacén
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr) {
                console.error(xhr);
                $('#inventario-table').html(`
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center text-danger py-4">
                                <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos
                            </td>
                        </tr>
                    </tbody>
                `);
            },
            complete: function() {
                $btnFiltrar.prop('disabled', false).html('<i class="fas fa-filter"></i> Filtrar');
            }
        });
    }

    window.confirmDelete = function(form) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    };
});
</script>
@stop