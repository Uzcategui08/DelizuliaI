@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Venta #{{ $registroV->id }}</h1>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-store"></i> {{ $registroV->lugarventa }}
                            <small class="float-right">Fecha: {{ \Carbon\Carbon::parse($registroV->fecha_h)->format('m/d/Y') }}</small>
                        </h4>
                    </div>
                </div>

                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <strong>Técnico</strong>
                        <address>
                            <i class="fas fa-user mr-1"></i> {{ $registroV->empleado->nombre }}<br>
                            <i class="fas fa-info-circle mr-1"></i> Estado: 
                            <span class="badge 
                                @if($registroV->estatus == 'pagado') badge-success 
                                @elseif($registroV->estatus == 'parcialemente pagado') badge-warning 
                                @elseif($registroV->estatus == 'pendiente') badge-danger 
                                @else badge-secondary 
                                @endif">
                                {{ ucfirst($registroV->estatus) }}
                            </span>
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <strong><i class="fas fa-car mr-1"></i> Vehículo</strong>
                        <address>
                            Marca: {{ $registroV->marca }}<br>
                            Modelo: {{ $registroV->modelo }}<br>
                            Año: {{ $registroV->año }}
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <strong><i class="fas fa-file-invoice-dollar mr-1"></i> Factura #{{ $registroV->id }}</strong><br>
                        <b>Valor Total:</b> ${{ number_format($registroV->valor_v, 2) }}<br>
                        @if(isset($registroV->monto_ce) && $registroV->monto_ce > 0)
                            <b>Descuento:</b> -${{ number_format($registroV->monto_ce, 2) }}<br>
                        @endif
                        <b>Total Pagado:</b> ${{ number_format($totalPagado = array_reduce($registroV->pagos ?? [], function($carry, $pago) { return $carry + $pago['monto']; }, 0), 2) }}<br>
                        <b>Saldo Pendiente:</b> ${{ number_format($registroV->valor_v - $totalPagado, 2) }}
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Información del Cliente</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cliente</label>
                                            <p class="form-control bg-light">
                                                <i class="fas fa-user mr-1 text-secondary"></i>
                                                {{ $registroV->cliente?->nombre ?? $registroV->cliente ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-tasks mr-2"></i>Items de Trabajo</h3>
                            </div>
                            <div class="card-body">
                                @if(!empty($items) && count($items) > 0)
                                    @foreach($items as $itemIndex => $itemGroup)
                                        <div class="mb-4 p-3 border rounded bg-light">
                                            <h5 class="mb-3"><i class="fas fa-wrench mr-2"></i>Trabajo #{{ $itemIndex + 1 }}</h5>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-4">
                                                        <label class="font-weight-bold">Trabajo:</label>
                                                        <p class="form-control bg-white">{{ $itemGroup['trabajo'] ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-4">
                                                        <label class="font-weight-bold">Precio Trabajo:</label>
                                                        <p class="form-control bg-white text-right">${{ number_format($itemGroup['precio_trabajo'] ?? 0, 2) }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-4">
                                                        <label class="font-weight-bold">Descripción Detallada:</label>
                                                        <p class="form-control bg-white">{{ $itemGroup['descripcion'] ?? 'No hay descripción adicional' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if(!empty($itemGroup['productos']))
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Código</th>
                                                                <th>Producto</th>
                                                                <th class="text-center">Cantidad</th>
                                                                <th>Almacén</th>
                                                                <th class="text-center">P. Unitario</th>
                                                                <th class="text-right">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($itemGroup['productos'] as $producto)
                                                                @php
                                                                    $almacenNombre = $almacenes->firstWhere('id_almacen', $producto['almacen'])->nombre ?? 'N/A';
                                                                    $subtotal = ($producto['precio'] ?? 0) * ($producto['cantidad'] ?? 1);
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $producto['codigo_producto'] ?? $producto['producto'] ?? 'N/A' }}</td>
                                                                    <td>{{ $producto['nombre_producto'] ?? 'N/A' }}</td>
                                                                    <td class="text-center">{{ $producto['cantidad'] ?? 1 }}</td>
                                                                    <td>{{ $almacenNombre }}</td>
                                                                    <td class="text-right">${{ number_format($producto['precio'] ?? 0, 2) }}</td>
                                                                    <td class="text-right">${{ number_format($subtotal, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot class="bg-gray">
                                                            <tr>
                                                                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                                                                <td class="text-right">
                                                                    ${{ number_format(array_reduce($itemGroup['productos'], function($carry, $producto) {
                                                                        return $carry + (($producto['precio'] ?? 0) * ($producto['cantidad'] ?? 1));
                                                                    }, 0), 2) }}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="alert alert-warning">No hay productos registrados para este trabajo</div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-warning">No hay items de trabajo registrados</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>Costos Extras</h3>
                                <div class="card-tools">
                                    <span class="badge badge-warning p-2">
                                        Total: ${{ number_format(array_sum(array_column($costosExtras, 'monto')), 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(!empty($costosExtras) && count($costosExtras) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th class="text-right">Monto</th>
                                                    <th class="text-center">Subcategoría</th>
                                                    <th>Método de Pago</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($costosExtras as $costo)
                                                <tr>
                                                    <td>{{ $costo['descripcion'] ?? 'N/A' }}</td>
                                                    <td class="text-right">${{ number_format($costo['monto'] ?? 0, 2) }}</td>
                                                    <td class="text-center">
                                                        @foreach($categorias as $categoria)
                                                            @if($categoria->id_categoria == ($costo['subcategoria'] ?? null))
                                                                {{ $categoria->nombre }}
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach($tiposDePago as $tipo)
                                                            @if($tipo->id == ($costo['metodo_pago'] ?? null))
                                                                {{ $tipo->name }}
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <span class="badge 
                                                            @if(($costo['cobro'] ?? '') == 'pagado') badge-success 
                                                            @else badge-warning 
                                                            @endif">
                                                            {{ ucfirst($costo['cobro'] ?? 'pendiente') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-gray">
                                                <tr>
                                                    <th colspan="4" class="text-right">% Cerrajero (36%):</th>
                                                    <th class="text-right">${{ number_format($registroV->porcentaje_c, 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">No hay costos extras registrados</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card card-outline card-danger">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>Gastos</h3>
                                <div class="card-tools">
                                    <span class="badge badge-danger p-2">
                                        Total: ${{ number_format(array_sum(array_column($gastos, 'monto')), 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(!empty($gastos) && count($gastos) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th class="text-right">Monto</th>
                                                    <th class="text-center">Subcategoría</th>
                                                    <th>Método de Pago</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($gastos as $gasto)
                                                <tr>
                                                    <td>{{ $gasto['descripcion'] ?? 'N/A' }}</td>
                                                    <td class="text-right">${{ number_format($gasto['monto'] ?? 0, 2) }}</td>
                                                    <td class="text-center">
                                                        @foreach($categorias as $categoria)
                                                            @if($categoria->id_categoria == ($gasto['subcategoria'] ?? null))
                                                                {{ $categoria->nombre }}
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach($tiposDePago as $tipo)
                                                            @if($tipo->id == ($gasto['metodo_pago'] ?? null))
                                                                {{ $tipo->name }}
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <span class="badge 
                                                            @if(($gasto['estatus'] ?? '') == 'pagado') badge-success 
                                                            @else badge-warning 
                                                            @endif">
                                                            {{ ucfirst($gasto['estatus'] ?? 'pendiente') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">No hay gastos registrados</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-credit-card mr-2"></i>Registro de Pagos</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <strong>Valor Total:</strong><br>
                                            <span class="h4">${{ number_format($registroV->valor_v, 2) }}</span>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <strong>Total Pagado:</strong><br>
                                            <span class="h4">${{ number_format($totalPagado, 2) }}</span>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <strong>Saldo Pendiente:</strong><br>
                                            <span class="h4">${{ number_format($registroV->valor_v - $totalPagado, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($registroV->pagos) && count($registroV->pagos) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fecha</th>
                                                    <th>Método</th>
                                                    <th>Cobrador</th>
                                                    <th class="text-right">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($registroV->pagos as $index => $pago)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $pago['fecha'] ?? 'N/A' }}</td>
                                                        <td>
                                                            @php
                                                                $metodoPago = collect($tiposDePago)->firstWhere('id', $pago['metodo_pago'] ?? null);
                                                            @endphp
                                                            {{ $metodoPago->name ?? ($pago['metodo_pago'] ?? 'N/A') }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $cobrador = collect($empleados)->firstWhere('id_empleado', $pago['cobrador_id'] ?? null);
                                                            @endphp
                                                            {{ $cobrador->nombre ?? 'N/A' }}
                                                        </td>
                                                        <td class="text-right">${{ number_format($pago['monto'] ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">No hay pagos registrados</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row no-print mt-3">
                    <div class="col-12">
                        <button onclick="window.print()" class="btn btn-default">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                        <a href="{{ route('registro-vs.edit', $registroV->id) }}" class="btn btn-primary float-right mr-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('registro-vs.index') }}" class="btn btn-secondary float-right mr-2">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .invoice {
        position: relative;
        background: #fff;
        border: 1px solid #f4f4f4;
        padding: 20px;
    }
    
    .invoice-title {
        margin-top: 0;
    }
    
    .table thead th {
        vertical-align: middle;
    }
    
    @media print {
        body {
            font-size: 11pt;
        }
        
        .no-print {
            display: none;
        }
        
        .card-header, .card-body {
            padding: 0.5rem;
        }
        
        .card {
            border: none;
            box-shadow: none;
        }
        
        .table td, .table th {
            padding: 0.3rem;
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    
    $('.btn-print').click(function() {
        window.print();
    });
});
</script>
@endsection