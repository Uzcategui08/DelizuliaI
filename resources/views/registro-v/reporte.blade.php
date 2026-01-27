@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    <h1>Reporte de Cuentas por Cobrar</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form id="filtroForm">
            <div class="row align-items-end">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" 
                               value="{{ request('fecha_desde') ?? date('Y-m-01') }}" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="fecha_hasta">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" 
                               value="{{ request('fecha_hasta') ?? date('Y-m-t') }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="cliente_id">Cliente</label>
                        <select name="cliente_id" id="cliente_id" class="form-control select2">
                            <option value="">Todos los clientes</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}" 
                                    {{ request('cliente_id') == $cliente->id_cliente ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} {{ $cliente->apellido ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="estatus">Estatus</label>
                        <select name="estatus" id="estatus" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estatus') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="parcialemente pagado" {{ request('estatus') == 'parcialemente pagado' ? 'selected' : '' }}>Parcialmente Pagado</option>
                            <option value="pagado" {{ request('estatus') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="language">Lenguaje</label>
                        <select name="language" id="language" class="form-control">
                            <option value="">Seleccionar lenguaje</option>
                            <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>Inglés</option>
                            <option value="es" {{ request('language') == 'es' ? 'selected' : '' }}>Español</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 offset-md-9">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-search mr-1"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="mt-4">
            <div class="border border-top-0 p-3 bg-white">
                <div id="loading-resumen" class="text-center py-5" style="display: none;">
                    <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando resumen por cliente...</p>
                </div>
                <div id="contenido-resumen">
                    @if(isset($resumenClientes))
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Resumen de Cuentas por Cobrar por Cliente</h5>
                            <button class="btn btn-success generar-reporte" 
                                    data-tipo="resumen"
                                    data-fecha-desde="{{ request('fecha_desde') }}"
                                    data-fecha-hasta="{{ request('fecha_hasta') }}"
                                    data-cliente-id="{{ request('cliente_id') }}"
                                    data-estatus="{{ request('estatus') }}">
                                <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                            </button>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-calendar-alt text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">PERÍODO</h6>
                                                <h5 class="mb-0">
                                                    {{ \Carbon\Carbon::parse(request('fecha_desde', date('Y-m-01')))->format('m/d/Y') }} - 
                                                    {{ \Carbon\Carbon::parse(request('fecha_hasta', date('Y-m-t')))->format('m/d/Y') }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-users text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">CLIENTES</h6>
                                                <h5 class="mb-0">{{ $resumenClientes->count() }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-cash-register text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">VENTAS</h6>
                                                <h5 class="mb-0">{{ $resumenClientes->sum('total_ventas') }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-hand-holding-usd text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">SALDO PENDIENTE</h6>
                                                <h5 class="mb-0">${{ number_format($resumenClientes->sum('saldo_pendiente'), 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Teléfono</th>
                                        <th class="text-right">Total Ventas</th>
                                        <th class="text-right">Total Descuento</th>
                                        <th class="text-right">Total Pagado</th>
                                        <th class="text-right">Saldo Pendiente</th>
                                        <th class="text-right">% Pagado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resumenClientes as $cliente)
                                        <tr>
                                            <td>{{ $cliente->cliente }}</td>
                                            <td>{{ $cliente->telefono }}</td>
                                            <td class="text-right">${{ number_format($cliente->total_ventas_monto, 2) }}</td>
                                            <td class="text-right">${{ number_format(data_get($cliente, 'total_descuento', 0), 2) }}</td>
                                            <td class="text-right">${{ number_format($cliente->total_pagado, 2) }}</td>
                                            <td class="text-right">${{ number_format($cliente->saldo_pendiente, 2) }}</td>
                                            <td class="text-right">
                                                @if($cliente->total_ventas > 0)
                                                    {{ number_format(($cliente->total_pagado / $cliente->total_ventas_monto) * 100, 2) }}%
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css"/>
<style>
    .table th {
        border-top: none;
    }
    .badge {
        font-size: 85%;
        padding: 0.35em 0.65em;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #d2d6de;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .card-header {
        background-color: #f8f9fa;
    }
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .table-success {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .thead-dark th {
        background-color: #343a40;
        color: white;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.js"></script>

<script>
    $('#filtroForm').on('submit', function(e) {
        e.preventDefault();
        const fechaDesde = $('#fecha_desde').val();
        const fechaHasta = $('#fecha_hasta').val();
        const language = $('#language').val();
        
        if(!fechaDesde || !fechaHasta) {
            Swal.fire('Error', language === 'es' ? 'Seleccione ambas fechas' : 'Select both dates', 'error');
            return;
        }

        $('#loading-resumen').show();
        $('#contenido-resumen').hide();

        cargarResumenClientes(fechaDesde, fechaHasta, $('#cliente_id').val(), $('#estatus').val(), language);
    });

    function cargarResumenClientes(fechaDesde, fechaHasta, clienteId = '', estatus = '', language = 'es') {
        $.ajax({
            url: "{{ route('reportes.cxc') }}",
            type: 'GET',
            data: { 
                fecha_desde: fechaDesde, 
                fecha_hasta: fechaHasta, 
                cliente_id: clienteId,
                estatus: estatus,
                language: language,
                tipo: 'resumen' 
            },
            success: function(response) {
                $('#contenido-resumen').html($(response).find('#contenido-resumen').html());
                $('#loading-resumen').hide();
                $('#contenido-resumen').show();
            },
            error: function() {
                const errorMsg = language === 'es' ? 'Error al cargar el resumen por cliente' : 'Error loading client summary';
                Swal.fire('Error', errorMsg, 'error');
                $('#loading-resumen').hide();
                $('#contenido-resumen').show();
            }
        });
    }

    $(document).on('click', '.generar-reporte', function() {
        const tipo = $(this).data('tipo');
        const fechaDesde = $(this).data('fecha-desde');
        const fechaHasta = $(this).data('fecha-hasta');
        const clienteId = $(this).data('cliente-id');
        const estatus = $(this).data('estatus');
        const language = $('#language').val();
        
        let url = "{{ url('reportes/cxc/generar-pdf') }}";
        url += `?fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}&tipo=${tipo}&language=${language}`;
        
        if(clienteId) url += `&cliente_id=${clienteId}`;
        if(estatus) url += `&estatus=${estatus}`;
        
        window.open(url, '_blank');
    });
</script>
@stop