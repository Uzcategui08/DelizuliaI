@extends('adminlte::page')

@section('title', 'Ventas')

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
                                {{ __('Ventas') }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>ID Venta</th>
                                        <th>Fecha</th>
                                        <th>Técnico</th>
                                        <th>Cliente</th>
                                        <th>Tipo de Trabajo</th>
                                        <th>Métodos de Pago</th>
                                        <th>Titular</th>
                                        <th>Productos</th>
                                        <th>Deuda</th>
                                        <th>Descuento</th>
                                        <th>Comisión</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registroVs as $registroV)
                                        @php
                                            $items = is_array($registroV->items) ? $registroV->items : (json_decode($registroV->items, true) ?? []);
                                            $pagos = is_array($registroV->pagos) ? $registroV->pagos : (json_decode($registroV->pagos, true) ?? []);

                                            $trabajos = collect($items)->pluck('trabajo')->filter()->unique()->toArray();

                                            $metodosPago = [];
                                            foreach ($pagos as $pago) {
                                                $metodo = $pago['metodo_pago'] ?? '';
                                                $monto = isset($pago['monto']) ? number_format($pago['monto'], 2) : '0.00';
                                                $cobrador = isset($pago['cobrador_id']) ? \App\Models\Empleado::find($pago['cobrador_id'])->nombre ?? 'Desconocido' : 'Desconocido';
                                                
                                                if (is_numeric($metodo)) {
                                                    $metodoObj = \App\Models\TiposDePago::find($metodo);
                                                    $nombreMetodo = $metodoObj ? $metodoObj->name : $metodo;
                                                    $metodosPago[] = "$nombreMetodo (\$$monto) - Cobrado por: $cobrador";
                                                } else {
                                                    $metodosPago[] = "$metodo (\$$monto) - Cobrado por: $cobrador";
                                                }
                                            }

                                            $productos = [];
                                            foreach ($items as $item) {
                                                if (isset($item['productos']) && is_array($item['productos'])) {
                                                    foreach ($item['productos'] as $producto) {
                                                        $codigo = $producto['producto'] ?? 'N/A';
                                                        $cantidad = $producto['cantidad'] ?? 0;
                                                        $nombre = $producto['nombre_producto'] ?? 'Producto no especificado';

                                                        $productoInfo = \App\Models\Producto::where('id_producto', $codigo)->first();
                                                        $nombreProducto = $productoInfo ? $productoInfo->item : $nombre;

                                                        $nombreProducto = json_decode('"' . $nombreProducto . '"');
                                                        
                                                        $productos[] = [
                                                            'nombre' => $nombreProducto,
                                                            'codigo' => $codigo,
                                                            'cantidad' => $cantidad
                                                        ];
                                                    }
                                                }
                                            }

                                            $estadosStyles = [
                                                'pagado' => ['class' => 'badge-success', 'icon' => 'fa-check-circle'],
                                                'pendiente' => ['class' => 'badge-danger', 'icon' => 'fa-clock'],
                                                'parcialemente pagado' => ['class' => 'badge-warning', 'icon' => 'fa-money-bill-wave']
                                            ];
                                            $estado = $estadosStyles[strtolower($registroV->estatus)] ?? ['class' => 'badge-secondary', 'icon' => 'fa-question'];
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold">{{ $registroV->id }}</td>
                                            <td>
                                                <span class="text-nowrap">
                                                    <i class="far fa-calendar-alt text-primary mr-1"></i>
                                                    {{ $registroV->fecha_h->format('m/d/Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-nowrap">
                                                    <i class="fas fa-user-tie text-info mr-1"></i>
                                                    {{ $registroV->empleado->nombre ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                                    <i class="fas fa-user mr-1 text-secondary"></i>
                                                     {{ $registroV->cliente?->nombre ?? $registroV->cliente ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($trabajos))
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($trabajos, 0, 2) as $trabajo)
                                                            <span class="badge bg-info text-white">
                                                                {{ json_decode('"' . explode(' - ', $trabajo)[0] . '"') }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($trabajos) > 2)
                                                            <span class="badge bg-light text-dark">+{{ count($trabajos) - 2 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($metodosPago))
                                                    <div class="d-flex flex-column">
                                                        @foreach($metodosPago as $metodo)
                                                            <small class="text-nowrap">
                                                                <i class="fas fa-credit-card mr-1 text-primary"></i>
                                                                {{ $metodo }}
                                                            </small>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-nowrap">
                                                    <i class="fas fa-id-card text-secondary mr-1"></i>
                                                    {{ $registroV->titular_c ?: 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($productos))
                                                    <div class="d-flex flex-column">
                                                        @foreach(array_slice($productos, 0, 2) as $producto)
                                                            <small>
                                                                <span class="font-weight-bold">#{{ $producto['codigo'] }}</span> - 
                                                                {{ $producto['nombre'] }}
                                                                <span class="text-muted">x{{ $producto['cantidad'] }}</span>
                                                            </small>
                                                        @endforeach
                                                        @if(count($productos) > 2)
                                                            <small class="text-muted">+{{ count($productos) - 2 }} más</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-success">
                                                <i class="fas fa-dollar-sign mr-1"></i>
                                                @php
                                                    $totalPagado = collect($pagos)->sum(function($pago) {
                                                        return isset($pago['monto']) ? floatval($pago['monto']) : 0;
                                                    });
                                                    $saldoPendiente = floatval($registroV->valor_v) - $totalPagado;
                                                @endphp
                                                {{ number_format($saldoPendiente, 2) }}
                                            </td>
                                            <td class="font-weight-bold text-danger">
                                                -${{ number_format(floatval(data_get($registroV, 'monto_ce', 0)), 2) }}
                                            </td>
                                            <td class="font-weight-bold text-primary">
                                                <i class="fas fa-percentage mr-1"></i>
                                                {{ number_format($registroV->porcentaje_c, 2) }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $estado['class'] }}">
                                                    <i class="fas {{ $estado['icon'] }} mr-1"></i>
                                                    {{ ucfirst($registroV->estatus) }}
                                                </span>
                                            </td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('registro-vs.show', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('registro-vs.edit', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-edit"></i> 
                                                    </a>
                                                    <a href="{{ route('registro-vs.pdf', $registroV->id) }}" class="btn btn-sm btn-warning" target="_blank">
                                                        <i class="">Es</i>
                                                    </a>
                                                    <a href="{{ route('invoice.pdf', $registroV->id) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="En">En</i>
                                                    </a>
                                                    <form onsubmit="return confirmDelete(this)" action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"> 
                                                            <i class="fa fa-fw fa-trash"></i>
                                                        </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
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
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        .table td {
            vertical-align: middle;
        }
        .text-nowrap {
            white-space: nowrap;
        }
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .text-muted {
            color: #6c757d;
        }
    </style>
@stop