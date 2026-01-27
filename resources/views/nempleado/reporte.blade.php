@extends('adminlte::page')

@section('title', 'Reporte de Pagos de Nómina')

@section('content_header')
    <h1>Reporte de Pagos de Nómina</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form id="filtroForm">
            <div class="row align-items-end"> 
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" 
                               value="{{ request('fecha_desde') }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_hasta">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" 
                               value="{{ request('fecha_hasta') }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="empleado_id">Empleado</label>
                        <select name="empleado_id" id="empleado_id" class="form-control">
                            <option value="">Todos los empleados</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}" 
                                    {{ request('empleado_id') == $empleado->id_empleado ? 'selected' : '' }}>
                                    {{ $empleado->nombre }} ({{ $empleado->cedula }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0"> 
                        <button type="submit" class="btn btn-primary w-100 py-2"> 
                            <i class="fas fa-search mr-1"></i> Buscar
                        </button>
                    </div>
                </div>                              
            </div>
        </form>

        <div class="mt-4">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="individual-tab" data-toggle="tab" href="#individual" role="tab">
                        Pagos Individuales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="general-tab" data-toggle="tab" href="#general" role="tab">
                        Resumen General
                    </a>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-3 bg-white" id="myTabContent">
                <div class="tab-pane fade show active" id="individual" role="tabpanel">
                    <div id="loading-individual" class="text-center py-5" style="display: none;">
                        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando pagos individuales...</p>
                    </div>
                    <div id="contenido-individual">
                        @if(isset($pagosIndividuales))
                            @if($pagosIndividuales->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Empleado</th>
                                                <th>Cédula</th>
                                                <th class="text-right">Sueldo Base</th>
                                                <th class="text-right">Total Abonos</th>
                                                <th class="text-right">Total Descuentos</th>
                                                <th class="text-right">Neto Pagado</th>
                                                <th class="text-center">Fecha Pago</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pagosIndividuales as $pago)
                                                <tr>
                                                    <td>{{ $pago->empleado->nombre }}</td>
                                                    <td>{{ $pago->empleado->cedula }}</td>
                                                    <td class="text-right">${{ number_format($pago->sueldo_base, 2) }}</td>
                                                    <td class="text-right">${{ number_format($pago->total_abonos, 2) }}</td>
                                                    <td class="text-right">${{ number_format($pago->total_descuentos, 2) }}</td>
                                                    <td class="text-right">${{ number_format($pago->total_pagado, 2) }}</td>
                                                    <td class="text-center">{{ $pago->fecha_pago ?: 'N/A' }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-primary generar-pdf" 
                                                                data-tipo="individual"
                                                                data-id="{{ $pago->id_nempleado }}">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-light text-center mt-3">
                                    @if(request('empleado_id'))
                                        No se encontraron pagos para este empleado en el rango de fechas
                                    @else
                                        No hay pagos registrados en el rango de fechas seleccionado
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="alert alert-light text-center mt-3">
                                Seleccione un rango de fechas para comenzar
                            </div>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="general" role="tabpanel">
                    <div id="loading-general" class="text-center py-5" style="display: none;">
                        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando resumen general...</p>
                    </div>
                    <div id="contenido-general">
                        @if(isset($resumenGeneral))
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Resumen General de Pagos</h5>
                                <button class="btn btn-success generar-pdf" 
                                        data-tipo="general"
                                        data-fecha-desde="{{ request('fecha_desde') }}"
                                        data-fecha-hasta="{{ request('fecha_hasta') }}">
                                    <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-calendar-alt text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-muted small">PERÍODO</h6>
                                                    <h5 class="mb-0">
                                                        {{ \Carbon\Carbon::parse(request('fecha_desde'))->format('d/m/Y') }} - 
                                                        {{ \Carbon\Carbon::parse(request('fecha_hasta'))->format('d/m/Y') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-users text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-muted small">EMPLEADOS</h6>
                                                    <h5 class="mb-0">{{ $resumenGeneral['total_empleados'] }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-money-bill-wave text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-muted small">TOTAL PAGADO</h6>
                                                    <h5 class="mb-0">${{ number_format($resumenGeneral['total_pagado'], 2) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-hand-holding-usd text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-muted small">PAGOS</h6>
                                                    <h5 class="mb-0">{{ $resumenGeneral['total_pagos'] }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Distribución por Conceptos</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Concepto</th>
                                                            <th class="text-right">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Sueldo Base</td>
                                                            <td class="text-right">${{ number_format($resumenGeneral['total_sueldo_base'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Abonos</td>
                                                            <td class="text-right">${{ number_format($resumenGeneral['total_abonos'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Descuentos</td>
                                                            <td class="text-right">${{ number_format($resumenGeneral['total_descuentos'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Costos</td>
                                                            <td class="text-right">${{ number_format($resumenGeneral['total_costos'], 2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Métodos de Pago</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Método</th>
                                                            <th class="text-right">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($resumenGeneral['metodos_pago'] as $metodo => $total)
                                                        <tr>
                                                            <td>{{ ucfirst($metodo) }}</td>
                                                            <td class="text-right">${{ number_format($total, 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light text-center">
                                Seleccione un rango de fechas para ver el resumen
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css"/>
<style>
    .nav-tabs .nav-link {
        font-weight: 500;
        padding: 12px 20px;
    }
    .btn-block {
        padding: 8px 12px;
    }
    .rounded-circle {
        flex-shrink: 0;
    }
    .table th {
        border-top: none;
    }
    .btn-outline-primary {
        border-width: 2px;
    }
    .spinner-grow {
        opacity: 0.7;
    }
    .text-muted {
        color: #6c757d!important;
    }
    #filtroForm .row {
        display: flex;
        align-items: flex-end;
    }
    #filtroForm select {
        height: 38px
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #d2d6de;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }
    .select2-container .select2-selection--single {
        height: 38px;
    }
    #filtroForm .btn {
        height: 38px;
        margin-bottom: 16px;
    }
    .card-header {
        background-color: #f8f9fa;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.js"></script>
<script>
$(document).ready(function() {
    $('#empleado_id').select2({
        theme: 'bootstrap',
        language: 'es',
        placeholder: 'Seleccione un empleado',
        width: '100%'
    });

    $('#filtroForm').on('submit', function(e) {
        e.preventDefault();
        const fechaDesde = $('#fecha_desde').val();
        const fechaHasta = $('#fecha_hasta').val();
        
        if(!fechaDesde || !fechaHasta) {
            Swal.fire('Error', 'Seleccione ambas fechas', 'error');
            return;
        }

        const tabActiva = $('.tab-pane.active').attr('id');
        $(`#loading-${tabActiva}`).show();
        $(`#contenido-${tabActiva}`).hide();

        if(tabActiva === 'individual') {
            cargarPagosIndividuales(fechaDesde, fechaHasta, $('#empleado_id').val());
        } else {
            cargarResumenGeneral(fechaDesde, fechaHasta);
        }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const fechaDesde = $('#fecha_desde').val();
        const fechaHasta = $('#fecha_hasta').val();
        if(!fechaDesde || !fechaHasta) return;

        const target = $(e.target).attr("href").replace('#', '');
        $(`#loading-${target}`).show();
        $(`#contenido-${target}`).hide();

        if(target === 'individual') {
            cargarPagosIndividuales(fechaDesde, fechaHasta, $('#empleado_id').val());
        } else {
            cargarResumenGeneral(fechaDesde, fechaHasta);
        }
    });

    function cargarPagosIndividuales(fechaDesde, fechaHasta, empleadoId = '') {
        $.ajax({
            url: "{{ route('nempleados.reporte') }}",
            type: 'GET',
            data: { 
                fecha_desde: fechaDesde, 
                fecha_hasta: fechaHasta, 
                empleado_id: empleadoId,
                tipo: 'individual' 
            },
            success: function(response) {
                $('#contenido-individual').html($(response).find('#contenido-individual').html());
                $('#loading-individual').hide();
                $('#contenido-individual').show();
            },
            error: function() {
                Swal.fire('Error', 'Error al cargar pagos individuales', 'error');
                $('#loading-individual').hide();
                $('#contenido-individual').show();
            }
        });
    }

    function cargarResumenGeneral(fechaDesde, fechaHasta) {
    $.ajax({
        url: "{{ route('nempleados.reporte') }}",
        type: 'GET',
        data: { 
            fecha_desde: fechaDesde, 
            fecha_hasta: fechaHasta,
            tipo: 'general' 
        },
        success: function(response) {
            $('#contenido-general').html($(response).find('#contenido-general').html());
            $('#loading-general').hide();
            $('#contenido-general').show();
        },
        error: function(xhr) {
            console.error('Error detallado:', xhr.responseText);
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al cargar el resumen general. Por favor verifica los logs.',
                icon: 'error'
            });
            $('#loading-general').hide();
            $('#contenido-general').show();
        }
    });
}


    $(document).on('click', '.generar-pdf', function(e) {
        e.preventDefault();
        const tipo = $(this).data('tipo');
        
        if(tipo === 'individual') {
            const url = "{{ route('nempleados.pdf', ['id' => ':id']) }}"
                .replace(':id', $(this).data('id'));
            window.open(url, '_blank');
        } else {
            const fechaDesde = $('#fecha_desde').val();
            const fechaHasta = $('#fecha_hasta').val();
            
            if(!fechaDesde || !fechaHasta) {
                Swal.fire('Error', 'Seleccione ambas fechas', 'error');
                return;
            }

            const url = "{{ url('nomina/generar-recibo-general') }}/" + fechaDesde + "/" + fechaHasta;
            window.open(url, '_blank');
        }
    });
});
</script>
@stop